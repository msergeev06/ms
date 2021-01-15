<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2017 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Type;

use Ms\Core\Entity\Errors\FileLogger;
use Ms\Core\Entity\System\Application;
use Ms\Core\Entity\Modules\Loader;
use Ms\Core\Exceptions\Arguments\ArgumentException;
use Ms\Core\Exceptions\SystemException;
use Ms\Dates\Lib\WorkCalendar;

/**
 * Класс Ms\Core\Entity\Type\Date
 * Класс, описывающий тип переменной "Дата и время"
 * Используется во всех операциях с датой и/или временем
 */
class Date extends \DateTime
{
    const DEFAULT_TIMEZONE = 'Europe/Moscow';

    protected $logger = null;

    /**
     * Конструктор объекта даты
     *
     * Основные форматы
     * <table>
     * <tr><td>db</td><td>YYYY-MM-DD (Y-m-d)</td></tr>
     * <tr><td>site</td><td>DD.MM.YYYY (d.m.Y)</td></tr>
     * <tr><td>db_datetime</td><td>YYYY-MM-DD HH:II:SS (Y-m-d H:i:s)</td></tr>
     * <tr><td>site_datetime</td><td>DD.MM.YYYY HH:II:SS (d.m.Y H:i:s)</td></tr>
     * <tr><td>site_time</td><td>HH:II:SS (H:i:s)</td></tr>
     * </table>
     *
     * Список возможных символов для составления строки format
     * <table>
     * <tr><td colspan="3">День</td></tr>
     * <tr>
     * <td>d и j</td>
     * <td>День месяца, 2 цифры с нулем в начале или без него</td>
     * <td>От 01 до 31 либо от 1 до 31</td>
     * </tr>
     * <tr>
     * <td>D и l</td>
     * <td>Текстовое представление дня месяца</td>
     * <td>От Mon до Sun либо от Sunday до Saturday</td>
     * </tr>
     * <tr>
     * <td>S</td>
     * <td>Суффикс для числа в английской нумерации, 2 буквы. Эти буквы будут пропущены при разборе строки.</td>
     * <td>st, nd, rd или th</td>
     * </tr>
     * <tr>
     * <td>z</td>
     * <td>Номер дня с начала года (начиная с нуля)</td>
     * <td>C 0 по 365</td>
     * </tr>
     * <tr><td colspan="3">Месяц</td></tr>
     * <tr>
     * <td>F и M</td>
     * <td>Текстовое представление месяца, например January или Sept</td>
     * <td>С January по December либо с Jan по Dec</td>
     * </tr>
     * <tr>
     * <td>m и n</td>
     * <td>Числовое представление месяца с первым нулем или без него</td>
     * <td>С 01 по 12 либо с 1 по 12</td>
     * </tr>
     * <tr><td colspan="3">Год</td></tr>
     * <tr>
     * <td>Y</td>
     * <td>Полное числовое представление года, 4 цифры</td>
     * <td>Примеры: 1999 или 2003</td>
     * </tr>
     * <tr>
     * <td>y</td>
     * <td>2 цифры в представлении года (в диапазоне 1970-2069 включительно)</td>
     * <td>Примеры: 99 или 03 (будет расшифровано как 1999 и 2003 соответственно)</td>
     * </tr>
     * <tr><td colspan="3">Время</td></tr>
     * <tr>
     * <td>a и A</td>
     * <td>До полудня и После полудня</td>
     * <td>am или pm</td>
     * </tr>
     * <tr>
     * <td>g и h</td>
     * <td>12-ти часовой формат времени с первым нулем или без него</td>
     * <td>С 1 по 12 либо с 01 по 12</td>
     * </tr>
     * <tr>
     * <td>G и H</td>
     * <td>24-х часовой формат времени с нулем в начале или без него</td>
     * <td>С 0 по 23 или с 00 по 23</td>
     * </tr>
     * <tr>
     * <td>i</td>
     * <td>Минуты с нулем в начале</td>
     * <td>С 00 по 59</td>
     * </tr>
     * <tr>
     * <td>s</td>
     * <td>Секунды с нулем в начале</td>
     * <td>От 00 до 59</td>
     * </tr>
     * <tr>
     * <td>u</td>
     * <td>Микросекунды (до 6 цифр)</td>
     * <td>Примеры: 45, 654321</td>
     * </tr>
     * <tr><td colspan="3">Временная зона</td></tr>
     * <tr>
     * <td>e, O, P и T</td>
     * <td>Идентификатор временной зоны, либо разница в часах</td>
     * <td>Примеры: UTC, GMT, Atlantic/Azores относительно UTC, либо разница относительно UTC с запятой
     * <br>или +0200 или +02:00 или EST, MDT между часами и минутами, либо аббревиатура временной зоны</td>
     * </tr>
     * <tr><td colspan="3">Дата/Время полностью</td></tr>
     * <tr>
     * <td>U</td>
     * <td>Количество секунд с начала Эпохи Unix (January 1 1970 00:00:00 GMT)</td>
     * <td>Пример: 1292177455</td>
     * </tr>
     * <tr><td colspan="3">Пробел и Разделители</td></tr>
     * <tr>
     * <td>(пробел)</td>
     * <td>Один пробел или один отступ табуляции</td>
     * <td>Пример:</td>
     * </tr>
     * <tr>
     * <td>#</td>
     * <td>Один из следующих символов: ; : / . , - ( или )</td>
     * <td>Пример: /</td>
     * </tr>
     * <tr>
     * <td>; : / . , - ( или )</td>
     * <td>Символ разделитель</td>
     * <td>Пример: -</td>
     * </tr>
     * <tr>
     * <td>?</td>
     * <td>Один случайный (любой) символ</td>
     * <td>Пример: ^ (Будьте внимательны: в UTF-8 кодировке вам может потребоваться более одного ?, так как там один
     * символ может занимать более одного байта. В таких случаях может помочь использование *</td>
     * </tr>
     * <tr>
     * <td>*</td>
     * <td>Любое количество любых символов до следующего разделителя</td>
     * <td>Пример: * в Y-*-d для строки 2009-aWord-08 будет соответствовать aWord</td>
     * </tr>
     * <tr>
     * <td>!</td>
     * <td>Приводит значения всех полей (год, месяц, день, час, минута, секунда, временная зона) ко времени начала
     * Эпохи Unix. </td>
     * <td>Без ! все поля будут соответствовать текущему времени.</td>
     * </tr>
     * <tr>
     * <td>|</td>
     * <td>Приводит значения незаданных полей (год, месяц, день, час, минута, секунда, временная зона) ко времени
     * начала Эпохи Unix.</td>
     * <td>Y-m-d| установит год, месяц и день в соответствии с данными в строке, а часы, минуты и секунды установит в
     * 0.</td>
     * </tr>
     * <tr>
     * <td>+</td>
     * <td>Если задан этот спецификатор, данные, завершающие строку (нуль байт например) не будут вызывать ошибку,
     * только предупреждение</td>
     * <td>Используйте Date::getLastErrors() для определения, были ли в строке завершающие символы</td>
     * </tr>
     * </table>
     *
     * @param string|null        $date     Строковое представление даты
     * @param string|null        $format   Формат строкового представления даты
     * @param \DateTimeZone|null $timezone Временная зона
     */
    public function __construct (string $date = 'now', string $format = 'db', \DateTimeZone $timezone = null)
    {
        $this->logger = new FileLogger('core');
        $settings = Application::getInstance()->getSettings();
        //Определяем временнУю зону и верно ли она задана
        if (is_null($timezone) || !in_array($timezone, static::getTimezonesList()))
        {
            $timezone = new \DateTimeZone($settings->getTimezone());
        }
        //Если дата не задана, будем использовать текущее время
        if (is_null($date) || $date == 'now')
        {
            try
            {
                parent::__construct('now', $timezone);
            }
            catch (\Exception $e)
            {
                (new SystemException($e->getMessage(), $e->getCode(), $e->getFile(), $e->getLine(), $e))
                    ->addMessageToLog($this->logger)
                ;
                try
                {
                    parent::__construct('now');
                }
                catch (\Exception $e)
                {
                }
            }

            return;
        }
        //Если формат не задан, считаем что это формат БД
        if (!isset($format))
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
                $dt = parent::createFromFormat($settings->getSiteDateFormat(), $date, $timezone);
                break;
            case 'site_datetime': //DD.MM.YYYY HH:II:SS (или другой из настроек)
                $dt = parent::createFromFormat($settings->getSiteDateTimeFormat(), $date, $timezone);
                break;
            case 'site_time': //HH:II:SS (или другой из настроек)
                $dt = parent::createFromFormat($settings->getSiteTimeFormat(), $date, $timezone);
                break;
            default: //Другой формат
                $dt = parent::createFromFormat($format, $date, $timezone);
                break;
        }

        if ($dt === false)
        {
            (new ArgumentException('Wrong date "' . $date . '" in format "' . $format . '"'))
                ->addMessageToLog($this->logger)
            ;
            try
            {
                parent::__construct('now');
            }
            catch (\Exception $e)
            {
            }
        }

        try
        {
            parent::__construct($dt->format('Y-m-d H:i:s'), $timezone);
        }
        catch (\Exception $e)
        {
            (new SystemException($e->getMessage(), $e->getCode(), $e->getFile(), $e->getLine(), $e))
                ->addMessageToLog($this->logger)
            ;
        }
        unset($dt);
    }

    /** Static */

    /**
     * Возвращает массив со списком возможных временнЫх зон
     *
     * @return array
     */
    public static function getTimezonesList ()
    {
        $arTimezones = [
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
        ];

        return $arTimezones;
    }

    /**
     * Возвращает список значений для <select>
     *
     * @return array
     */
    public static function getTimezonesSelectValues ()
    {
        $arValues = [];
        IncludeLangFile(__FILE__);
        foreach (static::getTimezonesList() as $code)
        {
            $_code = strtolower(str_replace('/', '_', $code));
            $arValues[] = [
                'VALUE' => $code,
                'NAME'  => GetCoreMessage('timezone_' . $_code)
            ];
        }

        return $arValues;
    }

    /**
     * Вызывает функцию установки временнОй зоны по умолчанию. Вызывается в прологе
     *
     * @param string $timezone
     */
    public static function setDefaultTimezone ($timezone = self::DEFAULT_TIMEZONE)
    {
        if (is_null($timezone) || !in_array($timezone, static::getTimezonesList()))
        {
            $timezone = self::DEFAULT_TIMEZONE;
        }

        date_default_timezone_set($timezone);
    }

    /**
     * Возвращает текущее время, либо переданной в параметре в заданном формате
     *
     * @param string $format
     * @param int    $timestamp
     *
     * @return bool|string
     */
    public static function getDateTimestamp ($format = "Y-m-d", $timestamp = null)
    {
        try
        {
            $date = new self();
            if (!is_null($timestamp))
            {
                $date->setTimestamp($timestamp);
            }

            return $date->format($format);
        }
        catch (SystemException $e)
        {
            return date($format, $timestamp);
        }
    }

    /**
     * Возвращает текущее время или переданное в параметре в формате базы данных
     *
     * @param int $timestamp
     *
     * @return bool|string
     */
    public static function getDateDBTimestamp ($timestamp = null)
    {
        try
        {
            $date = new self();
            if (!is_null($timestamp))
            {
                $date->setTimestamp($timestamp);
            }

            return $date->format('Y-m-d');
        }
        catch (SystemException $e)
        {
            return date('Y-m-d',$timestamp);
        }
    }

    /**
     * Проверяет правильность указанной даты
     *
     * Верные даты: 'YYYY-MM-DD' и 'YYYY-M-D'
     * Существует зависимость от ошибки 2038 года - максимально возможной датой является 31.12.2037
     *
     * @param string $date Дата
     *
     * @return bool true - если дата верна, иначе false
     * @since 0.2.0
     * @api
     *
     */
    public static function checkDate ($date)
    {
        $arData = explode('-', $date);
        if (
            (intval($arData[2]) >= 1 && intval($arData[2]) <= 31)
            && (intval($arData[1]) >= 1 && intval($arData[1] <= 12))
            && (intval($arData[0]) >= 0000 && intval($arData[0]) <= 9999)
        )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

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
     * @param Date  $date   - дата, из которой берутся недостающие параметры в массиве
     *
     * @return $this
     */
    public function setDateFromArray ($arDate, Date $date = null)
    {
        if (is_null($date))
        {
            $date = $this;
        }

        if (!isset($arDate['DAY']) || is_null($arDate['DAY']))
        {
            $day = $date->format('j');
        }
        else
        {
            $day = $arDate['DAY'];
        }

        if (!isset($arDate['MONTH']) || is_null($arDate['MONTH']))
        {
            $month = $date->format('n');
        }
        else
        {
            $month = $arDate['MONTH'];
        }

        if (!isset($arDate['YEAR']) || is_null($arDate['YEAR']))
        {
            $year = $date->format('Y');
        }
        else
        {
            $year = $arDate['YEAR'];
        }

        if (!isset($arDate['HOUR']) || is_null($arDate['HOUR']))
        {
            $hour = $date->format('G');
        }
        else
        {
            $hour = $arDate['HOUR'];
        }

        if (!isset($arDate['MIN']) || is_null($arDate['MIN']))
        {
            $min = (int)$date->format('i');
        }
        else
        {
            $min = $arDate['MIN'];
        }

        if (!isset($arDate['SEC']) || is_null($arDate['SEC']))
        {
            $sec = 0;
        }
        else
        {
            $sec = $arDate['SEC'];
        }

        $this->setDate($year, $month, $day);
        $this->setTime($hour, $min, $sec);

        return $this;
    }

    /**
     * Устанавливает дату по параметрам
     *
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return $this
     */
    public function setDate ($year, $month, $day)
    {
        parent::setDate($year, $month, $day);

        return $this;
    }

    /**
     * Устанавливает число месяца
     *
     * @param int $day Число месяца от 1 до 31 (в зависимости от месяца даты)
     *
     * @return $this
     */
    public function setDay (int $day)
    {
        $month = (int)$this->format('m');
        $year = (int)$this->format('Y');
        $day = (int)$day;
        parent::setDate($year, $month, $day);

        return $this;
    }

    /**
     * Устанавливает месяц
     *
     * @param int $month Месяц от 1 до 12
     *
     * @return $this
     */
    public function setMonth ($month)
    {
        $day = (int)$this->format('d');
        $year = (int)$this->format('Y');
        $month = (int)$month;
        parent::setDate($year, $month, $day);

        return $this;
    }

    /**
     * Устанавливает год
     *
     * @param int $year Год от 0000 до 9999
     *
     * @return $this
     */
    public function setYear ($year)
    {
        $month = (int)$this->format('m');
        $day = (int)$this->format('d');
        $year = (int)$year;
        parent::setDate($year, $month, $day);

        return $this;
    }

    /**
     * Устанавливает время по параметрам
     *
     * @param int $hour         Часы от 0 до 23
     * @param int $minute       Минуты от 0 до 59
     * @param int $second       Секунды от 0 до 59
     * @param int $microseconds Микросекунды от 0
     *
     * @return Date
     */
    public function setTime ($hour, $minute, $second = 0, $microseconds = 0)
    {
        parent::setTime($hour, $minute, $second);

        return $this;
    }

    /**
     * Изменяет дату текущей метки времени при помощи модификаторов
     *
     * @param string $modify Текстовый модификатор даты
     *
     * @return $this
     */
    public function modify ($modify)
    {
        parent::modify($modify);

        return $this;
    }

    /**
     * Устанавливает начало дня (время 00:00:00) для текущей метки времени
     *
     * @return $this
     */
    public function setStartDay ()
    {
        $this->setTime(0, 0);

        return $this;
    }

    /**
     * Устанавливает конец дня (время 23:59:59) для текущей метки времени
     *
     * @return $this
     */
    public function setEndDay ()
    {
        $this->setTime(23, 59, 59);

        return $this;
    }

    /**
     * Меняет текущую метку времени на завтрашний день
     *
     * @return $this
     */
    public function setNextDay ()
    {
        $this->modify("+1 days");

        return $this;
    }

    /**
     * Меняет текущую метку времени на вчерашний день
     *
     * @return $this
     */
    public function setPrevDay ()
    {
        $this->modify("-1 days");

        return $this;
    }

    /**
     * Меняет текущую метку времени на следующий месяц
     *
     * @return $this
     */
    public function setNextMonth ()
    {
        $this->modify("+1 month");

        return $this;
    }

    /**
     * Меняет текущую метку премени на вредыдущий месяц
     *
     * @return $this
     */
    public function setPrevMonth ()
    {
        $this->modify("-1 month");

        return $this;
    }

    /**
     * Меняет текущую метку времени на следующий год
     *
     * @return $this
     */
    public function setNextYear ()
    {
        $this->modify("+1 year");

        return $this;
    }

    /**
     * Меняет текущую метку времени на предыдущий год
     *
     * @return $this
     */
    public function setPrevYear ()
    {
        $this->modify("-1 year");

        return $this;
    }

    /**
     * Меняет текущую метку времени, устанавливая первый день текущего месяца
     *
     * @return $this
     */
    public function setFirstDayOfMonth ()
    {
        $this->modify('first day of ' . $this->format('F') . ' ' . $this->format('Y'));

        return $this;
    }

    /**
     * Меняет текущую метку времени, устанавливая последний день текущего месяца
     *
     * @return $this
     */
    public function setLastDayOfMonth ()
    {
        $this->modify('last day of ' . $this->format('F') . ' ' . $this->format('Y'));

        return $this;
    }

    /**
     * Меняет текущую метку времени, устанавливая первый день текущего года
     *
     * @return $this
     */
    public function setFirstDayOfYear ()
    {
        $this->setDate($this->format('Y'), 1, 1);

        return $this;
    }

    /**
     * Возвращает текущую или переданную в параметре метку времени в заданном формате
     * Формат задается аналогичный функции date
     *
     * @link  http://php.net/manual/ru/function.date.php
     *
     * @param string $format    Формат возвращаемой даты
     * @param int    $timestamp Секунды с начала эпохи
     *
     * @return string
     */
    public function getDate ($format = "Y-m-d", $timestamp = null)
    {
        if (!is_null($timestamp))
        {
            try
            {
                $tmp = new self();
            }
            catch (SystemException $e)
            {
                return date($format,$timestamp);
            }
            $tmp->setTimestamp($timestamp);

            return $tmp->format($format);
        }

        return $this->format($format);
    }

    /**
     * Возвращает текущую или переданную в параметре метку времени в формате даты сайта
     *
     * @param int $timestamp Секунды с начала эпохи
     *
     * @return string
     */
    public function getDateSite ($timestamp = null)
    {
        $formatSiteDate = Application::getInstance()->getSettings()->getSiteDateFormat();

        return $this->getDate($formatSiteDate, $timestamp);
    }

    /**
     * Возвращает текущую или переданную в параметре метку времени в формате даты и времени сайта
     *
     * @param int $timestamp Секунды с начала эпохи
     *
     * @return string
     */
    public function getDateTimeSite ($timestamp = null)
    {
        $formatDateTimeSite = Application::getInstance()->getSettings()->getSiteDateTimeFormat();

        return $this->getDate($formatDateTimeSite, $timestamp);
    }

    /**
     * Возвращает текущую или переданную в параметре метку времени в формате даты базы данных
     *
     * @param int $timestamp Секунды с начала эпохи
     *
     * @return string
     */
    public function getDateDB ($timestamp = null)
    {
        return $this->getDate('Y-m-d', $timestamp);
    }

    /**
     * Возвращает текущую или переданную в параметре метку времени в формате даты и времени базы данных
     *
     * @param int $timestamp
     *
     * @return string
     */
    public function getDateTimeDB ($timestamp = null)
    {
        return $this->getDate('Y-m-d H:i:s', $timestamp);
    }

    /**
     * Возвращает текущую или переданную в параметре метку времени в формате времени
     *
     * @param int $timestamp
     *
     * @return string
     */
    public function getTime ($timestamp = null)
    {
        return $this->getDate('H:i:s', $timestamp);
    }

    /**
     * Возвращает время в формате сайта для текущей даты или для переданного timestamp
     *
     * @param int $timestamp - метка времени unix
     *
     * @return string
     */
    public function getTimeSite ($timestamp = null)
    {
        $formatTimeSite = Application::getInstance()->getSettings()->getSiteTimeFormat();

        return $this->getDate($formatTimeSite, $timestamp);
    }

    /**
     * Возвращает краткое наименование дня недели
     * ('Вс.', 'Пн.', 'Вт.' и т.д.)
     *
     * @param int|null $day День недели в формате date('w')
     *
     * @return bool|string Краткое наименование дня недели, либо false
     */
    public function getShortNameDayOfWeek ($day = null)
    {
        if (is_null($day))
        {
            $day = $this->format('w');
        }
        IncludeLangFile(__FILE__);

        switch ((int)$day)
        {
            case 0:
                return GetCoreMessage('date_su');
            case 1:
                return GetCoreMessage('date_mo');
            case 2:
                return GetCoreMessage('date_tu');
            case 3:
                return GetCoreMessage('date_we');
            case 4:
                return GetCoreMessage('date_th');
            case 5:
                return GetCoreMessage('date_fr');
            case 6:
                return GetCoreMessage('date_st');
            default:
                return false;
        }
    }

    /**
     * Возвращает полное наименование дня недели
     *
     * ('Воскресенье', 'Понедельник', 'Вторник' и т.д.)
     *
     * @param int $day День недели в формате date('w')
     *
     * @return bool|string Полное наименование дня недели, либо false
     */
    public function getNameDayOfWeek ($day = null)
    {
        if (is_null($day))
        {
            $day = $this->format('w');
        }
        IncludeLangFile(__FILE__);

        switch ((int)$day)
        {
            case 0:
                return GetCoreMessage('date_sunday');
            case 1:
                return GetCoreMessage('date_monday');
            case 2:
                return GetCoreMessage('date_tuesday');
            case 3:
                return GetCoreMessage('date_wednesday');
            case 4:
                return GetCoreMessage('date_thursday');
            case 5:
                return GetCoreMessage('date_friday');
            case 6:
                return GetCoreMessage('date_saturday');
            default:
                return false;
        }
    }

    /**
     * Возвращает наименование месяца
     *
     * ('Январь', 'Февраль', 'Март' и т.д.)
     *
     * @param int $month Месяц в формате date('n')
     *
     * @return bool|string Наименование месяца, либо false
     */
    public function getNameMonth ($month = null)
    {
        if (is_null($month))
        {
            $month = $this->format('n');
        }
        IncludeLangFile(__FILE__);

        switch ((int)$month)
        {
            case 1:
                return GetCoreMessage('date_january');
            case 2:
                return GetCoreMessage('date_february');
            case 3:
                return GetCoreMessage('date_march');
            case 4:
                return GetCoreMessage('date_april');
            case 5:
                return GetCoreMessage('date_may');
            case 6:
                return GetCoreMessage('date_june');
            case 7:
                return GetCoreMessage('date_july');
            case 8:
                return GetCoreMessage('date_august');
            case 9:
                return GetCoreMessage('date_september');
            case 10:
                return GetCoreMessage('date_october');
            case 11:
                return GetCoreMessage('date_november');
            case 12:
                return GetCoreMessage('date_december');
            default:
                return false;
        }
    }

    /**
     * Возвращает наименование месяца в винительном падеже
     *
     * ('января', 'февраля', 'марта' и т.д.)
     *
     * @param int $month Месяц в формате date('n')
     *
     * @return bool|string Наименование месяца, либо false
     */
    public function getNameMonthAccusative ($month = null)
    {
        if (is_null($month))
        {
            $month = $this->format('n');
        }
        IncludeLangFile(__FILE__);

        switch ((int)$month)
        {
            case 1:
                return GetCoreMessage('date_accusative_january');
            case 2:
                return GetCoreMessage('date_accusative_february');
            case 3:
                return GetCoreMessage('date_accusative_march');
            case 4:
                return GetCoreMessage('date_accusative_april');
            case 5:
                return GetCoreMessage('date_accusative_may');
            case 6:
                return GetCoreMessage('date_accusative_june');
            case 7:
                return GetCoreMessage('date_accusative_july');
            case 8:
                return GetCoreMessage('date_accusative_august');
            case 9:
                return GetCoreMessage('date_accusative_september');
            case 10:
                return GetCoreMessage('date_accusative_october');
            case 11:
                return GetCoreMessage('date_accusative_november');
            case 12:
                return GetCoreMessage('date_accusative_december');
            default:
                return false;
        }
    }

    /**
     * Возвращает краткое наименование месяца
     *
     * ('янв', 'фев', 'мар' и т.д.)
     *
     * @param int $month Месяц в формате date('n')
     *
     * @return bool|string Наименование месяца, либо false
     */
    public function getNameMonthShort ($month = null)
    {
        if (is_null($month))
        {
            $month = $this->format('n');
        }
        IncludeLangFile(__FILE__);

        switch ((int)$month)
        {
            case 1:
                return GetCoreMessage('date_short_jan');
            case 2:
                return GetCoreMessage('date_short_feb');
            case 3:
                return GetCoreMessage('date_short_mar');
            case 4:
                return GetCoreMessage('date_short_apr');
            case 5:
                return GetCoreMessage('date_short_may');
            case 6:
                return GetCoreMessage('date_short_jun');
            case 7:
                return GetCoreMessage('date_short_jul');
            case 8:
                return GetCoreMessage('date_short_aug');
            case 9:
                return GetCoreMessage('date_short_sep');
            case 10:
                return GetCoreMessage('date_short_oct');
            case 11:
                return GetCoreMessage('date_short_nov');
            case 12:
                return GetCoreMessage('date_short_dec');
            default:
                return false;
        }
    }

    /**
     * Проверяет, является текущая или переданная в параметре метка времени сегодняшним днем
     *
     * @param Date $date
     *
     * @return bool
     */
    public function isToday ($date = null)
    {
        if (is_null($date))
        {
            $date = $this;
        }

        try
        {
            $now = new self();

            return ($date->format('Y-m-d') == $now->format('Y-m-d'));
        }
        catch (SystemException $e)
        {
            return ($date->format('Y-m-d') == date('Y-m-d'));
        }
    }

    /**
     * Проверяет совпадают ли даты текущей метки времени и переданной в параметре
     *
     * @param Date $date
     *
     * @return bool
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
     */
    public function isWeekEnd ($fromDates = true)
    {
        $bDates = false;
        if (Loader::issetModule('ms.dates'))
        {
            try
            {
                Loader::includeModule('ms.dates');
                $bDates = true;
            }
            catch (SystemException $e)
            {
                //empty
            }
        }

        if ($fromDates && $bDates)
        {
            return WorkCalendar::isWeekEnd($this);
        }
        else
        {
            if ($this->format('w') >= 1 && $this->format('w') <= 5)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
    }

    /**
     * Обертка функции strtotime для текущей метки времени, либо переданной в параметре
     *
     * Параметры в функции идентичны strtotime {@link http://php.net/manual/ru/function.strtotime.php}
     * за исключением метки времени, так как если она не передана, используется метка времени объекта,
     * а не текущее время
     *
     * @param string $time - строковое представление времени
     * @param int    $now  - метка времени
     *
     * @return $this|null
     */
    public function strToTime ($time, $now = null)
    {
        if (!is_null($now))
        {
            try
            {
                $tmp = new self();
                $tmp->setTimestamp($now);
                $tmp->modify($time);

                return $tmp;
            }
            catch (SystemException $e)
            {
                return null;
            }
        }

        $this->modify($time);

        return $this;
    }

    /**
     * Возвращает строковое представление объекта в формате даты/времени сайта
     *
     * @return string
     */
    public function __toString ()
    {
        return $this->getDateTimeSite();
    }
}
