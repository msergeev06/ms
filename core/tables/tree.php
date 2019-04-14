<?php
/**
 * Ms\Core\Tables\TreeTable
 *
 * @package Ms\Core
 * @subpackage Tables
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */
//www.getinfo.ru/article610.html

namespace Ms\Core\Tables;

use Ms\Core\Entity\Db\DBResult;
use Ms\Core\Lib;
use Ms\Core\Entity\Db\Fields;
use Ms\Core\Entity\Db\SqlHelper;
use Ms\Core\Entity\Db\Query;

class TreeTable extends Lib\DataManager
{
    private static $defaultArSelect = ['ID','LEFT_MARGIN','RIGHT_MARGIN','DEPTH_LEVEL'];

	public static function getTableTitle()
	{
		return 'Дерево';
	}

	protected static function getMap()
	{
		return [
			new Fields\IntegerField('ID',[
				'primary' => true,
				'autocomplete' => true,
				'title' => 'ID ветки'
			]),
			Lib\TableHelper::activeField(),
			new Fields\IntegerField('LEFT_MARGIN',[
				'required' => true,
				'default_create' => 0,
				'default_insert' => 0,
				'title' => 'Левая граница'
			]),
			new Fields\IntegerField('RIGHT_MARGIN',[
				'required' => true,
				'default_create' => 0,
				'default_insert' => 0,
				'title' => 'Правая граница'
			]),
			new Fields\IntegerField('DEPTH_LEVEL',[
				'required' => true,
				'default_create' => 0,
				'default_insert' => 0,
				'title' => 'Уровень вложенности'
			])
		];
	}

	/**
	 * Добавляет индекс в таблицу
	 * Функция запускается автоматически после создания таблицы.
	 *
	 * @return bool
	 */
	public static function OnAfterCreateTable()
	{
		$sqlHelper = new SqlHelper(static::getTableName());
		$sql = "CREATE INDEX "
			.$sqlHelper->wrapQuotes('LEFT_MARGIN')." ON "
			.$sqlHelper->wrapTableQuotes()." ("
			.$sqlHelper->wrapQuotes('LEFT_MARGIN').", "
			.$sqlHelper->wrapQuotes('RIGHT_MARGIN').", "
			.$sqlHelper->wrapQuotes('DEPTH_LEVEL').")";
		$query = new Query\QueryBase($sql);
		$res = $query->exec();
		if ($res->getResult())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	//<editor-fold defaultstate="collapse" desc=">> Методы взаимодействия с деревом">
	/**
	 * Возвращает массив узлов дерева, либо FALSE
	 *
	 * @param array $arSelect Массив возвращаемых полей
	 * @param bool  $bActive  Выбрать только активные узлы
	 *
	 * @return bool|array
	 */
	final public static function getTreeList ($arSelect=[],$bActive=false)
	{
		$arGetList = [
			'order' => ['LEFT_MARGIN' => 'ASC']
		];
		if ($arSelect && !empty($arSelect))
		{
			$arGetList['select'] = $arSelect;
		}
		if ($bActive)
		{
			$arGetList['filter'] = ['ACTIVE'=>true];
		}

		if ($arResult = static::getList($arGetList))
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает массив дочерних узлов для указанного, либо FALSE
	 *
	 * @param int        $ID          ID верхнего узла
	 * @param bool|array $arSelect    Массив возвращаемных полей
	 * @param bool       $bActive     Вывести только активные узлы
	 * @param int        $iDepthLevel Если > 0 - будут выбраны только указанного уровня вложенности
	 *
	 * @return bool|array
	 */
	final public static function getChildren ($ID, $arSelect = [], $bActive=false, $iDepthLevel=0)
	{
		$arNode = static::getById($ID,['LEFT_MARGIN','RIGHT_MARGIN']);
		if (!$arNode)
		{
			return false;
		}
		$arGetList = [
			'filter' => [
				'>=LEFT_MARGIN'=>$arNode['LEFT_MARGIN'],
				'<=RIGHT_MARGIN'=>$arNode['RIGHT_MARGIN']
			],
			'order' => ['LEFT_MARGIN'=>'ASC']
		];
		if ($arSelect && !empty($arSelect))
		{
			$arGetList['select'] = $arSelect;
		}
		if ($bActive)
		{
			$arGetList['filter'] = array_merge($arGetList['filter'], ['ACTIVE'=>true]);
		}
		$iDepthLevel = (int)$iDepthLevel;
		if ($iDepthLevel>0)
		{
			$arGetList['filter'] = array_merge($arGetList['filter'],['DEPTH_LEVEL'=>$iDepthLevel]);
		}
		if ($arResult = static::getList($arGetList))
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает массив родителей узла, либо FALSE
	 *
	 * @param int        $ID        ID узла
	 * @param bool|array $arSelect  Массив возвращаемых полей
	 * @param bool       $bActive   Вернуть только активные узлы
	 *
	 * @return bool|array
	 */
	final public static function getParents ($ID, $arSelect=[], $bActive=false)
	{
		$arNode = static::getById($ID,['LEFT_MARGIN','RIGHT_MARGIN']);
		if (!$arNode)
		{
			return false;
		}
		$arGetList = [
			'filter' => [
				'<=LEFT_MARGIN'=>$arNode['LEFT_MARGIN'],
				'>=RIGHT_MARGIN'=>$arNode['RIGHT_MARGIN']
			],
			'order' => ['LEFT_MARGIN'=>'ASC']
		];
		if ($arSelect && !empty($arSelect))
		{
			$arGetList['select'] = $arSelect;
		}
		if ($bActive)
		{
			$arGetList['filter'] = array_merge($arGetList['filter'],['ACTIVE'=>true]);
		}
		if ($arResult = static::getList($arGetList))
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает всю ветку, в которой участвует узел, либо FALSE
	 *
	 * @param int        $ID        ID узла
	 * @param bool|array $arSelect  Массив возвращаемых полей
	 * @param bool       $bActive   Вернуть только активные узлы
	 *
	 * @return bool|array
	 */
	final public static function getBranch ($ID, $arSelect=[], $bActive=false)
	{
		$arNode = static::getById($ID,['LEFT_MARGIN','RIGHT_MARGIN']);
		$arGetList = [
			'filter' => [
				'>RIGHT_MARGIN'=>$arNode['LEFT_MARGIN'],
				'<LEFT_MARGIN'=>$arNode['RIGHT_MARGIN']
			],
			'order' => ['LEFT_MARGIN'=>'ASC']
		];
		if ($arSelect && !empty($arSelect))
		{
			$arGetList['select'] = $arSelect;
		}
		if ($bActive)
		{
			$arGetList['filter'] = array_merge($arGetList['filter'],['ACTIVE'=>true]);
		}
		if ($arResult = static::getList($arGetList))
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает массив параметров родительского узла, либо FALSE
	 *
	 * @param int $ID    ID узла
	 * @param bool|array Массив возвращаемых полей
	 *
	 * @return array|bool
	 */
	final public static function getParentInfo ($ID, $arSelect=[])
	{
	    //OK
		$arNode = static::getById($ID,['LEFT_MARGIN','RIGHT_MARGIN']);
		if (!$arNode)
		{
			return false;
		}
		$arGetOne = [
			'filter' => [
				'>RIGHT_MARGIN'=>$arNode['RIGHT_MARGIN'],
				'<LEFT_MARGIN'=>$arNode['LEFT_MARGIN']
			],
			'order' => ['LEFT_MARGIN'=>'DESC']
		];
		if ($arSelect && !empty($arSelect))
		{
			$arGetOne['select'] = $arSelect;
		}
		if ($arResult = static::getOne($arGetOne))
		{
			return $arResult;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Возвращает ID родительского узла, либо FALSE
	 *
	 * @param int $ID ID узла
	 *
	 * @return bool|int
	 */
	final public static function getParentID ($ID)
	{
	    //OK
		if ($arResult = static::getParentInfo($ID,['ID']))
		{
			return (int)$arResult['ID'];
		}
		else
		{
			return false;
		}
	}

	/**
	 * Определяет уровень родительского узла
	 *
	 * @param int $ID ID узла
	 *
	 * @return bool|int
	 */
	final public static function getParentLevel ($ID)
	{
	    //OK
		if ($arResult = static::getParentInfo($ID,['DEPTH_LEVEL']))
		{
			return (int)$arResult['DEPTH_LEVEL'];
		}
		else
		{
			return false;
		}
	}

    /**
     * Определяет ключи и уровень перемещаемого узла
     * Записывает в $arParams следующие поля:
     * level        уровень вложенности
     * left_key     левая граница
     * right_key    правая граница
     *
     * @api
     *
     * @param array $arNode Массив полей узла
     * @param mixed &$arParams Массив параметров, куда будут записаны данные. Передается по ссылке
     */
    final protected static function getKeysAndLevel (array $arNode, &$arParams)
    {
        $arParams['level'] = $arNode['DEPTH_LEVEL'];
        $arParams['left_key'] = $arNode['LEFT_MARGIN'];
        $arParams['right_key'] = $arNode['RIGHT_MARGIN'];
    }

    /**
	 * Добавляет новый узел в дерево
	 *
	 * @param array &$arNode Массив полей узла
	 *
	 * @return bool|int
	 */
	final public static function addNode (&$arNode)
	{
	    //OK
		/*		Создание узла – самое простое действие над деревом. Для того, что бы его осуществить нам потребуется уровень и
				правый ключ родительского узла (узел в который добавляется новый), либо максимальный правый ключ, если у
				нового узла не будет родительского.*/

		$helper = new SqlHelper(static::getTableName());
		if (!static::checkNodeFields($arNode))
		{
			return false;
		}

		/*		Пусть $right_key – правый ключ родительского узла, или максимальный правый ключ плюс единица (если
				родительского узла нет, то узел с максимальным правым ключом не будет обновляться, соответственно, чтобы небыло
				повторов, берем число на единицу большее). $level – уровень родительского узла, либо 0, если родительского нет.*/
		if (
			(isset($arNode['PARENT_ID']) && (int)$arNode['PARENT_ID']==0)
			|| !isset($arNode['PARENT_ID'])
		) {
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
			$arParent = static::getById($arNode['PARENT_ID'],['RIGHT_MARGIN','LEFT_MARGIN','DEPTH_LEVEL']);
			if ($arParent)
			{
				$right_key = $arParent['RIGHT_MARGIN'];
				$level = $arParent['DEPTH_LEVEL'];
			}
			else
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
			if (!$res->getResult())
			{
				return false;
			}
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
		$res = $query->exec();
		if (!$res->getResult())
		{
			return false;
		}

		//3. Теперь добавляем новый узел :
		$arNode['LEFT_MARGIN'] = $right_key;
		$arNode['RIGHT_MARGIN'] = $right_key + 1;
		$arNode['DEPTH_LEVEL'] = $level + 1;
		if (isset($arNode['PARENT_ID']))
		{
			unset($arNode['PARENT_ID']);
		}
		if (isset($arNode['ID']))
		{
			unset($arNode['ID']);
		}
		$res = static::add($arNode);
		$insertID = $res->getInsertId();
		$arNode['ID'] = $insertID;

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
	 * Проверяет наличие обязательных полей, удаляет неизменяемые поля. Возвращает измененный массив полей раздела
	 *
	 * @param array $arNode
	 *
	 * @return bool|array
	 */
	final protected static function checkNodeFields (array &$arNode)
	{
	    //OK
		if (is_null($arNode))
		{
			return false;
		}
		if (isset($arNode['ID']))
		{
			unset($arNode['ID']);
		}
		if (isset($arNode['LEFT_MARGIN']))
		{
			unset($arNode['LEFT_MARGIN']);
		}
		if (isset($arNode['RIGHT_MARGIN']))
		{
			unset($arNode['RIGHT_MARGIN']);
		}
		if (isset($arNode['DEPTH_LEVEL']))
		{
			unset($arNode['DEPTH_LEVEL']);
		}

		return $arNode;
	}

	/**
	 * Перемещает узел в другой родительский узел.
	 *
	 * @param int      $nodeID      ID раздела
	 * @param int|null $newParentID ID нового родительского раздела
	 *
	 * @return bool
	 */
	final public static function changeParent ($nodeID, $newParentID=null)
	{
	    //OK
		$arNode = static::getById($nodeID,self::$defaultArSelect);
        $arParent['ID'] = static::getParentID($arNode['ID']);

		//msDebug($arParent['ID']);
		//msDebug($arSection['PARENT_SECTION_ID']);
        $newParentID = (int)$newParentID;
		if ($arParent['ID'] == $newParentID)
		{
			//Если раздел уже лежит в том разделе, где должен - ничего делать не нужно.
			return true;
		}

		$helper = new SqlHelper(static::getTableName());
		$arParams = array();

		//1. Ключи и уровень перемещаемого узла
		$arParams['level'] = $arNode['DEPTH_LEVEL'];
		$arParams['left_key'] = $arNode['LEFT_MARGIN'];
		$arParams['right_key'] = $arNode['RIGHT_MARGIN'];

		//2. Уровень нового родительского узла (если узел перемещается в "корень" то сразу можно подставить значение 0)
		if ($newParentID == 0)
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
			else
			{
				return false;
			}
			//msDebug('parent=0');
		}
		else
		{
			$arNewParent = static::getById($newParentID,self::$defaultArSelect);
			$arChild = static::getChildren($arNewParent['ID']);
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
			if ($arNewParent['DEPTH_LEVEL'] == ($arNode['DEPTH_LEVEL'] - 2))
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
					.$helper->wrapFieldQuotes('ID')." = ".$arParent['ID'];
				$query = new Query\QueryBase($sql);
				$res = $query->exec();
				if ($ar_res = $res->fetch())
				{
					$arParams['right_key_near'] = $ar_res['RIGHT_MARGIN'];
				}
				else
				{
					return false;
				}
			}
		}

		//4. Определяем смещения:
		$arParams['skew_level'] = $arParams['level_up'] - $arParams['level'] + 1; // - смещение уровня изменяемого узла;
		$arParams['skew_tree'] = $arParams['right_key'] - $arParams['left_key'] + 1; // - смещение ключей дерева;

		//5. Выбираем все узлы перемещаемой ветки:
		$arRes = static::getList(
			array(
				'select' => ['ID'],
				'filter' => [
					'>=LEFT_MARGIN'=>$arParams['left_key'],
					'<=RIGHT_MARGIN'=>$arParams['right_key']
				]
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
			$res = $query->exec();
			//msEchoVar($sql);
			if (!$res->getResult())
			{
				return false;
			}

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
			$res = $query->exec();
			//msEchoVar($sql);
			if (!$res->getResult())
			{
				return false;
			}

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
			$res = $query->exec();
			//msEchoVar($sql);
			if (!$res->getResult())
			{
				return false;
			}
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
			$res = $query->exec();
			//msEchoVar($sql);
			if (!$res->getResult())
			{
				return false;
			}

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
			$res = $query->exec();
			//msEchoVar($sql);
			if (!$res->getResult())
			{
				return false;
			}

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
			$res = $query->exec();
			//msEchoVar($sql);
			if (!$res->getResult())
			{
				return false;
			}
		}

		//msDebug($arParams);

		return true;
	}

	/**
	 * Обработчик события таблицы, перед обновлением данных. Исключает неизменяемые поля
	 *
	 * @param int         $nodeID     ID узла
	 * @param array       &$arUpdate  Массив обновляемых полей
	 * @param null|string &$sSqlWhere SQL запрос WHERE
	 *
	 * @return mixed|false
	 */
	protected static function OnBeforeUpdate ($nodeID, &$arUpdate, &$sSqlWhere=null)
	{
		static::checkUpdateFields($arUpdate);

		return (!empty($arUpdate));
	}

	/**
	 * Убирает из массива обновления параметров раздела неизменяемые поля:
	 *
	 * Левая граница, правая граница, уровень вложенности
	 *
	 * Для изменения левой и правой границы
	 * @see static::sortNode ($nodeID, $prevNode=0)
	 *
	 * Для изменения уровня вложенности
	 * @see static::changeParent ($nodeID, $newParentID=null)
	 *
	 * @param array $arNode Массив полей узла
	 */
	final public static function checkUpdateFields (&$arNode)
	{
		//Левая граница
		if (isset($arNode['LEFT_MARGIN']))
		{
			unset($arNode['LEFT_MARGIN']);
		}
		//Правая граница
		if (isset($arNode['RIGHT_MARGIN']))
		{
			unset($arNode['RIGHT_MARGIN']);
		}
		//Уровень вложенности
		if (isset($arNode['DEPTH_LEVEL']))
		{
			unset($arNode['DEPTH_LEVEL']);
		}
	}

	/**
	 * Удаляет указанный узел
	 *
	 * @param int $nodeID ID удаляемого узла
	 *
	 * @return bool
	 */
	final public static function deleteNode ($nodeID)
	{
		/*		Удаление узла не намного сложнее, но требуется учесть, что у удаляемого узла могут быть подчиненные узлы. Для
				осуществления этого действия нам потребуется левый и правый ключ удаляемого узла.*/

		$helper = new SqlHelper(static::getTableName());

		//Пусть $left_key – левый ключ удаляемого узла, а $right_key – правый
		$arNode = static::getById($nodeID,['ID','LEFT_MARGIN','RIGHT_MARGIN','DEPTH_LEVEL']);
		$left_key = $arNode['LEFT_MARGIN'];
		$right_key = $arNode['RIGHT_MARGIN'];

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
		$res = $query->exec();
		if (!$res->getResult())
		{
			return FALSE;
		}

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
		$res = $query->exec();
		if (!$res->getResult())
		{
			return FALSE;
		}

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
		$res = $query->exec();
		if (!$res->getResult())
		{
			return false;
		}

		//3. Проверяем.

		return true;
	}

	/**
	 * Деактивирует узел
	 *
	 * @param int $nodeID ID узла
	 *
	 * @return false|DBResult|string
	 */
	final public static function deactivateNode ($nodeID)
	{
		$arUpdate = ['ACTIVE'=>false];

		return static::update($nodeID,$arUpdate);
	}

	/**
	 * Активирует узел
	 *
	 * @param int $nodeID ID узла
	 *
	 * @return false|DBResult|string
	 */
	final public static function activateNode ($nodeID)
	{
		$arUpdate = ['ACTIVE'=>true];

		return static::update($nodeID,$arUpdate);
	}

	/**
	 * Устанавливает местоположение узла, сортируя узлы ветки по указанному полю, в указанном направлении
	 *
	 * @param int    $nodeID ID перемещаемого узла
	 * @param string $sort   Поле для сортировки
	 * @param string $order  Направление сортировки
     *
     * @return bool
	 */
	final public static function sortNode ($nodeID, $sort='LEFT_MARGIN', $order='ASC')
	{
	    //OK
	    /*
            Перемещение узла
            Перемещение узла – самое сложное действие в управлении деревом.

	        1. Вверх по дереву (в область вышестоящих узлов), включает в себя:
                a Перенос ветки (узла) в подчинение нижестоящему по дереву узлу;
                b Перенос ветки (узла) вверх без изменения родительского узла (изменение порядка узлов);
            2. Вниз по дереву (в область нижестоящих узлов), включает в себя.
                a Перенос ветки в «корень» дерева (учитывая, что переносимая ветка будет последней по порядку);
                b Перенос ветки (узла) вниз без изменения родительского узла (изменение порядка узлов);
                c Поднятие узла (ветки) на уровень выше;
                d Перемещение ветки вниз по дереву;

	        Сортировка подразумевает п.п.: 1.b. и 2.b.
	     */
	    //Обрабатываем входящие переменные
		$nodeID = (int)$nodeID;
		$sort = strtoupper($sort);
		$order = strtoupper($order);

		//Выбираем основные поля дерева
		$arSelect = self::$defaultArSelect;
		//Добавляем поле по которому будет сортировка, если его еще не выбрали
		if (!in_array($sort,$arSelect))
		{
			$arSelect[] = $sort;
		}

		//Получаем выбранные поля сортируемого узла
		$arNode = static::getById($nodeID,$arSelect);
//		msDebug($arNode);
		/** @var Lib\DataManager $className */
		$helper = new SqlHelper(static::getTableName());

		//Создаем и наполняем массив параметров
		$arParams = [];
        //<editor-fold defaultstate="collapse" desc="1. Ключи и уровень перемещаемого узла">
		$arParams['level'] = $arNode['DEPTH_LEVEL'];
		$arParams['left_key'] = $arNode['LEFT_MARGIN'];
		$arParams['right_key'] = $arNode['RIGHT_MARGIN'];
        //</editor-fold>

        //<editor-fold defaultstate="collapse" desc="2. Уровень родительского узла">
        //Получаем данные родителя нашего узла
		$arParentNode = static::getParentInfo($nodeID,$arSelect);
//        msDebug($arParentNode);
		//Если родитель существует, сохраняем его уровень вложенности
		if ($arParentNode)
		{
			$arParams['level_up'] = $arParentNode['DEPTH_LEVEL'];
		}
		//Если узел лежит в корне, уровень вложенности родителя приравниваем к 0
		else
		{
			$arParams['level_up'] = 0;
		}
        //</editor-fold>

        //<editor-fold defaultstate="collapse" desc="3. Правый, левый ключ, уровень узла за который мы вставляем узел (ветку)">
        //Если родитель существует, получаем массив дочерних узлов
		if ($arParentNode)
		{
			$arChild = static::getChildren($arParentNode['ID'],$arSelect);
		}
		//Если родителя нет и наш узел перемещается в корень, получаем все узлы 1 уровня вложенности
		else
		{
			$arChild = static::getNodesByDepthLevel(1,$arSelect);
		}

//		msDebug($arChild);
		$arParent = $arChild[0];
		unset($arChild[0]);
		//Не понимаю, зачем это нужно, ведь и так мы получили точно только потомков нашего родителя
/*
		if (!empty($arChild))
		{
			foreach ($arChild as $i=>$ar_child)
			{
			    //TO DO: Оптимизировать. Не дело запросы к базе в цикле
                $iChildParentId = static::getParentID($ar_child['ID']);
                //Если ID родителя дочернего узла не равен ID родительского узла нашего узла
				if ($iChildParentId!=$arParentNode['ID'])
				{
					unset($arChild[$i]);
				}
			}
		}*/
		$arTemp = $arChild;
		$arChild = array();
		$arSort = array();
		if (!empty($arTemp))
		{
			foreach ($arTemp as $ar_child)
			{
				$arChild[$ar_child['ID']] = $ar_child;
				$arSort[$ar_child['ID']] = $ar_child[$sort];
			}
		}
		unset($arTemp);
		$i=0;
		foreach ($arSort as $id=>$sort)
		{
			if ($id==$arNode['ID'])
			{
				$arParams['position_now'] = $i;
			}
			$i++;
		}
		if ($order=='ASC')
        {
            //Сортируем в прямом направлении
            asort($arSort);
        }
		else
        {
            //Сортируем в обратном направлении
            arsort($arSort);
        }
		$arParams['arSort'] = $arSort;
		$p = 0;
		$temp_right_key = $temp_level = $temp_left_key = $arParams['level_near'] = $arParams['left_key_near'] = $arParams['right_key_near'] = 0;
		foreach ($arSort as $id=>$sort)
		{
			if ($id==$arNode['ID'])
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
			return true;
		}
        //</editor-fold>

        //<editor-fold defaultstate="collapse" desc="4. Определяем смещения">
        //$level_up - $level + 1 = $skew_level - смещение уровня изменяемого узла;
		$arParams['skew_level'] = $arParams['level_up'] - $arParams['level'] + 1;
		//$right_key - $left_key + 1 = $skew_tree - смещение ключей дерева;
		$arParams['skew_tree'] = $arParams['right_key'] - $arParams['left_key'] + 1;
        //</editor-fold>

        //<editor-fold defaultstate="collapse" desc="5. Получаем $id_edit - список id номеров перемещаемой ветки">
        //Выбираем все узлы перемещаемой ветки:
		$arRes = static::getList(
			array(
				'select' => ['ID'],
				'filter' => ['>=LEFT_MARGIN'=>$arParams['left_key'], '<=RIGHT_MARGIN'=>$arParams['right_key']]
			)
		);
		$arParams['id_edit'] = array();
		foreach($arRes as $ar_res)
		{
			$arParams['id_edit'][] = $ar_res['ID'];
		}
        //</editor-fold>

        //<editor-fold defaultstate="collapse" desc="6. Определяем в какую область перемещается узел и производим перенос">
        //Если перемещаемся выше и встаем сразу за родительским узлом
		if (($arParams['left_key_near'] < $arParams['left_key']) && ($arParams['level'] > $arParams['level_near']))
		{
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
			$res = $query->exec();
            if (!$res->getResult())
            {
                return false;
            }

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
            $res = $query->exec();
            if (!$res->getResult())
            {
                return false;
            }
		}
        //Если перемещаемся выше
		elseif ($arParams['left_key_near'] < $arParams['left_key'])
		{
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
			$res = $query->exec();
			if (!$res->getResult())
            {
                return false;
            }

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
			$res = $query->exec();
			if (!$res->getResult())
            {
                return false;
            }
		}
        //Если перемещаемся ниже
		else
		{
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
			$res = $query->exec();
			if (!$res->getResult())
            {
                return false;
            }

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
			$res = $query->exec();
			if (!$res->getResult())
            {
                return false;
            }
		}
        //</editor-fold>

        return true;
	}

    /**
     * Возвращает список узлов указанного уровня вложенности
     *
     * @param int   $iDepthLevel    Уровень вложенности. По-умолчанию 1, разделы, лежащие в корне
     * @param array $arSelect       Массив возвращаемых полей узлов. Если передан пустой массив,
     *                              будут выбраны поля по-молчанию: 'ID', 'LEFT_MARGIN', 'RIGHT_MARGIN', 'DEPTH_LEVEL'
     *
     * @return array|bool
     */
	final public static function getNodesByDepthLevel ($iDepthLevel=1, $arSelect=[])
    {
        $iDepthLevel = (int)$iDepthLevel;
        if ($iDepthLevel<=0)
        {
            $iDepthLevel=1;
        }
        if (empty($arSelect))
        {
            $arSelect = self::$defaultArSelect;
        }
        else
        {
            $arSelect = array_merge($arSelect,self::$defaultArSelect);
            $arSelect = array_unique($arSelect);
        }
        $arRes = static::getList([
            'select' => $arSelect,
            'filter' => ['DEPTH_LEVEL'=>$iDepthLevel],
            'order' => ['LEFT_MARGIN'=>'ASC']
        ]);

        return $arRes;
    }

	/**
	 * Проверка целостности таблицы. Если все в порядке, возвращает false.
	 * Если есть проблемы, возвращает массив проблемных записей по 6 проверкам
	 *
	 * @return array|bool Массив проблеммных записей, либо false
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
		$arResult = [];
		$helper = new SqlHelper(static::getTableName());

		//1. Левый ключ ВСЕГДА меньше правого;
		//Если все правильно то результата работы запроса не будет, иначе, получаем список идентификаторов неправильных строк;
		$res1 = static::getList(
			[
				'select' => ['ID'],
				'filter' => ['>=LEFT_MARGIN'=>'FIELD_RIGHT_MARGIN']
			]
		);
		if ($res1 && !empty($res1))
		{
			foreach ($res1 as $ar_res1)
			{
				$arResult['RULE1'][] = $ar_res1;
			}
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

	//</editor-fold>
 
}