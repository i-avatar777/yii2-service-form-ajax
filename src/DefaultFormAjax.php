<?php
/**
 * Created by PhpStorm.
 * User: s.arhangelskiy
 * Date: 18.01.2017
 * Time: 13:17
 */

namespace iAvatar777\services\FormAjax;

use Yii;
use yii\base\Action;
use yii\base\Model;
use yii\helpers\Html;
use yii\web\Response;

class DefaultFormAjax extends BaseAction
{
    public $model;
    public $formName = '';

    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $class = $this->model;
        /** @var Model | ActiveRecord $model */
        $model = new $class();
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $s = $model->save();

                return self::jsonSuccess($s);
            } else {
                return self::jsonErrorId(102, $model->getErrors102());
            }
        }
    }
}