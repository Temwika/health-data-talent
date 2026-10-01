<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('sector', 60);
            $table->string('contact_name', 100);
            $table->string('contact_title', 100)->nullable();
            $table->string('email', 160);
            $table->text('phone')->nullable(); // encrypted
            $table->string('service', 30);
            $table->text('message')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();
        });

        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 120);
            $table->string('slug', 160)->unique();
            $table->string('organisation_name', 120);
            $table->string('location', 80);
            $table->string('pattern', 40);
            $table->string('salary', 60);
            $table->string('contract_type', 60)->default('Permanent');
            $table->string('area', 20)->default('data')->index();
            $table->string('skills', 300);
            $table->text('description');
            $table->string('contact_name', 100)->nullable();
            $table->string('contact_email', 160)->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->boolean('is_example')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('track', 10)->default('data')->index();
            $table->string('name', 100);
            $table->string('email', 160)->index();
            $table->text('phone')->nullable(); // encrypted
            $table->string('location', 80);
            $table->string('current_title', 100);
            $table->string('years', 20);
            $table->string('qualification', 60)->nullable();
            $table->string('specialty', 100)->nullable();
            $table->text('registration')->nullable(); // encrypted
            $table->json('expertise')->nullable();
            $table->json('tools')->nullable();
            $table->json('ngo_work')->nullable();
            $table->string('desired_role', 100);
            $table->string('pattern', 20)->nullable();
            $table->string('salary', 40)->nullable();
            $table->string('availability', 30)->nullable();
            $table->string('right_to_work', 60)->nullable();
            $table->string('cv_path')->nullable();
            $table->string('cv_original_name', 160)->nullable();
            $table->unsignedInteger('cv_size')->nullable();
            $table->string('cv_scan_status', 20)->nullable();
            $table->boolean('marketing_opt_in')->default(false);
            $table->string('status', 20)->default('new')->index();
            $table->text('notes')->nullable();
            $table->timestamp('last_contact_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vacancy_id')->constrained()->cascadeOnDelete();
            $table->string('stage', 20)->default('new')->index();
            $table->string('source', 20)->default('applied'); // applied | matched
            $table->timestamp('consented_at')->nullable(); // candidate agreed to be put forward
            $table->timestamps();
            $table->unique(['candidate_id', 'vacancy_id']);
        });

        Schema::create('consents', function (Blueprint $table) {
            $table->id();
            $table->morphs('subject');
            $table->string('type', 30);
            $table->string('version', 20);
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('granted_at');
            $table->timestamp('withdrawn_at')->nullable();
        });

        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 160);
            $table->string('organisation', 120)->nullable();
            $table->string('subject', 120);
            $table->text('message');
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 140);
            $table->string('slug', 160)->unique();
            $table->string('category', 40);
            $table->string('excerpt', 300);
            $table->text('body');
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 60)->index();
            $table->string('subject_type', 60)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('summary', 255)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->index();
        });
    }

    public function down(): void
    {
        foreach (['audit_logs', 'posts', 'enquiries', 'consents', 'applications', 'candidates', 'vacancies', 'organisations'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
