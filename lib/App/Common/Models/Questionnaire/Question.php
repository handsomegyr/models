<?php

namespace App\Common\Models\Questionnaire;

use App\Common\Models\Base\Base;

class Question extends Base
{

    function __construct()
    {
        $this->setModel(new \App\Common\Models\Questionnaire\Mysql\Question());
    }

    public function getUploadPath()
    {
        return trim("question/question", '/');
    }
}
