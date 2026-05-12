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
        Schema::create('identitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_brand');
            $table->text('deskripsi_brand');
            $table->string('nomor_whatsapp');
            $table->string('nama_ig');
            $table->string('link_wa')->nullable();
            $table->string('link_ig')->nullable();
            $table->string('jam_buka');
            $table->string('jam_tutup');
            $table->string('logo')->nullable();
            $table->text('promo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identitas');
    }
};
