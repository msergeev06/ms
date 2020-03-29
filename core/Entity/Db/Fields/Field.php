<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

use Ms\Core\Exception;

/**
 * Абстрактный класс Ms\Core\Entity\Db\Fields\Field
 * Сущность поля базы данных
 */
abstract class Field
{
    const FOREIGN_CASCADE = 'cascade';
    const FOREIGN_SET_NULL = 'set_null';
    const FOREIGN_NO_ACTION = 'no_action';
    const FOREIGN_RESTRICT = 'restrict';
    const FOREIGN_SET_DEFAULT = 'set_default';

    const FOREIGN_LIST = [
        self::FOREIGN_CASCADE,
        self::FOREIGN_SET_NULL,
        self::FOREIGN_NO_ACTION,
        self::FOREIGN_RESTRICT,
        self::FOREIGN_SET_DEFAULT
    ];

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
	protected $initialParameters = [];

	/**
	 * @var string|null Описание поля
	 */
	protected $title=null;

	/**
	 * @var bool Является ли значение поля сериализованным массивом
	 */
	protected $isSerialized = false;

	/**
	 * @var Field|null Родительское поле
	 */
	protected $parentField = null;

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
	 * @var string Действие при обновлении значения в связанной таблицы
	 */
	protected $linkOnUpdate=self::FOREIGN_CASCADE;

	/**
	 * @var string Действие при удалении записи в связанной таблице
	 */
	protected $linkOnDelete=self::FOREIGN_CASCADE;

	/**
	 * @var bool Флаг, обозначающий, что поле связи с другой таблицей не является FOREIGN KEY
	 */
	protected $linkNotForeignKey = false;

    /**
     * Конструктор. Обрабатывает начальные параметры поля
     *
     * @param string $name Имя поля таблицы БД
     *
     * @throws \Ms\Core\Exception\ArgumentNullException
     */
	public function __construct($name)
	{
		//Название поля таблицы
        if (empty($name))
        {
            throw new Exception\ArgumentNullException('name');
        }
        else
        {
            $this->name = $name;
        }
	}

    /**
     * Устанавливает связь поля с записью в той же, либо другой таблице
     *
     * @param string $link Связь вида <имя_таблицы>.<имя_поля>
     *
     * @return $this
     * @throws \Ms\Core\Exception\ArgumentOutOfRangeException
     */
	public function setLink (string $link)
	{
		if (strpos($link,'.') !== false)
		{
			$this->link = $link;
		}
		else
		{
			throw new Exception\ArgumentOutOfRangeException('link','table.field');
		}

		return $this;
	}

    /**
     * Устанавливает действие для FOREIGN KEY при обновлении связанного поля
     *
     * @param string $linkOnUpdate
     *
     * @return $this
     * @throws \Ms\Core\Exception\ArgumentOutOfRangeException
     */
	protected function setForeignOnUpdate (string $linkOnUpdate = self::FOREIGN_CASCADE)
	{
		$linkOnUpdate = strtolower($linkOnUpdate);
		if (in_array($linkOnUpdate,self::FOREIGN_LIST))
		{
			$this->linkOnUpdate = $linkOnUpdate;
		}
		else
		{
			throw new Exception\ArgumentOutOfRangeException(
			    'linkOnUpdate',
                implode('|',self::FOREIGN_LIST)
            );
		}

		return $this;
	}

    /**
     * Устанавливает действие CASCADE для FOREIGN KEY при обновлении связанного поля
     *
     * @return $this
     */
	public function setForeignOnUpdateCascade ()
	{
		$this->linkOnUpdate = self::FOREIGN_CASCADE;

		return $this;
	}

    /**
     * Устанавливает действие SET_NULL для FOREIGN KEY при обновлении связанного поля
     *
     * @return $this
     */
	public function setForeignOnUpdateSetNull ()
	{
		$this->linkOnUpdate = self::FOREIGN_SET_NULL;

		return $this;
	}

    /**
     * Устанавливает действие NO_ACTION для FOREIGN KEY при обновлении связанного поля
     *
     * @return $this
     */
	public function setForeignOnUpdateNoAction ()
	{
		$this->linkOnUpdate = self::FOREIGN_NO_ACTION;

		return $this;
	}

    /**
     * Устанавливает действие RESTRICT для FOREIGN KEY при обновлении связанного поля
     *
     * @return $this
     */
	public function setForeignOnUpdateRestrict ()
	{
		$this->linkOnUpdate = self::FOREIGN_RESTRICT;

		return $this;
	}

    /**
     * Устанавливает действие SET_DEFAULT для FOREIGN KEY при обновлении связанного поля
     *
     * @return $this
     */
	public function setForeignOnUpdateSetDefault ()
	{
		$this->linkOnUpdate = 'set_default';

		return $this;
	}

    /**
     * Устанавливает действие для FOREIGN KEY при удалении связанного поля
     *
     * @param string $linkOnDelete
     *
     * @return $this
     * @throws \Ms\Core\Exception\ArgumentOutOfRangeException
     */
	protected function setForeignOnDelete (string $linkOnDelete = self::FOREIGN_CASCADE)
	{
		$linkOnDelete = strtolower($linkOnDelete);
		if (in_array($linkOnDelete,self::FOREIGN_LIST))
		{
			$this->linkOnDelete = $linkOnDelete;
		}
		else
		{
			throw new Exception\ArgumentOutOfRangeException(
			    'linkOnDelete',
                implode('|',self::FOREIGN_LIST)
            );
		}

		return $this;
	}

    /**
     * Устанавливает действие CASCADE для FOREIGN KEY при удалении связанного поля
     *
     * @return $this
     */
	public function setForeignOnDeleteCascade ()
	{
		$this->linkOnDelete = self::FOREIGN_CASCADE;

		return $this;
	}

    /**
     * Устанавливает действие SET_NULL для FOREIGN KEY при удалении связанного поля
     *
     * @return $this
     */
	public function setForeignOnDeleteSetNull ()
	{
		$this->linkOnDelete = self::FOREIGN_SET_NULL;

		return $this;
	}

    /**
     * Устанавливает действие NO_ACTION для FOREIGN KEY при удалении связанного поля
     *
     * @return $this
     */
	public function setForeignOnDeleteNoAction ()
	{
		$this->linkOnDelete = self::FOREIGN_NO_ACTION;

		return $this;
	}

    /**
     * Устанавливает действие RESTRICT для FOREIGN KEY при удалении связанного поля
     *
     * @return $this
     */
	public function setForeignOnDeleteRestrict ()
	{
		$this->linkOnDelete = self::FOREIGN_RESTRICT;

		return $this;
	}

    /**
     * Устанавливает действие SET_DEFAULT для FOREIGN KEY при удалении связанного поля
     *
     * @return $this
     */
	public function setForeignOnDeleteSetDefault ()
	{
		$this->linkOnDelete = self::FOREIGN_SET_DEFAULT;

		return $this;
	}

    /**
     * Устанавливает флаг того, что поле связано, но не является FOREIGN KEY
     *
     * @param bool $linkNotForeignKey Флаг отсутствия FOREIGN KEY
     *
     * @return $this
     */
	public function setLinkNotForeignKey ($linkNotForeignKey = true)
	{
		$this->linkNotForeignKey = $linkNotForeignKey;

		return $this;
	}

    /**
     * Устанавливает описание поля
     *
     * @param string $title
     *
     * @return $this
     */
	public function setTitle (string $title)
	{
		$this->title = $title;

		return $this;
	}

    /**
     * Устанавливает имя метода, который преобразует полученные из БД данные поля
     *
     * @param string $methodName Имя метода
     *
     * @return $this
     */
	public function setFetchDataModification (string $methodName)
	{
		$this->fetchDataModification = $methodName;

		return $this;
	}

    /**
     * Устанавливает имя метода, который преобразует данные поля, для сохранения в БД
     *
     * @param string $methodName Имя метода
     *
     * @return $this
     */
	public function setSaveDataModification (string $methodName)
	{
		$this->fetchDataModification = $methodName;

		return $this;
	}

    /**
     * Устанавливает флаг того, что поле содержит сериализованные данные
     *
     * @param bool $isSerialized
     *
     * @return $this
     */
	public function setSerialized (bool $isSerialized = true)
	{
		$this->isSerialized = $isSerialized;

		return $this;
	}

    /**
     * Устанавливает родительское поле
     * //TODO: Что это и для чего?
     *
     * @param string $parentField
     *
     * @return $this
     */
	public function setParentField (string $parentField)
	{
		$this->parentField = $parentField;

		return $this;
	}

	/**
	 * Возвращает название поля в коде
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Возвращает описание поля
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Возвращает тип поля в БД
	 *
	 * @return string
	 */
	public function getDataType()
	{
		return $this->dataType;
	}

	/**
	 * Возвращает тип поля в API
	 *
	 * @return string
	 */
	public function getFieldType()
	{
		return $this->fieldType;
	}

	/**
	 * Возвращает объект родительского поля
	 *
	 * @return Field
	 */
	public function getParentField()
	{
		return $this->parentField;
	}

	/**
	 * Возвращает строку - связь поля с другим полем
	 *
	 * @return null|string
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * Возвращает строку - действия при измененнии связанной записи другой таблицы
	 *
	 * @return null|string
	 */
	public function getLinkOnUpdate()
	{
	    if (!is_null($this->linkOnUpdate))
        {
            return strtoupper(str_replace('_',' ',$this->linkOnUpdate));
        }

        return null;
	}

	/**
	 * Возвращает строку - действия при удалении связанной записи другой таблицы
	 *
	 * @return null|string
	 */
	public function getLinkOnDelete()
	{
        if (!is_null($this->linkOnDelete))
        {
            return strtoupper(str_replace('_',' ',$this->linkOnDelete));
        }

        return null;
	}

	/**
	 * Сериализует массив
	 *
	 * @param array|string $value Массив
	 *
	 * @return string
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
	 * @param string $value Сериализованный массив
	 *
	 * @return array
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
	 */
	public function isSerialized ()
	{
		return $this->isSerialized;
	}

	/**
	 * Возвращает название функции для обработки значений полученных из базы данных
	 *
	 * @return callable|null
	 */
	public function getFetchDataModification ()
	{
		return $this->fetchDataModification;
	}

	/**
	 * Возвращает название функции для обработки значений перед сохранением в базу данных
	 *
	 * @return callable|null
	 */
	public function getSaveDataModification ()
	{
		return $this->saveDataModification;
	}

	/**
	 * Возвращает имя класса объекта
	 *
	 * @return string
	 */
	public function getClassName ()
	{
		return get_called_class();
	}

	/**
	 * Возвращает TRUE, если связь является FOREIGN KEY, иначе FALSE
	 *
	 * @return bool
	 */
	public function isLinkForeignKey ()
	{
		return !$this->linkNotForeignKey;
	}
}