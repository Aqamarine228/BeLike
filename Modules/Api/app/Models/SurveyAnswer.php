<?php

namespace Modules\Api\Models;

class SurveyAnswer extends \App\Models\SurveyAnswer
{
    protected $fillable = [
        'question_id',
        'answer',
    ];
}
