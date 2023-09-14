<?php

namespace iAvatar777\services\FormAjax;

use cs\services\VarDumper;
use yii\base\InvalidCallException;
use yii\base\Model;
use yii\base\Widget;
use yii\bootstrap\ActiveField;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;

class ActiveForm extends \yii\bootstrap\ActiveForm
{
    public $formSelector;
    public $formUrl;
    public $model;

    public $button = [
        'title' => 'Обновить',
        'class' => 'buttonAction',
    ];

    public static function begin($config = [])
    {
        Asset::register(Yii::$app->view);

        if (isset($config['options']['id'])) {
            $formSelector = '#' . $config['options']['id'];
        } else {
            $id = self::generateRandom(10);
            $formSelector = '#' . 'form' . $id;
            $config['options']['id'] = 'form' . $id;
        }
        $nameJS = 'form' . substr(hash('sha256', $formSelector),0,8);
        $success = $config['success'];
        unset($config['success']);

        $config['enableClientValidation'] = false;
        $formUrl = ArrayHelper::getValue($config, 'formUrl', '');
        $type = 1; // беру url из form.action
        if ($formUrl != '') {
            $type = 2; // беру url из $formUrl
        }
        /** @var Model $model */
        $model = $config['model'];
        $attributeWidgets = $model->attributeWidgets();
        $rows = [];
        foreach($attributeWidgets as $key => $value) {
            if (is_array($value)) {
                $class = $value['class'];
                unset($value['class']);
                $params = $value;
            } else {
                $class = $value;
                $params = [];
            }
            $params['model'] = $model;
            $params['attribute'] = $key;
            $params['class'] = $class;

            $o = Yii::createObject($params);
            if (method_exists($o, 'get_field_value')) {
                $rows[] = [
                    'name'      => Html::getInputName($model, $key),
                    'id'        => Html::getInputId($model, $key),
                    'type'      => 'function',
                    'function'  => new JsExpression($o->get_field_value())];
            }
        }

        $jsattributes = \yii\helpers\Json::encode($rows);

        Yii::$app->view->registerJs(<<<JS
var {$nameJS} = {
    onClick: null,
    url: '',
    lastStart: -1,
    thisStart: null,
    delta: 1000,
    isStart: null,
    attributes: {$jsattributes},
    success1: {$success}
};
if ({$type} == 1) {
    {$nameJS}.url = $('{$formSelector}').attr('action');
}
if ({$type} == 2) {
    {$nameJS}.url = '{$formUrl}';
}

{$nameJS}.onClick = function(e) {
    var b = $(this);
    b.off('click');
    var title = b.html();
    b.html($('<i>', {class: 'fa fa-spinner fa-spin fa-fw'}));
    b.attr('disabled', 'disabled');
    
    ajaxJson({
        url: {$nameJS}.url,
        data: iAvatar777_ActiveForm.getFields('{$formSelector}', {$nameJS}.attributes),
        success: function(ret) {
            b.on('click', {$nameJS}.onClick);
            b.html(title);
            b.removeAttr('disabled');
            {$nameJS}.success1(ret);
        },
        errorScript: function(ret) {
            b.on('click', {$nameJS}.onClick);
            b.html(title);
            b.removeAttr('disabled');
            if (ret.id == 102) {
                for (var key in ret.data.errors) {
                    if (ret.data.errors.hasOwnProperty(key)) {
                        var name = key;
                        var value = ret.data.errors[key];
                        var id;
                        for (var key2 in ret.data.fields) {
                            if (ret.data.fields.hasOwnProperty(key2)) {
                                var name2 = key2;
                                var value2 = ret.data.fields[key2];
                                if (name == name2) {
                                    id = 'field-' + value2;
                                }
                            }
                        }
                        var g = $('.' + id);
                        g.addClass('has-error');
                        g.find('p.help-block-error').html(value.join('<br>')).show();
                    }
                }
            }
        }
    });
};
$('{$formSelector} .buttonAction').click({$nameJS}.onClick);

$('{$formSelector}').submit(function(ret) {
    {$nameJS}.isStart = true;
    {$nameJS}.thisStart = (new Date()).getTime();
    
    if ({$nameJS}.lastStart == -1) {
        {$nameJS}.lastStart = {$nameJS}.thisStart;
    } else {
        if ({$nameJS}.lastStart + {$nameJS}.delta > {$nameJS}.thisStart) {
            {$nameJS}.isStart = false;
        }
    }
    
    if ({$nameJS}.isStart) {
        var b = $(this).find('.buttonAction');
        b.off('click');
        var title = b.html();
        b.html($('<i>', {class: 'fa fa-spinner fa-spin fa-fw'}));
        b.attr('disabled', 'disabled');
   
        ajaxJson({
            url: {$nameJS}.url,
            data: iAvatar777_ActiveForm.getFields('{$formSelector}', {$nameJS}.attributes),
            success: function(ret) {
                b.on('click', {$nameJS}.onClick);
                b.html(title);
                b.removeAttr('disabled');
                {$nameJS}.success1(ret);
            },
            errorScript: function(ret) {
                b.on('click', {$nameJS}.onClick);
                b.html(title);
                b.removeAttr('disabled');
                if (ret.id == 102) {
                    for (var key in ret.data.errors) {
                        if (ret.data.errors.hasOwnProperty(key)) {
                            var name = key;
                            var value = ret.data.errors[key];
                            var id;
                            for (var key2 in ret.data.fields) {
                                if (ret.data.fields.hasOwnProperty(key2)) {
                                    var name2 = key2;
                                    var value2 = ret.data.fields[key2];
                                    if (name == name2) {
                                        id = 'field-' + value2;
                                    }
                                }
                            }
                            var g = $('.' + id);
                            g.addClass('has-error');
                            g.find('p.help-block-error').html(value.join('<br>')).show();
                        }
                    }
                }
            }
        });
    }

    return false;
});

// снимает ошибочные признаки поля при фокусе
$('{$formSelector} .form-control').on('focus', function() {
    var o = $(this);
    var p = o.parent();
    if (p.hasClass('input-group')) {
        p = p.parent();
    }
    p.removeClass('has-error');
    p.find('p.help-block-error').hide();
});

JS
        );

        return parent::begin($config);
    }

    private static function generateRandom($length)
    {
        $string = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $string = str_shuffle($string);
        $string = substr($string, 0, $length);

        return $string;
    }

    public static function end($config = [])
    {
        if (ArrayHelper::getValue($config, 'isHide', false) == false) {
            echo Html::button(ArrayHelper::getValue($config, 'label', 'Обновить'), ['class' => 'btn btn-success buttonAction', 'style' => 'width:100%']);
        }

        parent::end();
    }

    /**
     * {@inheritdoc}
     * @return ActiveField the created ActiveField object
     */
    public function field($model, $attribute, $options = [])
    {
        $f = parent::field($model, $attribute, $options);
        if (method_exists($model, 'attributeWidgets')) {
            $fields = $model->attributeWidgets();
            if (isset($fields[$attribute])) {
                $widget = $fields[$attribute];
                if (is_array($widget)) {
                    $params = $widget;
                    $class = $widget['class'];
                    unset($params['class']);
                    $f->widget($class, $params);
                } else {
                    $f->widget($widget);
                }
            }
        }

        return $f;
    }
}