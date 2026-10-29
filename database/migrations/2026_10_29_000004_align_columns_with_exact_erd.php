<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Refuse narrowing if existing values would be truncated or made invalid.
        if (DB::table('users')->whereRaw('CHAR_LENGTH(nama_lengkap) > 100')->exists()) {
            throw new RuntimeException('ERD migration aborted: users.nama_lengkap contains values longer than 100 characters.');
        }
        if (DB::table('users')->whereRaw('CHAR_LENGTH(email) > 150')->exists()) {
            throw new RuntimeException('ERD migration aborted: users.email contains values longer than 150 characters.');
        }
        if (DB::table('detail_penumpang')->whereNull('umur')->exists()) {
            throw new RuntimeException('ERD migration aborted: detail_penumpang.umur has NULL values; provide ages before making the column required.');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_lengkap', 100)->change();
            $table->string('email', 150)->change();
        });
        Schema::table('armada', function (Blueprint $table) {
            $table->integer('kapasitas')->change();
        });
        Schema::table('jadwal', function (Blueprint $table) {
            $table->integer('kapasitas_tersedia')->change();
        });
        Schema::table('detail_penumpang', function (Blueprint $table) {
            $table->integer('umur')->nullable(false)->change();
        });
        Schema::table('rute', function (Blueprint $table) {
            $table->decimal('tarif_reguler', 12, 2)->change();
            $table->decimal('tarif_charter', 12, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_lengkap', 255)->change();
            $table->string('email', 255)->change();
        });
        Schema::table('armada', function (Blueprint $table) {
            $table->unsignedInteger('kapasitas')->change();
        });
        Schema::table('jadwal', function (Blueprint $table) {
            $table->unsignedInteger('kapasitas_tersedia')->change();
        });
        Schema::table('detail_penumpang', function (Blueprint $table) {
            $table->unsignedInteger('umur')->nullable()->change();
        });
        Schema::table('rute', function (Blueprint $table) {
            $table->decimal('tarif_reguler', 12, 2)->default(0)->change();
            $table->decimal('tarif_charter', 12, 2)->default(0)->change();
        });
    }
};
