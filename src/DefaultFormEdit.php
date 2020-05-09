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

class DefaultFormEdit extends BaseAction
{
    public $model;
    public $formName = '';

    /** @var string представление дял страницы */
    public $view;

    public function run()
    {
        $class = $this->model;
        /** @var \iAvatar777\services\FormAjax\ActiveRecord $model */
        $model = $class::findOne(Yii::$app->request->get('id'));

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $s = $model->save();

                return self::jsonSuccess($s);
            } else {
                return self::jsonErrorId(102, $model->getErrors102());
            }
        }

        if ($this->view) {
            $view = $this->view;
        } else {
            $view = $this->id;
        }

        return $this->controller->render($view, ['model' => $model]);
    }
}