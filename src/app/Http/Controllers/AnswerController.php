<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\Survey;
use App\Models\Question;

class AnswerController extends Controller
{
    public function store(Request $request, $surveyId)
    {
        $user = auth()->user();

        $input = $request->json()->all();
        $answers = $input['answers'] ?? null;

        $survey = Survey::where('id_survey', $surveyId)->first();

        if (!$survey) {
            return response()->json(['message' => 'Опрос не найден'], 404);
        }

        if ($survey->status !== 'published') {
            return response()->json(['message' => 'Опрос недоступен для прохождения (статус: ' . $survey->status . ')'], 403);
        }

        $alreadyAnswered = Answer::where('user_id', $user->id)
            ->whereHas('question', function ($q) use ($surveyId) {
                $q->where('survey_id', $surveyId);
            })->exists();
        
        if ($alreadyAnswered) {
            return response()->json(['message' => 'Вы уже проходили этот опрос'], 403);
        }

        if (!$answers || !is_array($answers)) {
            return response()->json(['message' => 'Ошибка: массив answers обязателен'], 422);
        }

        foreach ($answers as $ans) {
            $question = Question::where('id_question', $ans['question_id'])
                ->where('survey_id', $surveyId)
                ->first();

            if (!$question) {
                return response()->json(['message' => "Вопрос №{$ans['question_id']} не найден в этом опросе"], 422);
            }

            if ($question->type_id == 3) {
                if (!isset($ans['text_answer']) || trim($ans['text_answer']) === "") {
                    return response()->json([
                        'message' => "Ошибка валидации: Текстовый ответ для вопроса №{$ans['question_id']} обязателен"
                    ], 422);
                }
            } 
            
            if (in_array($question->type_id, [1, 2])) {
                if (empty($ans['option_id'])) {
                    return response()->json([
                        'message' => "Ошибка валидации: Нужно выбрать вариант для вопроса №{$ans['question_id']}"
                    ], 422);
                }
            }
        }

        foreach ($answers as $ans) {
            Answer::create([
                'user_id' => $user->id,
                'question_id' => $ans['question_id'],
                'option_id' => $ans['option_id'] ?? null,
                'text_answer' => $ans['text_answer'] ?? null,
            ]);
        }

        return response()->json(['message' => 'Ответы успешно сохранены!'], 201);
    }
}