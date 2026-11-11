<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supporting_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perti_id')->nullable()->constrained('pertis')->cascadeOnDelete();
            $table->foreignId('prodi_id')->nullable()->constrained('prodis')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('category', 120)->index();
            $table->string('title');
            $table->string('academic_year', 40)->nullable()->index();
            $table->text('description')->nullable();
            $table->foreignId('requirement_id')->nullable()->constrained('requirements')->nullOnDelete();
            $table->string('table_code', 30)->nullable()->index();
            $table->json('files')->nullable();
            $table->json('google_drive_links')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supporting_documents');
    }
};
