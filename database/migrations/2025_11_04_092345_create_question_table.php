<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('question', function (Blueprint $table) {
            $table->id();
            $table->string("question")->unique();
            $table->foreignId('subject_id')->constrained('subject')->onDelete('cascade');
            $table->foreignId('difficulty_id')->constrained('difficulty')->onDelete('cascade');
            $table->foreignId('question_type_id')->constrained('question_type')->onDelete('cascade');
            $table->string("proposal_1");
            $table->string("proposal_2");
            $table->string("proposal_3");
            $table->string("answer");
            $table->timestamps();
        });

        DB::table('question')->insert([
            'question' => 'Quel est le nom du personnage principal de la série The Witcher ?',
            'subject_id' => 1,
            'difficulty_id' => 1,
            'question_type_id' => 1,
            'proposal_1' => 'Yennefer',
            'proposal_2' => 'Ratchet',
            'proposal_3' => 'Mario',
            'answer' => 'Geralt de Riv',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question');
    }
};
