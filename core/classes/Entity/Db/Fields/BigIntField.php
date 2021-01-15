<?php
/**
 * @package SHF "Доброжил"
 * @subpackage Ms\Core
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2016 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Db\Fields;

/**
 * Класс Ms\Core\Entity\Db\Fields\BigIntField
 * Сущность поля базы данных, содержащего большое целое число
 */
class BigIntField extends IntegerField
{
	/**
	 * @var int $size Размерность поля bigint базы данных
	 */
	protected $size = 20;

    /**
     * Конструктор
     *
     * @param string $name Имя поля таблицы БД
     *
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     */
	public function __construct($name)
	{
		parent::__construct($name);

		$this->dataType = 'bigint';
		$this->fieldType = 'integer';
	}
}