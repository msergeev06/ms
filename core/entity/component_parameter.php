<?php
/**
 * Ms\Core\Entity\ComponentParameter
 * Основной объект параметров компонентов
 *
 * @package Ms\Core
 * @subpackage Entity
 * @author Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright 2017 Mikhail Sergeev
 * @since 0.2.0
 */

namespace Ms\Core\Entity;

use Ms\Core\Lib\Tools;

class ComponentParameter
{
	/*
	"код параметра" => array(
		"PARENT" => "код группы",  // если нет - ставится ADDITIONAL_SETTINGS
		"NAME" => "название параметра на текущем языке",
		"TYPE" => "тип элемента управления, в котором будет устанавливаться параметр",
		"REFRESH" => "перегружать настройки или нет после выбора (N/Y)",
		"MULTIPLE" => "одиночное/множественное значение (N/Y)",
		"VALUES" => "массив значений для списка (TYPE = LIST)",
		"ADDITIONAL_VALUES" => "показывать поле для значений, вводимых вручную (Y/N)",
		"SIZE" => "число строк для списка (если нужен не выпадающий список)",
		"DEFAULT" => "значение по умолчанию",
		"COLS" => "ширина поля в символах",
	),
	TYPE:
	LIST - выбор из списка значений. Для типа LIST ключ VALUES содержит массив значений следующего вида:
		VALUES => array(
		   "ID или код, сохраняемый в настройках компонента" => "языкозависимое описание",
		),
	STRING - текстовое поле ввода.
	CHECKBOX - да/нет.
	CUSTOM - позволяет создавать кастомные элементы управления.
	FILE - выбор файла.
	$ext = 'wmv,wma,flv,vp6,mp3,mp4,aac,jpg,jpeg,gif,png';
	Array(
	   "PARENT" => "BASE_SETTINGS",
	   "NAME" => 'Выберите файл:',
	   "TYPE" => "FILE",
	   "FD_TARGET" => "F",
	   "FD_EXT" => $ext,
	   "FD_UPLOAD" => true,
	   "FD_USE_MEDIALIB" => true,
	   "FD_MEDIALIB_TYPES" => Array('video', 'sound')
	);
	 */

	/**
	 * Код параметра
	 * @var string
	 * @access protected
	 */
	protected $code;
	//protected $parent;

	/**
	 * Название параметра на текущем языке
	 * @var string
	 * @access protected
	 */
	protected $name;

	/**
	 * Тип элемента управления в котором будет устанавливаться параметер
	 * @var string
	 * @access protected
	 */
	protected $type;
	//protected $refresh;

	/**
	 * Флаг множественного значения (N/Y)
	 * @var string
	 * @access protected
	 */
	protected $multiple;

	/**
	 * Массив значений для списка (TYPE == 'LIST')
	 * @var array
	 * @access protected
	 */
	protected $values;
	//protected $add_values
	//protected $size;

	/**
	 * Значение по-умолчанию
	 * @var mixed|null
	 * @access protected
	 */
	protected $default=null;

	/**
	 * Ширина поля в символах
	 * @var int
	 * @access protected
	 */
	protected $cols;

	/**
	 * @var string
	 * @access protected
	 */
	protected $fdTarget;

	/**
	 * Расширение файла
	 * @var string
	 * @access protected
	 */
	protected $fdExt;

	/**
	 * @var bool
	 * @access protected
	 */
	protected $fdUpload;
	//protected $fdUseMedialib;
	//protected $fdMedialibTypes;

	/**
	 * Значение параметра
	 * @var mixed|null
	 * @access protected
	 */
	protected $value=null;

	/**
	 * Конструктор объекта параметра
	 *
	 * @param string $code      Код параметра
	 * @param array  $arParams  Массив параметров параметра
	 * @param mixed  $value     Значение параметра
	 */
	public function __construct ($code, array $arParams, $value=null)
	{
		//Код свойства
		$this->code = $code;
		if (isset($arParams['NAME']))
		{
			$this->name = $arParams['NAME'];
		}
		//Тип свойства
		if (isset($arParams['TYPE']))
		{
			$this->type = strtoupper($arParams['TYPE']);
			if ($this->type=='LIST')
			{
				//Возможные значения свойства, для списков
				if (isset($arParams['VALUES']))
				{
					$this->values = $arParams['VALUES'];
				}
			}
			elseif ($this->type=='FILE')
			{
				//Дополнительные параметры для файлов
				if (isset($arParams['FD_TARGET']))
				{
					$this->fdTarget = $arParams['FD_TARGET'];
				}
				if (isset($arParams['FD_EXT']))
				{
					$this->fdExt = $arParams['FD_EXT'];
				}
				if (isset($arParams['FD_UPLOAD']))
				{
					$this->fdUpload = $arParams['FD_UPLOAD'];
				}
			}
		}
		//Флаг множественного свойства
		if (isset($arParams['MULTIPLE']))
		{
			$this->multiple = $arParams['MULTIPLE'];
		}
		//Значение по-умолчанию, для незаданного свойства
		if (isset($arParams['DEFAULT']))
		{
			$this->default = $arParams['DEFAULT'];
		}

		//В качестве значения свойства берется либо переданное, либо по-умолчанию
		if (!is_null($value))
		{
			$this->value = $value;
		}
		elseif(!is_null($this->default))
		{
			$this->value = $this->default;
		}

		//Если тип свойства bool, преобразуем его в правильную форму
		if ($this->type == 'BOOL')
		{
			$this->value = Tools::validateBoolVal($this->value);
		}
	}

	/**
	 * Метод установки значение параметра
	 * @param mixed $value
	 * @access public
	 */
	public function setValue ($value)
	{
		$this->value = $value;
	}

	/**
	 * Метод получения значения параметра
	 *
	 * @access public
	 * @return mixed|null
	 */
	public function getValue ()
	{
		return $this->value;
	}
}