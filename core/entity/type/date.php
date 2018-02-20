<?php
/**
 * MSergeev\Core\Entity\Type\Date
 * Класс, описывающий тип переменной "Дата и время"
 * Используетя во всех операциях с датой и/или временем
 *
 * @package MSergeev\Core
 * @subpackage Entity\Type
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 * @since 0.2.0
 */

namespace MSergeev\Core\Entity\Type;

use MSergeev\Core\Exception;
use MSergeev\Core\Lib\Loader;
use MSergeev\Core\Lib\Loc;
use MSergeev\Modules\Dates\Lib\WorkCalendar;

class Date extends \DateTime
{
	/**
	 * Текущая метка времени UNIX
	 * @var int
	 */
	protected $timestamp = NULL;

	/**
	 * Дата и время в формате базы данных (YYYY-MM-DD HH:II;SS)
	 * @var string
	 */
	protected $dateTimeDB = null;

	/**
	 * Конструктор объекта даты
	 *
	 * Основные форматы
	 * db               YYYY-MM-DD
	 * site             DD.MM.YYYY
	 * db_datetime      YYYY-MM-DD HH:II:SS
	 * site_datetime    DD.MM.YYYY HH:II:SS
	 * site_time        HH:II:SS
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
		if (is_null ($date))
		{
			$this->timestamp = time ();
			$this->dateTimeDB = $this->getDateTimeDB();

			return $this;
		}

		if (is_null ($format))
		{
			$this->timestamp = $this->createFromFormatEx ('U', $date, $timezone);;
			$this->dateTimeDB = $this->getDateTimeDB();

			return $this;
		}

		switch ($format)
		{
			case 'db':
				$this->timestamp = $this->createFromFormatEx ('Y-m-d', $date, $timezone);
				$this->dateTimeDB = $this->getDateTimeDB();
				return $this;
			case 'site':
				$this->timestamp = $this->createFromFormatEx ('d.m.Y', $date, $timezone);
				$this->dateTimeDB = $this->getDateTimeDB();
				return $this;
			case 'db_datetime':
				$this->timestamp = $this->createFromFormatEx ('Y-m-d H:i:s', $date, $timezone);
				$this->dateTimeDB = $this->getDateTimeDB();
				return $this;
			case 'site_datetime':
				$this->timestamp = $this->createFromFormatEx ('d.m.Y H:i:s', $date, $timezone);
				$this->dateTimeDB = $this->getDateTimeDB();
				return $this;
			case 'site_time':
				$this->timestamp = $this->createFromFormatEx ('H:i:s', $date, $timezone);
				$this->dateTimeDB = $this->getDateTimeDB();
				return $this;
			default:
				$this->timestamp = $this->createFromFormatEx ($format, $date, $timezone);
				$this->dateTimeDB = $this->getDateTimeDB();
				return $this;
		}

	}

	/**
	 * Разбирает строку, содержащую время, в соответствии с заданным форматом
	 *
	 * Алиас функции \DateTime::createFromFormat
	 * @link http://php.net/manual/ru/datetime.createfromformat.php
	 *
	 * @param string        $format
	 * @param string        $time
	 * @param \DateTimeZone $timezone
	 *
	 * @return int
	 * @since 0.2.0
	 */
	public function createFromFormatEx ($format, $time, \DateTimeZone $timezone = NULL)
	{
		if (is_null ($timezone))
		{
			$date = \DateTime::createFromFormat ($format, $time);
		} else
		{
			$date = \DateTime::createFromFormat ($format, $time, $timezone);
		}

		if ($date)
		{
			return $date->getTimestamp();
		}
		else
		{
			//msDebugNoAdmin($format);
			//msDebugNoAdmin($time);
			//msDebugNoAdmin($date);
			//msDebugNoAdmin(debug_backtrace());
			return false;
		}
	}

	/**
	 * Устанавливает произвольную дату из параметров
	 *
	 * Алиас функции \DateTime::setDate
	 * @link  http://php.net/manual/ru/datetime.setdate.php
	 *
	 * @param int $year  - год
	 * @param int $month - месяц
	 * @param int $day   - день
	 *
	 * @return void
	 * @since 0.2.0
	 */
	public function setDate ($year, $month, $day)
	{
		$date = parent::setDate(intval($year),intval($month),intval($day));
		$this->timestamp = $date->getTimestamp();
	}

	/**
	 * Устанавливает произвольное время из параметров
	 *
	 * Алиас функции \DateTime::setTime
	 * @link http://php.net/manual/ru/datetime.setdate.php
	 *
	 * @param int $hour - часы
	 * @param int $minute - минуты
	 * @param int $second - секунды
	 *
	 * @return void
	 * @since 0.2.0
	 */
	public function setTime ($hour, $minute, $second=0)
	{
		$time = parent::setTime(intval($hour),intval($minute),intval($second));
		$this->timestamp = $time->getTimestamp();
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
	 * @param Date  $date - дата, из которой берутся недостающие параметры в массиве
	 * @since 0.2.0
	 */
	public function setDateFromArray ($arDate, Date $date = NULL)
	{
		if (is_null ($date))
		{
			$date = $this;
		}

		if (!isset($arDate['DAY']) || is_null ($arDate['DAY']))
		{
			$day = $date->getDate ('j');
		} else
		{
			$day = $arDate['DAY'];
		}

		if (!isset($arDate['MONTH']) || is_null ($arDate['MONTH']))
		{
			$month = $date->getDate ('n');
		} else
		{
			$month = $arDate['MONTH'];
		}

		if (!isset($arDate['YEAR']) || is_null ($arDate['YEAR']))
		{
			$year = $date->getDate ('Y');
		} else
		{
			$year = $arDate['YEAR'];
		}

		if (!isset($arDate['HOUR']) || is_null ($arDate['HOUR']))
		{
			$hour = $date->getDate ('G');
		} else
		{
			$hour = $arDate['HOUR'];
		}

		if (!isset($arDate['MIN']) || is_null ($arDate['MIN']))
		{
			$min = intval ($date->getDate ('i'));
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

		$this->timestamp = mktime (
			$hour,
			$min,
			$sec,
			$month,
			$day,
			$year
		);
	}

	/**
	 * Устанавливает начало дня (время 00:00:00) для текущей метки времени
	 * @since 0.2.0
	 */
	public function setStartDay()
	{
		$this->setTime(0,0);
	}

	/**
	 * Устанавливает конец дня (время 23:59:59) для текущей метки времени
	 * @since 0.2.0
	 */
	public function setEndDay()
	{
		$this->setTime(23,59,59);
	}

	/**
	 * Возвращает текущую метку времени
	 *
	 * @return int
	 * @since 0.2.0
	 */
	public function getTimestamp ()
	{
		return $this->timestamp;
	}

	/**
	 * Устанавливает метку времени
	 *
	 * @param int $timestamp
	 *
	 * @return void
	 * @since 0.2.0
	 */
	public function setTimestamp ($timestamp=null)
	{
		if (!is_null($timestamp))
		{
			$this->timestamp = $timestamp;
			$this->dateTimeDB = $this->getDateTimeDB();
		}
	}

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
		if (is_null ($timestamp))
		{
			$timestamp = $this->getTimestamp ();
		}

		return date ($format, $timestamp);
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
		if (is_null ($timestamp))
		{
			$timestamp = $this->getTimestamp ();
		}

		return date ("d.m.Y", $timestamp);
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
		if (is_null ($timestamp))
		{
			$timestamp = $this->getTimestamp ();
		}

		return date ("d.m.Y H:i:s", $timestamp);
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
		if (is_null ($timestamp))
		{
			$timestamp = $this->getTimestamp ();
		}

		return date ("Y-m-d", $timestamp);
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
		if (is_null ($timestamp))
		{
			$timestamp = $this->getTimestamp ();
		}

		return date ("Y-m-d H:i:s", $timestamp);
	}

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

		$date->setTime(0,0);
		$checkDate = new Date();
		$checkDate->setTime(0,0);

		return ($date->timestamp == $checkDate->timestamp);
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
		$date->setTime(0,0);
		$tmp = $this;
		$tmp->setTime(0,0);

		return ($date->getTimestamp() == $tmp->getTimestamp());
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
		if (is_null ($timestamp))
		{
			$timestamp = $this->getTimestamp ();
		}

		return date ("H:i:s", $timestamp);
	}

	/**
	 * Меняет текущую метку времени на завтрашний день
	 *
	 * @return $this
	 * @since 0.2.0
	 */
	public function nextDay ()
	{
		$this->setTimestamp(strtotime ("+1 days", $this->getTimestamp ()));
		return $this;
	}

	/**
	 * Меняет текущую метку времени на вчерашний день
	 *
	 * @return $this
	 * @since 0.2.0
	 */
	public function prevDay ()
	{
		$this->setTimestamp(strtotime ("-1 days", $this->getTimestamp ()));
		return $this;
	}

	/**
	 * Меняет текущую метку времени на следующий месяц
	 *
	 * @return $this
	 * @since 0.2.0
	 */
	public function nextMonth()
	{
		$this->setTimestamp(strtotime('+1 month', $this->getTimestamp()));
		return $this;
	}

	/**
	 * Меняет текущую метку премени на вредыдущий месяц
	 *
	 * @return $this
	 * @since 0.2.0
	 */
	public function prevMonth()
	{
		$this->setTimestamp(strtotime('-1 month', $this->getTimestamp()));
		return $this;
	}

	/**
	 * Меняет текущую метку времени на следующий год
	 *
	 * @return $this
	 * @since 0.2.0
	 */
	public function nextYear()
	{
		$this->setTimestamp(strtotime('+1 year', $this->getTimestamp()));
		return $this;
	}

	/**
	 * Меняет текущую метку времени на предыдущий год
	 *
	 * @return $this
	 * @since 0.2.0
	 */
	public function prevYear()
	{
		$this->setTimestamp(strtotime('-1 year', $this->getTimestamp()));
		return $this;
	}

	/**
	 * Меняет текущую метку времени, устанавливая первый день текущего месяца
	 * @since 0.2.0
	 */
	public function setFirstDayOfMonth()
	{
		$phrase = 'first day of '.$this->getDate('F').' '.$this->getDate('Y');
		$this->setTimestamp(strtotime($phrase));
	}

	/**
	 * Меняет текущую метку времени, устанавливая последний день текущего месяца
	 * @since 0.2.0
	 */
	public function setLastDayOfMonth()
	{
		$phrase = 'last day of '.$this->getDate('F').' '.$this->getDate('Y');
		$this->setTimestamp(strtotime($phrase));
	}

	/**
	 * Меняет текущую метку времени, устанавливая первый день текущего года
	 * @since 0.2.0
	 */
	public function setFirstDayOfYear()
	{
		$dateTime = $this->getDateTimeDB();
		list($date,$time) = explode(' ',$dateTime);
		list($year,,) = explode('-',$date);
		$day = $month = '01';
		$strDate = $year.'-'.$month.'-'.$day.' '.$time;
		$this->setTimestamp($this->createFromFormatEx('Y-m-d H:i:s',$strDate));
	}

	/**
	 * Возвращает true, если сегодняшний день выходной
	 *
	 * Если параметр отсутствует или равен true, а также если модуль dates установлен, проверка осуществляется с
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
		if ($fromDates && Loader::issetModule('dates') && Loader::includeModule('dates'))
		{
			return WorkCalendar::isWeekEnd($this);
		}
		else
		{
			if ($this->getDate('w')>=1 && $this->getDate('w')<=5)
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
	 * @param int $now - метка времени
	 *
	 * @return $this
	 * @since 0.2.0
	 */
	public function strToTime ($time, $now = NULL)
	{
		if (is_null ($now))
		{
			$now = $this->getTimestamp ();
		}
		$this->setTimestamp(strtotime ($time, $now));

		return $this;
	}

	/**
	 * Возвращает строковое представление объекта в формате даты сайта
	 *
	 * @return string
	 * @since 0.2.0
	 */
	public function __toString ()
	{
		return $this->getDateSite ();
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
			$day = intval($this->getDate('w'));
		}
		Loc::includeLocFile(__FILE__,'ms_core_');

		switch (intval($day))
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
			$day = intval($this->getDate('w'));
		}
		Loc::includeLocFile(__FILE__,'ms_core_');

		switch (intval($day))
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
			$month = intval($this->getDate('n'));
		}
		Loc::includeLocFile(__FILE__,'ms_core_');

		switch ($month)
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
			$month = intval($this->getDate('n'));
		}
		Loc::includeLocFile(__FILE__,'ms_core_');

		switch ($month)
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
			$month = intval($this->getDate('n'));
		}
		Loc::includeLocFile(__FILE__,'ms_core_');

		switch ($month)
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
		if (is_null ($timestamp))
		{
			$timestamp = time ();
		}

		return date ($format, $timestamp);
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
		if (is_null ($timestamp))
		{
			$timestamp = time ();
		}

		return date ("Y-m-d", $timestamp);
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
			&& (intval ($arData[0]) >= 1970 && intval ($arData[0]) <= 2037)
			//TODO: Подправить код после решения вопроса с ошибкой 2038 года
		)
		{
			return TRUE;
		} else
		{
			return FALSE;
		}
	}
}
