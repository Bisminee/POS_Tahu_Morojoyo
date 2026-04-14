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
            $table->unsignedBigInteger('id_cabang');
            $table->unsignedBigInteger('id_pcs_tahu');
            $table->integer('jumlah_stok');
            $table->timestamps();

            $table->foreign('id_cabang')
                ->references('idCabang')
                ->on('cabangs')
                ->cascadeOnDelete();

            $table->foreign('id_pcs_tahu')
                ->references('id_pcs')
                ->on('pcs_tahus')
                ->cascadeOnDelete();

            $table->unique(['id_cabang', 'id_pcs_tahu']);
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
