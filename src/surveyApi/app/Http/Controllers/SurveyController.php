<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index()
    {
        return Survey::with('creator:id,fio')->get();
    }

    public function show($id)
    {
        return Survey::with('questions.options')->findOrFail($id);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $survey = Survey::create([
            'title' => $fields['title'],
            'description' => $fields['description'] ?? '',
            'creator_id' => auth()->id(), // Берем ID из api_token
            'status' => 'draft'
        ]);

        return response($survey, 201);
    }

    public function addQuestion(Request $request, $id)
    {
        $survey = Survey::findOrFail($id);

        $fields = $request->validate([
            'question_text' => 'required|string',
            'type_id' => 'required|integer|exists:answer_type,id_type',
            'order_priority' => 'nullable|integer' // Добавили валидацию
        ]);

        $question = $survey->questions()->create([
            'question_text' => $fields['question_text'],
            'type_id' => $fields['type_id'],
            'order_priority' => $fields['order_priority'] ?? 0
        ]);

        return response($question, 201);
    }

    public function changeStatus(Request $request, $id)
    {
        $survey = Survey::where('id_survey', $id)
                        ->where('creator_id', auth()->id())
                        ->firstOrFail();

        $fields = $request->validate([
            'status' => 'required|in:draft,published,closed'
        ]);

        $survey->update(['status' => $fields['status']]);

        return response([
            'message' => "Статус опроса №{$id} изменен на: " . $fields['status'],
            'new_status' => $survey->status
        ]);
    }

    public function addOption(Request $request, $questionId)
    {
        $question = Question::findOrFail($questionId);

        if ($question->type_id == 3) {
            return response([
                'message' => 'Нельзя добавить варианты ответа к текстовому вопросу'
            ], 422);
        }

        $fields = $request->validate([
            'option_text' => 'required|string'
        ]);

        $option = $question->options()->create([
            'option_text' => $fields['option_text']
        ]);

        return response($option, 201);
    }
}