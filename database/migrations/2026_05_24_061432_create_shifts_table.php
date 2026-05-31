<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel jadwal sesi (siang/sore, per hari per user)
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('sesi', ['siang', 'sore']);
            $table->date('tanggal');
            $table->time('jam_mulai');   // batas absen masuk (misal 10:00)
            $table->time('jam_selesai'); // jam sesi otomatis berakhir (misal 15:00)
            $table->integer('toleransi_menit')->default(15); // toleransi telat
            $table->timestamps();

            // 1 user hanya punya 1 sesi per hari
            $table->unique(['user_id', 'tanggal', 'sesi']);
        });

        // Tabel rekap absensi
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Jam masuk aktual
            $table->timestamp('jam_masuk')->nullable();
            $table->enum('status_masuk', ['tepat_waktu', 'telat', 'tidak_hadir'])->default('tidak_hadir');
            $table->integer('telat_menit')->default(0);

            // Foto wajah saat absen (path ke storage)
            $table->string('foto_absen')->nullable();
            $table->float('face_confidence')->nullable(); // skor kecocokan wajah 0-1

            // Jam keluar (manual atau otomatis)
            $table->timestamp('jam_keluar')->nullable();
            $table->enum('jenis_keluar', ['manual', 'otomatis', 'pergantian'])->nullable();

            // Kalau ada pergantian — siapa penggantinya
            $table->foreignId('digantikan_oleh')->nullable()->constrained('users')->nullOnDelete();

            // Catatan owner/manager
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('shifts');
    }
};