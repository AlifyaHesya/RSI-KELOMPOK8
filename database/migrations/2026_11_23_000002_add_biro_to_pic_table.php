<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pic', function (Blueprint $table) {
            $table->string('biro', 100)->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('pic', function (Blueprint $table) {
            $table->dropColumn('biro');
        });
    }
};
