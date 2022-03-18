# FormAjax

Сервис для yii2 для для валидации и исполнения формы по AJAX.
Делает вместро submit с обновлением страницы - ajax запрос и вызов JS метода `success`.



## Концепция

Чтобы форма передавалась по AJAX.

После возвращения выводились ошибки.

Исключение составляет в том что нельзя по AJAX передать файл, или сложно, поэтому применяется виджет для онлайн загрузки где по AJAX передается только файл.

\iAvatar777\services\FormAjax\ActiveRecord::attributeWidgets - здесь указываются виджеты и их настроки для вывода в форме

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

## Отличие от стандартной обработки по AJAX в YII2





# Установка

# Показывание WIDGET

В функции `attributeWidgets` указывается соответствие поля и виджета. Этот виджет будет рисоваться автоматически если в форме указано:

`<?= $form->field($model, 'image') ?>`

```php
class ProductImage extends \iAvatar777\services\FormAjax\ActiveRecord
{
    
    public function attributeWidgets()
    {
        return [
            'image' => [
                'class'    => '\iAvatar777\widgets\FileUpload7\FileUpload',
                'update'   => \avatar\controllers\CabinetSchoolPagesConstructorController::getUpdate(),
                'settings' => \avatar\controllers\CabinetSchoolPagesConstructorController::getSettingsLibrary($this->_school_id, $this->_type_id),
                'events'   => [
                    'onDelete' => function ($item) {
                        $r = new \cs\services\Url($item->image);
                        $d = pathinfo($r->path);
                        $start = $d['dirname'] . '/' . $d['filename'];

                        File::deleteAll(['like', 'file', $start]);
                    },
                ],
            ],
        ];
    }

}
```

# Отключить кнопку

Если надо не показывать кнопку и внучную отработать то можно так:

`<?php \iAvatar777\services\FormAjax\ActiveForm::end(['isHide' => true]) ?>`

```js
ajaxJson({
    url: '/...',
    data: $('{$formSelector}').serializeArray(),
    success: function(ret) {
        //
    },
    errorScript: function(ret) {
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

```

# Особенность

Событие submit на Enter вызывается два раза, причем только первый раз. Выяснить почему так выяснить не удалось. В связи с этим для того чтобы обойти поставил обход через установку параметра `delta` = 1000 мс, в течение которого нельзя вызвать повторно событие `мой submit`.
```js
$('#formc2ff52cf').submit(function(ret) {
    form1.isStart = true;
    form1.thisStart = (new Date()).getTime();
    
    if (form1.lastStart == -1) {
        form1.lastStart = form1.thisStart;
    } else {
        if (form1.lastStart + form1.delta > form1.thisStart) {
            form1.isStart = false;
        }
    }
    
    if (form1.isStart) {
        // AJAX
    }

    return false;
});
```

# Использование в контроллере при добавлении или обновлении

Если модель класса `\yii\db\ActiveRecord` или `\yii\base\Model` то код для сохранения в контроллере такой:

```php
if (Yii::$app->request->isPost) {
    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        $s = $model->save();
        return self::jsonSuccess($s);
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

Если модель класса `\iAvatar777\services\FormAjax\ActiveRecord` или `\iAvatar777\services\FormAjax\Model` то в них существует функция `getErrors102()` и тогда код уменьшается и можно писать так:

```php
if (Yii::$app->request->isPost) {
    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        $s = $model->save();
        return self::jsonSuccess($s);
    } else {
        return self::jsonErrorId(102, $model->getErrors102());
    }
}
```

## Пример использования

Если  сохранение вызывается в Model то parent:: будет вызывать onBeforeUpdate и onAfterUpdate


## Стандартные действия

Для упрощения стандартных действий в библиотеке есть стандартный набор обработчиков действий:

- `\iAvatar777\services\FormAjax\DefaultFormAjax` - применяется для обработки AJAX обработки формы
- `\iAvatar777\services\FormAjax\DefaultFormAdd` - применяется для рисования формы добавления на странице, на страницу передается переменная `$model`
- `\iAvatar777\services\FormAjax\DefaultFormEdit` - применяется для рисования формы редактирования на странице, на страницу передается переменная `$model`. Идентификатор записи передается по методу GET в параметре `id`.
- `\iAvatar777\services\FormAjax\DefaultFormDelete` - применяется для удаления записи по AJAX. Идентификатор записи передается по методу POST в параметре `id`.

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

## Событийная модель для `\iAvatar777\services\FormAjax\ActiveRecord`

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

Как они описываются и что туда епередается?

`function onBeforeUpdate() {}`

## Модель запуска событий
 
Например `delete`

1. attributeWidgets() `['events']['onBeforeDelete']`
2. Widget:onBeforeDelete
3. \yii\db\ActiveRecord::delete()
4. Widget:onAfterDelete
5. attributeWidgets() `['events']['onAfterDelete']`

То есть можно написать свой обработчик если надо и вместе с этим в самом виджете использовать дополнительные обработки.

Например это актуально если в форме выводятся значения в одном формате а хранятся в БД в другом формате тогда можно а обработчике виджета прописать приведение типа.

Или например это актуально если нужно удалить картинку по факту удаления записи.

## три сценария показа формы
 
1. Всего три сценария показа формы

```
1
$model->findOne()

2 - если прошла валидация
$model->findOne()
$model->load()
$model->validate()
$model->save() 

3 - если не прошла валидация
$model->findOne()
$model->load()
$model->validate()
```

Если в форме производится изменение формата хранения поля то нужно учитывать что на момент рисования формы в поле должно хранится один и тот же формат поля.

Если производится конвертация форматов то лучшей практикой будет такое:

Я могу сделать конвертацию в `onAfterLoadDb` из YYYY-mm-dd в dd.mm.YYYY
а в событии `onBeforeUpdate` из dd.mm.YYYY в YYYY-mm-dd
то при валидации в поле будет формат dd.mm.YYYY, при выводе формы будет dd.mm.YYYY
При загрузке формы значение будет загружено в формате dd.mm.YYYY поэтому ничего менять не нужно

Если я хочу сохранять (держать значение в формате `DateTime`) то как должна быть конвертация? Как должна проводиться валидация? Как должно выводиться значение в форме в виджете?
Например:
Я могу сделать конвертацию в `onAfterLoadDb` из YYYY-mm-dd в `DateTime`
в событии `onAfterLoad` делается конвертация из dd.mm.YYYY в `DateTime`
валидировать нужно введенное значение от пользователя формата `DateTime`.
в событии `onBeforeUpdate` из `DateTime` в YYYY-mm-dd
В виджете рисования будет из формата `DateTime`

Так как по факту сохранения формы действие заканчивается то после сохранения формы она уже не отрисовывается.

# Виджеты

Базовый класс: `\iAvatar777\services\FormAjax\Widget`

Виджеты предназначены для того чтобы можно было в них вызывать события для обработки постуивших данных.

Например в виджете с датой указывается дата в формате dd.mm.yyyy для валидации она должна быть в формате dd.mm.yyyy, 
при сохранении она должна быть в виде yyyy-mm-dd


# Пример функции в виджете

```php
public function onAfterLoadDb($field) 
{

}
```

# Инициализация формы после простой html выдачи

Иногда возникает необходимость обработать, навесить обработчики формы после выдачи простого HTML. Тогда чтобы форма работала, нужно вызвать функцию
```JS
iAvatar777_ActiveForm.init(formId, formSelector, formUrl, functionSuccess, type);
```

# Получение значений полей формы

Иногда возникает необходимость получить значение поля, но нестандартным способом. Например из редактора такста.
Тогда в виджете задается функция получения значения поля

```php
class Widget extends \yii\base\Widget
{
    public function get_field_value()
    {
        $id = 'field-' . Html::getInputId($this->model, $this->attribute);
        $name = Html::getInputName($this->model, $this->attribute);
        
        return <<<JS
function (fields) {
    
    // очищенный результат
    var rows;
    var serializeArray = $(formSelector).serializeArray();
    // функция зачистки, учитывая что значений может быть много то алгоритм такой, прохожусь по всему массиву, если втретилось это поле то не вклчаю его в результат, остальное включаю.
    for (var i=0; i < serializeArray.length; i++) {
        if (serializeArray[i].name == '{name}') {
            // делаю замену
            rows.push({name: 'name', value: '1'});
        } else {
            // добавляю
            rows.push(serializeArray[i]);
        }
    }
    
    return rows; 
}
JS;
    }
}
```

Если функции в виджете нет то значени поля будет выбираться по ID поля `INPUT`.
Собираются только те поля которые перечислены полями (`INPUT`) в форме.

Пример скрипта очистки значений из serrializeArray:

```js
// очищенный результат
var rows;
var serializeArray = $(formSelector).serializeArray();
// функция зачистки, учитывая что значений может быть много то алгоритм такой, прохожусь по всему массиву, если втретилось это поле то не вклчаю его в результат, остальное включаю.
for (var i=0; i < serializeArray.length; i++) {
    if (serializeArray[i].name == '{name}') {
        // ничего не делаю
    } else {
        // добавляю
        rows.push(serializeArray[i]);
    }
}
```

Пример скрипта для замены простого поля:

```js
// очищенный результат
var rows;
var serializeArray = $(formSelector).serializeArray();
// функция зачистки, учитывая что значений может быть много то алгоритм такой, прохожусь по всему массиву, если втретилось это поле то не вклчаю его в результат, остальное включаю.
for (var i=0; i < serializeArray.length; i++) {
    if (serializeArray[i].name == '{name}') {
        // делаю замену
        rows.push({name: 'name', value: '1'});
    } else {
        // добавляю
        rows.push(serializeArray[i]);
    }
}
```

