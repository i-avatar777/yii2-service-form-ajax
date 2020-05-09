<?php

namespace iAvatar777\services\FormAjax;

use yii\base\InvalidCallException;
use yii\base\Model;
use yii\base\Widget;
use yii\bootstrap\ActiveField;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

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
        $formName = $model->formName();

        Yii::$app->view->registerJs(<<<JS
var {$nameJS} = {
    onClick: null,
    url: '',
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
        data: $('{$formSelector}').serializeArray(),
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
        echo Html::button(ArrayHelper::getValue($config, 'label', 'Обновить'), ['class' => 'btn btn-success buttonAction', 'style' => 'width:100%']);
        parent::end();
    }

    /**
     * {@inheritdoc}
     * @return ActiveField the created ActiveField object
     */
    public function field($model, $attribute, $options = [])
    {
        return parent::field($model, $attribute, $options);
    }
}