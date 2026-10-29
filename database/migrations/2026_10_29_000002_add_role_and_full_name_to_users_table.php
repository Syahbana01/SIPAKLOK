<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->restrictOnDelete();
            $table->renameColumn('name', 'nama_lengkap');
        });
        DB::table('roles')->insertOrIgnore(['nama_role' => 'penumpang', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('users')->whereNull('role_id')->update([
            'role_id' => DB::table('roles')->where('nama_role', 'penumpang')->value('id'),
        ]);
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable(false)->change();
            $table->string('no_telepon', 20)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn('no_telepon');
            $table->renameColumn('nama_lengkap', 'name');
        });
    }
};
