<?php
/**
 * MSergeev\Core\Entity\Db\Fields\Field
 * Сущность поля базы данных
 *
 * @package MSergeev\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 */

namespace MSergeev\Core\Entity\Db\Fields;
use MSergeev\Core\Exception;

/**
 * Class Field
 * @package MSergeev\Core\Entity

 * @var string                      $name                   Название поля в API
 * @var string                      $dataType               Тип поля в базе данных
 * @var string                      $fieldType              Тип поля в API
 * @var array                       $initialParameters      Параметры инициализации
 * @var string                      $title                  Описание поля
 * @var bool                        $isSerialized           Является ли значение поля сериализованным массивом
 * @var Field                       $parentField            Родительское поле
 * @var null|callback               $fetchDataModification  Функция обработки полученных значений из базы
 * @var null|callback               $saveDataModification   Функция обработки перед записью значений в базу
 * @var null|string                 $link                   Связь поля таблицы
 * @var null|string                 $linkOnUpdate           Действие при обновлении значения в связанной таблицы
 * @var null|string                 $linkOnDelete           Действие при удалении записи в связанной таблице
 *
 */
abstract class Field
{
	/**
	 * @var string Название поля в API
	 */
	protected $name;

	/**
	 * @var string Тип поля в базе данных
	 */
	protected $dataType;

	/**
	 * @var string Тип поля в API
	 */
	protected $fieldType;

	/**
	 * @var array Параметры инициализации
	 */
	protected $initialParameters;

	/**
	 * @var string Описание поля
	 */
	protected $title=null;

	/**
	 * @var bool Является ли значение поля сериализованным массивом
	 */
	protected $isSerialized = false;

	/**
	 * @var Field Родительское поле
	 */
	protected $parentField;

	/**
	 * @var null|callback Функция обработки полученных значений из базы
	 */
	protected $fetchDataModification = null;

	/**
	 * @var null|callback Функция обработки перед записью значений в базу
	 */
	protected $saveDataModification = null;

	/**
	 * @var null|string Связь поля таблицы
	 */
	protected $link=null;

	/**
	 * @var null|string Действие при обновлении значения в связанной таблицы
	 */
	protected $linkOnUpdate=null;

	/**
	 * @var null|string Действие при удалении записи в связанной таблице
	 */
	protected $linkOnDelete=null;

	/**
	 * Конструктор. Обрабатывает начальные параметры поля
	 *
	 * @param string $name       Название поля в API
	 * @param array  $parameters Параметры поля
	 * @since 0.1.0
	 */
	public function __construct($name, $parameters = array())
	{
		//Название поля таблицы
		try
		{
			if (!strlen($name))
			{
				throw new Exception\ArgumentNullException('$name');
			}
			else
			{
				$this->name = $name;
			}
		}
		catch(Exception\ArgumentNullException $e)
		{
			die($e->showException());
		}

		//Параметры поля таблицы
		$this->initialParameters = $parameters;

		//Описание поля таблицы (Comment)
		if (isset($parameters['title']))
		{
			$this->title = $parameters['title'];
		}

		//Связь поля текущей таблицы с primary другой таблицы
		if (isset($parameters['link']))
		{
			$this->link = $parameters['link'];
		}

		//Действие при изменении записи в связанной таблице
		/*
		 * CASCADE: если связанная запись родительской таблицы обновлена или удалена,
		 * и мы хотим чтобы соответствующие записи в таблицах-потомках также были обновлены или удалены.
		 * Что происходит с записью в родительской таблице, тоже самое произойдет с записью в дочерних
		 * таблицах. Однако не забывайте, что здесь можно легко попасться в ловушку бесконечного цикла.
		 * SET NULL:если запись в родительской таблице обновлена или удалена, а мы хоти чтобы в дочерней
		 * таблице некоторым занчениям было присвоено NULL (конечно если поле таблицы это позволяет)
		 * NO ACTION: смотри RESTRICT
		 * RESTRICT:если связанные записи родительской таблицы обновляются или удаляются со значениями
		 * которые уже/еще содержатся в соответствующих записях дочерней таблицы, то база данных не
		 * позволит изменять записи в родительской таблице. Обе команды NO ACTION и RESTRICT
		 * эквивалентны отсутствию подвыражений ON UPDATE or ON DELETE для внешних ключей.
		 * SET DEFAULT:На данный момент эта команда распознается парсером, но движок InnoDB никак на нее не реагирует.
		 */
		if (
			isset($parameters['on_update'])
			&& (in_array($parameters['on_update'],array('cascade','set_null','no_action','restrict','set_default')))
		)
		{
			$this->linkOnUpdate = $parameters['on_update'];
		}
		else
		{
			$this->linkOnUpdate = 'cascade';
		}

		//Действие при удалении записи в связанной таблице
		/*
		 * CASCADE: если связанная запись родительской таблицы обновлена или удалена,
		 * и мы хотим чтобы соответствующие записи в таблицах-потомках также были обновлены или удалены.
		 * Что происходит с записью в родительской таблице, тоже самое произойдет с записью в дочерних
		 * таблицах. Однако не забывайте, что здесь можно легко попасться в ловушку бесконечного цикла.
		 * SET NULL:если запись в родительской таблице обновлена или удалена, а мы хоти чтобы в дочерней
		 * таблице некоторым занчениям было присвоено NULL (конечно если поле таблицы это позволяет)
		 * NO ACTION: смотри RESTRICT
		 * RESTRICT:если связанные записи родительской таблицы обновляются или удаляются со значениями
		 * которые уже/еще содержатся в соответствующих записях дочерней таблицы, то база данных не
		 * позволит изменять записи в родительской таблице. Обе команды NO ACTION и RESTRICT
		 * эквивалентны отсутствию подвыражений ON UPDATE or ON DELETE для внешних ключей.
		 * SET DEFAULT:На данный момент эта команда распознается парсером, но движок InnoDB никак на нее не реагирует.
		 */
		if (
			isset($parameters['on_delete'])
			&& (in_array($parameters['on_delete'],array('cascade','set_null','no_action','restrict','set_default')))
		)
		{
			$this->linkOnDelete = $parameters['on_delete'];
		}
		else
		{
			$this->linkOnDelete = 'restrict';
		}

		//Функция, подготавливающая данные после получения их из базы данных
		if (isset($parameters['fetch_data_modification']))
		{
			$this->fetchDataModification = $parameters['fetch_data_modification'];
		}

		//Функция, подготавливающая данные перед добавлением их в базу данных
		if (isset($parameters['save_data_modification']))
		{
			$this->saveDataModification = $parameters['save_data_modification'];
		}

		//Если указано, считается что значение поля представляет собой сериализированный массив
		if (isset($parameters['serialized']) && $parameters['serialized'])
		{
			$this->isSerialized = $parameters['serialized'];
		}

		//Родительское поле
		if (isset($parameters['parent']))
		{
			$this->parentField = $parameters['parent'];
		}
	}

	/**
	 * Возвращает название поля в API
	 *
	 * @api
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Возвращает описание поля
	 *
	 * @api
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Возвращает тип поля в базы данных
	 *
	 * @api
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function getDataType()
	{
		return $this->dataType;
	}

	/**
	 * Возвращает тип поля в API
	 *
	 * @api
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function getFieldType()
	{
		return $this->fieldType;
	}

	/**
	 * Возвращает объект родительского поля
	 *
	 * @api
	 *
	 * @return Field
	 * @since 0.1.0
	 */
	public function getParentField()
	{
		return $this->parentField;
	}

	/**
	 * Возвращает строку - связь поля с другим полем
	 *
	 * @api
	 *
	 * @return null|string
	 * @since 0.1.0
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * Возвращает строку - действия при измененнии связанной записи другой таблицы
	 *
	 * @return null|string
	 * @since 0.2.0
	 */
	public function getLinkOnUpdate()
	{
		switch ($this->linkOnUpdate)
		{
			case 'cascade':
				return 'CASCADE';
			case 'set_null':
				return 'SET NULL';
			case 'no_action':
				return 'NO ACTION';
			case 'restrict':
				return 'RESTRICT';
			case 'set_default':
				return 'SET DEFAULT';
			default:
				return null;
		}
	}

	/**
	 * Возвращает строку - действия при удалении связанной записи другой таблицы
	 *
	 * @return null|string
	 * @since 0.2.0
	 */
	public function getLinkOnDelete()
	{
		switch ($this->linkOnDelete)
		{
			case 'cascade':
				return 'CASCADE';
			case 'set_null':
				return 'SET NULL';
			case 'no_action':
				return 'NO ACTION';
			case 'restrict':
				return 'RESTRICT';
			case 'set_default':
				return 'SET DEFAULT';
			default:
				return null;
		}
	}

	/**
	 * Сериализует массив
	 *
	 * @api
	 *
	 * @param array|string $value Массив
	 *
	 * @return string
	 * @since 0.1.0
	 */
	public function serialize($value)
	{
		if (!is_string($value))
		{
			$value = serialize($value);
		}

		return $value;
	}

	/**
	 * Десериализирует массив
	 *
	 * @api
	 *
	 * @param string $value Сериализованный массив
	 *
	 * @return array
	 * @since 0.1.0
	 */
	public function unserialize($value)
	{
		if (is_array($value))
		{
			return $value;
		}
		return unserialize($value);
	}

	/**
	 * Возвращает флаг, обозначающий факт того,
	 * является ли значение данного поля сериализованным массивом
	 *
	 * @return bool
	 * @since 0.1.0
	 */
	public function isSerialized ()
	{
		return $this->isSerialized;
	}

	/**
	 * Возвращает название функции для обработки значений полученных из базы данных
	 *
	 * @api
	 *
	 * @return callable|null
	 * @since 0.1.0
	 */
	public function getFetchDataModification ()
	{
		return $this->fetchDataModification;
	}

	/**
	 * Возвращает название функции для обработки значений перед сохранением в базу данных
	 *
	 * @api
	 *
	 * @return callable|null
	 * @since 0.1.0
	 */
	public function getSaveDataModification ()
	{
		return $this->saveDataModification;
	}

	/**
	 * Возвращает имя класса объекта
	 *
	 * @api
	 *
	 * @return string
	 * @since 0.2.0
	 */
	public function getClassName ()
	{
		return get_called_class();
	}
}