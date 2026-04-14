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
        Schema::create('stok_pcs', function (Blueprint $table) {
            $table->id('idStokPcs');
            $table->unsignedBigInteger('idCabang');
            $table->unsignedBigInteger('idPcs');
            $table->integer('jumlahStok');
            $table->timestamps();

            $table->foreign('idCabang')
                ->references('idCabang')
                ->on('cabangs')
                ->cascadeOnDelete();

            $table->foreign('idPcs')
                ->references('idPcs')
                ->on('pcs_tahus')
                ->cascadeOnDelete();

            $table->unique(['idCabang', 'idPcs']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_pcs');
    }
};
