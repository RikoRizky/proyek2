<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('file_path')->nullable()->change();
            $table->string('original_filename')->nullable()->change();
            $table->json('google_drive_links')->nullable()->after('is_latest');
            $table->json('files')->nullable()->after('google_drive_links');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('file_path')->nullable(false)->change();
            $table->string('original_filename')->nullable(false)->change();
            $table->dropColumn(['google_drive_links', 'files']);
        });
    }
};
