<?php

namespace App\Common\Models\Questionnaire\Mysql;

use App\Common\Models\Base\Mysql\Base;

class QuestionShowType extends Base
{

    /**
     * 问卷-题目展现方式
     * This model is mapped to the table iquestionnaire_question_showtype
     */
    public function getSource()
    {
        return 'iquestionnaire_question_showtype';
    }
}
