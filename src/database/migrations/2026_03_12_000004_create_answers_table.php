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
    Schema::create('answer', function (Blueprint $table) {
        $table->id('id_answer');
        $table->foreignId('user_id')->constrained('users', 'id');
        $table->foreignId('question_id')->constrained('question', 'id_question');
        $table->foreignId('option_id')->nullable()->constrained('question_option', 'id_option');
        $table->text('text_answer')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
