<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Preserve existing admin accounts as PIC accounts before removing the ADM enum value.
        $adminIds = DB::table('users')->where('role', 'ADM')->pluck('id');
        $now = Carbon::now();

        foreach ($adminIds as $userId) {
            DB::table('pic')->insertOrIgnore([
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('users')->where('role', 'ADM')->update(['role' => 'PIC']);
        DB::table('kode_akses')->where('role', 'ADM')->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['MHS', 'PIC'])->default('MHS')->change();
        });

        Schema::table('kode_akses', function (Blueprint $table) {
            $table->enum('role', ['PIC'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['MHS', 'PIC', 'ADM'])->default('MHS')->change();
        });

        Schema::table('kode_akses', function (Blueprint $table) {
            $table->enum('role', ['PIC', 'ADM'])->change();
        });
    }
};
