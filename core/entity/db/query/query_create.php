<?php
/**
 * Ms\Core\Entity\Db\Query\QueryCreate
 * Сущность CREATE запроса к базе данных
 *
 * @package Ms\Core
 * @subpackage Entity\Db\Query
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity\Db\Query;

use Ms\Core\Entity\Db\Fields;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Lib\DataManager;
use Ms\Core\Exception;

class QueryCreate extends QueryBase
{
	private $tableTitle = null;
	private $arLinked = array();

	/**
	 * Создает SQL запрос типа "create"
	 *
	 * @param string $tableClass Класс таблицы
	 * @throws
	 * @since 0.2.0
	 */
	public function __construct ($tableClass)
	{
		$this->setType('create');
		/** @var DataManager $tableClass */
		$this->setTableMap($tableClass::getMapArray());
		$this->setTableName($tableClass::getTableName());
		$this->tableTitle = $tableClass::getTableTitle();

		$arMap = $this->getTableMap();
		$tableName = $this->getTableName();

		$helper = new SqlHelper($tableName);

		$primaryField=null;
		$sql = "CREATE TABLE IF NOT EXISTS ".$tableName." (\n\t";
		$bFirst = true;
		$bAutoIncrement = false;
		/**
		 * @var string $fields
		 * @var Fields\ScalarField $objData
		 */
		foreach ($arMap as $fields=>$objData)
		{
			//var_dump ($objData);
			if ($bFirst)
			{
				$bFirst = false;
			}
			else
			{
				$sql .= ",\n\t";
			}
			//Если поле имеет флаг PRIMARY, сохраняем его
			if ($objData->isPrimary())
			{
				$primaryField = $objData->getColumnName();
			}
			//Получаем название поля базы данных
			$field = $objData->getColumnName();
			//Добавляем название поля и его тип
			$sql .= $helper->wrapQuotes($field)." ".$objData->getDataType();
			//Для полей типа int и varchar устанавливаем размер значения
			switch ($objData->getDataType())
			{
				case "int":
				case "varchar":
					/** @var Fields\IntegerField $objData */
					$sql .= "(".$objData->getSize().") ";
					break;
				default:
					$sql .= " ";
					break;
			}
			//Обработка значения по-умолчанию
			$sql .= $this->getDefaultValue($objData);
			//Обработка автоинкремента
			if ($objData->isAutocomplete())
			{
				$sql .= "AUTO_INCREMENT ";
				$bAutoIncrement = true;
			}
			//Обработка описания поля таблицы
			if (!is_null($objData->getTitle()))
			{
				$sql .= "COMMENT '".$objData->getTitle()."'";
			}
			//Обработка линкованных полей
			if (!is_null($objData->getLink()))
			{
				$this->arLinked[] = $objData;
			}
		}
		//Указываем PRIMARY ключ, если существует
		if (!is_null($primaryField))
		{
			$sql .= ",\n\tPRIMARY KEY (".$helper->wrapQuotes($primaryField).")";
		}
		//Если существуют связи таблиц, указываем их
		if (!empty($this->arLinked))
		{
			$sql .= $this->getLinkedFields();
		}
		$sql .= "\n\t) ENGINE=InnoDB CHARACTER SET=utf8 COMMENT=\"".$this->tableTitle."\" ";
		if ($bAutoIncrement)
		{
			$sql .= "AUTO_INCREMENT=1 ";
		}
		$sql .= ";";

		$this->setSql($sql);
	}

	/**
	 * Возвращает значение по-умолчанию для CREATE запроса
	 *
	 * @param Fields\ScalarField $objData
	 *
	 * @return string
	 * @since 0.2.0
	 */
	private function getDefaultValue (Fields\ScalarField $objData)
	{
		$sql = '';
		$isNotNull = ($objData->isPrimary() || $objData->isRequired());

		if (!is_null($objData->getDefaultValue('create')) && $isNotNull)
		{
			$sql .= "NOT NULL ";
			if ($objData->isDefaultSql('create'))
			{
				$sql .= "DEFAULT ".$objData->getDefaultValue('create')." ";
			}
			else
			{
				/** @var Fields\ScalarField $class */
				$class = $objData->getClassName();
				$value = $class::saveDataModification($objData->getDefaultValue('create'),$objData);
				$sql .= "DEFAULT ".$objData->getSqlValue($value)." ";
			}
		}
		//Если значение обязательное
		elseif ($isNotNull)
		{
			$sql .= "NOT NULL ";
		}
		//Если значение не обязательное
		elseif (!$isNotNull)
		{
			$sql .= "DEFAULT NULL ";
		}

		return $sql;
	}

	/**
	 * Возвращает часть SQL запроса, касающийся FOREIGN KEY
	 *
	 * @return string
	 * @since 0.2.0
	 */
	private function getLinkedFields ()
	{
		$sql = '';
		$helper = new SqlHelper();
		if (!empty($this->arLinked))
		{
			/** @var Fields\ScalarField $objLinked */
			foreach ($this->arLinked as $objLinked)
			{
				if (!is_null($link = $objLinked->getLink()))
				{
					list($table,$field) = explode('.',$link);
					$sql .= ",\n\tFOREIGN KEY (".$helper->wrapQuotes($objLinked->getColumnName()).")"
						." REFERENCES ".$helper->wrapQuotes($table)."(".$helper->wrapQuotes($field).")\n\t\t";
					$sql .= "ON UPDATE ".$objLinked->getLinkOnUpdate()."\n\t\t";
					$sql .= "ON DELETE ".$objLinked->getLinkOnDelete();
				}
			}
		}

		return $sql;
	}
}