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

class BaseAction extends Action
{

    public static function jsonErrorId($id, $data = null)
    {
        $ret = [
            'id' => $id,
        ];
        if (!is_null($data)) {
            $ret['data'] = $data;
        }

        return self::jsonError($ret);
    }

    public static function jsonSuccess($data = null)
    {
        $ret = [
            'success' => true,
        ];
        if (!is_null($data)) {
            $ret['data'] = $data;
        }

        return self::json($ret);
    }

    public static function jsonError($data = null)
    {
        $ret = [
            'success' => false,
        ];
        if (!is_null($data)) {
            $ret['data'] = $data;
        }

        return self::json($ret);
    }

    public static function json($data)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $data;
    }

    /**
     * Возвращает переменную из REQUEST
     *
     * @param string $name    имя переменной
     * @param mixed  $default значние по умолчанию [optional]
     *
     * @return string|null
     * Если такой переменной нет, то будет возвращено null
     */
    public static function getParam($name, $default = null)
    {
        $vGet = \Yii::$app->request->get($name);
        $vPost = \Yii::$app->request->post($name);
        $value = (is_null($vGet)) ? $vPost : $vGet;

        return (is_null($value)) ? $default : $value;
    }
}