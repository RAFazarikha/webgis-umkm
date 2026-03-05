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
        Schema::create('cluster_results', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel UMKM
            $table->foreignId('umkm_id')
                ->constrained('umkms')
                ->onDelete('cascade');

            // Hasil cluster
            $table->integer('cluster')->nullable();
            $table->boolean('is_noise')->default(false);

            // Jenis filter clustering (misal: makanan_berat, minuman, dll)
            $table->string('filter');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cluster_results');
    }
};
