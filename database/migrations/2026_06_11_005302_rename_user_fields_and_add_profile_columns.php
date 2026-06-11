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
            $table->renameColumn('name', 'nama_anggota');
            $table->renameColumn('username', 'id_anggota');
            $table->string('ttl')->nullable()->after('id_anggota');
            $table->string('jenis_kelamin')->nullable()->after('ttl');
            $table->string('no_telp')->nullable()->after('jenis_kelamin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ttl', 'jenis_kelamin', 'no_telp']);
            $table->renameColumn('nama_anggota', 'name');
            $table->renameColumn('id_anggota', 'username');
        });
    }
};
