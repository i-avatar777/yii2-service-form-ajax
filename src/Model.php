<?php

namespace iAvatar777\services\FormAjax;

use yii\helpers\Html;

class Model extends \yii\base\Model
{
    public function getErrors102($attribute = null)
    {
        $fields = [];
        foreach ($this->attributes as $k => $v) {
            $fields[$k] = Html::getInputId($this, $k);
        }
        $data =    [
            'errors' => $this->errors,
            'fields' => $fields,
        ];

        return $data;
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        return true;
    }
}