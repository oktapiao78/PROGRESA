<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Preserve existing accounts and data, but normalize legacy staff roles
        // into Dosen because PROGRESA now exposes only Mahasiswa and Dosen.
        DB::table('users')
            ->whereNotIn('role', ['mahasiswa', 'dosen'])
            ->update(['role' => 'dosen']);
    }

    public function down(): void
    {
        // Legacy role values are intentionally not reconstructed.
    }
};
