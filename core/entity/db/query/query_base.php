<?php
/**
 * MSergeev\Core\Entity\Db\Query\QueryBase
 * Базовая сущность запроса к базе данных
 *
 * @package MSergeev\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace MSergeev\Core\Entity\Db\Query;

use MSergeev\Core\Entity\Db\DBResult;
use MSergeev\Core\Exception;
use MSergeev\Core\Entity\Application;
use MSergeev\Core\Entity\Db\Fields;

class QueryBase
{
	/**
	 * @var array
	 */
	protected $arFieldsEntity = null;

	/**
	 * @var string
	 */
	private $type = null;

	/**
	 * @var string
	 */
	private $sql = null;

	/**
	 * @var array
	 */
	private $tableMapArray = null;

	/**
	 * @var string
	 */
	private $tableName = null;

	/**
	 * Конструктор
	 *
	 * @param string $sql
	 * @since 0.2.0
	 */
	public function __construct ($sql = null)
	{
		if (!is_null($sql))
		{
			$this->setSql($sql);
		}
		$this->setType('sql');
	}

	/**
	 * Выполняет запрос к базе данных
	 *
	 * @param bool $debug
	 *
	 * @return DBResult
	 * @since 0.2.0
	 */
	public function exec ($debug=false)
	{
		try
		{
			if (is_null($this->getSql()))
			{
				throw new Exception\Db\SqlQueryException('No SQL Query','','');
			}
		}
		catch (Exception\Db\SqlQueryException $e)
		{
			die($e->showException());
		}
		//msEchoVar($this->getQueryBuildParts());
		//TODO: Доделать остановку exec с использованием константы
		if ($debug || (defined("NO_QUERY_EXEC") && NO_QUERY_EXEC===true))
		{
			return $this->getSql();
		}
/*		elseif ($this->getSql()===false && $this->getType() == "delete")
		{
			return $this->massDeleteResult;
		}*/
		else
		{
			$DB = Application::getInstance()->getConnection();
			try
			{
				$res = $DB->query ($this);
				if (!$res->getResult ())
				{
					throw new Exception\Db\SqlQueryException(
						"Error ".$this->getType ()." query",
						$res->getResultErrorText (),
						$this->getSql()
					);
				}
				return $res;
			}
			catch (Exception\Db\SqlQueryException $e)
			{
				$e->showException();
			}
		}

		return new DBResult();
	}

	/**
	 * Устанавливает значение SQL запроса
	 *
	 * @param $sql
	 * @since 0.2.0
	 */
	public function setSql ($sql)
	{
		$this->sql = $sql;
	}

	/**
	 * Возвращает текст SQL запроса
	 *
	 * @return string
	 * @since 0.2.0
	 */
	public function getSql ()
	{
		return $this->sql;
	}

	/**
	 * Возвращает тип объекта
	 *
	 * @return string
	 * @since 0.2.0
	 */
	public function getType ()
	{
		return $this->type;
	}

	/**
	 * Возвращает массив, описывающий поля таблицы
	 *
	 * @return null|Fields\ScalarField[]
	 * @since 0.2.0
	 */
	public function getTableMap ()
	{
		return $this->tableMapArray;
	}

	/**
	 * Возвращает сущности полей таблицы
	 *
	 * @return array
	 * @since 0.2.0
	 */
	public function getFieldsEntity ()
	{
		return $this->arFieldsEntity;
	}

	/**
	 * Собирает SQL запрос из параметров
	 * @since 0.2.0
	 */
	protected function buildQuery ()
	{
		try
		{
			throw new Exception\NotImplementedException('QueryBase::buildQuery()');
		}
		catch (Exception\NotImplementedException $e)
		{
			die($e->showException());
		}
	}

	/**
	 * Устанавливает массив, описывающий поля таблицы
	 *
	 * @param null $arTableMapArray
	 *
	 * @throws Exception\ArgumentTypeException
	 * @since 0.2.0
	 */
	protected function setTableMap ($arTableMapArray=null)
	{
		try
		{
			if (is_null($arTableMapArray) || (is_array($arTableMapArray) && empty($arTableMapArray)))
			{
				throw new Exception\ArgumentNullException('$arTableMapArray');
			}
			elseif (!is_array($arTableMapArray))
			{
				throw new Exception\ArgumentTypeException('$arTableMapArray','array');
			}
			else
			{
				$this->tableMapArray = $arTableMapArray;
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			die($e->showException());
		}
	}

	/**
	 * Устанавливает сущности полей таблицы
	 *
	 * @param $arFieldsEntity
	 *
	 * @throws Exception\ArgumentTypeException
	 * @since 0.2.0
	 */
	protected function setFieldsEntity ($arFieldsEntity)
	{
		try
		{
			if (is_null($arFieldsEntity) || (is_array($arFieldsEntity) && empty($arFieldsEntity)))
			{
				throw new Exception\ArgumentNullException('$arFieldsEntity');
			}
			elseif (!is_array($arFieldsEntity))
			{
				throw new Exception\ArgumentTypeException('$arFieldsEntity','array');
			}
			else
			{
				$this->arFieldsEntity = $arFieldsEntity;
			}
		}
		catch (Exception\ArgumentNullException $e)
		{
			die($e->showException());
		}
	}

	/**
	 * Устанавливает тип объекта запроса
	 *
	 * @param $type
	 *
	 * @since 0.2.0
	 */
	protected function setType ($type)
	{
		try
		{
			if (!in_array(strtolower($type),array('select','create','insert','update','delete','drop','sql')))
			{
				throw new Exception\ArgumentOutOfRangeException(strtolower($type),array('select','create','insert','update','delete','drop','sql'));
			}
			else
			{
				$this->type = strtolower($type);
			}
		}
		catch (Exception\ArgumentOutOfRangeException $e)
		{
			die($e->showException());
		}
	}

	/**
	 * Устанавливает имя таблицы
	 *
	 * @param $tableName
	 * @since 0.2.0
	 */
	protected function setTableName ($tableName)
	{
		$this->tableName = $tableName;
	}

	/**
	 * Возвращает имя таблицы
	 *
	 * @return string
	 * @since 0.2.0
	 */
	protected function getTableName ()
	{
		return $this->tableName;
	}
}