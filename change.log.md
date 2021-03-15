0.0.14
Исправлено сохранение в ActiveRecord.php

0.0.13
Отлажена JS фунция iAvatar777_ActiveForm.init() 

0.0.12
Добавлена JS фунция iAvatar777_ActiveForm.init() для инициализации формы 

0.0.11
Исправлена функция save() для класса `ActiveRecord` и `Model`

0.0.10
Сделан вызов фугкций в виджетах `onBeforeInsert` `onBeforeUpdate` `onAfterLoadDb` `onBeforeLoad` `onAfterLoad` в классе `ActiveRecord` и `Model`

0.0.9
Добавлена возможность не показывать кнопку форму для ручной обработки формы

0.0.8
Добавлен вызов событий `onBeforeDelete` и `onAfterDelete` в виджете.

0.0.7
Добавлен вызов функций `onBeforeDelete` и `onAfterDelete` в виджете.

0.0.6
Поправил ошибку обработки события form.submit
Было на первый Enter вызов двух событий, поставил Delta = 1000 мс, в течении которой нельзя второй раз вызвать submit

0.0.5
Добавил обработку события form.submit

0.0.4
Title кнопки стало не обязательным

0.0.3
Обновил версию

0.0.2
Прописал в `\iAvatar777\services\FormAjax\ActiveForm::begin` функцию `self::generateRandom`
 
0.0.1
Инициализация