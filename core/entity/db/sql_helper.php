<?php
/**
 * Ms\Core\Entity\Db\SqlHelper
 * Помощник обработки SQL запросов
 *
 * @package Ms\Core
 * @subpackage Entity\Db
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 * @since 0.1.0
 */

namespace Ms\Core\Entity\Db;

/**
 * Class SqlHelper
 * @package Ms\Core
 * @subpackage Entity\Db
 *
 * @var string $tableName Имя таблицы
 */
class SqlHelper
{
	/**
	 * Константа, содержащая кавычку, используемую в SQL запросах
	 */
	const QUOTES = '`';

	/**
	 * Имя таблицы
	 * @var string
	 */
	protected $tableName = '';

	/**
	 * Объект SQL помощника по датам
	 * @var SqlHelperDate
	 */
	//protected $sqlHelperDate = null;

	/**
	 * Объект SQL помощника по математическим функциям
	 * @var SqlHelperMath
	 */
	//protected $sqlHelperMath = null;

	/**
	 * Объект SQL помощника по строкам
	 * @var SqlHelperStr
	 */
	//protected $sqlHelperStr = null;

	/**
	 * Создает объект SQL помощника
	 *
	 * @param string $tableName Имя таблицы
	 * @since 0.1.0
	 */
	public function __construct ($tableName='')
	{
		$this->tableName = $tableName;
	}


	/**
	 * Возвращает объект класса SqlHelperDate, для получения доступа к его функциям
	 *
	 * @api
	 *
	 * @return SqlHelperDate
	 * @since 0.1.0
	 */
/*	public function helperDate ()
	{
		if (is_null($this->sqlHelperDate))
		{
			$this->sqlHelperDate = new SqlHelperDate($this->tableName);
		}

		return $this->sqlHelperDate;
	}*/

	/**
	 * Возвращает объект класса SqlHelperMath, для получения доступа к его функциям
	 *
	 * @api
	 *
	 * @return SqlHelperMath
	 * @since 0.1.0
	 */
/*	public function helperMath ()
	{
		if (is_null($this->sqlHelperMath))
		{
			$this->sqlHelperMath = new SqlHelperMath($this->tableName);
		}

		return $this->sqlHelperMath;
	}*/

	/**
	 * Возвращает объект класса SqlHelperStr, для получения доступа к его функциям
	 *
	 * @api
	 *
	 * @return SqlHelperStr
	 * @since 0.1.0
	 */
/*	public function helperStr()
	{
		if (is_null($this->sqlHelperStr))
		{
			$this->sqlHelperStr = new SqlHelperStr($this->tableName);
		}

		return $this->sqlHelperStr;
	}*/

	/**
	 * Возвращает оборнутое кавычками значение str
	 *
	 * @api
	 *
	 * @param string $str Строка, которую нужно обернуть кавычками
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function wrapQuotes ($str)
	{
		return self::QUOTES.$str.self::QUOTES;
	}

	/**
	 * Возвращает поле обернутое кавычками.
	 * Если имя таблицы не пустое, имя таблицы оборачивается кавычками и добавляется к полю.
	 *
	 * @api
	 *
	 * @param string $field Поле, которое необходимо обернуть кавычками
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function wrapFieldQuotes ($field)
	{
		$return = '';
		if ($this->tableName!='')
		{
			$return .= self::QUOTES.$this->tableName.self::QUOTES.'.';
		}
		$return .= self::QUOTES.$field.self::QUOTES;

		return $return;
	}

	/**
	 * Возвращает обернутое кавычками значение tableName переданное в параметре,
	 * либо взятое из свойства объекта
	 *
	 * @api
	 *
	 * @param string $tableName Имя таблицы
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function wrapTableQuotes ($tableName='')
	{
		if ($tableName=='')
		{
			if ($this->tableName!='')
			{
				$tableName = $this->tableName;
			}
			else
			{
				return '';
			}
		}

		return self::QUOTES.$tableName.self::QUOTES;
	}

	/**
	 * Функция возвращает одиночную кавычку
	 *
	 * @api
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function getQuote ()
	{
		return self::QUOTES;
	}

	/**
	 * Возвращает строковое представление SQL функции COUNT
	 *
	 * @api
	 *
	 * @param string $params    Поле или *
	 * @param null   $newColumn Алиас значения
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function getCountFunction ($params="*", $newColumn=null)
	{
		if (is_null($newColumn))
		{
			if ($params=="*")
			{
				$newColumn = 'COUNT';
			}
			else
			{
				$newColumn = 'COUNT_'.$params;
			}
		}
		if ($params != '*' && $this->tableName!='')
		{
			$params = $this->wrapFieldQuotes($params);
		}

		return 'COUNT('.$params.') '.$this->wrapQuotes($newColumn);
	}

	/**
	 * Возвращает строковое представление SQL функции MAX
	 *
	 * @api
	 *
	 * @param string $column    Имя поля
	 * @param string $newColumn Алиас значения
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function getMaxFunction ($column="", $newColumn=null)
	{
		if ($column=="") return "";

		if (is_null($newColumn))
		{
			$newColumn = 'MAX_'.$column;
		}
		$column = $this->wrapFieldQuotes($column);

		return 'MAX('.$column.') '.$this->wrapQuotes($newColumn);
	}

	/**
	 * Возвращает строковое представление SQL функции MIN
	 *
	 * @api
	 *
	 * @param string $column    Имя поля
	 * @param string $newColumn Алиас значения
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function getMinFunction ($column="", $newColumn=null)
	{
		if ($column=="") return "";

		if (is_null($newColumn))
		{
			$newColumn = 'MIN_'.$column;
		}
		$column = $this->wrapFieldQuotes($column);

		return 'MIN('.$column.') '.$this->wrapQuotes($newColumn);
	}

	/**
	 * Возвращает строковое представление SQL функции SUM
	 *
	 * @api
	 *
	 * @param string $column    Имя поля
	 * @param string $newColumn Алиас значения
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function getSumFunction ($column="", $newColumn=null)
	{
		if ($column=="") return "";

		if (is_null($newColumn))
		{
			$newColumn = 'SUM_'.$column;
		}
		$column = $this->wrapFieldQuotes($column);

		return 'SUM('.$column.') '.$this->wrapQuotes($newColumn);
	}

	/**
	 * Возвращает строковое представление SQL функции AVG
	 *
	 * @param string $column    Имя поля
	 * @param null   $newColumn Алиас значения
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function getAvgFunction ($column="", $newColumn=null)
	{
		if ($column=="") return "";

		if (is_null($newColumn))
		{
			$newColumn = 'AVG_'.$column;
		}
		$column = $this->wrapFieldQuotes($column);

		return 'AVG('.$column.') '.$this->wrapQuotes($newColumn);
	}
}