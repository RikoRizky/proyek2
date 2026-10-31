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
        Schema::table('requirements', function (Blueprint $table) {
            $table->string('input_type', 30)->default('document')->after('description'); // document, table, matrix, hybrid
            $table->string('table_code', 30)->nullable()->after('input_type'); // e.g. '2.B.1'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requirements', function (Blueprint $table) {
            $table->dropColumn(['input_type', 'table_code']);
        });
    }
};
