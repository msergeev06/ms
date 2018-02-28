<?php
/**
 * Ms\Core\Lib\Sections
 * Обработка дерева каталогов
 *
 * @package Ms\Core
 * @subpackage Lib
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 * @since 0.1.0
 */
//www.getinfo.ru/article610.html
namespace Ms\Core\Lib;

use Ms\Core\Entity\Db\Query;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\Db\DBResult;

abstract class Sections
{
	protected static $selectFields = array('ID','ACTIVE','SORT','NAME','LEFT_MARGIN','RIGHT_MARGIN','DEPTH_LEVEL','PARENT_SECTION_ID');
	protected static $tableClassName = null;

	/**
	 * Возвращает имя таблицы
	 *
	 * @api
	 *
	 * @return string
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_get_table_name
	 */
	abstract public static function getTableName();

	/**
	 * Возвращает название класса таблицы по имени таблицы
	 *
	 * @api
	 *
	 * @use Tools::getClassNameByTableName('table')
	 *
	 * @return string
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_get_class_name
	 */
	public static function getClassName()
	{
		if (is_null(static::$tableClassName))
		{
			static::$tableClassName = Tools::getClassNameByTableName(static::getTableName());
		}

		return static::$tableClassName;
	}

	/**
	 * Возвращает значение параметра класса, содержащую массив полей таблицы
	 *
	 * @api
	 *
	 * @return array
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_get_select_fields
	 */
	public static function getSelectFields ()
	{
		return static::$selectFields;
	}

	/**
	 * Возвращает массив дерева каталогов, либо FALSE
	 *
	 * @api
	 *
	 * @param bool $bActive - выбрать только активные разделы
	 *
	 * @return bool|array
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_get_tree_list
	 */
	final public static function getTreeList ($bActive=false)
	{
		/** @var DataManager $className */
		$className = static::getClassName();
		$arGetList = array(
			'order' => array('LEFT_MARGIN' => 'ASC')
		);
		if ($bActive)
		{
			$arGetList['filter'] = array('ACTIVE'=>true);
		}

		if ($arResult = $className::getList($arGetList))
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает массив параметров раздела по его ID, либо FALSE
	 *
	 * @api
	 *
	 * @param int $ID - ID раздела
	 *
	 * @return bool|array
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_get_info
	 */
	final public static function getInfo ($ID)
	{
		/** @var DataManager $className */
		$className = static::getClassName();
		$arResult = $className::getOne(
			array(
				'select' => static::getSelectFields(),
				'filter' => array('ID'=>intval($ID))
			)
		);
		if ($arResult)
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает массив дочерних разделов указанного раздела, либо FALSE
	 *
	 * @api
	 *
	 * @param int  $ID          ID раздела
	 * @param int  $DEPTH_LEVEL уровень вложенности
	 * @param bool $bActive     вывести только активные разделы
	 *
	 * @return bool|array
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_get_child
	 */
	final public static function getChild ($ID, $bActive=false, $DEPTH_LEVEL=0)
	{
		/** @var DataManager $className */
		$className = static::getClassName();
		$arSection = static::getInfo($ID);
		$arGetList = array(
			'select' => static::getSelectFields(),
			'filter' => array(
				'>=LEFT_MARGIN'=>$arSection['LEFT_MARGIN'],
				'<=RIGHT_MARGIN'=>$arSection['RIGHT_MARGIN']
			),
			'order' => array('LEFT_MARGIN'=>'ASC')
		);
		if ($bActive)
		{
			$arGetList['filter'] = array_merge($arGetList['filter'], array('ACTIVE'=>true));
		}
		if (intval($DEPTH_LEVEL)>0)
		{
			$arGetList['filter'] = array_merge($arGetList['filter'],array('DEPTH_LEVEL'=>intval($DEPTH_LEVEL)));
		}
		if ($arResult = $className::getList($arGetList))
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает массив родителей раздела, либо FALSE
	 *
	 * @api
	 *
	 * @param int  $ID          ID раздела
	 * @param bool $bActive     вернуть только активные разделы
	 *
	 * @return bool|array
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_get_parent
	 */
	final public static function getParent ($ID, $bActive=false)
	{
		/** @var DataManager $className */
		$className = static::getClassName();
		$arSection = static::getInfo($ID);
		$arGetList = array(
			'select' => static::getSelectFields(),
			'filter' => array(
				'<=LEFT_MARGIN'=>$arSection['LEFT_MARGIN'],
				'>=RIGHT_MARGIN'=>$arSection['RIGHT_MARGIN']),
			'order' => array('LEFT_MARGIN'=>'ASC')
		);
		if ($bActive)
		{
			$arGetList['filter'] = array_merge($arGetList['filter'],array('ACTIVE'=>true));
		}
		if ($arResult = $className::getList($arGetList))
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает всю ветку, в которой участвует раздел, либо FALSE
	 *
	 * @api
	 *
	 * @param int  $ID          ID раздела
	 * @param bool $bActive     вернуть только активные разделы
	 *
	 * @return bool|array
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_get_branch
	 */
	final public static function getBranch ($ID, $bActive=false)
	{
		/** @var DataManager $className */
		$className = static::getClassName();
		$arSection = static::getInfo($ID);
		$arGetList = array(
			'select' => static::getSelectFields(),
			'filter' => array(
				'>RIGHT_MARGIN'=>$arSection['LEFT_MARGIN'],
				'<LEFT_MARGIN'=>$arSection['RIGHT_MARGIN']),
			'order' => array('LEFT_MARGIN'=>'ASC')
		);
		if ($bActive)
		{
			$arGetList['filter'] = array_merge($arGetList['filter'],array('ACTIVE'=>true));
		}
		if ($arResult = $className::getList($arGetList))
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает ID родительского раздела, либо FALSE
	 *
	 * @api
	 *
	 * @param int $ID       ID раздела
	 *
	 * @return bool|int
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_get_parent_id
	 */
	final public static function getParentID ($ID)
	{
		$arSection = static::getInfo($ID);
		if (isset($arSection['PARENT_SECTION_ID']))
		{
			return $arSection['PARENT_SECTION_ID'];
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает массив параметров родительского раздела, либо FALSE
	 *
	 * @api
	 *
	 * @param int $ID        ID раздела
	 *
	 * @return array|bool
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_get_parent_info
	 */
	final public static function getParentInfo ($ID)
	{
		if ($arSection = static::getInfo(static::getParentID($ID)))
		{
			return $arSection;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Добавляет новый раздел в дерево
	 *
	 * @api
	 *
	 * @param $arSection
	 *
	 * @return bool|int
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_add_section
	 */
	final public static function addSection ($arSection)
	{
		/*		Создание узла – самое простое действие над деревом. Для того, что бы его осуществить нам потребуется уровень и
				правый ключ родительского узла (узел в который добавляется новый), либо максимальный правый ключ, если у
				нового узла не будет родительского.*/

		$tableName = static::getTableName();
		/** @var DataManager $className */
		$className = static::getClassName();
		$helper = new SqlHelper($tableName);
		if (!$arSection = static::checkAddFields($arSection))
		{
			return false;
		}

		/*		Пусть $right_key – правый ключ родительского узла, или максимальный правый ключ плюс единица (если
				родительского узла нет, то узел с максимальным правым ключом не будет обновляться, соответственно, чтобы небыло
				повторов, берем число на единицу большее). $level – уровень родительского узла, либо 0, если родительского нет.*/
		if ($arSection['PARENT_SECTION_ID']==0)
		{
			$sql = "SELECT\n\t"
				.$helper->getMaxFunction('RIGHT_MARGIN','RIGHT_MARGIN')."\n"
				."FROM\n\t"
				.$helper->wrapTableQuotes();
			$query = new Query\QueryBase($sql);
			$res = $query->exec();
			if ($ar_res = $res->fetch())
			{
				$right_key = $ar_res['RIGHT_MARGIN'] + 1;
				$level = 0;
			}
			else
			{
				$right_key = 1;
				$level = 0;
			}
		}
		else
		{
			$arParent = static::getInfo ($arSection['PARENT_SECTION_ID']);
			$right_key = $arParent['RIGHT_MARGIN'];
			$level = $arParent['DEPTH_LEVEL'];

			//1. Обновляем ключи существующего дерева, узлы стоящие за родительским узлом:
			$sql = "UPDATE\n\t"
				.$helper->wrapTableQuotes()."\n"
				."SET\n\t"
				.$helper->wrapQuotes('LEFT_MARGIN')." = ".$helper->wrapQuotes('LEFT_MARGIN')." + 2,\n\t"
				.$helper->wrapQuotes('RIGHT_MARGIN')." = ".$helper->wrapQuotes('RIGHT_MARGIN')." + 2\n"
				."WHERE\n\t"
				.$helper->wrapQuotes('LEFT_MARGIN')." > ".$right_key;
			$query = new Query\QueryBase($sql);
			$res = $query->exec();
			/*			Но мы обновили только те узлы в которых изменяются оба ключа, при этом родительскую ветку (не узел, а все
						родительские узлы) мы не трогали, так как в них изменяется только правый ключ. Следует иметь в виду, что
						если у нас не будет родительского узла, то есть новый узел будет корневым, то данное обновление проводить
						нельзя.*/
		}


		//2. Обновляем родительскую ветку:
		$sql = "UPDATE\n\t"
			.$helper->wrapTableQuotes()."\n"
			."SET\n\t"
			.$helper->wrapQuotes('RIGHT_MARGIN')." = ".$helper->wrapQuotes('RIGHT_MARGIN')." + 2\n"
			."WHERE\n\t"
			.$helper->wrapQuotes('RIGHT_MARGIN')." >= ".$right_key." AND\n\t"
			.$helper->wrapQuotes('LEFT_MARGIN')." < ".$right_key;
		$query = new Query\QueryBase($sql);
		$query->exec();

		//3. Теперь добавляем новый узел :
		$arSection['LEFT_MARGIN'] = $right_key;
		$arSection['RIGHT_MARGIN'] = $right_key + 1;
		$arSection['DEPTH_LEVEL'] = $level + 1;
		if (isset($arSection['ID']))
		{
			unset($arSection['ID']);
		}
		$res = $className::add($arSection);
		$insertID = $res->getInsertId();
		$arSection['ID'] = $insertID;
		static::sortSection($insertID);

		if ($insertID > 0)
		{
			return $insertID;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Сортирует раздел по индексу сортировки.
	 * Если параметер sort указан, в начале обновляет значение этого параметра у раздела
	 * Изменяет такие поля как LEFT_MARGIN и RIGHT_MARGIN
	 *
	 * @api
	 *
	 * @param int       $sectionID   ID раздела
	 * @param int|null  $sort        Новый индекс сортировки, если необходим
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_sort_section
	 */
	final public static function sortSection ($sectionID, $sort=null)
	{
		$arSection = static::getInfo($sectionID);
		if (!is_null($sort))
		{
			$arSection['SORT'] = intval($sort);
			static::updateSection($sectionID,array('SORT'=>intval($sort)));
		}
		/** @var DataManager $className */
		$className = static::getClassName();
		$tableName = static::getTableName();
		$helper = new SqlHelper($tableName);
		$arParams = array();

		//1. Ключи и уровень перемещаемого узла
		static::getKeysAndLevel ($arSection, $arParams);

		//2. Уровень родительского узла:
		static::getParentLevel ($arSection, $arParams);

		//3. Правый, левый ключ, уровень узла за который мы вставляем узел (ветку):
		$arChild = static::getChild($arSection['PARENT_SECTION_ID']);
		$arParent = $arChild[0];
		unset($arChild[0]);
		if (!empty($arChild))
		{
			foreach ($arChild as $i=>$ar_child)
			{
				if ($ar_child['PARENT_SECTION_ID']!=$arSection['PARENT_SECTION_ID'])
				{
					unset($arChild[$i]);
				}
			}
		}
		$arTemp = $arChild;
		$arChild = array();
		$arSort = array();
		if (!empty($arTemp))
		{
			foreach ($arTemp as $ar_child)
			{
				$arChild[$ar_child['ID']] = $ar_child;
				$arSort[$ar_child['ID']] = $ar_child['SORT'];
			}
		}
		unset($arTemp);
		$i=0;
		foreach ($arSort as $id=>$sort)
		{
			if ($id==$arSection['ID'])
			{
				$arParams['position_now'] = $i;
			}
			$i++;
		}
		asort($arSort);
		$arParams['arSort'] = $arSort;
		$p = 0;
		$temp_right_key = $temp_level = $temp_left_key = $arParams['level_near'] = $arParams['left_key_near'] = $arParams['right_key_near'] = 0;
		foreach ($arSort as $id=>$sort)
		{
			if ($id==$arSection['ID'])
			{
				if ($p==0)
				{
					$arParams['left_key_near'] = $arParent['LEFT_MARGIN'];
					$arParams['right_key_near'] = $arParent['RIGHT_MARGIN'];
					$arParams['level_near'] = $arParent['DEPTH_LEVEL'];
					$arParams['position_target'] = $p;
					break;
				}
				else
				{
					$arParams['left_key_near'] = $temp_left_key;
					$arParams['right_key_near'] = $temp_right_key;
					$arParams['level_near'] = $temp_level;
					$arParams['position_target'] = $p;
					break;
				}
			}
			else
			{
				$temp_left_key = $arChild[$id]['LEFT_MARGIN'];
				$temp_right_key = $arChild[$id]['RIGHT_MARGIN'];
				$temp_level = $arChild[$id]['DEPTH_LEVEL'];
			}
			$p++;
		}
		//Получаем $arParams['right_key_near'] и $arParams['left_key_near'] (для варианта изменения порядка)
		//Если элемент должен находится в самом низу и находится там - выходим, так как ничего делать не нужно
		if ($arParams['position_now']==$arParams['position_target'])
		{
			return;
		}

		//4. Определяем смещения:
		//$level_up - $level + 1 = $skew_level - смещение уровня изменяемого узла;
		$arParams['skew_level'] = $arParams['level_up'] - $arParams['level'] + 1;
		//$right_key - $left_key + 1 = $skew_tree - смещение ключей дерева;
		$arParams['skew_tree'] = $arParams['right_key'] - $arParams['left_key'] + 1;

		//Выбираем все узлы перемещаемой ветки:
		$arRes = $className::getList(
			array(
				'select' => array('ID'),
				'filter' => array('>=LEFT_MARGIN'=>$arParams['left_key'], '<=RIGHT_MARGIN'=>$arParams['right_key'])
			)
		);
		$arParams['id_edit'] = array();
		foreach($arRes as $ar_res)
		{
			$arParams['id_edit'][] = $ar_res['ID'];
		}
		//Получаем $id_edit - список id номеров перемещаемой ветки.

		// Определяем в какую область перемещается узел
		if (($arParams['left_key_near'] < $arParams['left_key']) && ($arParams['level'] > $arParams['level_near']))
		{
			//Если перемещаемся выше и встаем сразу за родительским узлом
			$arParams['up_down'] = "parent";

			//Определяем смещение ключей редактируемого узла $right_key_near - $left_key + 1 = $skew_edit;
			$arParams['skew_edit'] = $arParams['left_key_near'] - $arParams['left_key'] + 1;

			/* 1.
			UPDATE
				tableName
			SET
				RIGHT_MARGIN = RIGHT_MARGIN + $skew_tree
				LEFT_MARGIN = LEFT_MARGIN + $skew_tree
			WHERE
				RIGHT_MARGIN < $left_key AND
				RIGHT_MARGIN > $left_key_near AND
				ID NOT IN ($id_edit)
			 */
			$sql = "UPDATE\n\t"
				.$helper->wrapTableQuotes()."\n"
				."SET\n\t"
				.$helper->wrapQuotes('RIGHT_MARGIN')." = ".$helper->wrapQuotes('RIGHT_MARGIN')." + ".$arParams['skew_tree'].",\n\t"
				.$helper->wrapQuotes('LEFT_MARGIN')." = ".$helper->wrapQuotes('LEFT_MARGIN')." + ".$arParams['skew_tree']."\n"
				."WHERE\n\t"
				.$helper->wrapQuotes('RIGHT_MARGIN')." < ".$arParams['left_key']." AND\n\t"
				.$helper->wrapQuotes('RIGHT_MARGIN')." > ".$arParams['left_key_near']." AND\n\t"
				.$helper->wrapQuotes('ID')." NOT IN (".implode(',',$arParams['id_edit']).")";
			$query = new Query\QueryBase($sql);
			$query->exec();

			//Теперь можно переместить ветку:
			/*
			UPDATE
				tableName
			SET
				LEFT_MARGIN = LEFT_MARGIN + $skew_edit,
				RIGHT_MARGIN = RIGHT_MARGIN + $skew_edit,
			WHERE
				ID IN ($id_edit)
			*/
			$sql = "UPDATE\n\t"
				.$helper->wrapTableQuotes()."\n"
				."SET\n\t"
				.$helper->wrapQuotes('LEFT_MARGIN')." = ".$helper->wrapQuotes('LEFT_MARGIN')." + ".$arParams['skew_edit'].",\n\t"
				.$helper->wrapQuotes('RIGHT_MARGIN')." = ".$helper->wrapQuotes('RIGHT_MARGIN')." + ".$arParams['skew_edit']."\n"
				."WHERE\n\t"
				.$helper->wrapQuotes('ID')." IN (".implode(',',$arParams['id_edit']).")";
			$query = new Query\QueryBase($sql);
			$query->exec();
		}
		elseif ($arParams['left_key_near'] < $arParams['left_key'])
		{
			//Если перемещаемся выше
			$arParams['up_down'] = "up";

			//Определяем смещение ключей редактируемого узла $right_key_near - $left_key + 2 = $skew_edit;
			$arParams['skew_edit'] = ($arParams['left_key_near'] - $arParams['left_key'] + 2)*(-1);

			/* 1.
			UPDATE
				tableName
			SET
				RIGHT_MARGIN = RIGHT_MARGIN + $skew_tree
				LEFT_MARGIN = LEFT_MARGIN + $skew_tree
			WHERE
				RIGHT_MARGIN < $left_key AND
				RIGHT_MARGIN > $left_key_near AND
				ID NOT IN ($id_edit)
			 */
			$sql = "UPDATE\n\t"
				.$helper->wrapTableQuotes()."\n"
				."SET\n\t"
				.$helper->wrapQuotes('RIGHT_MARGIN')." = ".$helper->wrapQuotes('RIGHT_MARGIN')." + ".$arParams['skew_tree'].",\n\t"
				.$helper->wrapQuotes('LEFT_MARGIN')." = ".$helper->wrapQuotes('LEFT_MARGIN')." + ".$arParams['skew_tree']."\n"
				."WHERE\n\t"
				.$helper->wrapQuotes('RIGHT_MARGIN')." < ".$arParams['right_key']." AND\n\t"
				.$helper->wrapQuotes('RIGHT_MARGIN')." > ".$arParams['right_key_near']." AND\n\t"
				.$helper->wrapQuotes('ID')." NOT IN (".implode(',',$arParams['id_edit']).")";
			$query = new Query\QueryBase($sql);
			$query->exec();

			//Теперь можно переместить ветку:
			/*
			UPDATE
				tableName
			SET
				LEFT_MARGIN = LEFT_MARGIN - ($skew_edit*(-1)),
				RIGHT_MARGIN = RIGHT_MARGIN - ($skew_edit*(-1)),
			WHERE
				ID IN ($id_edit)
			*/
			$sql = "UPDATE\n\t"
				.$helper->wrapTableQuotes()."\n"
				."SET\n\t"
				.$helper->wrapQuotes('LEFT_MARGIN')." = ".$helper->wrapQuotes('LEFT_MARGIN')." - ".$arParams['skew_edit'].",\n\t"
				.$helper->wrapQuotes('RIGHT_MARGIN')." = ".$helper->wrapQuotes('RIGHT_MARGIN')." - ".$arParams['skew_edit']."\n"
				."WHERE\n\t"
				.$helper->wrapQuotes('ID')." IN (".implode(',',$arParams['id_edit']).")";
			$query = new Query\QueryBase($sql);
			$query->exec();
		}
		else
		{
			//Если перемещаемся ниже
			$arParams['up_down'] = "down";

			//Определяем смещение ключей редактируемого узла $right_key_near - $left_key + 1 - $skew_tree = $skew_edit.
			$arParams['skew_edit'] = $arParams['right_key_near'] - $arParams['left_key'] + 1 - $arParams['skew_tree'];

			/* 1.
			UPDATE
				tableName
			SET
				RIGHT_MARGIN = RIGHT_MARGIN - $skew_tree
				LEFT_MARGIN = LEFT_MARGIN - $skew_tree
			WHERE
				RIGHT_MARGIN > $right_key AND
				RIGHT_MARGIN <= $right_key_near AND
				ID NOT IN ($id_edit)
			*/
			$sql = "UPDATE\n\t"
				.$helper->wrapTableQuotes()."\n"
				."SET\n\t"
				.$helper->wrapQuotes('RIGHT_MARGIN')." = ".$helper->wrapQuotes('RIGHT_MARGIN')." - ".$arParams['skew_tree'].",\n\t"
				.$helper->wrapQuotes('LEFT_MARGIN')." = ".$helper->wrapQuotes('LEFT_MARGIN')." - ".$arParams['skew_tree']."\n"
				."WHERE\n\t"
				.$helper->wrapQuotes('RIGHT_MARGIN')." > ".$arParams['right_key']." AND\n\t"
				.$helper->wrapQuotes('RIGHT_MARGIN')." <= ".$arParams['right_key_near']." AND\n\t"
				.$helper->wrapQuotes('ID')." NOT IN (".implode(',',$arParams['id_edit']).")";
			$query = new Query\QueryBase($sql);
			$query->exec();

			//Теперь можно переместить ветку:
			/*
			UPDATE
				nameTable
			SET
				LEFT_MARGIN = LEFT_MARGIN + $skew_edit,
				RIGHT_MARGIN = RIGHT_MARGIN + $skew_edit,
			WHERE
				ID IN ($id_edit)
			*/
			$sql = "UPDATE\n\t"
				.$helper->wrapTableQuotes()."\n"
				."SET\n\t"
				.$helper->wrapQuotes('LEFT_MARGIN')." = ".$helper->wrapQuotes('LEFT_MARGIN')." + ".$arParams['skew_edit'].",\n\t"
				.$helper->wrapQuotes('RIGHT_MARGIN')." = ".$helper->wrapQuotes('RIGHT_MARGIN')." + ".$arParams['skew_edit']."\n"
				."WHERE\n\t"
				.$helper->wrapQuotes('ID')." IN (".implode(',',$arParams['id_edit']).")";
			$query = new Query\QueryBase($sql);
			$query->exec();
		}
	}

	/**
	 * Перемещает раздел в другой подраздел.
	 * Если параметер newParentID указан, в начале обновляет значение этого параметра у раздела
	 * Изменяет поле DEPTH_LEVEL
	 *
	 * @param int       $sectionID      ID раздела
	 * @param int|null  $newParentID    ID нового родительского раздела
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_change_parent
	 */
	final public static function changeParent ($sectionID, $newParentID=null)
	{
		$arSection = static::getInfo($sectionID);
		//msDebug(is_null($newParentID));
		if (!is_null($newParentID))
		{
			$arSection['PARENT_SECTION_ID'] = intval($newParentID);
			static::updateSection($sectionID,array('PARENT_SECTION_ID'=>intval($newParentID)));
		}
		$arParent = static::getParentFromTree($arSection['ID']);
		//msDebug($arParent['ID']);
		//msDebug($arSection['PARENT_SECTION_ID']);
		if ($arParent['ID'] == $arSection['PARENT_SECTION_ID'])
		{
			//Если раздел уже лежит в том разделе, где должен - ничего делать не нужно. Просто сортируем и выходим
			//msDebug('sort and return');
			static::sortSection($arSection['ID']);
			return;
		}

		/** @var DataManager $className */
		$className = static::getClassName();
		$tableName = static::getTableName();
		$helper = new SqlHelper($tableName);
		$arParams = array();

		//1. Ключи и уровень перемещаемого узла
		static::getKeysAndLevel($arSection,$arParams);

		//2. Уровень нового родительского узла (если узел перемещается в "корень" то сразу можно подставить значение 0)
		if ($arSection['PARENT_SECTION_ID'] == 0)
		{
			$arParams['level_up'] = 0;
			//3. Правый ключ узла за который мы вставляем узел (ветку)
			//При переносе узла в корень дерева – максимальный правый ключ ветки;
			/*
			 * SELECT
			 *      MAX(`RIGHT_MARGIN`) AS `RIGHT_MARGIN`
			 * FROM
			 *      ms_core_sections
			 */
			$sql = "SELECT\n\t"
				.$helper->getMaxFunction('RIGHT_MARGIN','RIGHT_MARGIN')."\n"
				."FROM\n\t"
				.$helper->wrapTableQuotes();
			$query = new Query\QueryBase($sql);
			$res = $query->exec();
			if ($ar_res = $res->fetch())
			{
				$arParams['right_key_near'] = $ar_res['RIGHT_MARGIN'];
			}
			//msDebug('parent=0');
		}
		else
		{
			$arNewParent = static::getInfo($arSection['PARENT_SECTION_ID']);
			$arChild = static::getChild($arSection['PARENT_SECTION_ID']);
			//msDebug($arChild);
			if (count($arChild)>1)
			{
				$arParams['isset_children'] = true;
			}
			else
			{
				$arParams['isset_children'] = false;
			}
			$arParams['level_up'] = $arNewParent['DEPTH_LEVEL'];
			//3. Правый ключ узла за который мы вставляем узел (ветку)
			if ($arNewParent['DEPTH_LEVEL'] == ($arSection['DEPTH_LEVEL'] - 2))
			{
				//msDebug('NEW_DEPTH ('.$arNewParent['DEPTH_LEVEL'].') == OLD_DEPTH ('.$arSection['DEPTH_LEVEL'].') - 2');
				//При поднятии узла на уровень выше – правый ключ старого родительского узла
				$arParams['right_key_near'] = $arNewParent['RIGHT_MARGIN']-1;
				//msDebug($arParent);
			}
			else
			{
				//При простом перемещении в другой узел;
				/*
				 * SELECT
				 *      (`RIGHT_MARGIN` - 1) AS `RIGHT_MARGIN`
				 * FROM
				 *      `ms_core_sections`
				 * WHERE
				 *      `ID` = $arSection['PARENT_SECTION_ID']
				 */
				$sql = "SELECT\n\t"
					."(".$helper->wrapFieldQuotes('RIGHT_MARGIN')." - 1) AS `RIGHT_MARGIN`\n"
					//.$helper->wrapFieldQuotes('RIGHT_MARGIN')."\n"
					."FROM\n\t"
					.$helper->wrapTableQuotes()."\n"
					."WHERE\n\t"
					.$helper->wrapFieldQuotes('ID')." = ".$arSection['PARENT_SECTION_ID'];
				$query = new Query\QueryBase($sql);
				$res = $query->exec();
				if ($ar_res = $res->fetch())
				{
					$arParams['right_key_near'] = $ar_res['RIGHT_MARGIN'];
				}
			}
		}

		//4. Определяем смещения:
		$arParams['skew_level'] = $arParams['level_up'] - $arParams['level'] + 1; // - смещение уровня изменяемого узла;
		$arParams['skew_tree'] = $arParams['right_key'] - $arParams['left_key'] + 1; // - смещение ключей дерева;

		//5. Выбираем все узлы перемещаемой ветки:
		$arRes = $className::getList(
			array(
				'select' => array('ID'),
				'filter' => array('>=LEFT_MARGIN'=>$arParams['left_key'], '<=RIGHT_MARGIN'=>$arParams['right_key'])
			)
		);
		$arParams['id_edit'] = array();
		foreach($arRes as $ar_res)
		{
			$arParams['id_edit'][] = $ar_res['ID'];
		}
		//Получаем $id_edit - список id номеров перемещаемой ветки.

		//6. Определяем куда перемещается узел
		if ($arParams['right_key_near'] < $arParams['right_key'])
		{
			//Перемещаемся вверх
			$arParams['up_down'] = "up";

			//6.1. Определяем смещение ключей редактируемого узла
			$arParams['skew_edit'] = $arParams['right_key_near'] - $arParams['left_key'] + 1;

			/*
			 * 6.2.
			 * UPDATE
			 *      table_name
			 * SET
			 *      RIGHT_MARGIN = RIGHT_MARGIN + $skew_tree
			 * WHERE
			 *      RIGHT_MARGIN < $left_key AND
			 *      RIGHT_MARGIN > $right_key_near AND
			 *      ID NOT IN ($id_edit)
			 */
			$sql = "UPDATE\n\t"
				.$helper->wrapTableQuotes()."\n"
				."SET\n\t"
				.$helper->wrapFieldQuotes('RIGHT_MARGIN'). " = "
				.$helper->wrapFieldQuotes('RIGHT_MARGIN'). " + ".$arParams['skew_tree']."\n"
				."WHERE\n\t"
				.$helper->wrapFieldQuotes('RIGHT_MARGIN')." < ".$arParams['left_key']." AND \n\t"
				.$helper->wrapFieldQuotes('RIGHT_MARGIN')." > ".$arParams['right_key_near']." AND\n\t"
				.$helper->wrapFieldQuotes('ID')." NOT IN (".implode(',',$arParams['id_edit']).")";
			$query = new Query\QueryBase($sql);
			$query->exec();
			//msEchoVar($sql);

			/*
			 * 6.3.
			 * UPDATE
			 *      table_name
			 * SET
			 *      LEFT_MARGIN = LEFT_MARGIN + $skew_tree
			 * WHERE
			 *      LEFT_MARGIN < $left_key AND
			 *      LEFT_MARGIN > $right_key_near
			 *      ID NOT IN ($id_edit)
			 */
			$sql = "UPDATE\n\t"
				.$helper->wrapTableQuotes()."\n"
				."SET\n\t"
				.$helper->wrapFieldQuotes('LEFT_MARGIN')." = "
				.$helper->wrapFieldQuotes('LEFT_MARGIN')." + ".$arParams['skew_tree']."\n"
				."WHERE\n\t"
				.$helper->wrapFieldQuotes('LEFT_MARGIN')." < ".$arParams['left_key']." AND\n\t"
				.$helper->wrapFieldQuotes('LEFT_MARGIN')." > ".$arParams['right_key_near']." AND\n\t"
				.$helper->wrapFieldQuotes('ID')." NOT IN (".implode(',',$arParams['id_edit']).")";
			$query = new Query\QueryBase($sql);
			$query->exec();
			//msEchoVar($sql);

			/*
			 * 6.4.
			 * UPDATE
			 *      table_name
			 * SET
			 *      LEFT_MARGIN = LEFT_MARGIN + $skew_edit,
			 *      RIGHT_MARGIN = RIGHT_MARGIN + $skew_edit,
			 *      DEPTH_LEVEL = DEPTH_LEVEL + $skew_level
			 * WHERE
			 *      ID IN ($id_edit)
			 */
			$sql = "UPDATE\n\t"
				.$helper->wrapTableQuotes()."\n"
				."SET\n\t"
				.$helper->wrapFieldQuotes('LEFT_MARGIN')." = "
				.$helper->wrapFieldQuotes('LEFT_MARGIN')." + ".$arParams['skew_edit'].",\n\t"
				.$helper->wrapFieldQuotes('RIGHT_MARGIN')." = "
				.$helper->wrapFieldQuotes('RIGHT_MARGIN')." + ".$arParams['skew_edit'].",\n\t"
				.$helper->wrapFieldQuotes('DEPTH_LEVEL')." = "
				.$helper->wrapFieldQuotes('DEPTH_LEVEL')." + ".$arParams['skew_level']."\n"
				."WHERE\n\t"
				.$helper->wrapFieldQuotes('ID')." IN (".implode(',',$arParams['id_edit']).")";
			$query = new Query\QueryBase($sql);
			$query->exec();
			//msEchoVar($sql);
		}
		else
		{
			//TO DO: Убрать bExec
			//$bExec = false;
			//$bExec = true;
			//Перемещаемся вниз
			$arParams['up_down'] = "down";

			//6.1. Определяем смещение ключей редактируемого узла
			$arParams['skew_edit'] = $arParams['right_key_near'] - $arParams['left_key'] - $arParams['skew_tree'] + 1;

			/*
			 * 6.2.
			 * UPDATE
			 *      table_name
			 * SET
			 *      RIGHT_MARGIN = RIGHT_MARGIN - $skew_tree
			 * WHERE
			 *      RIGHT_MARGIN > $right_key AND
			 *      RIGHT_MARGIN <= $right_key_near
			 *      ID NOT IN ($id_edit)
			 */
			$sql = "UPDATE\n\t"
				.$helper->wrapTableQuotes()."\n"
				."SET\n\t"
				.$helper->wrapFieldQuotes('RIGHT_MARGIN')." = "
				.$helper->wrapFieldQuotes('RIGHT_MARGIN')." - ".$arParams['skew_tree']."\n"
				."WHERE\n\t"
				.$helper->wrapFieldQuotes('RIGHT_MARGIN')." > ".$arParams['right_key']." AND\n\t"
				.$helper->wrapFieldQuotes('RIGHT_MARGIN')." <";
			//			if ($arParams['isset_children'])
			//			{
			$sql .= "=";
			//			}
			$sql .= " ".$arParams['right_key_near']." AND\n\t"
				.$helper->wrapFieldQuotes('ID')." NOT IN (".implode(',',$arParams['id_edit']).")";
			$query = new Query\QueryBase($sql);
			//TO DO: Убрать if
			//if ($bExec) $query->exec();
			$query->exec();
			//msEchoVar($sql);

			/*
			 * 6.3.
			 * UPDATE
			 *      table_name
			 * SET
			 *      LEFT_MARGIN = LEFT_MARGIN - $skew_tree
			 * WHERE
			 *      LEFT_MARGIN < $left_key AND
			 *      LEFT_MARGIN > $right_key_near
			 *      ID NOT IN ($id_edit)
			 */
			$sql = "UPDATE\n\t"
				.$helper->wrapTableQuotes()."\n"
				."SET\n\t"
				.$helper->wrapFieldQuotes('LEFT_MARGIN')." = "
				.$helper->wrapFieldQuotes('LEFT_MARGIN')." - ".$arParams['skew_tree']."\n"
				."WHERE\n\t"
				.$helper->wrapFieldQuotes('LEFT_MARGIN')." > ".$arParams['left_key']." AND\n\t"
				.$helper->wrapFieldQuotes('LEFT_MARGIN')." <= ".$arParams['right_key_near']." AND\n\t"
				.$helper->wrapFieldQuotes('ID')." NOT IN (".implode(',',$arParams['id_edit']).")";
			$query = new Query\QueryBase($sql);
			//TO DO: Убрать if
			//if ($bExec) $query->exec();
			$query->exec();
			//msEchoVar($sql);

			/*			if ($arSection['PARENT_SECTION_ID'] == 0)
						{
							$arParent0 = static::getParentFromTree($arSection['ID'],true);

							/*
							 * 6.4.
							 * UPDATE
							 *      table_name
							 * SET
							 *      RIGHT_MARGIN = RIGHT_MARGIN - $skew_tree
							 * WHERE
							 *      ID = $arParent[0][ID]
							 * /
							$query = new Query('update');
							$sql = "UPDATE\n\t"
								.$helper->wrapTableQuotes()."\n"
								."SET\n\t"
								.$helper->wrapFieldQuotes('RIGHT_MARGIN')." = "
								.$helper->wrapFieldQuotes('RIGHT_MARGIN')." - ".$arParams['skew_tree']."\n"
								."WHERE\n\t"
								.$helper->wrapFieldQuotes('ID')." = ".$arParent0['ID'];
							$query->setQueryBuildParts($sql);
							//TO DO: Убрать if
							if ($bExec) $query->exec();
							msEchoVar($sql);
						}*/
			/*
			 * 6.5.
			 * UPDATE
			 *      table_name
			 * SET
			 *      LEFT_MARGIN = LEFT_MARGIN + $skew_edit,
			 *      RIGHT_MARGIN = RIGHT_MARGIN + $skew_edit,
			 *      DEPTH_LEVEL = DEPTH_LEVEL + $skew_level
			 * WHERE
			 *      ID IN ($id_edit)
			 */
			/*if ($arSection['PARENT_SECTION_ID']==0)
			{
				$arParams['skew_edit']++;
			}*/
			$sql = "UPDATE\n\t"
				.$helper->wrapTableQuotes()."\n"
				."SET\n\t"
				.$helper->wrapFieldQuotes('LEFT_MARGIN')." = "
				.$helper->wrapFieldQuotes('LEFT_MARGIN')." + ".$arParams['skew_edit'].",\n\t"
				.$helper->wrapFieldQuotes('RIGHT_MARGIN')." = "
				.$helper->wrapFieldQuotes('RIGHT_MARGIN')." + ".$arParams['skew_edit'].",\n\t"
				.$helper->wrapFieldQuotes('DEPTH_LEVEL')." = "
				.$helper->wrapFieldQuotes('DEPTH_LEVEL')." + ".$arParams['skew_level']."\n"
				."WHERE\n\t"
				.$helper->wrapFieldQuotes('ID')." IN (".implode(',',$arParams['id_edit']).")";
			$query = new Query\QueryBase($sql);
			//TO DO: Убрать if
			//if ($bExec) $query->exec();
			$query->exec();
			//msEchoVar($sql);
		}

		static::sortSection($arSection['ID']);

		//msDebug($arParams);
	}

	/**
	 * Возвращает данные родительского раздела (осуществляет поиск по дереву разделов), либо false
	 *
	 * @param int   $sectionID      ID раздела
	 * @param bool  $mainSection    Флаг, обозначающий необходимость вернуть самый верхний раздел
	 *
	 * @return array|bool
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_get_parent_from_tree
	 */
	final protected static function getParentFromTree ($sectionID, $mainSection=false)
	{
		if (intval($sectionID)>0)
		{
			$arParents = static::getParent(intval($sectionID));
			if ($mainSection)
			{
				return $arParents[0];
			}
			for ($i=0; $i<count($arParents)-1; $i++)
			{
				if ($arParents[$i+1]['ID'] == intval($sectionID))
				{
					//$arParent = $arParents[$i];
					$arParams['right_key_near'] = $arParents[$i]['RIGHT_MARGIN'];
					return $arParents[$i];
				}
			}
		}

		return false;
	}

	/**
	 * Определяет ключи и уровень перемещаемого узла
	 *
	 * @api
	 *
	 * @param $arSection
	 * @param $arParams
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_get_keys_and_level
	 */
	final protected static function getKeysAndLevel ($arSection, &$arParams)
	{
		$arParams['level'] = $arSection['DEPTH_LEVEL'];
		$arParams['left_key'] = $arSection['LEFT_MARGIN'];
		$arParams['right_key'] = $arSection['RIGHT_MARGIN'];
	}

	/**
	 * Определяет уровень родительского узла
	 *
	 * @param $arSection
	 * @param $arParams
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_get_parent_level
	 */
	final protected static function getParentLevel ($arSection, &$arParams)
	{
		$arParams['level_up'] = (($arSection['DEPTH_LEVEL']<=0)?0:($arSection['DEPTH_LEVEL']-1));
	}

	/**
	 * Обновляет указанный раздел, предварительно исключив неизменяемые поля
	 *
	 * @param $sectionID
	 * @param $arUpdate
	 *
	 * @return DBResult
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_update_section
	 */
	final protected static function updateSection ($sectionID, $arUpdate)
	{
		/** @var DataManager $className */
		$className = static::getClassName();
		static::checkUpdateFields($arUpdate);
		$res = $className::update($sectionID,$arUpdate);

		return $res;
	}


	/**
	 * Удаляет указанный раздел
	 *
	 * @api
	 *
	 * @param int $sectionID ID удаляемого раздела
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_delete_section
	 */
	final public static function deleteSection ($sectionID)
	{
		/*		Удаление узла не намного сложнее, но требуется учесть, что у удаляемого узла могут быть подчиненные узлы. Для
				осуществления этого действия нам потребуется левый и правый ключ удаляемого узла.*/

		$tableName = static::getTableName();
		$helper = new SqlHelper($tableName);

		//Пусть $left_key – левый ключ удаляемого узла, а $right_key – правый
		$arSection = static::getInfo($sectionID);
		$left_key = $arSection['LEFT_MARGIN'];
		$right_key = $arSection['RIGHT_MARGIN'];

		//1. Удаляем узел (ветку)
		/*
		DELETE FROM
			tableName
		WHERE
			`LEFT_MARGIN` >= $left_key
			AND
			`RIGHT_MARGIN` <= $right_key
		 */
		$sql = "DELETE FROM\n\t"
			.$helper->wrapTableQuotes()."\n"
			."WHERE\n\t"
			.$helper->wrapQuotes('LEFT_MARGIN')." >= ".$left_key." AND\n\t"
			.$helper->wrapQuotes('RIGHT_MARGIN')." <= ".$right_key;
		$query = new Query\QueryBase($sql);
		$query->exec();

		//2. Обновляем ключи оставшихся веток:
		/*		Как и в случае с добавлением обновление происходит двумя командами: обновление ключей родительской ветки и
				обновление ключей узлов, стоящих за родительской веткой. Следует правда учесть, что обновление будет
				производиться в другом порядке, так как ключи у нас уменьшаются.*/
		//2.1. Обновление родительской ветки :
		/*
		UPDATE
			tableName
		SET
			`RIGHT_MARGIN` = `RIGHT_MARGIN` – ($right_key - $left_key + 1)
		WHERE
			`RIGHT_MARGIN` > $right_key
			AND
			`LEFT_MARGIN` < $left_key
		 */
		$sql = "UPDATE\n\t"
			.$helper->wrapTableQuotes()."\n"
			."SET\n\t"
			.$helper->wrapQuotes('RIGHT_MARGIN')." = "
			.$helper->wrapQuotes('RIGHT_MARGIN')." - (".$right_key." - ".$left_key." + 1)\n"
			."WHERE\n\t"
			.$helper->wrapQuotes('RIGHT_MARGIN')." > ".$right_key." AND\n\t"
			.$helper->wrapQuotes('LEFT_MARGIN')." < ".$left_key;
		$query = new Query\QueryBase($sql);
		$query->exec();

		//2.2. Обновление последующих узлов :
		/*
		UPDATE
			tableName
		SET
			`LEFT_MARGIN` = `LEFT_MARGIN` – ($right_key - $left_key + 1),
			`RIGHT_MARGIN` = `RIGHT_MARGIN` – ($right_key - $left_key + 1)
		WHERE
			`LEFT_MARGIN` > $right_key
		 */
		$sql = "UPDATE\n\t"
			.$helper->wrapTableQuotes()."\n"
			."SET\n\t"
			.$helper->wrapQuotes('LEFT_MARGIN')." = "
			.$helper->wrapQuotes('LEFT_MARGIN')." - (".$right_key." - ".$left_key." + 1),\n\t"
			.$helper->wrapQuotes('RIGHT_MARGIN')." = "
			.$helper->wrapQuotes('RIGHT_MARGIN')." - (".$right_key." - ".$left_key." + 1)\n"
			."WHERE\n\t"
			.$helper->wrapQuotes('LEFT_MARGIN')." > ".$right_key;
		$query = new Query\QueryBase($sql);
		$query->exec();

		//3. Проверяем.
	}

	/**
	 * Деактивирует раздел
	 *
	 * @api
	 *
	 * @param int $sectionID ID раздела
	 *
	 * @return DBResult
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_deactivate_section
	 */
	final public static function deactivateSection($sectionID)
	{
		$arUpdate = array('ACTIVE'=>false);

		$res = static::updateSection($sectionID,$arUpdate);

		return $res;
	}

	/**
	 * Активирует раздел
	 *
	 * @api
	 *
	 * @param int $sectionID ID раздела
	 *
	 * @return DBResult
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_activate_section
	 */
	final public static function activateSection ($sectionID)
	{
		$arUpdate = array('ACTIVE'=>true);

		$res = static::updateSection($sectionID,$arUpdate);

		return $res;
	}

	/**
	 * Убирает из массива обновления параметров раздела неизменяемые поля:
	 *
	 * Левая граница, правая граница, уровень вложенности
	 *
	 * Для изменения левой и правой границы
	 * @see static::sortSection ($sectionID, $sort=null)
	 *
	 * Для изменения уровня вложенности
	 * @see static::changeParent ($sectionID, $newParentID=null)
	 *
	 * @param $arSection
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_check_update_fields
	 */
	final protected static function checkUpdateFields (&$arSection)
	{
		//Левая граница
		if (isset($arSection['LEFT_MARGIN']))
		{
			unset($arSection['LEFT_MARGIN']);
		}
		//Правая граница
		if (isset($arSection['RIGHT_MARGIN']))
		{
			unset($arSection['RIGHT_MARGIN']);
		}
		//Уровень вложенности
		if (isset($arSection['DEPTH_LEVEL']))
		{
			unset($arSection['DEPTH_LEVEL']);
		}
	}

	/**
	 * Проверяет наличие обязательных полей, удаляет неизменяемые поля. Возвращает измененный массив полей раздела
	 *
	 * @param null|array $arSection
	 *
	 * @return null|array
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_check_add_fields
	 */
	final protected static function checkAddFields ($arSection=null)
	{
		if (is_null($arSection) || !isset($arSection['NAME']) || strlen($arSection['NAME'])<=0)
		{
			return false;
		}
		if (isset($arSection['ID']))
		{
			unset($arSection['ID']);
		}
		if (isset($arSection['LEFT_MARGIN']))
		{
			unset($arSection['LEFT_MARGIN']);
		}
		if (isset($arSection['RIGHT_MARGIN']))
		{
			unset($arSection['RIGHT_MARGIN']);
		}
		if (isset($arSection['DEPTH_LEVEL']))
		{
			unset($arSection['DEPTH_LEVEL']);
		}

		return $arSection;
	}

	/**
	 * Проверка целостности таблицы. Если все в порядке, возвращает false.
	 * Если есть проблемы, возвращает массив проблемных записей по 6 проверкам
	 *
	 * @api
	 *
	 * @return array|bool Массив проблеммных записей, либо false
	 * @link http://docs.dobrozhil.ru/doku.php/ms/core/lib/sections/method_check_table
	 */
	final public static function checkTable ()
	{
		/* ОСНОВНЫЕ ПРАВИЛА ХРАНЕНИЯ ДЕРЕВА КАТАЛОГОВ
		 *
		 * 1. Левый ключ ВСЕГДА меньше правого;
		 * 2. Наименьший левый ключ ВСЕГДА равен 1;
		 * 3. Наибольший правый ключ ВСЕГДА равен двойному числу узлов;
		 * 4. Разница между правым и левым ключом ВСЕГДА нечетное число;
		 * 5. Если уровень узла нечетное число то тогда левый ключ ВСЕГДА нечетное число, то же самое и для четных чисел;
		 * 6. Ключи ВСЕГДА уникальны, вне зависимости от того правый он или левый;
		 */
		$bError = false;
		$arResult = array();
		$helper = new SqlHelper(static::getTableName());
		/** @var DataManager $className */
		$className = static::getClassName();

		//1. Левый ключ ВСЕГДА меньше правого;
		//Если все правильно то результата работы запроса не будет, иначе, получаем список идентификаторов неправильных строк;
		$res1 = $className::getList(
			array(
				'select' => array('ID'),
				'filter' => array('>=LEFT_MARGIN'=>'FIELD_RIGHT_MARGIN')
			)
		);
		foreach ($res1 as $ar_res1)
		{
			$arResult['RULE1'][] = $ar_res1;
		}

		//2. Наименьший левый ключ ВСЕГДА равен 1;
		//3. Наибольший правый ключ ВСЕГДА равен двойному числу узлов;
		//Получаем количество записей (узлов), минимальный левый ключ и максимальный правый ключ, проверяем значения.
		$sql2 = "SELECT\n\t"
			.$helper->getCountFunction('ID','COUNT').",\n\t"
			.$helper->getMinFunction('LEFT_MARGIN','MIN').",\n\t"
			.$helper->getMaxFunction('RIGHT_MARGIN','MAX')."\n"
			."FROM\n\t"
			.$helper->wrapTableQuotes();
		$query2 = new Query\QueryBase($sql2);
		$res2 = $query2->exec();
		if ($ar_res2 = $res2->fetch())
		{
			if ($ar_res2['MIN'] != 1)
			{
				$bError = true;
				$arResult['RULE2']['MIN'] = $ar_res2['MIN'];
			}
			$double = $ar_res2['COUNT']*2;
			if ($ar_res2['MAX'] != $double)
			{
				$bError = true;
				$arResult['RULE3']['COUNT'] = $ar_res2['COUNT'];
				$arResult['RULE3']['DOUBLE'] = $double;
				$arResult['RULE3']['MAX'] = $ar_res2['MAX'];
			}
		}
		else
		{
			$bError = true;
			$arResult['RULE2'] = false;
			$arResult['RULE3'] = false;
		}


		//4. Разница между правым и левым ключом ВСЕГДА нечетное число;
		//Если все правильно то результата работы запроса не будет, иначе, получаем список идентификаторов неправильных строк;
		$sql4 = "SELECT\n\t"
			.$helper->wrapQuotes('ID').",\n\t"
			."MOD((".$helper->wrapFieldQuotes('RIGHT_MARGIN')." - ".$helper->wrapFieldQuotes('LEFT_MARGIN')."), 2) AS REMAINDER\n "
			."FROM\n\t"
			.$helper->wrapTableQuotes()."\n"
			."WHERE\n\t"
			."MOD((".$helper->wrapFieldQuotes('RIGHT_MARGIN')." - ".$helper->wrapFieldQuotes('LEFT_MARGIN')."), 2) = 0";
		$query4 = new Query\QueryBase($sql4);
		$res4 = $query4->exec();
		if ($res4->getResult())
		{
			while ($ar_res4 = $res4->fetch())
			{
				$bError = true;
				$arResult['RULE4'][] = $ar_res4;
			}
		}

		//5. Если уровень узла нечетное число то тогда левый ключ ВСЕГДА нечетное число, то же самое и для четных чисел;
		//Если все правильно то результата работы запроса не будет, иначе, получаем список идентификаторов неправильных строк;
		$sql5 = "SELECT\n\t"
			.$helper->wrapQuotes('ID').",\n\t"
			."MOD((".$helper->wrapFieldQuotes('LEFT_MARGIN')." - ".$helper->wrapFieldQuotes('DEPTH_LEVEL')." + 2), 2) AS REMAINDER \n"
			."FROM\n\t"
			.$helper->wrapTableQuotes()."\n"
			."WHERE\n\t"
			."MOD((".$helper->wrapFieldQuotes('LEFT_MARGIN')." - ".$helper->wrapFieldQuotes('DEPTH_LEVEL')." + 2), 2) = 1";
		$query5 = new Query\QueryBase($sql5);
		$res5 = $query5->exec();
		if ($res5->getResult())
		{
			while ($ar_res5 = $res5->fetch())
			{
				$bError = true;
				$arResult['RULE5'][] = $ar_res5;
			}
		}

		//6. Ключи ВСЕГДА уникальны, вне зависимости от того правый он или левый;
		/*
			Здесь, я думаю, потребуется некоторое пояснение запроса. Выборка по сути осуществляется из одной таблицы,
			но в разделе FROM эта таблица "виртуально" продублирована 3 раза: из первой мы выбираем все записи по
			порядку и начинаем сравнивать с записями второй таблицы (раздел WHERE) в результате мы получаем все записи
			неповторяющихся значений. Для того, что бы определить сколько раз запись не повторялась в таблице,
			производим группировку (раздел GROUP BY) и получаем число "не повторов" (COUNT(t1.id)). По условию,
			если все ключи уникальны, то число не повторов будет меньше на одну единицу чем общее количество записей.
			Для того, чтобы определить количество записей в таблице, берем максимальный правый ключ (MAX(t3.right_key)),
			так как его значение - двойное число записей, но так как в условии отбора для записи с максимальным правым
			ключом - максимальный правый ключ будет другим, вводится третья таблица, при этом число "неповторов"
			увеличивается умножением его на количество записей. SQRT(4*rep +1) - решение уравнения x^2 + x = rep.
			Если все правильно то результата работы запроса не будет, иначе, получаем список идентификаторов
			неправильных строк;
		 */
		$sql6 = "SELECT\n\t"
			."t1.".$helper->wrapQuotes('ID').",\n\t"
			."COUNT(t1.".$helper->wrapQuotes('ID').") AS rep,\n\t"
			."MAX(t3.".$helper->wrapQuotes('RIGHT_MARGIN').") AS max_right\n"
			."FROM\n\t"
			.$helper->wrapTableQuotes()." AS t1,\n\t"
			.$helper->wrapTableQuotes()." AS t2,\n\t"
			.$helper->wrapTableQuotes()." AS t3\n"
			."WHERE\n\t"
			."t1.".$helper->wrapQuotes('LEFT_MARGIN')." <> t2.".$helper->wrapQuotes('LEFT_MARGIN')." AND\n\t"
			."t1.".$helper->wrapQuotes('LEFT_MARGIN')." <> t2.".$helper->wrapQuotes('RIGHT_MARGIN')." AND\n\t"
			."t1.".$helper->wrapQuotes('RIGHT_MARGIN')." <> t2.".$helper->wrapQuotes('LEFT_MARGIN')." AND\n\t"
			."t1.".$helper->wrapQuotes('RIGHT_MARGIN')." <> t2.".$helper->wrapQuotes('RIGHT_MARGIN')."\n"
			."GROUP BY\n\t"
			."t1.".$helper->wrapQuotes('ID')."\n"
			."HAVING\n\t"
			."max_right <> SQRT(4 * rep + 1) + 1";
		$query6 = new Query\QueryBase($sql6);
		$res6 = $query6->exec();
		if ($res6->getResult())
		{
			while ($ar_res6 = $res6->fetch())
			{
				$bError = true;
				$arResult['RULE6'][] = $ar_res6;
			}
		}

		if ($bError)
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}
}