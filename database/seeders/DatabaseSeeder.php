<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Post;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();
        $this->seedExampleVacancies();
        $this->seedPosts();
    }

    private function seedAdmin(): void
    {
        $email = config('hdt.admin_email');
        $configured = config('hdt.admin_password');

        // ADMIN_PASSWORD, when set, is the source of truth: changing it and redeploying resets a lost login.
        $existing = User::where('email', $email)->first();
        if ($existing) {
            if ($configured && ! Hash::check($configured, $existing->password)) {
                $existing->forceFill(['password' => $configured])->save();
                AuditLog::record('user.saved', $existing, "Password for {$email} reset from ADMIN_PASSWORD");
                $this->command?->warn("Admin password for {$email} reset from ADMIN_PASSWORD.");
            }

            return;
        }

        // Never create a second admin: this seeder also runs on every deploy.
        if (User::where('role', 'admin')->exists()) {
            return;
        }

        // No default password is baked into the code: use ADMIN_PASSWORD or a random one, shown once.
        $password = $configured ?: Str::password(20, symbols: false);

        $user = new User(['name' => 'Site administrator', 'email' => $email, 'password' => $password]);
        $user->role = 'admin';
        $user->save();

        $this->command?->warn('Admin login created');
        $this->command?->line('  Email:    '.$email);
        $this->command?->line('  Password: '.($configured ?'(from ADMIN_PASSWORD in .env)' : $password));
        $this->command?->line('  Sign in at /admin/login.');
    }

    private function seedExampleVacancies(): void
    {
        if (Vacancy::where('is_example', true)->exists()) {
            return;
        }

        $examples = [
            ['data', 'Health Data Analyst', 'Integrated care board', 'Leeds', 'Hybrid', '£38,000–£45,000', 'Permanent', 'SQL, Power BI, NHS datasets',
                'Build reporting on elective recovery and urgent care across the system, working with clinical leads to turn SUS and local data into decisions.'],
            ['informatics', 'Clinical Informatics Specialist', 'Acute hospital trust', 'Manchester', 'Hybrid', '£46,000–£54,000', 'Permanent', 'EPR/EHR, Epic, Clinical informatics',
                'Support optimisation of the trust\'s electronic patient record, working between clinicians and the systems team on workflow design.'],
            ['digital', 'Healthcare Data Engineer', 'Health-tech scale-up', 'London', 'Remote (UK)', '£60,000–£72,000', 'Permanent', 'Python, Azure, FHIR/HL7',
                'Design and run pipelines that bring GP and hospital data into a secure analytics platform.'],
            ['data', 'Population Health Analyst', 'Local authority public health team', 'Bristol', 'Hybrid', '£42,000–£48,000', 'Permanent', 'R, SQL, Population health',
                'Produce needs assessments and evaluate prevention programmes using linked health and social data.'],
            ['doctor', 'Remote Clinical Adviser (Doctor)', 'International health NGO', 'Remote', 'Remote (international)', 'Day rate, agreed with NGO', 'Fixed-term (engaged directly by employer)', 'Clinical advisory, Guideline development',
                'Review maternal and child health protocols for programmes in sub-Saharan Africa, part-time and fully remote.'],
        ];

        foreach ($examples as $i => [$area, $title, $org, $location, $pattern, $salary, $contract, $skills, $description]) {
            $vacancy = new Vacancy([
                'area' => $area, 'title' => $title, 'organisation_name' => $org, 'location' => $location,
                'pattern' => $pattern, 'salary' => $salary, 'contract_type' => $contract,
                'skills' => $skills, 'description' => $description,
            ]);
            $vacancy->is_example = true;
            $vacancy->status = 'live';
            $vacancy->published_at = now()->subMinutes($i);
            $vacancy->save();
        }
    }

    private function seedPosts(): void
    {
        if (Post::exists()) {
            return;
        }

        $posts = [
            ['Hiring', 'Writing a health data job advert that the right people answer',
                'Most adverts list tools. The candidates you want are looking for the datasets, the decisions and the team.',
                "A typical advert for a health data analyst reads as a list of software: SQL, Power BI, Excel. Every analyst has those. What separates one role from another, and what experienced candidates look for, is everything the advert leaves out.\n\n## Name the data\n\nSay which datasets the person will work with. Someone who has spent three years in SUS and HES extracts reads an advert very differently from someone whose experience is in primary care systems. Naming the data lets the right people recognise themselves.\n\n## Name the decisions\n\nDescribe what the analysis is for. Reporting on elective recovery to an executive board is a different job from supporting a clinical audit, even when the job title is the same.\n\n## Be plain about working pattern and salary\n\nAdverts without a salary range or a clear on-site expectation get fewer replies from people who already have a job. Those are usually the people you most want to hear from.\n\n## Keep the essentials short\n\nThree or four essential skills is enough. A long list of essentials tells good candidates that the team has not decided what the job is.",
            ],
            ['Careers', 'Moving from a clinical role into health informatics',
                'Clinicians bring something informatics teams struggle to hire: knowing how the work is really done. Here is how to present it.',
                "Nurses, pharmacists, allied health professionals and doctors move into informatics every year. The ones who do it well tend to describe their experience in a particular way.\n\n## Lead with the workflow, not the system\n\nBeing a confident user of an electronic patient record is a start. Being the person who noticed that a discharge workflow forced staff to enter the same information twice, and who worked with the systems team to fix it, is what a hiring manager remembers.\n\n## Collect evidence of change\n\nSuper-user roles, clinical safety work, testing during a go-live, training colleagues: write each one down with what changed as a result.\n\n## Learn enough of the technical language\n\nYou do not need to be a developer. Knowing what an interface, a data dictionary and a clinical safety case are will let you take part in the conversation from day one.\n\n## Expect a sideways step\n\nA first informatics post sometimes pays the same as, or slightly less than, a senior clinical role. Ask about the route beyond it before deciding.",
            ],
            ['Global health', 'What remote NGO work looks like for doctors',
                'Advisory, guideline, review and training contracts can be done from home. A short guide to what NGOs ask for.',
                "Not all global health work means relocating. NGOs and international health organisations regularly need clinical expertise for work that is done at a desk.\n\n## The kinds of contract\n\nThe most common are technical review of protocols and guidelines, clinical advisory input to programme design, remote case review, evaluation and research support, and online training for health workers. Most are part-time and time-limited.\n\n## What organisations look for\n\nCurrent registration, a clear specialty, and evidence that you can write. Much of the work ends in a document: a reviewed guideline, a set of recommendations, a training module. Experience in low-resource settings helps but is not always essential.\n\n## Licensing matters\n\nAdvisory and review work is usually possible on your existing registration. Work that involves treating patients, including by telemedicine, may require registration in the country where the patient is. The NGO is responsible for checking this, and you should ask how they have done so.\n\n## How contracts work\n\nDoctors are normally engaged directly by the NGO as consultants. Agree the scope, the deliverables and the day rate in writing before starting.",
            ],
        ];

        foreach ($posts as $i => [$category, $title, $excerpt, $body]) {
            $post = new Post(compact('category', 'title', 'excerpt', 'body'));
            $post->published_at = now()->subDays($i * 9 + 2);
            $post->save();
        }
    }
}
