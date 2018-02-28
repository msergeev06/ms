<?php
/**
 * Ms\Core\Entity\Type\Date
 * Класс, описывающий тип переменной "Дата и время"
 * Используетя во всех операциях с датой и/или временем
 *
 * @package Ms\Core
 * @subpackage Entity\Type
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity\Type;

use Ms\Core\Entity\Application;
use Ms\Core\Exception;
use Ms\Core\Lib\Loader;
use Ms\Core\Lib\Loc;
use Ms\Dates\Lib\WorkCalendar;

class Date extends \DateTime
{
	/**
	 * Конструктор объекта даты
	 *
	 * Основные форматы
	 * db               YYYY-MM-DD (Y-m-d)
	 * site             DD.MM.YYYY (d.m.Y)
	 * db_datetime      YYYY-MM-DD HH:II:SS (Y-m-d H:i:s)
	 * site_datetime    DD.MM.YYYY HH:II:SS (d.m.Y H:i:s)
	 * site_time        HH:II:SS (H:i:s)
	 *
	 * Список возможных символов для составления строки format
	 *      День
	 * d и j        День месяца, 2 цифры с нулем в начале или без него              От 01 до 31 либо от 1 до 31
	 * D и l        Текстовое представление дня месяца                              От Mon до Sun либо от Sunday до Saturday
	 * S            Суффикс для числа в английской нумерации, 2 буквы. Эти буквы    st, nd, rd или th.
	 *              будут пропущены при разборе строки.
	 * z            Номер дня с начала года (начиная с нуля)                        C 0 по 365
	 *      Месяц
	 * F и M        Текстовое представление месяца, например January или Sept       С January по December либо с Jan по Dec
	 * m и n        Числовое представление месяца с первым нулем или без него       С 01 по 12 либо с 1 по 12
	 *      Год
	 * Y            Полное числовое представление года, 4 цифры                     Примеры: 1999 или 2003
	 * y            2 цифры в представлении года (в диапазоне 1970-2069             Примеры: 99 или 03 (будет расшифровано как
	 *              включительно)                                                   1999 и 2003 соответственно)
	 *      Время
	 * a и A        До полудня и После полудня                                      am или pm
	 * g и h        12-ти часовой формат времени с первым нулем или без него        С 1 по 12 либо с 01 по 12
	 * G и H        24-х часовой формат времени с нулем в начале или без него       С 0 по 23 или с 00 по 23
	 * i            Минуты с нулем в начале                                         С 00 по 59
	 * s            Секунды с нулем в начале                                        От 00 до 59
	 * u            Микросекунды (до 6 цифр)                                        Примеры: 45, 654321
	 *      Временная зона
	 * e, O, P и T  Идентификатор временной зоны, либо разница в часах              Примеры: UTC, GMT, Atlantic/Azores
	 *              относительно UTC, либо разница относительно UTC с запятой       или +0200 или +02:00 или EST, MDT
	 *              между часами и минутами, либо аббревиатура временной зоны
	 *      Дата/Время полностью
	 * U            Количество секунд с начала Эпохи Unix                           Пример: 1292177455
	 *              (January 1 1970 00:00:00 GMT)
	 *      Пробел и Разделители
	 * (пробел)     Один пробел или один отступ табуляции                           Пример:
	 * #            Один из следующих символов: ;, :, /, ., ,, -, ( или )           Пример: /
	 * ;, :, /, .,  Символ разделитель.                                             Пример: -
	 * ,, -, ( или )
	 * ?            Один случайный (любой) символ                                   Пример: ^ (Будьте внимательны:
	 *                                                                              в UTF-8 кодировке вам может потребоваться
	 *                                                                              более одного ?, так как там один символ
	 *                                                                              может занимать более одного байта.
	 *                                                                              В таких случаях может помочь
	 *                                                                              использование *.
	 * *            Любое количество любых символов до следующего разделителя       Пример: * в Y-*-d для строки
	 *                                                                              2009-aWord-08 будет соответствовать aWord
	 * !            Приводит значения всех полей (год, месяц, день, час, минута,    Без ! все поля будут соответствовать текущему времени.
	 *              секунда, временная зона) ко времени начала Эпохи Unix.
	 * |            Приводит значения незаданных полей (год, месяц, день, час,      Y-m-d| установит год, месяц и день в
	 *              минута, секунда, временная зона) ко времени начала Эпохи Unix.  соответствии с данными в строке, а часы,
	 *                                                                              минуты и секунды установит в 0.
	 * +            Если задан этот спецификатор, данные, завершающие строку        Используйте DateTime::getLastErrors()
	 *              (нуль байт например) не будут вызывать ошибку, только           для определения, были ли в строке
	 *              предупреждение                                                  завершающие символы.
	 *
	 * @param string|null        $date     Строковое представление даты
	 * @param string|null        $format   Формат строкового представления даты
	 * @param \DateTimeZone|null $timezone Временная зона
	 * @since 0.2.0
	 */
	public function __construct ($date = NULL, $format = NULL, \DateTimeZone $timezone = NULL)
	{
		$settings = Application::getInstance()->getSettings();
		//Определяем временнУю зону и верно ли она задана
		if (is_null($timezone) || !in_array($timezone, static::getTimezonesList()))
		{
			$timezone = new \DateTimeZone($settings->getTimezone());
		}
		//Если дата не задана, будем использовать текущее время
		if (is_null($date))
		{
			parent::__construct('now', $timezone);
			return $this;
		}
		//Если формат не задан, считаем что это формат БД
		if (is_null($format))
		{
			$format = 'db';
		}
		//В зависимости от формата формируем дату
		switch ($format)
		{
			case 'time': //timestamp
				$dt = parent::createFromFormat('U', $date, $timezone);
				break;
			case 'db': //YYYY-MM-DD
				$dt = parent::createFromFormat('Y-m-d', $date, $timezone);
				break;
			case 'db_datetime': //YYYY-MM-DD HH:II:SS
				$dt = parent::createFromFormat('Y-m-d H:i:s', $date, $timezone);
				break;
			case 'db_time': //HH:II:SS
				$dt = parent::createFromFormat('H:i:s', $date, $timezone);
				break;
			case 'site': //DD.MM.YYYY (или другой из настроек)
				$dt = parent::createFromFormat($settings->getSiteDate(), $date, $timezone);
				break;
			case 'site_datetime': //DD.MM.YYYY HH:II:SS (или другой из настроек)
				$dt = parent::createFromFormat($settings->getSiteDateTime(), $date, $timezone);
				break;
			case 'site_time': //HH:II:SS (или другой из настроек)
				$dt = parent::createFromFormat($settings->getSiteTime(), $date, $timezone);
				break;
			default: //Другой формат
				$dt = parent::createFromFormat($format, $date, $timezone);
				break;
		}

		try
		{
			if ($dt === false)
			{
				throw new Exception\ArgumentException('Wrong date "' . $date . '" in format "' . $format . '"');
			}
		}
		catch (Exception\ArgumentException $e)
		{
			die($e->showException());
		}

		parent::__construct($dt->format('Y-m-d H:i:s'), $timezone);
		unset($dt);

		return $this;
	}

	/** Static */

	/**
	 * Возвращает массив со списком возможных временнЫх зон
	 *
	 * @return array
	 */
	public static function getTimezonesList ()
	{
		$arTimezones = array(
			'Africa/Abidjan',
			'Africa/Accra',
			'Africa/Addis_Ababa',
			'Africa/Algiers',
			'Africa/Asmara',
			'Africa/Bamako',
			'Africa/Bangui',
			'Africa/Banjul',
			'Africa/Bissau',
			'Africa/Blantyre',
			'Africa/Brazzaville',
			'Africa/Bujumbura',
			'Africa/Cairo',
			'Africa/Casablanca',
			'Africa/Ceuta',
			'Africa/Conakry',
			'Africa/Dakar',
			'Africa/Dar_es_Salaam',
			'Africa/Djibouti',
			'Africa/Douala',
			'Africa/El_Aaiun',
			'Africa/Freetown',
			'Africa/Gaborone',
			'Africa/Harare',
			'Africa/Johannesburg',
			'Africa/Juba',
			'Africa/Kampala',
			'Africa/Khartoum',
			'Africa/Kigali',
			'Africa/Kinshasa',
			'Africa/Lagos',
			'Africa/Libreville',
			'Africa/Lome',
			'Africa/Luanda',
			'Africa/Lubumbashi',
			'Africa/Lusaka',
			'Africa/Malabo',
			'Africa/Maputo',
			'Africa/Maseru',
			'Africa/Mbabane',
			'Africa/Mogadishu',
			'Africa/Monrovia',
			'Africa/Nairobi',
			'Africa/Ndjamena',
			'Africa/Niamey',
			'Africa/Nouakchott',
			'Africa/Ouagadougou',
			'Africa/Porto-Novo',
			'Africa/Sao_Tome',
			'Africa/Tripoli',
			'Africa/Tunis',
			'Africa/Windhoek',

			'America/Adak',
			'America/Anchorage',
			'America/Anguilla',
			'America/Antigua',
			'America/Araguaina',
			'America/Argentina/Buenos_Aires',
			'America/Argentina/Catamarca',
			'America/Argentina/Cordoba',
			'America/Argentina/Jujuy',
			'America/Argentina/La_Rioja',
			'America/Argentina/Mendoza',
			'America/Argentina/Rio_Gallegos',
			'America/Argentina/Salta',
			'America/Argentina/San_Juan',
			'America/Argentina/San_Luis',
			'America/Argentina/Tucuman',
			'America/Argentina/Ushuaia',
			'America/Aruba',
			'America/Asuncion',
			'America/Atikokan',
			'America/Bahia',
			'America/Bahia_Banderas',
			'America/Barbados',
			'America/Belem',
			'America/Belize',
			'America/Blanc-Sablon',
			'America/Boa_Vista',
			'America/Bogota',
			'America/Boise',
			'America/Cambridge_Bay',
			'America/Campo_Grande',
			'America/Cancun',
			'America/Caracas',
			'America/Cayenne',
			'America/Cayman',
			'America/Chicago',
			'America/Chihuahua',
			'America/Costa_Rica',
			'America/Creston',
			'America/Cuiaba',
			'America/Curacao',
			'America/Danmarkshavn',
			'America/Dawson',
			'America/Dawson_Creek',
			'America/Denver',
			'America/Detroit',
			'America/Dominica',
			'America/Edmonton',
			'America/Eirunepe',
			'America/El_Salvador',
			'America/Fort_Nelson',
			'America/Fortaleza',
			'America/Glace_Bay',
			'America/Godthab',
			'America/Goose_Bay',
			'America/Grand_Turk',
			'America/Grenada',
			'America/Guadeloupe',
			'America/Guatemala',
			'America/Guayaquil',
			'America/Guyana',
			'America/Halifax',
			'America/Havana',
			'America/Hermosillo',
			'America/Indiana/Indianapolis',
			'America/Indiana/Knox',
			'America/Indiana/Marengo',
			'America/Indiana/Petersburg',
			'America/Indiana/Tell_City',
			'America/Indiana/Vevay',
			'America/Indiana/Vincennes',
			'America/Indiana/Winamac',
			'America/Inuvik',
			'America/Iqaluit',
			'America/Jamaica',
			'America/Juneau',
			'America/Kentucky/Louisville',
			'America/Kentucky/Monticello',
			'America/Kralendijk',
			'America/La_Paz',
			'America/Lima',
			'America/Los_Angeles',
			'America/Lower_Princes',
			'America/Maceio',
			'America/Managua',
			'America/Manaus',
			'America/Marigot',
			'America/Martinique',
			'America/Matamoros',
			'America/Mazatlan',
			'America/Menominee',
			'America/Merida',
			'America/Metlakatla',
			'America/Mexico_City',
			'America/Miquelon',
			'America/Moncton',
			'America/Monterrey',
			'America/Montevideo',
			'America/Montserrat',
			'America/Nassau',
			'America/New_York',
			'America/Nipigon',
			'America/Nome',
			'America/Noronha',
			'America/North_Dakota/Beulah',
			'America/North_Dakota/Center',
			'America/North_Dakota/New_Salem',
			'America/Ojinaga',
			'America/Panama',
			'America/Pangnirtung',
			'America/Paramaribo',
			'America/Phoenix',
			'America/Port-au-Prince',
			'America/Port_of_Spain',
			'America/Porto_Velho',
			'America/Puerto_Rico',
			'America/Punta_Arenas',
			'America/Rainy_River',
			'America/Rankin_Inlet',
			'America/Recife',
			'America/Regina',
			'America/Resolute',
			'America/Rio_Branco',
			'America/Santarem',
			'America/Santiago',
			'America/Santo_Domingo',
			'America/Sao_Paulo',
			'America/Scoresbysund',
			'America/Sitka',
			'America/St_Barthelemy',
			'America/St_Johns',
			'America/St_Kitts',
			'America/St_Lucia',
			'America/St_Thomas',
			'America/St_Vincent',
			'America/Swift_Current',
			'America/Tegucigalpa',
			'America/Thule',
			'America/Thunder_Bay',
			'America/Tijuana',
			'America/Toronto',
			'America/Tortola',
			'America/Vancouver',
			'America/Whitehorse',
			'America/Winnipeg',
			'America/Yakutat',
			'America/Yellowknife',

			'Antarctica/Casey',
			'Antarctica/Davis',
			'Antarctica/DumontDUrville',
			'Antarctica/Macquarie',
			'Antarctica/Mawson',
			'Antarctica/McMurdo',
			'Antarctica/Palmer',
			'Antarctica/Rothera',
			'Antarctica/Syowa',
			'Antarctica/Troll',
			'Antarctica/Vostok',

			'Arctic/Longyearbyen',

			'Asia/Aden',
			'Asia/Almaty',
			'Asia/Amman',
			'Asia/Anadyr',
			'Asia/Aqtau',
			'Asia/Aqtobe',
			'Asia/Ashgabat',
			'Asia/Atyrau',
			'Asia/Baghdad',
			'Asia/Bahrain',
			'Asia/Baku',
			'Asia/Bangkok',
			'Asia/Barnaul',
			'Asia/Beirut',
			'Asia/Bishkek',
			'Asia/Brunei',
			'Asia/Chita',
			'Asia/Choibalsan',
			'Asia/Colombo',
			'Asia/Damascus',
			'Asia/Dhaka',
			'Asia/Dili',
			'Asia/Dubai',
			'Asia/Dushanbe',
			'Asia/Famagusta',
			'Asia/Gaza',
			'Asia/Hebron',
			'Asia/Ho_Chi_Minh',
			'Asia/Hong_Kong',
			'Asia/Hovd',
			'Asia/Irkutsk',
			'Asia/Jakarta',
			'Asia/Jayapura',
			'Asia/Jerusalem',
			'Asia/Kabul',
			'Asia/Kamchatka',
			'Asia/Karachi',
			'Asia/Kathmandu',
			'Asia/Khandyga',
			'Asia/Kolkata',
			'Asia/Krasnoyarsk',
			'Asia/Kuala_Lumpur',
			'Asia/Kuching',
			'Asia/Kuwait',
			'Asia/Macau',
			'Asia/Magadan',
			'Asia/Makassar',
			'Asia/Manila',
			'Asia/Muscat',
			'Asia/Nicosia',
			'Asia/Novokuznetsk',
			'Asia/Novosibirsk',
			'Asia/Omsk',
			'Asia/Oral',
			'Asia/Phnom_Penh',
			'Asia/Pontianak',
			'Asia/Pyongyang',
			'Asia/Qatar',
			'Asia/Qyzylorda',
			'Asia/Riyadh',
			'Asia/Sakhalin',
			'Asia/Samarkand',
			'Asia/Seoul',
			'Asia/Shanghai',
			'Asia/Singapore',
			'Asia/Srednekolymsk',
			'Asia/Taipei',
			'Asia/Tashkent',
			'Asia/Tbilisi',
			'Asia/Tehran',
			'Asia/Thimphu',
			'Asia/Tokyo',
			'Asia/Tomsk',
			'Asia/Ulaanbaatar',
			'Asia/Urumqi',
			'Asia/Ust-Nera',
			'Asia/Vientiane',
			'Asia/Vladivostok',
			'Asia/Yakutsk',
			'Asia/Yangon',
			'Asia/Yekaterinburg',
			'Asia/Yerevan',

			'Atlantic/Azores',
			'Atlantic/Bermuda',
			'Atlantic/Canary',
			'Atlantic/Cape_Verde',
			'Atlantic/Faroe',
			'Atlantic/Madeira',
			'Atlantic/Reykjavik',
			'Atlantic/South_Georgia',
			'Atlantic/St_Helena',
			'Atlantic/Stanley',

			'Australia/Adelaide',
			'Australia/Brisbane',
			'Australia/Broken_Hill',
			'Australia/Currie',
			'Australia/Darwin',
			'Australia/Eucla',
			'Australia/Hobart',
			'Australia/Lindeman',
			'Australia/Lord_Howe',
			'Australia/Melbourne',
			'Australia/Perth',
			'Australia/Sydney',

			'Europe/Amsterdam',
			'Europe/Andorra',
			'Europe/Astrakhan',
			'Europe/Athens',
			'Europe/Belgrade',
			'Europe/Berlin',
			'Europe/Bratislava',
			'Europe/Brussels',
			'Europe/Bucharest',
			'Europe/Budapest',
			'Europe/Busingen',
			'Europe/Chisinau',
			'Europe/Copenhagen',
			'Europe/Dublin',
			'Europe/Gibraltar',
			'Europe/Guernsey',
			'Europe/Helsinki',
			'Europe/Isle_of_Man',
			'Europe/Istanbul',
			'Europe/Jersey',
			'Europe/Kaliningrad',
			'Europe/Kiev',
			'Europe/Kirov',
			'Europe/Lisbon',
			'Europe/Ljubljana',
			'Europe/London',
			'Europe/Luxembourg',
			'Europe/Madrid',
			'Europe/Malta',
			'Europe/Mariehamn',
			'Europe/Minsk',
			'Europe/Monaco',
			'Europe/Moscow',
			'Europe/Oslo',
			'Europe/Paris',
			'Europe/Podgorica',
			'Europe/Prague',
			'Europe/Riga',
			'Europe/Rome',
			'Europe/Samara',
			'Europe/San_Marino',
			'Europe/Sarajevo',
			'Europe/Saratov',
			'Europe/Simferopol',
			'Europe/Skopje',
			'Europe/Sofia',
			'Europe/Stockholm',
			'Europe/Tallinn',
			'Europe/Tirane',
			'Europe/Ulyanovsk',
			'Europe/Uzhgorod',
			'Europe/Vaduz',
			'Europe/Vatican',
			'Europe/Vienna',
			'Europe/Vilnius',
			'Europe/Volgograd',
			'Europe/Warsaw',
			'Europe/Zagreb',
			'Europe/Zaporozhye',
			'Europe/Zurich',

			'Indian/Antananarivo',
			'Indian/Chagos',
			'Indian/Christmas',
			'Indian/Cocos',
			'Indian/Comoro',
			'Indian/Kerguelen',
			'Indian/Mahe',
			'Indian/Maldives',
			'Indian/Mauritius',
			'Indian/Mayotte',
			'Indian/Reunion',

			'Pacific/Apia',
			'Pacific/Auckland',
			'Pacific/Bougainville',
			'Pacific/Chatham',
			'Pacific/Chuuk',
			'Pacific/Easter',
			'Pacific/Efate',
			'Pacific/Enderbury',
			'Pacific/Fakaofo',
			'Pacific/Fiji',
			'Pacific/Funafuti',
			'Pacific/Galapagos',
			'Pacific/Gambier',
			'Pacific/Guadalcanal',
			'Pacific/Guam',
			'Pacific/Honolulu',
			'Pacific/Kiritimati',
			'Pacific/Kosrae',
			'Pacific/Kwajalein',
			'Pacific/Majuro',
			'Pacific/Marquesas',
			'Pacific/Midway',
			'Pacific/Nauru',
			'Pacific/Niue',
			'Pacific/Norfolk',
			'Pacific/Noumea',
			'Pacific/Pago_Pago',
			'Pacific/Palau',
			'Pacific/Pitcairn',
			'Pacific/Pohnpei',
			'Pacific/Port_Moresby',
			'Pacific/Rarotonga',
			'Pacific/Saipan',
			'Pacific/Tahiti',
			'Pacific/Tarawa',
			'Pacific/Tongatapu',
			'Pacific/Wake',
			'Pacific/Wallis'
		);

		return $arTimezones;
	}

	/**
	 * Возвращает список значений для <select>
	 *
	 * @return array
	 */
	public static function getTimezonesSelectValues ()
	{
		$arValues = array();
		Loc::includeLocFile(__FILE__,'ms_core_');
		foreach (static::getTimezonesList() as $code)
		{
			$_code = strtolower(str_replace('/','_',$code));
			$arValues[] = array(
				'VALUE' => $code,
				'NAME' => Loc::getMessage('ms_core_timezone_'.$_code)
			);
		}

		return $arValues;
	}

	/**
	 * Вызывает функцию установки временнОй зоны по умолчанию. Вызывается в прологе
	 *
	 * @param string $timezone
	 */
	public static function setDefaultTimezone($timezone = 'Europe/Moscow')
	{
		if (is_null($timezone) || !in_array($timezone,static::getTimezonesList()))
		{
			$timezone = 'Europe/Moscow';
		}

		date_default_timezone_set($timezone);
	}

	/**
	 * Возвращает текущее время, либо переданной в параметре в заданном формате
	 *
	 * Является обёрткой функции date {@link http://php.net/manual/ru/function.date.php}
	 *
	 * @param string $format
	 * @param int    $timestamp
	 *
	 * @return bool|string
	 * @since 0.2.0
	 */
	public static function getDateTimestamp ($format = "Y-m-d", $timestamp = NULL)
	{
		$date = new self();
		if (!is_null ($timestamp))
		{
			$date->setTimestamp($timestamp);
		}

		return $date->format($format);
	}

	/**
	 * Возвращает текущее время или переданное в параметре в формате базы данных
	 *
	 * @param int $timestamp
	 *
	 * @return bool|string
	 * @since 0.2.0
	 */
	public static function getDateDBTimestamp ($timestamp = NULL)
	{
		$date = new self();
		if (!is_null ($timestamp))
		{
			$date->setTimestamp($timestamp);
		}

		return $date->format('Y-m-d');
	}

	/**
	 * Проверяет правильность указанной даты
	 *
	 * Верные даты: 'YYYY-MM-DD' и 'YYYY-M-D'
	 * Существует зависимость от ошибки 2038 года - максимально возможной датой является 31.12.2037
	 *
	 * @api
	 *
	 * @param string $date Дата
	 *
	 * @return bool true - если дата верна, иначе false
	 * @since 0.2.0
	 */
	public static function checkDate ($date)
	{
		$arData = explode ('-', $date);
		if (
			(intval ($arData[2]) >= 1 && intval ($arData[2]) <= 31)
			&& (intval ($arData[1]) >= 1 && intval ($arData[1] <= 12))
			&& (intval ($arData[0]) >= 0000 && intval ($arData[0]) <= 9999)
		)
		{
			return TRUE;
		} else
		{
			return FALSE;
		}
	}

	/** SET */

	/**
	 * Устанавливает дату из массива
	 *
	 * @param array $arDate - массив, который может иметь следующие ключи:
	 *                      DAY - день
	 *                      MONTH - месяц
	 *                      YEAR - год
	 *                      HOUR - часы
	 *                      MIN - минуты
	 *                      SEC - секунды.
	 *                      При отсутствии какого-либо параметра, его значение
	 *                      берется из текущей даты и времени, либо если задан второй параметр,
	 *                      то дата берется из него
	 * @param Date  $date - дата, из которой берутся недостающие параметры в массиве
	 * @since 0.2.0
	 *
	 * @return Date $this
	 */
	public function setDateFromArray ($arDate, Date $date = NULL)
	{
		if (is_null ($date))
		{
			$date = $this;
		}

		if (!isset($arDate['DAY']) || is_null ($arDate['DAY']))
		{
			$day = $date->format('j');
		} else
		{
			$day = $arDate['DAY'];
		}

		if (!isset($arDate['MONTH']) || is_null ($arDate['MONTH']))
		{
			$month = $date->format ('n');
		} else
		{
			$month = $arDate['MONTH'];
		}

		if (!isset($arDate['YEAR']) || is_null ($arDate['YEAR']))
		{
			$year = $date->format ('Y');
		} else
		{
			$year = $arDate['YEAR'];
		}

		if (!isset($arDate['HOUR']) || is_null ($arDate['HOUR']))
		{
			$hour = $date->format ('G');
		} else
		{
			$hour = $arDate['HOUR'];
		}

		if (!isset($arDate['MIN']) || is_null ($arDate['MIN']))
		{
			$min = (int)$date->format ('i');
		} else
		{
			$min = $arDate['MIN'];
		}

		if (!isset($arDate['SEC']) || is_null ($arDate['SEC']))
		{
			$sec = 0;
		} else
		{
			$sec = $arDate['SEC'];
		}

		$this->setDate($year, $month, $day);
		$this->setTime($hour, $min, $sec);

		return $this;
	}

	/**
	 * Устанавливает начало дня (время 00:00:00) для текущей метки времени
	 *
	 * @return Date $this
	 * @since 0.2.0
	 */
	public function setStartDay ()
	{
		$this->setTime(0, 0);

		return $this;
	}

	/**
	 * Устанавливает конец дня (время 23:59:59) для текущей метки времени
	 *
	 * @return Date $this
	 * @since 0.2.0
	 */
	public function setEndDay ()
	{
		$this->setTime(23, 59, 59);

		return $this;
	}

	/**
	 * Меняет текущую метку времени на завтрашний день
	 *
	 * @return Date $this
	 * @since 0.2.0
	 */
	public function setNextDay ()
	{
		$this->modify("+1 days");

		return $this;
	}

	/**
	 * Меняет текущую метку времени на вчерашний день
	 *
	 * @return Date $this
	 * @since 0.2.0
	 */
	public function setPrevDay ()
	{
		$this->modify("-1 days");

		return $this;
	}

	/**
	 * Меняет текущую метку времени на следующий месяц
	 *
	 * @return Date $this
	 * @since 0.2.0
	 */
	public function setNextMonth()
	{
		$this->modify("+1 month");

		return $this;
	}

	/**
	 * Меняет текущую метку премени на вредыдущий месяц
	 *
	 * @return Date $this
	 * @since 0.2.0
	 */
	public function setPrevMonth()
	{
		$this->modify("-1 month");

		return $this;
	}

	/**
	 * Меняет текущую метку времени на следующий год
	 *
	 * @return Date $this
	 * @since 0.2.0
	 */
	public function setNextYear()
	{
		$this->modify("+1 year");

		return $this;
	}

	/**
	 * Меняет текущую метку времени на предыдущий год
	 *
	 * @return Date $this
	 * @since 0.2.0
	 */
	public function setPrevYear()
	{
		$this->modify("-1 year");

		return $this;
	}

	/**
	 * Меняет текущую метку времени, устанавливая первый день текущего месяца
	 *
	 * @return Date $this
	 * @since 0.2.0
	 */
	public function setFirstDayOfMonth()
	{
		$this->modify('first day of '.$this->format('F').' '.$this->format('Y'));

		return $this;
	}

	/**
	 * Меняет текущую метку времени, устанавливая последний день текущего месяца
	 *
	 * @return Date $this
	 * @since 0.2.0
	 */
	public function setLastDayOfMonth()
	{
		$this->modify('last day of '.$this->format('F').' '.$this->format('Y'));

		return $this;
	}

	/**
	 * Меняет текущую метку времени, устанавливая первый день текущего года
	 *
	 * @return Date $this
	 * @since 0.2.0
	 */
	public function setFirstDayOfYear()
	{
		$this->setDate($this->format('Y'),1,1);

		return $this;
	}


	/** GET */

	/**
	 * Возвращает текущую или переданную в параметре метку времени в заданном формате
	 * Формат задается аналогичный функции date
	 * @link http://php.net/manual/ru/function.date.php
	 *
	 * @param string $format
	 * @param int    $timestamp
	 *
	 * @return string
	 * @since 0.2.0
	 */
	public function getDate ($format = "Y-m-d", $timestamp = NULL)
	{
		if (!is_null($timestamp))
		{
			$tmp = new self();
			$tmp->setTimestamp($timestamp);

			return $tmp->format($format);
		}

		return $this->format($format);
	}

	/**
	 * Возвращает текущую или переданную в параметре метку времени в формате даты сайта
	 *
	 * @param int $timestamp
	 *
	 * @return string
	 * @since 0.2.0
	 */
	public function getDateSite ($timestamp = NULL)
	{
		$formatSiteDate = Application::getInstance()->getSettings()->getSiteDate();

		return $this->getDate($formatSiteDate,$timestamp);
	}

	/**
	 * Возвращает текущую или переданную в параметре метку времени в формате даты и времени сайта
	 *
	 * @param int $timestamp
	 *
	 * @return string
	 * @since 0.2.0
	 */
	public function getDateTimeSite ($timestamp = NULL)
	{
		$formatDateTimeSite = Application::getInstance()->getSettings()->getSiteDateTime();

		return $this->getDate($formatDateTimeSite,$timestamp);
	}

	/**
	 * Возвращает текущую или переданную в параметре метку времени в формате даты базы данных
	 *
	 * @param int $timestamp
	 *
	 * @return string
	 * @since 0.2.0
	 */
	public function getDateDB ($timestamp = NULL)
	{
		return $this->getDate('Y-m-d',$timestamp);
	}

	/**
	 * Возвращает текущую или переданную в параметре метку времени в формате даты и времени базы данных
	 *
	 * @param int $timestamp
	 *
	 * @return string
	 * @since 0.2.0
	 */
	public function getDateTimeDB ($timestamp = NULL)
	{
		return $this->getDate('Y-m-d H:i:s',$timestamp);
	}

	/**
	 * Возвращает текущую или переданную в параметре метку времени в формате времени
	 *
	 * @param int $timestamp
	 *
	 * @return string
	 * @since 0.2.0
	 */
	public function getTime ($timestamp = NULL)
	{
		return $this->getDate('H:i:s',$timestamp);
	}

	/**
	 * Возвращает время в формате сайта для текущей даты или для переданного timestamp
	 *
	 * @param int $timestamp - метка времени unix
	 *
	 * @return string
	 */
	public function getTimeSite ($timestamp = NULL)
	{
		$formatTimeSite = Application::getInstance()->getSettings()->getSiteTime();

		return $this->getDate($formatTimeSite,$timestamp);
	}

	/**
	 * Возвращает краткое наименование дня недели
	 * ('Вс.', 'Пн.', 'Вт.' и т.д.)
	 *
	 * @api
	 *
	 * @param int $day День недели в формате date('w')
	 *
	 * @return bool|string Краткое наименование дня недели, либо false
	 * @since 0.2.0
	 */
	public function getShortNameDayOfWeek ($day=null)
	{
		if (is_null($day))
		{
			$day = $this->format('w');
		}
		Loc::includeLocFile(__FILE__,'ms_core_');

		switch ((int)$day)
		{
			case 0:
				return Loc::getModuleMessage('core','date_su');
			case 1:
				return Loc::getModuleMessage('core','date_mo');
			case 2:
				return Loc::getModuleMessage('core','date_tu');
			case 3:
				return Loc::getModuleMessage('core','date_we');
			case 4:
				return Loc::getModuleMessage('core','date_th');
			case 5:
				return Loc::getModuleMessage('core','date_fr');
			case 6:
				return Loc::getModuleMessage('core','date_st');
			default:
				return false;
		}
	}

	/**
	 * Возвращает полное наименование дня недели
	 *
	 * ('Воскресенье', 'Понедельник', 'Вторник' и т.д.)
	 *
	 * @api
	 *
	 * @param int $day День недели в формате date('w')
	 *
	 * @return bool|string Полное наименование дня недели, либо false
	 * @since 0.2.0
	 */
	public function getNameDayOfWeek ($day=null)
	{
		if (is_null($day))
		{
			$day = $this->format('w');
		}
		Loc::includeLocFile(__FILE__,'ms_core_');

		switch ((int)$day)
		{
			case 0:
				return Loc::getModuleMessage('core','date_sunday');
			case 1:
				return Loc::getModuleMessage('core','date_monday');
			case 2:
				return Loc::getModuleMessage('core','date_tuesday');
			case 3:
				return Loc::getModuleMessage('core','date_wednesday');
			case 4:
				return Loc::getModuleMessage('core','date_thursday');
			case 5:
				return Loc::getModuleMessage('core','date_friday');
			case 6:
				return Loc::getModuleMessage('core','date_saturday');
			default:
				return false;
		}
	}

	/**
	 * Возвращает наименование месяца
	 *
	 * ('Январь', 'Февраль', 'Март' и т.д.)
	 *
	 * @api
	 *
	 * @param int $month Месяц в формате date('n')
	 *
	 * @return bool|string Наименование месяца, либо false
	 * @since 0.2.0
	 */
	public function getNameMonth ($month=null)
	{
		if (is_null($month))
		{
			$month = $this->format('n');
		}
		Loc::includeLocFile(__FILE__,'ms_core_');

		switch ((int)$month)
		{
			case 1:
				return Loc::getModuleMessage('core','date_january');
			case 2:
				return Loc::getModuleMessage('core','date_february');
			case 3:
				return Loc::getModuleMessage('core','date_march');
			case 4:
				return Loc::getModuleMessage('core','date_april');
			case 5:
				return Loc::getModuleMessage('core','date_may');
			case 6:
				return Loc::getModuleMessage('core','date_june');
			case 7:
				return Loc::getModuleMessage('core','date_july');
			case 8:
				return Loc::getModuleMessage('core','date_august');
			case 9:
				return Loc::getModuleMessage('core','date_september');
			case 10:
				return Loc::getModuleMessage('core','date_october');
			case 11:
				return Loc::getModuleMessage('core','date_november');
			case 12:
				return Loc::getModuleMessage('core','date_december');
			default:
				return FALSE;
		}
	}

	/**
	 * Возвращает наименование месяца в винительном падеже
	 *
	 * ('января', 'февраля', 'марта' и т.д.)
	 *
	 * @api
	 *
	 * @param int $month Месяц в формате date('n')
	 *
	 * @return bool|string Наименование месяца, либо false
	 * @since 0.2.0
	 */
	public function getNameMonthAccusative ($month=null)
	{
		if (is_null($month))
		{
			$month = $this->format('n');
		}
		Loc::includeLocFile(__FILE__,'ms_core_');

		switch ((int)$month)
		{
			case 1:
				return Loc::getModuleMessage('core','date_accusative_january');
			case 2:
				return Loc::getModuleMessage('core','date_accusative_february');
			case 3:
				return Loc::getModuleMessage('core','date_accusative_march');
			case 4:
				return Loc::getModuleMessage('core','date_accusative_april');
			case 5:
				return Loc::getModuleMessage('core','date_accusative_may');
			case 6:
				return Loc::getModuleMessage('core','date_accusative_june');
			case 7:
				return Loc::getModuleMessage('core','date_accusative_july');
			case 8:
				return Loc::getModuleMessage('core','date_accusative_august');
			case 9:
				return Loc::getModuleMessage('core','date_accusative_september');
			case 10:
				return Loc::getModuleMessage('core','date_accusative_october');
			case 11:
				return Loc::getModuleMessage('core','date_accusative_november');
			case 12:
				return Loc::getModuleMessage('core','date_accusative_december');
			default:
				return FALSE;
		}
	}

	/**
	 * Возвращает краткое наименование месяца
	 *
	 * ('янв', 'фев', 'мар' и т.д.)
	 *
	 * @api
	 *
	 * @param int $month Месяц в формате date('n')
	 *
	 * @return bool|string Наименование месяца, либо false
	 * @since 0.2.0
	 */
	public function getNameMonthShort ($month=null)
	{
		if (is_null($month))
		{
			$month = $this->format('n');
		}
		Loc::includeLocFile(__FILE__,'ms_core_');

		switch ((int)$month)
		{
			case 1:
				return Loc::getModuleMessage('core','date_short_jan');
			case 2:
				return Loc::getModuleMessage('core','date_short_feb');
			case 3:
				return Loc::getModuleMessage('core','date_short_mar');
			case 4:
				return Loc::getModuleMessage('core','date_short_apr');
			case 5:
				return Loc::getModuleMessage('core','date_short_may');
			case 6:
				return Loc::getModuleMessage('core','date_short_jun');
			case 7:
				return Loc::getModuleMessage('core','date_short_jul');
			case 8:
				return Loc::getModuleMessage('core','date_short_aug');
			case 9:
				return Loc::getModuleMessage('core','date_short_sep');
			case 10:
				return Loc::getModuleMessage('core','date_short_oct');
			case 11:
				return Loc::getModuleMessage('core','date_short_nov');
			case 12:
				return Loc::getModuleMessage('core','date_short_dec');
			default:
				return FALSE;
		}
	}


	/** IS */

	/**
	 * Проверяет, является текущая или переданная в параметре метка времени сегодняшним днем
	 *
	 * @param Date $date
	 *
	 * @return bool
	 * @since 0.2.0
	 */
	public function isToday($date=null)
	{
		if (is_null($date))
		{
			$date = $this;
		}
		$now = new self();

		return ($date->format('Y-m-d') == $now->format('Y-m-d'));
	}

	/**
	 * Проверяет совпадают ли даты текущей метки времени и переданной в параметре
	 *
	 * @param Date $date
	 *
	 * @return bool
	 * @since 0.2.0
	 */
	public function isDateEqual (Date $date)
	{
		return ($date->format('Y-m-d') == $this->format('Y-m-d'));
	}

	/**
	 * Возвращает true, если сегодняшний день выходной
	 *
	 * Если параметр отсутствует или равен true, а также если модуль ms.dates установлен, проверка осуществляется с
	 * использованием метода этого модуля. Это позволит считать выходными праздничные дни, а не только субботу и
	 * воскресенье. Если передан параметр false, метод фактически смотрит суббота сегодня или воскресенье.
	 *
	 * @param bool $fromDates - флаг работы с использованием модуля dates
	 *
	 * @return bool
	 * @since 0.2.0
	 */
	public function isWeekEnd ($fromDates=true)
	{
		if ($fromDates && Loader::issetModule('ms.dates') && Loader::includeModule('ms.dates'))
		{
			return WorkCalendar::isWeekEnd($this);
		}
		else
		{
			if ($this->format('w')>=1 && $this->format('w')<=5)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
	}


	/** OTHER */

	/**
	 * Обертка функции strtotime для текущей метки времени, либо переданной в параметре
	 *
	 * Параметры в функции идентичны strtotime {@link http://php.net/manual/ru/function.strtotime.php}
	 * за исключением метки времени, так как если она не передана, используется метка времени объекта,
	 * а не текущее время
	 *
	 * @param string $time - строковое представление времени
	 * @param int $now - метка времени
	 *
	 * @return $this
	 * @since 0.2.0
	 */
	public function strToTime ($time, $now = NULL)
	{
		if (!is_null ($now))
		{
			$tmp = new self();
			$tmp->setTimestamp($now);
			$tmp->modify($time);

			return $tmp;
		}
		return $this->modify($time);
	}


	/** MAGIC */

	/**
	 * Возвращает строковое представление объекта в формате даты/времени сайта
	 *
	 * @return string
	 * @since 0.2.0
	 */
	public function __toString ()
	{
		return $this->getDateTimeSite();
	}
}
