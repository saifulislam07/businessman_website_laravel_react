<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Lets a milestone span a range, e.g. "2010 - 2011". */
    public function up(): void
    {
        Schema::table('milestones', fn (Blueprint $table) => $table->string('year', 20)->change());
    }

    public function down(): void
    {
        Schema::table('milestones', fn (Blueprint $table) => $table->string('year', 10)->change());
    }
};
