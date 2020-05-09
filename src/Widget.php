<?php
namespace iAvatar777\services\FormAjax;


class Widget extends \yii\base\Widget
{
    // false - при добавлении(ActiveRecord::insert) записи вызывается Update после получения ID, и оба события
    // true - при добавлении(ActiveRecord::insert) записи вызывается Insert и оба события
    // по умолчанию true
    public $isInsert = true;

}