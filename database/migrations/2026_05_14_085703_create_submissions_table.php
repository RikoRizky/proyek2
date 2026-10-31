<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requirement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type', 128)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('status', 32)->default('uploaded');
            $table->unsignedInteger('version')->default(1);
            $table->boolean('is_latest')->default(true);
            $table->timestamps();

            $table->unique(['requirement_id', 'user_id', 'version']);
            $table->index(['requirement_id', 'user_id', 'is_latest']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
