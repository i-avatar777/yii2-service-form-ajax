# FormAjax

Сервис для yii2 для для валидации и исполнения формы по AJAX.
Делает вместро submit с обновлением страницы - ajax запрос и вызов `success`.

## Концепция

Чтобы форма передавалась по AJAX.

После возвращения выводились ошибки.

Исключение составляет в том что нельзя по AJAX передать файл, или сложно, поэтому применяется виджет для онлайн загрузки где по AJAX передается только файл.

\common\services\FormAjax\ActiveRecord::attributeWidgets - здесь указываются виджеты и их настроки для вывода в форме

Так указывается форма на странице:
```php
<?php $model = new \avatar\models\validate\CabinetSchoolFilesCloudSave(); ?>
<?php $form = \iAvatar777\services\FormAjax\ActiveForm::begin([
    'model'   => $model,
    'formUrl' => '/cabinet-school-files/add',
    'success' => <<<JS
function (ret) {
    $('#modalInfo').on('hidden.bs.modal', function() {
        
    }).modal();
}
JS

]) ?>
    <?= $form->field($model, 'url') ?>
    <?= $form->field($model, 'key')->passwordInput() ?>
<?php \iAvatar777\services\FormAjax\ActiveForm::end(['label' => 'Сохранить']) ?>
```

Если вы используете эту `ActiveForm` то в виде модели для нее надо использовать эту модель `ActiveRecord` или эту `Model`. В них прописана функция возврата ошибки валидации `getErrors102`.
Или вы можете использовать свои но скопировать эту функцию в свою модель.

В виде обработчика формы главным считается `DefaultFormAjax` он прописывается в контроллере так:

```php
class CabinetBlogController extends CabinetBaseController
{
    public function actions()
    {
        return [
            'add' => [
                'class'    => '\iAvatar777\services\FormAjax\DefaultFormAjax',
                'model'    => '\avatar\models\forms\BlogItem',
            ],
        ];
    }
}
```

Где `model` - это модель формы, а `add` - это идентификатор действия (`Action::id`).

Чтобы форма отправляла проверку на этот обработчик, надо вформе прописать


# Установка



# Использование

Код для сохранения:
```php
if (Yii::$app->request->isPost) {
    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        $s = $model->save();

        return self::jsonSuccess();
    } else {
        $fields = [];
        foreach ($model->attributes as $k => $v) {
            $fields[$k] = Html::getInputId($model, $k);
        }

        return self::jsonErrorId(102, [
            'errors' => $model->errors,
            'fields' => $fields,
        ]);
    }
}
```

## Пример использования

## Использование если на странице форма добавления

В контроллере прописываю в функции `actions()`

```php
class CabinetBlogController extends CabinetBaseController
{
    public function actions()
    {
        return [
            'add' => [
                'class'    => '\common\services\FormAjax\DefaultFormAdd',
                'model'    => '\avatar\models\forms\BlogItem',
                'view'     => '@avatar/views/blog/add',
            ],
        ];
    }
}
```

Параметр `view` не обязателен, если не указан то используется идентификатор действия (action).



## Событийная модель для ActiveRecord

```
Widget:onBeforeLoad выполняется до   $model->load()
Widget:onAfterLoad выполняется после $model->load()

Widget:onAfterLoadDb выполняется после $model->findOne()

Widget:onBeforeInsert выполняется до   $model->save()
Widget:onAfterInsert выполняется после $model->save()

Widget:onBeforeUpdate выполняется до   $model->save()
Widget:onAfterUpdate выполняется после $model->save()

Widget:onBeforeValidate выполняется до   $model->validate()
Widget:onAfterValidate выполняется после $model->validate()

Widget:onBeforeDelete выполняется до   $model->delete()
Widget:onAfterDelete выполняется после $model->delete()
```

Всего два сценария показа формы

```
1
$model->findOne()

2
$model->findOne()
$model->load()
$model->validate()
$model->save() - если прошла валидация
```


# Виджеты

Базовый класс: `\iAvatar777\services\FormAjax\Widget`

Виджеты предназначены для того чтобы можно было в них вызывать события для обработки постуивших данных.

Например в виджете с датой указывается дата в формате dd.mm.yyyy для валидации она должна быть в формате dd.mm.yyyy, 
при сохранении она должна быть в виде yyyy-mm-dd