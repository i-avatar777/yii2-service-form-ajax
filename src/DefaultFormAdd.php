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

class DefaultFormAdd extends BaseAction
{
    public $model;

    /** @var string представление дял страницы */
    public $view;
    public $formName = '';

    public function run()
    {
        $class = $this->model;
        /** @var Model $model */
        $model = new $class();

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