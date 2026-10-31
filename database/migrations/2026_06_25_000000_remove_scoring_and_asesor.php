<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('assessments');

        if (Schema::hasColumn('modules', 'weight')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->dropColumn('weight');
            });
        }

        DB::table('submissions')
            ->whereIn('status', ['under_review', 'completed'])
            ->update(['status' => 'uploaded']);

        DB::table('users')->where('role', 'asesor')->delete();
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->decimal('weight', 8, 2)->default(0)->after('description');
        });

        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asesor_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->unique('submission_id');
        });
    }
};
