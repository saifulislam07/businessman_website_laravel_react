<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $this->contentColumns($table);
            $table->timestamps();
        });

        Schema::create('abouts', function (Blueprint $table) {
            $table->id();
            $table->longText('bio');
            $table->text('vision')->nullable();
            $this->contentColumns($table);
            $table->timestamps();
        });

        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('value');
            $table->string('icon')->nullable();
            $this->contentColumns($table);
            $table->timestamps();
        });

        Schema::create('ventures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('industry');
            $table->enum('country', ['BN', 'MY', 'OTHER'])->default('BN');
            $table->text('description')->nullable();
            $table->string('website_url')->nullable();
            $table->unsignedSmallInteger('founded_year')->nullable();
            $this->contentColumns($table);
            $table->timestamps();
        });

        Schema::create('expertises', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $this->contentColumns($table);
            $table->timestamps();
        });

        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->string('year', 10);
            $table->string('title');
            $table->text('description')->nullable();
            $this->contentColumns($table);
            $table->timestamps();
        });

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('caption')->nullable();
            $table->string('category')->nullable()->index();
            $this->contentColumns($table);
            $table->timestamps();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url')->nullable();
            $this->contentColumns($table);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('company')->nullable();
            $table->text('quote');
            $this->contentColumns($table);
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->string('subject', 150)->nullable();
            $table->text('message');
            $table->string('ip', 45)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['contact_messages', 'testimonials', 'partners', 'gallery_items', 'milestones', 'expertises', 'ventures', 'stats', 'abouts', 'settings'] as $t) {
            Schema::dropIfExists($t);
        }
    }

    private function contentColumns(Blueprint $table): void
    {
        $table->integer('sort_order')->default(0);
        $table->boolean('is_active')->default(true);
    }
};
