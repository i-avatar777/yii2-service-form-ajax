<?php
namespace iAvatar777\services\FormAjax;


use yii\web\AssetBundle;

/**
 * https://ned.im/noty/#/
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since  2.0
 */
class Asset extends AssetBundle
{
    public $sourcePath = '@vendor/i-avatar777/yii2-service-form-ajax/src/js';
    public $css = [
    ];
    public $js = [
        'default.js'
    ];
    public $depends = [
        '\yii\web\JqueryAsset',
    ];
}
