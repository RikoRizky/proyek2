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
        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email');
            $table->string('whatsapp', 20);
            $table->string('perusahaan');           // Perguruan Tinggi
            $table->string('jabatan');
            $table->json('kebutuhan');              // Array pilihan kebutuhan (maks 3)
            $table->text('kebutuhan_lainnya')->nullable(); // Jika pilih "Lainnya"
            $table->string('sistem_saat_ini');      // internal/vendor/community/none
            $table->string('investasi');            // near/budgeted/planning/next
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussions');
    }
};
