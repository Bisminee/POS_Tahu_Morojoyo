<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('attendances');

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('karyawan_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('cabang_id')->nullable();

            $table->date('tanggal');
            $table->timestamp('jam_masuk')->nullable();
            $table->timestamp('jam_pulang')->nullable();

            $table->string('foto_masuk')->nullable();
            $table->string('foto_pulang')->nullable();

            $table->float('face_confidence_masuk')->nullable();
            $table->float('face_confidence_pulang')->nullable();

            $table->enum('status', ['sedang_shift', 'selesai'])->default('sedang_shift');
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->foreign('karyawan_id')
                ->references('idKaryawan')
                ->on('karyawans')
                ->cascadeOnDelete();

            $table->foreign('cabang_id')
                ->references('idCabang')
                ->on('cabangs')
                ->nullOnDelete();

            $table->index(['karyawan_id', 'tanggal']);
            $table->index(['cabang_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};