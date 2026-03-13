<?php

namespace App\Http\Controllers;

use App\Models\Survey;
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
}