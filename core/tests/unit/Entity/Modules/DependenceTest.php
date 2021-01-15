<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use \Ms\Core\Entity\Modules\Dependence;

/**
 * Класс \DependenceTest
 * Тесты класса \Ms\Core\Entity\Modules\Dependence
 */
class DependenceTest extends \PHPUnit\Framework\TestCase
{
    /** @var Dependence */
    protected $ob = null;

    protected function setUp ()
    {
        $this->ob = new Dependence('core');
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Dependence::setModuleName
     * @covers \Ms\Core\Entity\Modules\Dependence::getModuleName
     */
    public function testGetModuleName ()
    {
        $moduleName = 'ms.dobrozhil';
        try
        {
            $this->ob->setModuleName($moduleName);
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentException $e)
        {
            if (!\Ms\Core\Entity\Modules\Modules::getInstance()->checkModuleName($moduleName))
            {
                $this->assertTrue(true);
            }
            else
            {
                $this->assertTrue(false, $e->getMessage());
            }
            return;
        }
        $this->assertEquals($moduleName,$this->ob->getModuleName());
    }

    /**
     * Возвращает варианты указания версий:
     * 1. Точное соответствие (1.2.3)
     * 2. Диапазоны с операторами сравнения (<1.2.3)
     * 3. Комбинации этих операторов (>1.2.3 <1.3)
     * 4. Последняя доступная (1.2.*)
     * 5. Символ тильды (~1.2.3)
     * 6. Знак вставки (^1.2.3)
     * В вариант может входить несколько условий, разделенных запятыми.
     * Также версии могут начинатся с 'v.' или с 'v', указываемыми после операторов до номера мажорной версии
     *
     * @return array
     */
    public function versionProvider ()
    {
        /*
         * Варианты указания версий:
         * 1. Точное соответствие (1.2.3)
         * 2. Диапазоны с операторами сравнения (<1.2.3)
         * 3. Комбинации этих операторов (>1.2.3 <1.3)
         * 4. Последняя доступная (1.2.*)
         * 5. Символ тильды (~1.2.3) включает все версии до 1.3 не включительно
         * 6. Знак вставки (^1.2.3) означает "опасаться глобальных изменений" и включает все версии
         * вплоть до 2.0 не включительно
         * 7. Также перед мажорной версией может стоять символ "v" или "v."
         *
         * Отсюда следуют следующие правила определения ошибочного написания выражений версий:
         * 1. Если МАЖОРНАЯ версия равна 0 и версия ПАТЧА отсутствует, МИНОРНАЯ версия не может быть равна 0
         * 2. Если МАЖОРНАЯ версия равна 0 МИНОРНАЯ версия не может быть равна *
         * 3. Если МИНОРНАЯ версия равна *, версия ПАТЧА не должна быть указана
         * 4. Если установлен ОПЕРАТОР, то МИНОРНАЯ версия и версия ПАТЧА не могут быть равны *
         */
        $arOperator = ['','>', '>=', '<', '<=', '!=', '!', '=', '~', '^'];
        $arMajor = ['0','1'];
        $arMinor = ['.0','.1','.*'];
        $arPatch = ['','.0','.1','.*'];
        $arSymbolV = ['v','v.'];

        $arVersionsTrue = $arVersionsFalse = [];
        $bVersionTrue = true;
        for ($operator = 0; $operator < count($arOperator); $operator++)
        {
            for ($major = 0; $major < count ($arMajor); $major++)
            {
                for ($minor = 0; $minor < count ($arMinor); $minor++)
                {
                    for ($patch = 0; $patch < count ($arPatch); $patch++)
                    {
                        if (
                            //1. Если МАЖОРНАЯ версия равна 0 и версия ПАТЧА отсутствует, равна 0 или *, МИНОРНАЯ версия не может быть равна 0
                            ($major == 0 && ($patch == 0 || $patch == 1 || $patch == 3) && $minor == 0)
                            //2. Если МАЖОРНАЯ версия равна 0 МИНОРНАЯ версия не может быть равна *
                            || ($major == 0 && $minor == 2)
                            //3. Если МИНОРНАЯ версия равна *, версия ПАТЧА не должна быть указана
                            || ($minor == 2 && $patch > 0)
                            //4. Если установлен ОПЕРАТОР, то МИНОРНАЯ версия и версия ПАТЧА не могут быть равны *
                            || ($operator > 0 && ($minor == 2 || $patch == 3))
                        ){
                            $bVersionTrue = false;
                        }

                        try
                        {
                            $bPutV = (bool)random_int(0, 1);
                        }
                        catch (Exception $e)
                        {
                            $bPutV = false;
                        }
                        if ($bPutV)
                        {
                            try
                            {
                                $v = $arSymbolV[random_int(0, 1)];
                            }
                            catch (Exception $e)
                            {
                                $v = '';
                            }
                        }
                        else
                        {
                            $v = '';
                        }

                        if ($bVersionTrue)
                        {
                            $arVersionsTrue[] = [$arOperator[$operator] . $v . $arMajor[$major] . $arMinor[$minor] . $arPatch[$patch]];
                        }
                        else
                        {
                            $arVersionsFalse[] = [$arOperator[$operator] . $v . $arMajor[$major] . $arMinor[$minor] . $arPatch[$patch]];
                        }
                        $bVersionTrue = true;
                        // $arVersions[] = [$arOperator[$operator] . $v . $arMajor[$major] . $arMinor[$minor] . $arPatch[$patch]];
                    }
                }
            }
        }

        //Добавляем заведомо верные выражения версий
        $arVersions = $arVersionsTrue;
        //Добавляем заведомо неверные выражения версий
        $arNums = [];
        for ($i = 0; $i < 10; $i++)
        {
            try
            {
                $num = random_int(0, count($arVersionsFalse));
            }
            catch (Exception $e)
            {
                $i--;
                continue;
            }
            if (in_array($num,$arNums))
            {
                $i--;
                continue;
            }
            $arVersions[] = $arVersionsFalse[$i];
            $arNums[] = $num;
        }


        $arRangeVersions = [];
        for ($i = 0; $i < 4; $i++)
        {
            $strVersion = '';
            $bSearch = true;
            $bFirst = true;
            while ($bSearch)
            {
                try
                {
                    $ind = random_int(0, count($arVersions) - 1);
                }
                catch (Exception $e)
                {
                    continue;
                }
                $v = $arVersions[$ind][0];
                if ($bFirst)
                {
                    if (strpos($v, '>=') !== false || strpos($v, '>') !== false)
                    {
                        $strVersion .= $v . ' ';
                        $bFirst = false;
                    }
                }
                else
                {
                    if (strpos($v, '<=') !== false || strpos($v, '<') !== false)
                    {
                        $strVersion .= $v;
                        $bSearch = false;
                    }
                }
            }
            $arRangeVersions[] = $strVersion;
        }
        // print_r ($arRangeVersions);

        //Добавляем к списку проверок диапазоны версий
        foreach ($arRangeVersions as $range)
        {
            $arVersions[] = [$range];
        }

        //Получаем несколько значений версий и объединяем их через запятую, чтобы протестировать списки зависимостей
        $arListVersions = [];
        for ($i = 0; $i < 3; $i++)
        {
            try
            {
                $numInList = random_int(2, 4);
            }
            catch (\Exception $e)
            {
                $numInList = 3;
            }
            $arListVersions[$i] = [];
            $k = 0;
            for ($j = 0; $j < $numInList; $j++)
            {
                try
                {
                    $ind = random_int(0, count($arVersions) - 1);
                    $arListVersions[$i][] = $arVersions[$ind][0];
                }
                catch (Exception $e)
                {
                    if ($k < 100)
                    {
                        $j--;
                        $k++;
                    }
                }
            }
            $arListVersions[$i] = [implode(', ',$arListVersions[$i])];
        }
        // print_r($arListVersions);

        //Добавляем к списку проверок списки версий
        foreach ($arListVersions as $list)
        {
            $arVersions[] = $list;
        }

        return $arVersions;
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Dependence::setNeedVersion
     * @covers \Ms\Core\Entity\Modules\Dependence::getNeedVersion
     *
     * @dataProvider versionProvider
     *
     * @param string $version Выражение требуемой версии
     */
    public function testSetNeedVersion ($version)
    {
        // echo $version . PHP_EOL;
        try
        {
            $this->ob->setNeedVersion($version);
        }
        catch (\Ms\Core\Exceptions\Arguments\ArgumentException $e)
        {
            $arCheck = [];
            if (!\Ms\Core\Entity\Modules\Modules::getInstance()->checkVersionExpression($version, $arCheck))
            {
                if (!\Ms\Core\Entity\Modules\Modules::getInstance()->getErrorCollection()->isEmpty())
                {
                    $message = \Ms\Core\Entity\Modules\Modules::getInstance()->getErrorCollection()->getLast()->getMessage();
                }
                else
                {
                    $message = $e->getMessage();
                }
                $this->markTestSkipped('Версия ' . $version . ' пропущена: ' . $message);
            }
            else
            {
                $this->assertTrue(false, $e->getMessage());
            }
            return;
        }
        $this->assertEquals($version, $this->ob->getNeedVersion());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Dependence::setNeedInclude
     * @covers \Ms\Core\Entity\Modules\Dependence::isNeedInclude
     */
    public function testSetNeedInclude ()
    {
        $this->ob->setNeedInclude(true);
        $this->assertTrue($this->ob->isNeedInclude());
    }

    /**
     * @covers \Ms\Core\Entity\Modules\Dependence::setNeedInstalled
     * @covers \Ms\Core\Entity\Modules\Dependence::isNeedInstalled
     */
    public function testSetNeedInstalled ()
    {
        $this->ob->setNeedInstalled(true);
        $this->assertTrue($this->ob->isNeedInstalled());
    }
}