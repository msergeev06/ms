<?php
/**
 * MSergeev\Core\Entity\Db\Query\QueryDrop
 * Сущность DROP запроса к базе данных
 *
 * @package MSergeev\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2018 Mikhail Sergeev
 * @since 0.2.0
 */

namespace MSergeev\Core\Entity\Db\Query;

use MSergeev\Core\Lib\DataManager;

class QueryDrop extends QueryBase
{
	/**
	 * Конструктор
	 *
	 * @param null|string $tableClass
	 * @since 0.2.0
	 */
	public function __construct ($tableClass)
	{
		/** @var DataManager $tableClass */
		$this->setTableName($tableClass->getTableName());
	}
}