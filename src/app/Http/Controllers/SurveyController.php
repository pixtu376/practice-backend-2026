<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Answer;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Survey::with('creator:id,fio');

        // Фильтрация: мои опросы
        if ($request->filter === 'my') {
            $query->where('creator_id', auth()->id());
        }

        // Фильтрация по статусу
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Сортировка
        if ($request->sort === 'answers') {
            $query->withCount('answers')->orderBy('answers_count', $request->direction ?? 'desc');
        } else {
            $query->orderBy('created_at', $request->direction ?? 'desc');
        }

        return $query->paginate(10);
    }

    public function analytics($id)
    {
        $survey = Survey::where('id_survey', $id)->firstOrFail();

        // Подсчёт количества уникальных респондентов
        $respondentsCount = Answer::whereHas('question', function($q) use ($id) {
            $q->where('survey_id', $id);
        })->distinct('user_id')->count('user_id');

        $questions = Question::where('survey_id', $id)->get();
        $analytics = $questions->map(function ($question) {
            $data = [
                'question' => $question->question_text,
                'type' => $question->type_id == 3 ? 'text' : 'choice'
            ];

            if ($question->type_id == 3) {
                $data['responses'] = Answer::where('question_id', $question->id_question)
                    ->whereNotNull('text_answer')->pluck('text_answer');
            } else {
                $options = QuestionOption::where('question_id', $question->id_question)->get();
                $totalAnswers = Answer::where('question_id', $question->id_question)->count();

                $data['statistics'] = $options->map(function ($opt) use ($totalAnswers) {
                    $count = Answer::where('option_id', $opt->id_option)->count();
                    return [
                        'option' => $opt->option_text,
                        'count' => $count,
                        'percent' => $totalAnswers > 0 ? round(($count / $totalAnswers) * 100, 2) . '%' : '0%'
                    ];
                });
            }
            return $data;
        });

        return response()->json([
            'survey_title' => $survey->title,
            'total_respondents' => $respondentsCount,
            'data' => $analytics
        ]);
    }

    public function export($id)
    {
        $data = $this->analytics($id)->getData();
        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="survey_'.$id.'_export.json"');
    }

    public function show($id)
    {
        return Survey::with('questions.options')->where('id_survey', $id)->firstOrFail();
    }

    public function getPublished()
    {
        return response()->json(Survey::where('status', 'published')->get());
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
        if ($survey->creator_id !== auth()->id()) return response()->json(['message' => 'Это не ваш опрос'], 403);
        if ($survey->status !== 'draft') return response()->json(['message' => 'Нельзя редактировать опрос в статусе ' . $survey->status], 403);

        $survey->update($request->validate(['title' => 'sometimes|string', 'description' => 'sometimes|string']));
        return response()->json(['message' => 'Обновлено', 'survey' => $survey]);
    }

    public function changeStatus(Request $request, $id)
    {
        $survey = Survey::where('id_survey', $id)->firstOrFail();
        $weights = ['draft' => 1, 'published' => 2, 'closed' => 3];
        $currentWeight = $weights[$survey->status] ?? 0;
        $newWeight = $weights[$request->status] ?? 0;

        if ($newWeight === 0) return response()->json(['message' => 'Некорректный статус'], 422);
        if ($survey->status === 'closed') return response()->json(['message' => 'Архив нельзя менять'], 403);
        if ($newWeight < $currentWeight) return response()->json(['message' => 'Назад нельзя'], 403);
        if ($newWeight > $currentWeight + 1) return response()->json(['message' => 'Только по порядку'], 403);

        $survey->update(['status' => $request->status]);
        return response()->json(['message' => "Статус: {$request->status}"]);
    }

    public function addQuestion(Request $request, $id)
    {
        $survey = Survey::where('id_survey', $id)->firstOrFail();
        if ($survey->status !== 'draft') return response()->json(['message' => 'Опубликованные нельзя менять'], 403);

        $fields = $request->validate([
            'question_text' => 'required|string',
            'type_id' => 'required|integer|exists:answer_type,id_type'
        ]);

        return response()->json($survey->questions()->create($fields), 201);
    }

    public function addOption(Request $request, $questionId)
    {
        $question = Question::where('id_question', $questionId)->firstOrFail();
        if ($question->survey->status !== 'draft') return response()->json(['message' => 'Опрос активен'], 403);
        if ($question->type_id == 3) return response()->json(['message' => 'Текст не требует вариантов'], 422);

        $option = $question->options()->create($request->validate(['option_text' => 'required|string']));
        return response()->json($option, 201);
    }
}