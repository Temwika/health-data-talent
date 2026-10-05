<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\User;
use App\Models\Vacancy;
use App\Support\Totp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    private function candidatePayload(array $overrides = []): array
    {
        return array_merge([
            'track' => 'data', 'name' => 'Amina Khan', 'email' => 'amina@example.com',
            'location' => 'Leeds, UK', 'current_title' => 'Data Analyst', 'years' => '2–4',
            'desired_role' => 'Senior Health Data Analyst', 'tools' => ['SQL', 'Power BI'],
            'privacy' => '1',
        ], $overrides);
    }

    private function liveVacancy(): Vacancy
    {
        $vacancy = new Vacancy([
            'title' => 'Health Data Analyst', 'organisation_name' => 'ICB', 'location' => 'Leeds',
            'pattern' => 'Hybrid', 'salary' => '£40,000', 'contract_type' => 'Permanent',
            'skills' => 'SQL, Power BI', 'description' => 'Reporting.',
        ]);
        $vacancy->status = 'live';
        $vacancy->published_at = now();
        $vacancy->save();

        return $vacancy;
    }

    public function test_public_pages_load_with_security_headers(): void
    {
        $this->seed();

        foreach (['/', '/employers', '/employers/vacancy', '/candidates', '/doctors', '/jobs', '/about', '/insights', '/contact', '/privacy', '/terms'] as $url) {
            $this->get($url)->assertOk();
        }

        $job = Vacancy::live()->first();
        $this->get('/jobs/'.$job->slug)->assertOk()->assertSee($job->title);
        $this->get('/jobs?q=engineer')->assertOk()->assertSee('Healthcare Data Engineer')->assertDontSee('Population Health Analyst');

        $response = $this->get('/');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $this->assertStringContainsString("script-src 'self'", $response->headers->get('Content-Security-Policy'));
        $this->assertStringNotContainsString('style="', $response->getContent());
    }

    public function test_seeder_resets_admin_password_from_env(): void
    {
        config(['hdt.admin_email' => 'admin@example.com', 'hdt.admin_password' => 'first-password-123']);
        $this->seed();
        $admin = User::where('email', 'admin@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('first-password-123', $admin->password));

        config(['hdt.admin_password' => 'second-password-456']);
        $this->seed();
        $this->assertTrue(Hash::check('second-password-456', $admin->fresh()->password));
        $this->assertSame(1, User::where('role', 'admin')->count());
    }

    public function test_candidate_registers_with_cv_stored_privately(): void
    {
        Storage::fake('local');
        $vacancy = $this->liveVacancy();
        $cv = UploadedFile::fake()->createWithContent('cv.pdf', "%PDF-1.4\nfake");

        $this->post('/candidates', $this->candidatePayload(['cv' => $cv, 'job' => $vacancy->slug, 'marketing' => '1']))
            ->assertRedirect('/candidates')->assertSessionHas('status');

        $candidate = Candidate::firstOrFail();
        Storage::disk('local')->assertExists($candidate->cv_path);
        $this->assertStringStartsWith('cvs/', $candidate->cv_path);
        $this->assertSame('cv.pdf', $candidate->cv_original_name);
        $this->assertCount(2, $candidate->consents);
        $this->assertSame($vacancy->id, $candidate->applications->first()->vacancy_id);

        // Deleting the profile removes the file too.
        $path = $candidate->cv_path;
        $candidate->delete();
        Storage::disk('local')->assertMissing($path);
    }

    public function test_cv_with_wrong_contents_is_rejected(): void
    {
        Storage::fake('local');
        $fake = UploadedFile::fake()->createWithContent('cv.pdf', '<?php echo 1;');

        $this->post('/candidates', $this->candidatePayload(['cv' => $fake]))->assertSessionHasErrors('cv');
        $this->assertSame(0, Candidate::count());
    }

    public function test_validation_and_honeypot(): void
    {
        $this->post('/candidates', $this->candidatePayload(['privacy' => null, 'email' => 'nope']))
            ->assertSessionHasErrors(['privacy', 'email']);
        $this->post('/candidates', $this->candidatePayload(['track' => 'doctor']))->assertSessionHasErrors('specialty');
        $this->post('/candidates', $this->candidatePayload(['website' => 'http://spam.example']))->assertRedirect();

        $this->assertSame(0, Candidate::count());
    }

    public function test_submitted_vacancy_is_hidden_until_approved(): void
    {
        $this->post('/employers/vacancy', [
            'title' => 'BI Developer', 'organisation_name' => 'Trust', 'location' => 'York', 'pattern' => 'Hybrid',
            'salary' => '£50,000', 'contract_type' => 'Permanent', 'skills' => 'Power BI', 'description' => 'Dashboards.',
            'contact_name' => 'Sam Lee', 'contact_email' => 'sam@example.org', 'privacy' => '1',
        ])->assertRedirect('/employers/vacancy');

        $vacancy = Vacancy::firstOrFail();
        $this->assertSame('pending', $vacancy->status);
        $this->get('/jobs/'.$vacancy->slug)->assertNotFound();
        $this->get('/jobs')->assertDontSee('BI Developer');
    }

    public function test_admin_requires_login_and_two_factor(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/candidates')->assertRedirect('/admin/login');

        $user = User::factory()->create(['password' => 'correct-horse-battery']);
        $user->forceFill(['role' => 'recruiter'])->save();

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/admin/login', ['email' => $user->email, 'password' => 'correct-horse-battery'])->assertRedirect('/admin/candidates'); // back to the page first asked for

        // Signed in but no second factor yet: forced to enrol.
        $this->get('/admin')->assertRedirect('/admin/two-factor/setup');
        $this->get('/admin/two-factor/setup')->assertOk();
        $secret = $user->fresh()->two_factor_secret;

        $this->post('/admin/two-factor/setup', ['code' => '000000'])->assertSessionHasErrors('code');
        $this->post('/admin/two-factor/setup', ['code' => Totp::code($secret)])->assertRedirect('/admin');
        $this->get('/admin')->assertOk();

        // Recruiters cannot see the audit log or delete.
        $this->get('/admin/audit')->assertForbidden();
        foreach (['candidates', 'employers', 'vacancies', 'vacancies/create', 'applications', 'enquiries', 'posts', 'posts/create'] as $page) {
            $this->get('/admin/'.$page)->assertOk();
        }
    }

    public function test_admin_can_approve_vacancy_download_cv_and_delete(): void
    {
        Storage::fake('local');
        $this->post('/candidates', $this->candidatePayload(['cv' => UploadedFile::fake()->createWithContent('cv.pdf', '%PDF-1.4 x')]));
        $candidate = Candidate::firstOrFail();

        $vacancy = $this->liveVacancy();
        $vacancy->forceFill(['status' => 'pending'])->save();

        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin', 'two_factor_secret' => Totp::generateSecret(), 'two_factor_confirmed_at' => now()])->save();

        // Password alone is not enough.
        $this->actingAs($admin)->get('/admin/candidates/'.$candidate->id.'/cv')->assertRedirect('/admin/two-factor');
        $this->post('/admin/two-factor', ['code' => Totp::code($admin->two_factor_secret)])->assertRedirect('/admin');

        $this->get('/admin/vacancies')->assertOk()->assertSee('Amina Khan');
        $this->patch('/admin/vacancies/'.$vacancy->id.'/status', ['status' => 'live'])->assertRedirect();
        $this->get('/jobs/'.$vacancy->slug)->assertOk();

        $this->get('/admin/candidates/'.$candidate->id)->assertOk()->assertSee('amina@example.com');
        $this->get('/admin/candidates/'.$candidate->id.'/cv')->assertOk()->assertDownload('cv.pdf');
        $this->post('/admin/candidates/'.$candidate->id.'/applications', ['vacancy_id' => $vacancy->id, 'consent' => '1'])->assertRedirect();
        $this->get('/admin/applications')->assertOk()->assertSee('Amina Khan');
        $this->get('/admin/audit')->assertOk()->assertSee('cv.downloaded');

        $this->get('/admin')->assertOk();
        $this->get('/admin/vacancies/'.$vacancy->id.'/edit')->assertOk()->assertSee('Health Data Analyst');
        $this->post('/admin/posts', ['title' => 'Hello', 'category' => 'Hiring', 'excerpt' => 'Short.', 'body' => "## Head\n\nText", 'published' => '1'])->assertRedirect('/admin/posts');
        $post = \App\Models\Post::firstOrFail();
        $this->get('/admin/posts/'.$post->id.'/edit')->assertOk();
        $this->get('/insights/'.$post->slug)->assertOk()->assertSee('Head');
        $this->patch('/admin/candidates/'.$candidate->id, ['status' => 'screened', 'notes' => 'Called.', 'contacted' => '1'])->assertRedirect();
        $this->patch('/admin/applications/'.$candidate->applications()->first()->id, ['stage' => 'placed'])->assertRedirect();
        $this->assertSame('placed', $candidate->fresh()->status);

        $this->delete('/admin/candidates/'.$candidate->id)->assertRedirect('/admin/candidates');
        $this->assertSame(0, Candidate::count());
        Storage::disk('local')->assertMissing($candidate->cv_path);
    }

    public function test_retention_command_prunes_old_profiles(): void
    {
        $this->post('/candidates', $this->candidatePayload());
        $this->post('/candidates', $this->candidatePayload(['email' => 'old@example.com']));
        Candidate::where('email', 'old@example.com')->update(['last_contact_at' => now()->subMonths(25)]);

        $this->artisan('hdt:prune-candidates')->assertSuccessful();

        $this->assertSame(['amina@example.com'], Candidate::pluck('email')->all());
    }
}
