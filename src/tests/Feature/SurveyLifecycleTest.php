<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Survey;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SurveyLifecycleTest extends TestCase
{
    use RefreshDatabase;

    // Эта строка запустит твой DatabaseSeeder перед каждым тестом
    protected $seed = true; 

    /** 1. Тест: Защита от редактирования опубликованного опроса */
    public function test_cannot_update_survey_if_published()
    {
        // Теперь роль с id 1 уже есть в базе благодаря сидеру
        $user = User::factory()->create(['role_id' => 1]); 
        
        $survey = Survey::factory()->create([
            'creator_id' => $user->id,
            'status' => 'published'
        ]);

        $response = $this->actingAs($user, 'api')
            ->putJson("/api/surveys/{$survey->id_survey}", [
                'title' => 'Новое название'
            ]);

        $response->assertStatus(403)
                 ->assertJsonPath('message', 'Нельзя редактировать опрос в статусе published');
    }

    /** 2. Тест: Защита от повторного прохождения */
    public function test_user_cannot_answer_survey_twice()
    {
        $user = User::factory()->create(['role_id' => 2]); // Респондент
        $survey = Survey::factory()->create(['status' => 'published']);
        $question = Question::factory()->create(['survey_id' => $survey->id_survey]);

        // Создаем первый ответ вручную (имитация уже пройденного опроса)
        Answer::create([
            'user_id' => $user->id,
            'question_id' => $question->id_question,
            'text_answer' => 'Первый ответ'
        ]);

        // Пытаемся отправить второй раз через API
        $response = $this->actingAs($user, 'api')
            ->postJson("/api/surveys/{$survey->id_survey}/answers", [
                'answers' => [
                    ['question_id' => $question->id_question, 'text_answer' => 'Второй ответ']
                ]
            ]);

        $response->assertStatus(403)
                 ->assertJsonPath('message', 'Вы уже проходили этот опрос');
    }

    /** 3. Тест: Валидация обязательного текстового ответа */
    public function test_text_answer_is_required_for_type_3()
    {
        $user = User::factory()->create(['role_id' => 2]);
        $survey = Survey::factory()->create(['status' => 'published']);
        $question = Question::factory()->create([
            'survey_id' => $survey->id_survey,
            'type_id' => 3 // Тип: Текст
        ]);

        $response = $this->actingAs($user, 'api')
            ->postJson("/api/surveys/{$survey->id_survey}/answers", [
                'answers' => [
                    ['question_id' => $question->id_question, 'text_answer' => ''] // Пустой текст
                ]
            ]);

        $response->assertStatus(422)
             ->assertJsonFragment([
                 'message' => "Ошибка валидации: Текстовый ответ для вопроса №{$question->id_question} обязателен"
             ]);
    }
}