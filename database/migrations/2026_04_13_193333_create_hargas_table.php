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
        Schema::create('hargas', function (Blueprint $table) {
            $table->id('idHarga');
            $table->unsignedBigInteger('idMenu');
            $table->enum('metodePayment', [
                'takeAwayCash',
                'takeAwayQris',
                'shopeefood',
                'gofood',
            ]);
            $table->decimal('harga', 12, 2);
            $table->timestamps();

            $table->foreign('idMenu')
                ->references('idMenu')
                ->on('menus')
                ->cascadeOnDelete();

            $table->unique(['idMenu', 'metode_payment']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hargas');
    }
};
