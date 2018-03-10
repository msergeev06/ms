<?php
/**
 * Ms\Core\Entity\Db\Query\QueryDrop
 * Сущность DROP запроса к базе данных
 *
 * @package MSergeev\Core
 * @subpackage Entity\Db\Query
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity\Db\Query;

use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Lib\DataManager;

class QueryDrop extends QueryBase
{
	/**
	 * Конструктор
	 *
	 * @param string $tableClass Имя класса таблицы
	 * @since 0.2.0
	 */
	public function __construct ($tableClass)
	{
		$this->setType('drop');
		/** @var DataManager $tableClass */
		$helper = new SqlHelper($tableClass::getTableName());

		$sql = "DROP TABLE IF EXISTS ".$helper->wrapTableQuotes();

		$this->setSql($sql);
	}
}