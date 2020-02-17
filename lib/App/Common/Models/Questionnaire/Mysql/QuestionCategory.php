<?php

namespace App\Common\Models\Questionnaire\Mysql;

use App\Common\Models\Base\Mysql\Base;

class QuestionCategory extends Base
{

    /**
     * 问卷-题目分类
     * This model is mapped to the table iquestionnaire_question_category
     */
    public function getSource()
    {
        return 'iquestionnaire_question_category';
    }
}
