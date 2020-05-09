<?php
/**
 * Created by PhpStorm.
 * User: s.arhangelskiy
 * Date: 18.01.2017
 * Time: 13:17
 */

namespace iAvatar777\services\FormAjax;

use cs\services\VarDumper;
use Yii;
use yii\base\Action;
use yii\base\Model;
use yii\helpers\Html;
use yii\web\Response;

class DefaultFormDelete extends BaseAction
{
    public $model;
    public $formName = '';

    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $class = $this->model;
        /** @var Model | ActiveRecord $model */
        $model = $class::findOne(Yii::$app->request->post('id'));
        $s = $model->delete();

        return self::jsonSuccess($s);
    }
}