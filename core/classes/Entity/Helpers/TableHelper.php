<?php
/**
 * @package    SHF "Доброжил"
 * @subpackage Ms\Core
 * @author     Mikhail Sergeev <msergeev06@gmail.com>
 * @copyright  2020 Mikhail Sergeev
 */

namespace Ms\Core\Entity\Helpers;

use Ms\Core\Entity\Db\Fields;
use Ms\Core\Entity\Db\Links\ForeignKey;
use Ms\Core\Entity\Db\Links\LinkedField;
use Ms\Core\Entity\System\Application;
use Ms\Core\Entity\System\Multiton;
use Ms\Core\Entity\Type\Date;
use Ms\Core\Exceptions\Arguments\ArgumentTypeException;
use Ms\Core\Exceptions\SystemException;
use Ms\Core\Interfaces\Db\IField;
use Ms\Core\Tables\UsersTable;

/**
 * Класс Ms\Core\Entity\Helpers\TableHelper
 * Помощник описания полей таблиц
 */
class TableHelper extends Multiton
{
    /**
     * Возвращает сущность Fields\IntegerField для primary поля таблицы 'ID' (Ключ)
     *
     * @param string $field_name Свое имя поля
     *
     * @return IField
     * @unittest
     */
    public function primaryField (string $field_name = 'ID')
    {
        $field_name = strtoupper($field_name);

        $field = (new Fields\IntegerField($field_name))
            ->setPrimary()
            ->setAutocomplete()
            ->setTitle('Ключ')
        ;

        return $field;
    }

    /**
     * Возвращает сущность Fields\BooleanField для поля таблицы 'ACTIVE' (Активность)
     *
     * @param string $field_name Свое имя поля
     *
     * @return IField
     * @unittest
     */
    public function activeField (string $field_name = 'ACTIVE')
    {
        $field_name = strtoupper($field_name);

        $field = (new Fields\BooleanField($field_name))
            ->setRequired()
            ->setTitle('Активность')
        ;
        try
        {
            $field
                ->setDefaultCreate(true)
                ->setDefaultInsert(true)
            ;
        }
        catch (ArgumentTypeException $e)
        {
        }

        return $field;
    }

    /**
     * Возвращает сущность Fields\IntegerField для поля таблицы 'SORT' (Сортировка)
     *
     * @param string $field_name Свое имя поля
     *
     * @return IField
     * @unittest
     */
    public function sortField (string $field_name = 'SORT')
    {
        $field_name = strtoupper($field_name);

        $sortDefault = 500;

        $field = (new Fields\IntegerField($field_name))
            ->setRequired()
            ->setDefaultCreate($sortDefault)
            ->setDefaultInsert($sortDefault)
            ->setTitle('Сортировка')
        ;

        return $field;
    }

    /**
     * Возвращает сущность Fields\IntegerField для поля таблицы 'CREATED_BY' (Кем создан)
     *
     * @param string $field_name Свое имя поля
     *
     * @return IField
     * @unittest
     */
    public function createdByField (string $field_name = 'CREATED_BY')
    {
        $field_name = strtoupper($field_name);

        $user = Application::getInstance()->getUser();
        if ($user)
        {
            $userID = $user->getID();
        }
        else
        {
            $userID = 0;
        }

        $field = (new Fields\IntegerField($field_name))
            ->setRequired()
            ->setRequiredNull()
            ->setDefaultInsert($userID)
            ->setLink(
                (new LinkedField(
                    new UsersTable(),
                    'ID',
                    (new ForeignKey())
                        ->setOnUpdateCascade()
                        ->setOnDeleteSetNull()
                ))
            )
            ->setTitle('ID пользователя кем создан')
        ;

        return $field;
    }

    /**
     * Возвращает сущность Fields\DateTimeField для поля таблицы 'CREATED_DATE' (Дата создания)
     * Если указаны дополнительные параметры, они также добавляются к свойствам поля
     *
     * @param string $field_name
     *
     * @return IField
     * @unittest
     */
    public function createdDateField (string $field_name = 'CREATED_DATE')
    {
        $field_name = strtoupper($field_name);

        $field = (new Fields\DateTimeField($field_name))
            ->setRequired()
            ->setRequiredNull()
            ->setTitle('Дата создания')
        ;
        try
        {
            $field
                ->setDefaultInsert(new Date())
            ;
        }
        catch (SystemException $e)
        {
        }

        return $field;
    }

    /**
     * Возвращает сущность Fields\IntegerField для поля таблицы 'UPDATED_BY' (Кем изменен)
     * Если указаны дополнительные параметры, они также добавляются к свойствам поля
     *
     * @param string $field_name
     *
     * @return IField
     * @unittest
     */
    public function updatedByField (string $field_name = 'UPDATED_BY')
    {
        $field_name = strtoupper($field_name);

        $user = Application::getInstance()->getUser();
        if ($user)
        {
            $userID = $user->getID();
        }
        else
        {
            $userID = 0;
        }

        $field = (new Fields\IntegerField($field_name))
            ->setRequired()
            ->setRequiredNull()
            ->setDefaultInsert($userID)
            ->setLink(
                (new LinkedField(
                    new UsersTable(),
                    'ID',
                    (new ForeignKey())
                        ->setOnUpdateCascade()
                        ->setOnDeleteSetNull()
                ))
            )
            ->setTitle('ID пользователя, кем изменен')
        ;

        return $field;
    }

    /**
     * Возвращает сущность Fields\DateTimeField для поля таблицы 'UPDATED_DATE' (Дата изменения)
     * Если указаны дополнительные параметры, они также добавляются к свойствам поля
     *
     * @param string $field_name
     *
     * @return IField
     * @unittest
     */
    public function updatedDateField (string $field_name = 'UPDATED_DATE')
    {
        $field_name = strtoupper($field_name);

        $field = (new Fields\DateTimeField($field_name))
            ->setRequired()
            ->setRequiredNull()
            ->setTitle('Дата изменения')
        ;
        try
        {
            $field
                ->setDefaultInsert(new Date())
                ->setDefaultUpdate(new Date())
            ;
        }
        catch (SystemException $e)
        {
        }

        return $field;
    }

}