<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->timestamp('cancelled_at')->nullable()->after('is_published');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->string('correct_text')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('correct_text');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('cancelled_at');
        });
    }
};
