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
        Schema::create('buku', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penulis_id')->constrained('penulis')->cascadeOnDelete();
            $table->string('judul');
            $table->text('sinopsis');
            $table->string('sampul')->nullable();
            $table->string('file_pdf');
            $table->integer('jumlah_halaman');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
