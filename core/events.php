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

/**  */


return $arReturn;