<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

/**
 * Класс Ms\Core\Entity\Db\Fields\LongtextField
 * Сущность поля базы данных, содержащего длинный текст
 */
class LongtextField extends StringField
{
    /**
     * Конструктор
     *
     * @param string $name Имя поля таблицы БД
     *
     * @throws \Ms\Core\Exception\ArgumentNullException
     */
	function __construct($name)
	{
		parent::__construct($name);

		$this->dataType = 'longtext';
		$this->fieldType = 'text';
	}
}