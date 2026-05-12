<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hargas', function (Blueprint $table) {
            $table->decimal('harga_normal', 12, 2)->nullable()->after('idMenu');
            $table->decimal('harga_gofood', 12, 2)->nullable()->after('harga_normal');
            $table->decimal('harga_shopeefood', 12, 2)->nullable()->after('harga_gofood');
        });

        $menus = DB::table('hargas')->select('idMenu')->distinct()->get();

        foreach ($menus as $menu) {
            $records = DB::table('hargas')->where('idMenu', $menu->idMenu)->get();

            $hargaNormal = null;
            $hargaGofood = null;
            $hargaShopeefood = null;

            foreach ($records as $record) {
                if (in_array($record->metode_payment, ['take_away_cash', 'take_away_qris'])) {
                    if ($hargaNormal === null) {
                        $hargaNormal = $record->harga;
                    }
                }

                if ($record->metode_payment === 'gofood') {
                    $hargaGofood = $record->harga;
                }

                if ($record->metode_payment === 'shopeefood') {
                    $hargaShopeefood = $record->harga;
                }
            }

            $firstId = DB::table('hargas')
                ->where('idMenu', $menu->idMenu)
                ->orderBy('idHarga')
                ->value('idHarga');

            if ($firstId) {
                DB::table('hargas')
                    ->where('idHarga', $firstId)
                    ->update([
                        'harga_normal' => $hargaNormal,
                        'harga_gofood' => $hargaGofood,
                        'harga_shopeefood' => $hargaShopeefood,
                    ]);

                DB::table('hargas')
                    ->where('idMenu', $menu->idMenu)
                    ->where('idHarga', '!=', $firstId)
                    ->delete();
            }
        }

        Schema::table('hargas', function (Blueprint $table) {
            $table->dropColumn(['metode_payment', 'harga']);
        });
    }

    public function down(): void
    {
        Schema::table('hargas', function (Blueprint $table) {
            $table->string('metode_payment')->nullable()->after('idMenu');
            $table->decimal('harga', 12, 2)->nullable()->after('metode_payment');
        });

        Schema::table('hargas', function (Blueprint $table) {
            $table->dropColumn(['harga_normal', 'harga_gofood', 'harga_shopeefood']);
        });
    }
};