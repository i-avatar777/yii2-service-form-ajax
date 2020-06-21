<?php

namespace iAvatar777\services\FormAjax;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ActiveRecord extends \yii\db\ActiveRecord
{
    public function attributeWidgets()
    {
        return [
//            'name' => '\common\widgets\PlaceMapYandex\PlaceMap',
//            'point' => [
//                'class'     => '\iAvatar777\widgets\FileUpload7\FileUpload',
//                'update'    => \avatar\controllers\CabinetSchoolPagesConstructorController::getUpdate(),
//                'settings'  => \avatar\controllers\CabinetSchoolPagesConstructorController::getSettingsLibrary(2, 21),
//                'events'    => [
//                    'onDelete' => function ($item) {
//                        $r = new \cs\services\Url($item->image);
//                        $d = pathinfo($r->path);
//                        $start = $d['dirname'] . '/' . $d['filename'];
//
//                        File::deleteAll(['like', 'file', $start]);
//                    },
//                ],
//            ],
        ];
    }

//    public function formAttributes()
//    {
//        return [
//            [
//                'attribute' => 'name',
//                'label'     => 'Навание',
//                'rules'     => [],
//                'hint'      => '',
//                'widget'    => [
//                    '\iAvatar777\widgets\FileUpload7\FileUpload',
//                    [
//                        'update'   => \avatar\controllers\CabinetSchoolPagesConstructorController::getUpdate(),
//                        'settings' => \avatar\controllers\CabinetSchoolPagesConstructorController::getSettingsLibrary(2, 21),
//                        'events'   => [
//                            'onDelete' => function ($item) {
//                                $r = new \cs\services\Url($item->image);
//                                $d = pathinfo($r->path);
//                                $start = $d['dirname'] . '/' . $d['filename'];
//
//                                File::deleteAll(['like', 'file', $start]);
//                            },
//                        ],
//                    ],
//                ],
//            ],
//        ];
//    }
//
//    /**
//     * @param mixed $condition
//     * @return null|static
//     */
//    public static function findOne($condition)
//    {
//        $model = parent::findOne($condition);
//        if (is_null($model)) return null;
//        $model->executeMethod('onAfterLoadDb');
//
//        return $model;
//    }
//
//    /**
//     * Ищет строку с условием $condition и инициализирует объект параметрами $params
//     *
//     * @param mixed $condition
//     * @param array $params
//     *
//     * @return null|static
//     */
//    public static function findOneWithInit($condition, $params = [])
//    {
//        $model = parent::findOne($condition);
//        foreach ($params as $k => $v) {
//            $model->$k = $v;
//        }
//        if (is_null($model)) return null;
//        $model->executeMethod('onAfterLoadDb');
//
//        return $model;
//    }
//
//    /**
//     * Ищет поле среди полей формы
//     *
//     * @param $name
//     *
//     * @return mixed
//     *
//     * @throws Exception
//     */
//    public function findField($name)
//    {
//        foreach($this->formAttributes() as $field) {
//            if ($field['name'] == $name) {
//                return $field;
//            }
//        }
//        throw new Exception('Не найдено поле '. $name);
//    }
//
//    public function attributeLabels()
//    {
//        $return = [];
//        foreach ($this->formAttributes() as $field) {
//            $return[$field['name']] = $field['label'];
//        }
//
//        return $return;
//    }
//
//    public function attributeHints()
//    {
//        $return = [];
//        foreach ($this->formAttributes() as $field) {
//            if (isset($field['hint'])) {
//                $return[$field['name']] = $field['hint'];
//            }
//        }
//
//        return $return;
//    }

    /**
     * Вызывает метод в виджетах всей записи
     *
     * @param $methodName
     * @return array
     */
    private function executeMethod($methodName)
    {
        $fields = $this->attributeWidgets();
        $ret = [];
        foreach ($fields as $k => $v) {
            if (is_array($v)) {
                $class = $v['class'];
                $options = $v;
                unset($options['class']);
                $options = ArrayHelper::merge($options, [
                    'model'     => $this,
                    'attribute' => $k,
                    'value'     => $this->$k,
                ]);

            } else {
                $class = $v;
                $options = [
                    'model'     => $this,
                    'attribute' => $k,
                    'value'     => $this->$k,
                ];
            }
            $object = new $class($options);
            if (method_exists($object, $methodName)) {
                $object->$methodName();
            }
            $ret[] = $k;
        }

        return $ret;
    }

    /**
     * Вызывает событие в виджетах всей записи
     *
     * @param string $eventName
     * @return array
     */
    private function executeEvent($eventName)
    {
        $fields = $this->attributeWidgets();
        $ret = [];
        foreach ($fields as $k => $v) {
            if (is_array($v)) {
                if (isset($v['events'][$eventName])) {
                    $eventFunction = $v['events'][$eventName];
                    $eventFunction($this);
                    $ret[] = $k;
                }
            }
        }

        return $ret;
    }
//
//    public function load($data, $formName = null)
//    {
//        $this->executeMethod('onBeforeLoad');
//        $res = parent::load($data, $formName);
//        if ($res) {
//            $this->executeMethod('onAfterLoad');
//            return true;
//        } else {
//            return false;
//        }
//    }
//
//    public function insert($runValidation = true, $attributeNames = null)
//    {
//        if ($runValidation) if ($this->validate($attributeNames)) return false;
//
//        $ret = $this->executeMethod('onBeforeInsert');
//        parent::insert(false);
//        $ret = $this->executeMethod('onAfterInsert');
//        $this->id = self::getDb()->lastInsertID;
//
//        // Надо понять а есть ли еще поля которые надо UPDATE ?
//        // Найти поля Widget где поле isInsert = false; -> Update
//        // если поля Widget нет то значит -  пропуск
//        {
//            $fields = [];
//            $fields2 = $this->formAttributes();
//            foreach ($fields2 as $field) {
//                $isInsert = true;
//                // Собираю компонент
//                // Если есть INSERT? какой инсерт? где Insert? настройка в Widget::isInsert?
//                {
//                    if (isset($field['widget'])) {
//                        $widget = $field['widget'];
//                        $options = [];
//                        if (is_array($widget)) {
//                            $class = $widget[0];
//                            if (isset($widget[1])) {
//                                $options = $widget[1];
//                            }
//                        } else {
//                            $class = $widget;
//                        }
//                        $fieldName = $field['name'];
//                        $options = ArrayHelper::merge($options, [
//                            'model'     => $this,
//                            'attribute' => $fieldName,
//                            'value'     => $this->$fieldName,
//                        ]);
//                        $object = new $class($options);
//                        if (!$object->isInsert) {
//                            $isInsert = false;
//                            $fields[] = $field['attribute'];
//                        }
//                    }
//                }
//            }
//        }
//        if (count($fields) > 0) {
//            // Делаю UPDATE
//            $this->update(false, $fields);
//        }
//
//        return true;
//    }
//
//    public function update($runValidation = true, $attributeNames = null)
//    {
//        if ($runValidation) if ($this->validate($attributeNames)) return false;
//
//        $fields = $this->executeMethod('onBeforeUpdate');
//        parent::update(false, $fields);
//        $fields = $this->executeMethod('onAfterUpdate');
//
//        return true;
//    }
//
//    public function validate($runValidation = true, $attributeNames = null)
//    {
//        $fields = $this->executeMethod('onBeforeDelete');
//        if ($runValidation) $this->validate($attributeNames);
//        if ($this->hasErrors()) return false;
//        $fields = $this->executeMethod('onAfterDelete');
//
//        return true;
//    }

    public function delete()
    {
        $this->executeEvent('onBeforeDelete');
        $this->executeMethod('onBeforeDelete');
        parent::delete();
        $this->executeMethod('onAfterDelete');
        $this->executeEvent('onAfterDelete');

        return true;
    }

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
}