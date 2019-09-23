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
	 * @param string $tableClass         Имя класса таблицы
	 * @param bool   $bIgnoreForeignKeys Флаг, означающий необходимость игнорировать ограничения внешнего ключа
	 */
	public function __construct ($tableClass, $bIgnoreForeignKeys=false)
	{
		$this->setType('drop');
		/** @var DataManager $tableClass */
		$helper = new SqlHelper($tableClass::getTableName());

		$sql = '';
		if ($bIgnoreForeignKeys) $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
		$sql .= "DROP TABLE IF EXISTS ".$helper->wrapTableQuotes().';';
		if ($bIgnoreForeignKeys) $sql .= "\nSET FOREIGN_KEY_CHECKS=1;";

		$this->setSql($sql);
	}
}