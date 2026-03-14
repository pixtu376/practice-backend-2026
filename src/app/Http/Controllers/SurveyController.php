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
        // Поиск по id_survey, как в схеме БД
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

        // ТЗ: Запрет редактирования, если статус не "draft"
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
        $survey = Survey::where('id_survey', $id)->firstOrFail();

        if ($survey->creator_id !== auth()->id()) {
            return response()->json(['message' => 'Это не ваш опрос'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,published,closed'
        ]);

        $survey->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Статус успешно обновлен',
            'survey' => $survey
        ]);
    }

    public function addQuestion(Request $request, $id)
    {
        $survey = Survey::where('id_survey', $id)->firstOrFail();

        // ТЗ: Запрет изменения структуры опубликованного опроса
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