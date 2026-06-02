<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            $table->text('link_gmaps')->nullable();
            $table->text('detail_alamat')->nullable();
            $table->string('foto')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            $table->dropColumn(['link_gmaps', 'detail_alamat', 'foto']);
        });
    }
};
