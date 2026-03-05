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
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();

            // Identitas UMKM
            $table->string('nama_usaha');
            $table->enum('kategori', [
                'makanan_berat',
                'camilan_oleh_oleh',
                'makanan_khas',
                'minuman'
            ]);

            // Informasi Lokasi & Operasional
            $table->string('alamat', 1000);
            $table->foreignId('subdistrict_id')
                ->constrained('subdistricts')
                ->onDelete('cascade');
            $table->string('jam_operasional')->nullable();
            $table->double('rating')->nullable();
            $table->integer('jumlah_ulasan')->nullable();

            // Data Spasial (Input utama DBSCAN)
            $table->double('latitude');
            $table->double('longitude');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};
