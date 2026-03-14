<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Question;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index()
    {
        return Survey::with('creator:id,fio')->get();
    }

    public function show($id)
    {
        return Survey::with('questions.options')->where('id_survey', $id)->firstOrFail();
    }

    public function getPublished()
    {
        $surveys = Survey::where('status', 'published')->get();
        return response()->json($surveys);
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
            'creator_id' => auth()->id(),
            'status' => 'draft'
        ]);

        return response()->json($survey, 201);
    }

    public function update(Request $request, $id)
    {
        $survey = Survey::where('id_survey', $id)->firstOrFail();

        if ($survey->creator_id !== auth()->id()) {
            return response()->json(['message' => 'Это не ваш опрос'], 403);
        }

        if ($survey->status !== 'draft') {
            return response()->json([
                'message' => 'Нельзя редактировать опрос в статусе ' . $survey->status
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
        ]);

        $survey->update($validated);
        return response()->json(['message' => 'Опрос успешно обновлен', 'survey' => $survey]);
    }

    public function changeStatus(Request $request, $id)
    {
        $survey = \App\Models\Survey::where('id_survey', $id)->firstOrFail();
        $newStatus = $request->status;

        $weights = [
            'draft' => 1,
            'published' => 2,
            'closed' => 3
        ];

        $currentWeight = $weights[$survey->status] ?? 0;
        $newWeight = $weights[$newStatus] ?? 0;

        if ($newWeight === 0) {
            return response()->json(['message' => 'Некорректный статус'], 422);
        }

        if ($survey->status === 'closed') {
            return response()->json(['message' => 'Нельзя менять статус архивного опроса'], 403);
        }

        if ($newWeight < $currentWeight) {
            return response()->json(['message' => 'Нельзя вернуть опрос на предыдущий этап'], 403);
        }

        if ($newWeight > $currentWeight + 1) {
            return response()->json(['message' => 'Статусы должны меняться последовательно'], 403);
        }

        $survey->status = $newStatus;
        $survey->save();

        return response()->json(['message' => "Статус успешно изменен на {$newStatus}"]);
    }

    public function addQuestion(Request $request, $id)
    {
        $survey = Survey::where('id_survey', $id)->firstOrFail();

        if ($survey->status !== 'draft') {
            return response()->json(['message' => 'Нельзя менять структуру опубликованного опроса'], 403);
        }

        $fields = $request->validate([
            'question_text' => 'required|string',
            'type_id' => 'required|integer|exists:answer_type,id_type',
            'order_priority' => 'nullable|integer'
        ]);

        $question = $survey->questions()->create($fields);

        return response()->json($question, 201);
    }

    public function addOption(Request $request, $questionId)
    {
        $question = Question::where('id_question', $questionId)->firstOrFail();
        
        if ($question->survey->status !== 'draft') {
            return response()->json(['message' => 'Нельзя менять варианты в активном опросе'], 403);
        }

        if ($question->type_id == 3) { 
            return response()->json(['message' => 'Текстовый вопрос не требует вариантов'], 422);
        }

        $fields = $request->validate(['option_text' => 'required|string']);
        $option = $question->options()->create(['option_text' => $fields['option_text']]);

        return response()->json($option, 201);
    }
}