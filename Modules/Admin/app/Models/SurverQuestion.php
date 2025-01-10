<?php

namespace Modules\Admin\Models;

class SurveyQuestion extends \App\Models\SurveyQuestion
{

    protected $fillable = [
        'question',
        'options',
        'order',
    ];
}
