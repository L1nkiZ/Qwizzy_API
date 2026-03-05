<?php

use App\Models\Answer;
use App\Models\Question;
use App\Models\Subject;
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
        $subject_ow = Subject::create([
            'name' => 'Outer Wilds',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $question = Question::create([
            'question' => 'Sur quelle astre peut-on trouver Feldspath ?',
            'difficulty_id' => '2',
            'question_type_id' => '1',
            'subject_id' => $subject_ow->id,
            'proposal_1' => 'Cravité',
            'proposal_2' => 'Sombronces',
            'proposal_3' => 'Lune Quantique',
            'proposal_4' => 'Rocaille',
        ]);

        Answer::create([
            'question_id' => $question->id,
            'answer' => 2,
        ]);

        $question = Question::create([
            'question' => 'Quelle est la planète d\'origine du personnage principal ?',
            'difficulty_id' => '1',
            'question_type_id' => '1',
            'subject_id' => $subject_ow->id,
            'proposal_1' => 'Léviathe',
            'proposal_2' => 'Rocaille',
            'proposal_3' => 'L\'Intrus',
            'proposal_4' => 'Âtrebois',
        ]);

        Answer::create([
            'question_id' => $question->id,
            'answer' => 4,
        ]);

        $subject_mc = Subject::create([
            'name' => 'Minecraft',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $question = Question::create([
            'question' => 'Comment s\'appelle le boss final ?',
            'difficulty_id' => '1',
            'question_type_id' => '1',
            'subject_id' => $subject_mc->id,
            'proposal_1' => 'Le Wither',
            'proposal_2' => 'L\'Ender Dragon',
            'proposal_3' => 'Le Gardien',
            'proposal_4' => 'Le Blaze',
        ]);

        Answer::create([
            'question_id' => $question->id,
            'answer' => 2,
        ]);

        $question = Question::create([
            'question' => 'Quel est le matériau le plus résistant ?',
            'difficulty_id' => '2',
            'question_type_id' => '1',
            'subject_id' => $subject_mc->id,
            'proposal_1' => 'Le diamant',
            'proposal_2' => 'L\'obsidienne',
            'proposal_3' => 'La Netherite',
            'proposal_4' => 'Le fer',
        ]);

        Answer::create([
            'question_id' => $question->id,
            'answer' => 3,
        ]);

        $question = Question::create([
            'question' => 'De quoi ont peur les Creepers ?',
            'difficulty_id' => '3',
            'question_type_id' => '1',
            'subject_id' => $subject_mc->id,
            'proposal_1' => 'Des chiens',
            'proposal_2' => 'Des chats',
            'proposal_3' => 'Des fleurs',
            'proposal_4' => 'Du joueur',
        ]);

        Answer::create([
            'question_id' => $question->id,
            'answer' => 2,
        ]);

        $subject_ssbu = Subject::create([
            'name' => 'Super Smash Bros Ultimate',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $question = Question::create([
            'question' => 'Quel est le personnage le plus rapide ?',
            'difficulty_id' => '2',
            'question_type_id' => '1',
            'subject_id' => $subject_ssbu->id,
            'proposal_1' => 'Sonic',
            'proposal_2' => 'Pikachu',
            'proposal_3' => 'Lucario',
            'proposal_4' => 'Amphinobi',
        ]);

        Answer::create([
            'question_id' => $question->id,
            'answer' => 1,
        ]);

        $question = Question::create([
            'question' => 'Combien y a-t-il de personnages dans Super Smash Bros Ultimate hors DLC ?',
            'difficulty_id' => '1',
            'question_type_id' => '1',
            'subject_id' => $subject_ssbu->id,
            'proposal_1' => '70',
            'proposal_2' => '74',
            'proposal_3' => '80',
            'proposal_4' => '82',
        ]);

        Answer::create([
            'question_id' => $question->id,
            'answer' => 2,
        ]);

        $question = Question::create([
            'question' => 'Quel est le personnage phare du joueur MkLeo ?',
            'difficulty_id' => '3',
            'question_type_id' => '1',
            'subject_id' => $subject_ssbu->id,
            'proposal_1' => 'Joker',
            'proposal_2' => 'Cloud',
            'proposal_3' => 'Luigi',
            'proposal_4' => 'Sora',
        ]);

        Answer::create([
            'question_id' => $question->id,
            'answer' => 1,
        ]);

        $subject_pkmn = Subject::create([
            'name' => 'Pokémon',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $question = Question::create([
            'question' => 'Quel est le nom de la mascotte de Pokémon ?',
            'difficulty_id' => '1',
            'question_type_id' => '1',
            'subject_id' => $subject_pkmn->id,
            'proposal_1' => 'Pikachu',
            'proposal_2' => 'Ultra-Necrozma',
            'proposal_3' => 'Engloutyran',
            'proposal_4' => 'Nounourson',
        ]);

        Answer::create([
            'question_id' => $question->id,
            'answer' => 1,
        ]);

        $question = Question::create([
            'question' => 'De quel type est Persian de Kanto ?',
            'difficulty_id' => '2',
            'question_type_id' => '1',
            'subject_id' => $subject_pkmn->id,
            'proposal_1' => 'Fée',
            'proposal_2' => 'Ténèbres',
            'proposal_3' => 'Acier',
            'proposal_4' => 'Normal',
        ]);

        Answer::create([
            'question_id' => $question->id,
            'answer' => 4,
        ]);

        $question = Question::create([
            'question' => 'Comment s\'appelle le maître de la ligue de Galar ?',
            'difficulty_id' => '3',
            'question_type_id' => '1',
            'subject_id' => $subject_pkmn->id,
            'proposal_1' => 'Pierre',
            'proposal_2' => 'Tarak',
            'proposal_3' => 'Peter',
            'proposal_4' => 'Tili',
        ]);

        Answer::create([
            'question_id' => $question->id,
            'answer' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
