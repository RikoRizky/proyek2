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
        Schema::create('lkps_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requirement_id')->nullable()->constrained()->nullOnDelete();
            $table->string('table_code', 30)->index(); // Contoh: '2.B.1', '2.B.2', '1.A.4'
            $table->string('table_name')->nullable();
            $table->json('dynamic_headers')->nullable(); // Misal untuk kolom dinamis: PL 1, PL 2..
            $table->json('table_rows')->nullable(); // Data baris tabel
            $table->json('summary_data')->nullable(); // Total SKS, rata-rata, dll.
            $table->string('status', 30)->default('draft'); // draft, submitted, approved, revision
            $table->text('validation_notes')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'table_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lkps_submissions');
    }
};
