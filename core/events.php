<?php

/** OnBeforeUploadNewFile */
$arReturn['OnBeforeUploadNewFile'] = array(
	'DESCRIPTION' => 'Вызывается перед загрузкой нового файла',
	'PARAMETERS' => array(
		'arFile' => array(
			'NAME' => 'Массив с параметрами файла из суперглобального массива файлов $_FILE',
			'TYPE' => 'array',
			'MODIFIED' => true,
			'DESCRIPTION' => 'список полей смотрите в документации по $_FILE'
		),
		'arAdd' => array(
			'NAME' => 'Массив с параметрами, добавляемыми в базу данных',
			'MODIFIED' => true, //передача по ссылке
			'TYPE' => 'array',
			'FIELDS' => array(
				'ID' => array(
					'NAME' => 'ID файла',
					'TYPE' => 'int',
					'REQUIRED' => true,
					'DESCRIPTION' => 'PRIMARY KEY'
				),
				'MODULE' => array(
					'NAME' => 'Имя модуля, чей файл',
					'TYPE' => 'string'
				),
				'HEIGHT' => array(
					'NAME' => 'Высота изображения',
					'TYPE' => 'int'
				),
				'WIDTH' => array(
					'NAME' => 'Ширина изображения',
					'TYPE' => 'int'
				),
				'FILE_SIZE' => array(
					'NAME' => 'Размер файла в байтах',
					'TYPE' => 'int'
				),
				'CONTENT_TYPE' => array(
					'NAME' => 'Тип файла',
					'TYPE' => 'STRING',
					'DESCRIPTION' => 'content type файла, например image/jpeg'
				),
				'SUBDIR' => array(
					'NAME' => 'Поддиректория',
					'TYPE' => 'string',
					'DESCRIPTION' => 'поддиректория относительно папки upload'
				),
				'FILE_NAME' => array(
					'NAME' => 'Имя файла',
					'TYPE' => 'string',
					'REQUIRED' => true,
					'DESCRIPTION' => 'Назначенное уникальное имя файла'
				),
				'ORIGINAL_NAME' => array(
					'NAME' => 'Оригинальное имя файла',
					'TYPE' => 'string',
					'DESCRIPTION' => 'Как назывался файл у пользователя'
				),
				'DESCRIPTION' => array(
					'NAME' => 'Описание файла (примечание)',
					'TYPE' => 'string'
				),
				'HANDLER_ID' => array(
					'NAME' => 'ID обработчика',
					'TYPE' => 'int'
				),
				'EXTERNAL_ID' => array(
					'NAME' => 'Внешний код',
					'TYPE' => 'string'
				)
			)
		)
	)
);

/** OnAfterUploadNewFile */
$arReturn['OnAfterUploadNewFile'] = $arReturn['OnBeforeUploadNewFile'];
$arReturn['OnAfterUploadNewFile']['DESCRIPTION'] = 'Вызывается после загрузки нового файла';

/** OnBeforeAddNewFile */
$arReturn['OnBeforeAddNewFile'] = $arReturn['OnBeforeUploadNewFile'];
$arReturn['OnBeforeAddNewFile']['DESCRIPTION'] = 'Вызывается перед добавлением нового файла в базу данных';

/** OnAfterAddNewFile */
$arReturn['OnAfterAddNewFile'] = array(
	'DESCRIPTION' => 'Вызывается после добавления нового файла в базу данных',
	'PARAMETERS' => array(
		'arFile' => array(
			'NAME' => 'Массив с параметрами файла из суперглобального массива файлов $_FILE',
			'TYPE' => 'array',
			'DESCRIPTION' => 'список полей смотрите в документации по $_FILE'
		),
		'insertID' => array(
			'NAME' => 'ID только что добавленного файла',
			'TYPE' => 'int'
		)
	)
);

/** OnPrologBefore */
$arReturn['OnPrologBefore'] = array(
	'DESCRIPTION' => 'Вызывается в файле prolog_before.php перед подключением обязательных модулей'
);

/** OnProlog */
$arReturn['OnProlog'] = array(
	'DESCRIPTION' => 'Вызывается в файле prolog_before.php после выполнения всех действий'
);

/** OnPrologAfter */
$arReturn['OnPrologAfter'] = array(
	'DESCRIPTION' => 'Вызывается в файле prolog_after.php перед подключением шаблона',
	'PARAMETERS' => array(
		'templatePath' => array(
			'NAME' => 'Текущий шаблон страницы',
			'TYPE' => 'string',
			'MODIFIED' => true,
			'DESCRIPTION' => 'Можно изменить'
		)
	)
);

$arReturn['OnAddDebugMessageToLog'] = array (
	'DESCRIPTION' => 'Вызывается при добавлении отладочного сообщения в логи',
	'PARAMETERS' => array (
		array (
			'NAME' => 'Добавляемое сообщение',
			'TYPE' => 'string',
			'MODIFIED' => false,
			'DESCRIPTION' => 'В сообщении все #шаблоны# уже заменены на значения'
		)
	)
);

$arReturn['OnAddInfoMessageToLog'] = array (
	'DESCRIPTION' => 'Вызывается при добавлении информационного сообщения в логи',
	'PARAMETERS' => array (
		array (
			'NAME' => 'Добавляемое сообщение',
			'TYPE' => 'string',
			'MODIFIED' => false,
			'DESCRIPTION' => 'В сообщении все #шаблоны# уже заменены на значения'
		)
	)
);

$arReturn['OnAddNoticeMessageToLog'] = array (
	'DESCRIPTION' => 'Вызывается при добавлении уведомительного сообщения в логи',
	'PARAMETERS' => array (
		array (
			'NAME' => 'Добавляемое сообщение',
			'TYPE' => 'string',
			'MODIFIED' => false,
			'DESCRIPTION' => 'В сообщении все #шаблоны# уже заменены на значения'
		)
	)
);

$arReturn['OnAddWarningMessageToLog'] = array (
	'DESCRIPTION' => 'Вызывается при добавлении предупреждающего сообщения в логи',
	'PARAMETERS' => array (
		array (
			'NAME' => 'Добавляемое сообщение',
			'TYPE' => 'string',
			'MODIFIED' => false,
			'DESCRIPTION' => 'В сообщении все #шаблоны# уже заменены на значения'
		)
	)
);

$arReturn['OnAddCriticalMessageToLog'] = array (
	'DESCRIPTION' => 'Вызывается при добавлении критического сообщения в логи',
	'PARAMETERS' => array (
		array (
			'NAME' => 'Добавляемое сообщение',
			'TYPE' => 'string',
			'MODIFIED' => false,
			'DESCRIPTION' => 'В сообщении все #шаблоны# уже заменены на значения'
		)
	)
);

$arReturn['OnAddErrorMessageToLog'] = array (
	'DESCRIPTION' => 'Вызывается при добавлении сообщения об ошибке в логи',
	'PARAMETERS' => array (
		array (
			'NAME' => 'Добавляемое сообщение',
			'TYPE' => 'string',
			'MODIFIED' => false,
			'DESCRIPTION' => 'В сообщении все #шаблоны# уже заменены на значения'
		)
	)
);

$arReturn['OnBeforeInsert'] = [
	'DESCRIPTION' => 'Вызывается при попытке добавления информации в таблицу',
	'PARAMETERS' => [
		[
			'NAME' => 'Класс таблицы, в которую происходит добавление',
			'TYPE' => 'string',
			'MODIFIED' => false,
			'DESCRIPTION' => 'Через имя класса можно получить доступ к другим параметрам таблицы'
		],
		[
			'NAME' => 'Массив значений полей таблицы',
			'TYPE' => 'array',
			'MODIFIED' => true,
			'DESCRIPTION' => 'Добавляемые значения в таблицу, без значений, устанавливаемых по-умолчанию'
		]
	],
	'STOPPED' => true
];

$arReturn['OnAfterInsert'] = [
	'DESCRIPTION' => 'Вызывается после попытки добавления инфомации в таблицу',
	'PARAMETERS' => [
		[
			'NAME' => 'Класс таблицы, в которую была попытка добавить значения',
			'TYPE' => 'string',
			'MODIFIED' => false,
			'DESCRIPTION' => 'Через имя класса можно получить доступ к другим параметрам таблицы'
		],
		[
			'NAME' => 'Массив значений полей таблицы',
			'TYPE' => 'array',
			'MODIFIED' => false,
			'DESCRIPTION' => 'Добавляемые значения в таблицу, без значений, устанавливаемых по-умолчанию'
		],
		[
			'NAME' => 'Объект DBResult',
			'TYPE' => '\Ms\Core\Entity\Db\DBResult',
			'MODIFIED' => false,
			'DESCRIPTION' => 'Через объект можно получить информацию о результате попытки добавления данных'
		]
	],
	'STOPPED' => false
];

$arReturn['OnBeforeUpdate'] = [
	'DESCRIPTION' => 'Вызывается перед попыткой обновить запись в таблице',
	'PARAMETERS' => [
		[
			'NAME' => 'Класс таблицы, в которой происходит попытка изменения значения',
			'TYPE' => 'string',
			'MODIFIED' => false,
			'DESCRIPTION' => 'Через имя класса можно получить доступ к другим параметрам таблицы'
		],
		[
			'NAME' => 'Значение primary поля таблицы',
			'TYPE' => 'string|int',
			'MODIFIED' => false,
			'DESCRIPTION' => 'По primary полю можно получить текущие значения в таблице'
		],
		[
			'NAME' => 'Массив обновляемых полей с новыми значениями',
			'TYPE' => 'array',
			'MODIFIED' => true,
			'DESCRIPTION' => 'Массив новых значений записи - можно удалять, можно добавлять свои поля и значения'
		],
		[
			'NAME' => 'SQL код для запроса WHERE',
			'TYPE' => 'string',
			'MODIFIED' => true,
			'DESCRIPTION' => 'Можно изменить часть запроса перед обновлением значения'
		]
	],
	'STOPPED' => true
];

$arReturn['OnAfterUpdate'] = [
	'DESCRIPTION' => 'Вызывается после попытки обновить запись в таблице',
	'PARAMETERS' => [
		[
			'NAME' => 'Класс таблицы, в которой происходит попытка изменения значения',
			'TYPE' => 'string',
			'MODIFIED' => false,
			'DESCRIPTION' => 'Через имя класса можно получить доступ к другим параметрам таблицы'
		],
		[
			'NAME' => 'Значение primary поля таблицы',
			'TYPE' => 'string|int',
			'MODIFIED' => false,
			'DESCRIPTION' => 'По primary полю можно получить текущие значения в таблице'
		],
		[
			'NAME' => 'Массив обновляемых полей с новыми значениями',
			'TYPE' => 'array',
			'MODIFIED' => true,
			'DESCRIPTION' => 'Массив новых значений записи - можно удалять, можно добавлять свои поля и значения'
		],
		[
			'NAME' => 'Объект DBResult',
			'TYPE' => '\Ms\Core\Entity\Db\DBResult',
			'MODIFIED' => false,
			'DESCRIPTION' => 'Через объект можно получить информацию о результате попытки изменения данных'
		]
	],
	'STOPPED' => false
];

$arReturn['OnBeforeDelete'] = [
	'DESCRIPTION' => 'Вызывается перед попыткой удаления записи из таблицы',
	'PARAMETERS' => [
		[
			'NAME' => 'Класс таблицы, в которой происходит попытка удаления записи',
			'TYPE' => 'string',
			'MODIFIED' => false,
			'DESCRIPTION' => 'Через имя класса можно получить доступ к другим параметрам таблицы'
		],
		[
			'NAME' => 'Значение primary поля таблицы',
			'TYPE' => 'string|int',
			'MODIFIED' => false,
			'DESCRIPTION' => 'По primary полю можно получить текущие значения в таблице'
		],
		[
			'NAME' => 'Флаг, подтверждающий удаление всех связанных значений в других таблицах',
			'TYPE' => 'bool',
			'MODIFIED' => true,
			'DESCRIPTION' => 'Если true, все значения в других таблицах, связанных с удаляемой записью также будут удалены'
		]
	],
	'STOPPED' => true,
];

$arReturn['OnAfterDelete'] = [
	'DESCRIPTION' => 'Вызывается после попытки удаления записи из таблицы',
	'PARAMETERS' => [
		[
			'NAME' => 'Класс таблицы, в которой происходит попытка удаления записи',
			'TYPE' => 'string',
			'MODIFIED' => false,
			'DESCRIPTION' => 'Через имя класса можно получить доступ к другим параметрам таблицы'
		],
		[
			'NAME' => 'Значение primary поля таблицы',
			'TYPE' => 'string|int',
			'MODIFIED' => false,
			'DESCRIPTION' => 'По primary полю можно получить текущие значения в таблице'
		],
		[
			'NAME' => 'Флаг, подтверждающий удаление всех связанных значений в других таблицах',
			'TYPE' => 'bool',
			'MODIFIED' => false,
			'DESCRIPTION' => 'Если true, все значения в других таблицах, связанных с удаляемой записью также будут удалены'
		],
		[
			'NAME' => 'Объект DBResult',
			'TYPE' => '\Ms\Core\Entity\Db\DBResult',
			'MODIFIED' => false,
			'DESCRIPTION' => 'Через объект можно получить информацию о результате попытки изменения данных'
		]
	],
	'STOPPED' => false
];

return $arReturn;