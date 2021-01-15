<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Options;

use Ms\Core\Api\ApiAdapter;
use Ms\Core\Entity\Db\Tables\ORMController;
use Ms\Core\Entity\System\Application;
use Ms\Core\Entity\System\Multiton;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Lib\Tools;
use Ms\Core\Tables\OptionsTable;

/**
 * Класс Ms\Core\Entity\System\Options
 * Класс работы с опциями приложения
 */
class Options extends Multiton
{
    /** @var OptionsCollection */
    protected $optionsCollection = null;

    /**
     * Защищенный конструктор класса Options
     */
    protected function __construct ()
    {
        $arDefaultOptions = $this->loadDefaultOptions();
        $this->optionsCollection = new OptionsCollection();

        if (!empty($arDefaultOptions))
        {
            foreach ($arDefaultOptions as $option => $value)
            {
                $option = strtoupper($option);
                $option = str_replace('MS_CORE_', '', $option);
                $this->getOptionsCollection()->setOptionValue(
                    'ms.core',
                    $option,
                    $value
                )
                ;
            }
        }
    }

    /**
     * Функция обертка, возвращающая значение указанной опции в виде булевого значения
     *
     * @param string $moduleName         Имя модуля, который установил опцию
     * @param string $optionName         Имя опции
     * @param bool   $optionDefaultValue Значение опции по-умолчанию
     *
     * @return null|bool Булево значение указанной опции, либо NULL
     */
    public function getOptionBool (string $moduleName, string $optionName, bool $optionDefaultValue = null)
    {
        $optionVal = $this->getOption($moduleName, $optionName, $optionDefaultValue);

        if (!is_null($optionVal))
        {
            return $this->validateBoolVal($optionVal);
        }

        return $optionDefaultValue;
    }

    /**
     * Функция обертка, возвращающая значение указанной опции в виде вещественного числа
     *
     * @param string     $moduleName         Имя модуля, который установил опцию
     * @param string     $optionName         Имя опции
     * @param null|float $optionDefaultValue Значение опции по-умолчанию
     *
     * @return null|float Вещественное значение указанной опции, либо NULL
     */
    public function getOptionFloat (string $moduleName, string $optionName, float $optionDefaultValue = null)
    {
        $optionVal = $this->getOption($moduleName, $optionName, $optionDefaultValue);

        if (!is_null($optionVal))
        {
            return (float)$optionVal;
        }

        return $optionDefaultValue;
    }

    /**
     * Возвращает полное имя опции вида БРЕНД.ИМЯМОДУЛЯ:ИМЯ_ОПЦИИ
     *
     * @param string $moduleName
     * @param string $optionName
     *
     * @return string
     */
    public function getOptionFullName (string $moduleName, string $optionName)
    {
        $moduleName = strtolower($moduleName);
        if ($moduleName == 'core')
        {
            $moduleName = 'ms.core';
        }

        return strtolower($moduleName . ':' . $optionName);
    }

    /**
     * Функция обертка, возвращающая значение указанной опции в виде целого числа
     *
     * @param string   $moduleName         Имя модуля, который установил опцию
     * @param string   $optionName         Имя опции
     * @param null|int $optionDefaultValue Значение опции по-умолчанию
     *
     * @return null|int Целочисленное значение указанной опции, либо NULL
     */
    public function getOptionInt (string $moduleName, string $optionName, int $optionDefaultValue = null)
    {
        $optionVal = $this->getOption($moduleName, $optionName, $optionDefaultValue);

        if (!is_null($optionVal))
        {
            return (int)$optionVal;
        }

        return $optionDefaultValue;
    }

    /**
     * Функция обертка, возвращающая значение указанной опции в виде строки
     *
     * @param string      $moduleName         Имя модуля, который установил опцию
     * @param string      $optionName         Имя опции
     * @param null|string $optionDefaultValue Значение опции по-умолчанию
     *
     * @return null|string Значение указанной опции, либо NULL
     */
    public function getOptionStr (string $moduleName, string $optionName, string $optionDefaultValue = null)
    {
        $optionVal = $this->getOption($moduleName, $optionName, $optionDefaultValue);

        if (!is_null($optionVal))
        {
            return (string)$optionVal;
        }

        return $optionDefaultValue;
    }

    /**
     * @return OptionsCollection
     */
    public function getOptionsCollection ()
    {
        return $this->optionsCollection;
    }

    /**
     * Функция добавляющая опщии по-умолчанию, без записи новых в DB
     *
     * @param string $moduleName  Имя модуля, установившего опцию
     * @param string $optionName  Название опции
     * @param mixed  $optionValue Значение опции
     *
     * @return $this
     */
    public function setDefaultOption (string $moduleName, string $optionName, $optionValue)
    {
        $this->getOptionsCollection()->setOptionValue(
            $moduleName,
            $optionName,
            $optionValue
        )
        ;

        return $this;
    }

    /**
     * Загружает опции по умолчанию для указанного модуля из указанного файла
     *
     * @param string $moduleName Имя модуля
     * @param string $filename   Полный путь к файлу от корня
     *
     * @return bool
     */
    public function setDefaultOptionsFromFile (string $moduleName, string $filename)
    {
        $moduleName = strtolower($moduleName);
        if (!file_exists($filename))
        {
            return false;
        }
        $arModuleDefaultOptions = include($filename);
        if (isset($arModuleDefaultOptions) && is_array($arModuleDefaultOptions) && !empty($arModuleDefaultOptions))
        {
            foreach ($arModuleDefaultOptions as $optionName => $optionValue)
            {
                $optionName = str_replace($moduleName.':','',$optionName);
                $this->setDefaultOption(
                    $moduleName,
                    $optionName,
                    $optionValue
                );
            }
        }

        return true;
    }

    /**
     * Функция добавляет новые опции в базу данных и в текущую сессию
     *
     * @param string $moduleName  Имя модуля, установившего опцию
     * @param string $optionName  Название опции
     * @param mixed  $optionValue Значение опции
     *
     * @return bool true - опция сохранена, false - ошибка сохранения
     */
    public function setOption (string $moduleName, string $optionName, $optionValue)
    {
        $optionFullName = $this->getOptionFullName($moduleName, $optionName);
        if (
            !$this->getOptionsCollection()->offsetExists($optionFullName)
            || $this->getOptionsCollection()->getOptionByFullName($optionFullName)->getOptionValue() != $optionValue
        )
        {
            $arInsert = [
                'MODULE_NAME' => $moduleName,
                'NAME'        => $optionName,
                'VALUE'       => (string)(is_bool($optionValue) ? (int)$optionValue : $optionValue)
            ];
            try
            {
                $result = $this->getOne($moduleName, $optionName);
            }
            catch (SystemException $e)
            {
                return false;
            }
            if ($result)
            {
                try
                {
                    $res = $this->update($result['ID'], ['VALUE' => $optionValue]);
                    if ($res->isSuccess())
                    {
                        $this->getOptionsCollection()->setOptionValue(
                            $moduleName,
                            $optionName,
                            $optionValue
                        )
                        ;

                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
                catch (SystemException $e)
                {
                    return false;
                }
            }
            else
            {
                try
                {
                    $res = $this->insert($arInsert);
                    if ($res->isSuccess())
                    {
                        $this->getOptionsCollection()->setOptionValue(
                            $moduleName,
                            $optionName,
                            $optionValue
                        )
                        ;

                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
                catch (SystemException $e)
                {
                    return false;
                }
            }
        }
        else
        {
            return true;
        }
    }

    /**
     * @param string $moduleName
     * @param string $optionName
     *
     * @return array|bool|string
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentTypeException
     * @throws \Ms\Core\Exceptions\Db\SqlQueryException
     */
    protected function getOne (string $moduleName, string $optionName)
    {
        return $this->getOrmOptionsTable()->getOne(
            [
                'filter' => [
                    'MODULE_NAME' => $moduleName,
                    'NAME'        => $optionName
                ]
            ]
        )
            ;
    }

    /**
     * Функция возвращает значение опции либо из массива,
     * либо из базы данных, сохранив в массиве
     *
     * @param string     $moduleName         Имя модуля, который установил опцию
     * @param string     $optionName         Имя опции
     * @param null|mixed $optionDefaultValue Значение опции по-умолчанию
     *
     * @return null|mixed Значение опции, либо false
     */
    protected function getOption (string $moduleName, string $optionName, $optionDefaultValue = null)
    {
        $optionFullName = $this->getOptionFullName($moduleName, $optionName);
        if ($this->getOptionsCollection()->offsetExists($optionFullName))
        {
            return $this->getOptionsCollection()->getOptionByFullName($optionFullName)->getOptionValue();
        }
        else
        {
            try
            {
                $result = $this->getOne($moduleName, $optionName);
            }
            catch (SystemException $e)
            {
                return null;
            }
            if ($result)
            {
                $option = new Option(
                    $result['MODULE_NAME'],
                    $result['NAME'],
                    $result['VALUE']
                );
                $this->getOptionsCollection()->setOption($option);

                return $option->getOptionValue();
            }
            elseif (!is_null($optionDefaultValue))
            {
                return $optionDefaultValue;
            }
            else
            {
                return null;
            }
        }
    }

    /**
     * @param array $arInsert
     *
     * @return \Ms\Core\Entity\Db\Result\DBResult|string
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     * @throws \Ms\Core\Exceptions\Db\SqlQueryException
     * @throws \Ms\Core\Exceptions\Db\ValidateException
     */
    protected function insert (array $arInsert)
    {
        return $this->getOrmOptionsTable()->insert($arInsert);
    }

    /**
     * Загружает опции по умолчанию
     *
     * @return array|mixed
     */
    protected function loadDefaultOptions ()
    {
        $path = Application::getInstance()->getSettings()->getCoreRoot() . '/default_options.php';
        $arOptions = [];
        if (file_exists($path))
        {
            $arOptions = include($path);
        }

        return $arOptions;
    }

    /**
     * @param int   $primary
     * @param array $arUpdate
     *
     * @return \Ms\Core\Entity\Db\Result\DBResult|string
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentNullException
     * @throws \Ms\Core\Exceptions\Arguments\ArgumentOutOfRangeException
     * @throws \Ms\Core\Exceptions\Db\SqlQueryException
     * @throws \Ms\Core\Exceptions\Db\ValidateException
     */
    protected function update (int $primary, array $arUpdate)
    {
        return $this->getOrmOptionsTable()->update($primary, $arUpdate);
    }

    /**
     * Преобразует переданное значение в тип bool
     * Помимо стандартных преобразований, символ 'Y' - TRUE, а 'N' - FALSE (в отличие от нативной функции PHP, где
     * оба этих значения трактовались бы как TRUE)
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function validateBoolVal ($value)
    {
        return Tools::validateBoolVal($value);
    }

    /**
     * @return ORMController
     */
    private function getOrmOptionsTable ()
    {
        return ApiAdapter::getInstance()->getDbApi()->getTableOrmByClass(OptionsTable::class);
    }
}