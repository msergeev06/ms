<?php

use \Ms\Core\Entity\Events\Info;

$parameterArAddFields = (new Info\Parameters\ArrayParameter('arAdd'))
    ->setTitle('Массив с параметрами, добавляемыми в базу данных')
    ->setModified()
    ->addField(
        (new Info\Fields\IntField('ID'))
            ->setTitle('ID файла')
            ->setRequired()
            ->setDescription('PRIMARY KEY')
    )
    ->addField(
        (new Info\Fields\StringField('MODULE'))
            ->setTitle('Имя модуля, чей файл')
    )
    ->addField(
        (new Info\Fields\IntField('HEIGHT'))
            ->setTitle('Высота изображения')
    )
    ->addField(
        (new Info\Fields\IntField('WIDTH'))
            ->setTitle('Ширина изображения')
    )
    ->addField(
        (new Info\Fields\IntField('FILE_SIZE'))
            ->setTitle('Размер файла в байтах')
    )
    ->addField(
        (new Info\Fields\StringField('CONTENT_TYPE'))
            ->setTitle('Тип файла')
            ->setDescription('content type файла, например image/jpeg')
    )
    ->addField(
        (new Info\Fields\StringField('SUBDIR'))
            ->setTitle('Поддиректория')
            ->setDescription('поддиректория относительно папки upload')
    )
    ->addField(
        (new Info\Fields\StringField('FILE_NAME'))
            ->setTitle('Имя файла')
            ->setRequired()
            ->setDescription('Назначенное уникальное имя файла')
    )
    ->addField(
        (new Info\Fields\StringField('ORIGINAL_NAME'))
            ->setTitle('Оригинальное имя файла')
            ->setDescription('Как назывался файл у пользователя')
    )
    ->addField(
        (new Info\Fields\StringField('DESCRIPTION'))
            ->setTitle('Описание файла (примечание)')
    )
    ->addField(
        (new Info\Fields\IntField('HANDLER_ID'))
            ->setTitle('ID обработчика')
    )
    ->addField(
        (new Info\Fields\StringField('EXTERNAL_ID'))
            ->setTitle('Внешний код')
    )
;

$collection = (new Info\Collection())
    ->setModuleName('core')
    ->addEventInfo(
        (new Info\Event('OnBeforeUploadNewFile'))
            ->setDescription('Вызывается перед загрузкой нового файла')
            ->addParameter(
                (new Info\Parameters\ArrayParameter('arFile'))
                    ->setTitle('Массив с параметрами файла из суперглобального массива файлов $_FILE')
                    ->setModified()
                    ->setDescription('список полей смотрите в документации по $_FILE')
            )
            ->addParameter($parameterArAddFields)
    )


    ->addEventInfo(
        (new Info\Event('OnAfterUploadNewFile'))
            ->setDescription('Вызывается после загрузки нового файла')
            ->addParameter(
                (new Info\Parameters\ArrayParameter('arFile'))
                    ->setTitle('Массив с параметрами файла из суперглобального массива файлов $_FILE')
                    ->setModified()
                    ->setDescription('список полей смотрите в документации по $_FILE')
            )
            ->addParameter($parameterArAddFields)
    )


    ->addEventInfo(
        (new Info\Event('OnBeforeAddNewFile'))
            ->setDescription('Вызывается перед добавлением нового файла в базу данных')
            ->addParameter(
                (new Info\Parameters\ArrayParameter('arFile'))
                    ->setTitle('Массив с параметрами файла из суперглобального массива файлов $_FILE')
                    ->setModified()
                    ->setDescription('список полей смотрите в документации по $_FILE')
            )
            ->addParameter($parameterArAddFields)
    )


    ->addEventInfo(
        (new Info\Event('OnAfterAddNewFile'))
            ->setDescription('Вызывается после добавления нового файла в базу данных')
            ->addParameter(
                (new Info\Parameters\ArrayParameter('arFile'))
                    ->setTitle('Массив с параметрами файла из суперглобального массива файлов $_FILE')
                    ->setDescription('список полей смотрите в документации по $_FILE')
            )
            ->addParameter(
                (new Info\Parameters\IntParameter('insertID'))
                    ->setTitle('ID только что добавленного файла')
            )
    )


    ->addEventInfo(
        (new Info\Event('OnPrologBefore'))
            ->setDescription('Вызывается в файле prolog_before.php перед подключением обязательных модулей')
    )


    ->addEventInfo(
        (new Info\Event('OnProlog'))
            ->setDescription('Вызывается в файле prolog_before.php после выполнения всех действий')
    )


    ->addEventInfo(
        (new Info\Event('OnPrologAfter'))
            ->setDescription('Вызывается в файле prolog_after.php перед подключением шаблона')
            ->addParameter(
                (new Info\Parameters\StringParameter('templatePath'))
                    ->setTitle('Текущий шаблон страницы')
                    ->setModified()
                    ->setDescription('Можно изменить')
            )
    )


    ->addEventInfo(
        (new Info\Event('OnAddDebugMessageToLog'))
            ->setDescription('Вызывается при добавлении отладочного сообщения в логи')
            ->addParameter(
                (new Info\Parameters\StringParameter('message'))
                    ->setTitle('Добавляемое сообщение')
                    ->setDescription('В сообщении все #шаблоны# уже заменены на значения')
            )
    )


    ->addEventInfo(
        (new Info\Event('OnAddInfoMessageToLog'))
            ->setDescription('Вызывается при добавлении информационного сообщения в логи')
            ->addParameter(
                (new Info\Parameters\StringParameter('message'))
                    ->setTitle('Добавляемое сообщение')
                    ->setDescription('В сообщении все #шаблоны# уже заменены на значения')
            )
    )


    ->addEventInfo(
        (new Info\Event('OnAddNoticeMessageToLog'))
            ->setDescription('Вызывается при добавлении уведомительного сообщения в логи')
            ->addParameter(
                (new Info\Parameters\StringParameter('message'))
                    ->setTitle('Добавляемое сообщение')
                    ->setDescription('В сообщении все #шаблоны# уже заменены на значения')
            )
    )


    ->addEventInfo(
        (new Info\Event('OnAddWarningMessageToLog'))
            ->setDescription('Вызывается при добавлении предупреждающего сообщения в логи')
            ->addParameter(
                (new Info\Parameters\StringParameter('message'))
                    ->setTitle('Добавляемое сообщение')
                    ->setDescription('В сообщении все #шаблоны# уже заменены на значения')
            )
    )


    ->addEventInfo(
        (new Info\Event('OnAddCriticalMessageToLog'))
            ->setDescription('Вызывается при добавлении критического сообщения в логи')
            ->addParameter(
                (new Info\Parameters\StringParameter('message'))
                    ->setTitle('Добавляемое сообщение')
                    ->setDescription('В сообщении все #шаблоны# уже заменены на значения')
            )
    )


    ->addEventInfo(
        (new Info\Event('OnAddErrorMessageToLog'))
            ->setDescription('Вызывается при добавлении сообщения об ошибке в логи')
            ->addParameter(
                (new Info\Parameters\StringParameter('message'))
                    ->setTitle('Добавляемое сообщение')
                    ->setDescription('В сообщении все #шаблоны# уже заменены на значения')
            )
    )


    ->addEventInfo(
        (new Info\Event('OnBeforeInsert'))
            ->setDescription('Вызывается при попытке добавления информации в таблицу')
            ->addParameter(
                (new Info\Parameters\StringParameter('classTable'))
                    ->setTitle('Класс таблицы, в которую происходит добавление')
                    ->setDescription('Через имя класса можно получить доступ к другим параметрам таблицы')
            )
            ->addParameter(
                (new Info\Parameters\ArrayParameter('arAdd'))
                    ->setTitle('Массив значений полей таблицы')
                    ->setModified(true)
                    ->setDescription('Добавляемые значения в таблицу, без значений, устанавливаемых по-умолчанию')
            )
            ->setStopped(true)
    )


    ->addEventInfo(
        (new Info\Event('OnAfterInsert'))
            ->setDescription('Вызывается после попытки добавления инфомации в таблицу')
            ->addParameter(
                (new Info\Parameters\StringParameter('classTable'))
                    ->setTitle('Класс таблицы, в которую была попытка добавить значения')
                    ->setDescription('Через имя класса можно получить доступ к другим параметрам таблицы')
            )
            ->addParameter(
                (new Info\Parameters\ArrayParameter('arAdd'))
                    ->setTitle('Массив значений полей таблицы')
                    ->setDescription('Добавляемые значения в таблицу, без значений, устанавливаемых по-умолчанию')
            )
            ->addParameter(
                (new Info\Parameters\ObjectParameter('dbResult'))
                    ->setTitle('Объект DBResult')
                    ->setType(\Ms\Core\Entity\Db\Result\DBResult::class)
                    ->setDescription('Через объект можно получить информацию о результате попытки добавления данных')
            )
    )


    ->addEventInfo(
        (new Info\Event('OnBeforeUpdate'))
            ->setDescription('Вызывается перед попыткой обновить запись в таблице')
            ->addParameter(
                (new Info\Parameters\StringParameter('classTable'))
                    ->setTitle('Класс таблицы, в которой происходит попытка изменения значения')
                    ->setDescription('Через имя класса можно получить доступ к другим параметрам таблицы')
            )
            ->addParameter(
                (new Info\Parameters\StringParameter('primary'))
                    ->setTitle('Значение primary поля таблицы')
                    ->setDescription('По primary полю можно получить текущие значения в таблице')
            )
            ->addParameter(
                (new Info\Parameters\ArrayParameter('arUpdate'))
                    ->setTitle('Массив обновляемых полей с новыми значениями')
                    ->setModified(true)
                    ->setDescription('Массив новых значений записи - можно удалять, можно добавлять свои поля и значения')
            )
            ->addParameter(
                (new Info\Parameters\StringParameter('sqlWhere'))
                    ->setTitle('SQL код для запроса WHERE')
                    ->setModified(true)
                    ->setDescription('Можно изменить часть запроса перед обновлением значения')
            )
            ->setStopped(true)
    )


    ->addEventInfo(
        (new Info\Event('OnAfterUpdate'))
            ->setDescription('Вызывается после попытки обновить запись в таблице')
            ->addParameter(
                (new Info\Parameters\StringParameter('classTable'))
                    ->setTitle('Класс таблицы, в которой происходит попытка изменения значения')
                    ->setDescription('Через имя класса можно получить доступ к другим параметрам таблицы')
            )
            ->addParameter(
                (new Info\Parameters\StringParameter('primary'))
                    ->setTitle('Значение primary поля таблицы')
                    ->setDescription('По primary полю можно получить текущие значения в таблице')
            )
            ->addParameter(
                (new Info\Parameters\ArrayParameter('arUpdate'))
                    ->setTitle('Массив обновляемых полей с новыми значениями')
                    ->setModified(true)
                    ->setDescription('Массив новых значений записи - можно удалять, можно добавлять свои поля и значения')
            )
            ->addParameter(
                (new Info\Parameters\ObjectParameter('dbResult'))
                    ->setTitle('Объект DBResult')
                    ->setType(\Ms\Core\Entity\Db\Result\DBResult::class)
                    ->setDescription('Через объект можно получить информацию о результате попытки изменения данных')
            )
    )


    ->addEventInfo(
        (new Info\Event('OnBeforeDelete'))
            ->setDescription('Вызывается перед попыткой удаления записи из таблицы')
            ->addParameter(
                (new Info\Parameters\StringParameter('classTable'))
                    ->setTitle('Класс таблицы, в которой происходит попытка удаления записи')
                    ->setDescription('Через имя класса можно получить доступ к другим параметрам таблицы')
            )
            ->addParameter(
                (new Info\Parameters\StringParameter('primary'))
                    ->setTitle('Значение primary поля таблицы')
                    ->setDescription('По primary полю можно получить текущие значения в таблице')
            )
            ->setStopped(true)
    )


    ->addEventInfo(
        (new Info\Event('OnAfterDelete'))
            ->setDescription('Вызывается после попытки удаления записи из таблицы')
            ->addParameter(
                (new Info\Parameters\StringParameter('classTable'))
                    ->setTitle('Класс таблицы, в которой происходит попытка удаления записи')
                    ->setDescription('Через имя класса можно получить доступ к другим параметрам таблицы')
            )
            ->addParameter(
                (new Info\Parameters\StringParameter('primary'))
                    ->setTitle('Значение primary поля таблицы')
                    ->setDescription('По primary полю можно получить текущие значения в таблице')
            )
            ->addParameter(
                (new Info\Parameters\ObjectParameter('dbResult'))
                    ->setTitle('Объект DBResult')
                    ->setType(\Ms\Core\Entity\Db\Result\DBResult::class)
                    ->setDescription('Через объект можно получить информацию о результате попытки изменения данных')
            )
    )
;

return $collection;