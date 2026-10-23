<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('phone');
            $table->string('domicile')->nullable()->after('gender');
            $table->string('birth_date')->nullable()->after('domicile');
            $table->string('university')->nullable()->after('birth_date');
            $table->string('faculty')->nullable()->after('university');
            $table->string('major')->nullable()->after('faculty');
            $table->string('year')->nullable()->after('major');
            $table->string('semester')->nullable()->after('year');
            $table->string('gpa')->nullable()->after('semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'domicile',
                'birth_date',
                'university',
                'faculty',
                'major',
                'year',
                'semester',
                'gpa',
            ]);
        });
    }
};
