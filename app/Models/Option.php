<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    public function store(Request $request)
    {
        foreach ($request->answers as $i => $answer) {
            if ($answer) {
                QuestionOption::create([
                    'question_id' => $request->question_id,
                    'answer_text' => $answer,
                    'value' => $request->values[$i] ?? null,
                    'order' => $i + 1 // 🔥 urutan
                ]);
            }
        }

        return back();
    }
}
