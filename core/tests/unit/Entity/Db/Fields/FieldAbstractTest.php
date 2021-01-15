<?php
require_once (dirname(__FILE__).'/../../../../autoloader.php');

use \Ms\Core\Entity\Db\Fields\FieldAbstract;

class TestField extends FieldAbstract
{
    public function fetchDataModification ($value)
    {
        // TODO: Implement fetchDataModification() method.
    }

    public function saveDataModification ($value)
    {
        // TODO: Implement saveDataModification() method.
    }

    public function getAllowedValues ()
    {
        // TODO: Implement getAllowedValues() method.
    }

    public function setAllowedValues (array $allowedValues): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setAllowedValues() method.
    }

    public function getAllowedValuesRange ()
    {
        // TODO: Implement getAllowedValuesRange() method.
    }

    public function setAllowedValuesRange (float $rangeMin, float $rangeMax): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setAllowedValuesRange() method.
    }

    public function getColumnName (): string
    {
        // TODO: Implement getColumnName() method.
    }

    public function setColumnName (string $columnName): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setColumnName() method.
    }

    public function getDefaultValue (string $type)
    {
        // TODO: Implement getDefaultValue() method.
    }

    public function setDefaultValue ($defaultValue): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setDefaultValue() method.
    }

    public function getSqlValue ($value): string
    {
        // TODO: Implement getSqlValue() method.
    }

    public function isAutocomplete (): bool
    {
        // TODO: Implement isAutocomplete() method.
    }

    public function isDefaultSql (string $type): bool
    {
        // TODO: Implement isDefaultSql() method.
    }

    public function isPrimary (): bool
    {
        // TODO: Implement isPrimary() method.
    }

    public function isRequired (): bool
    {
        // TODO: Implement isRequired() method.
    }

    public function isRequiredNull (): bool
    {
        // TODO: Implement isRequiredNull() method.
    }

    public function isUnique (): bool
    {
        // TODO: Implement isUnique() method.
    }

    public function setAutocomplete (bool $isAutocomplete = true): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setAutocomplete() method.
    }

    public function setDefaultCreate ($defaultCreate): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setDefaultCreate() method.
    }

    public function setDefaultCreateSql (bool $isDefaultCreateSql = true): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setDefaultCreateSql() method.
    }

    public function setDefaultInsert ($defaultInsert): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setDefaultInsert() method.
    }

    public function setDefaultInsertSql (bool $isDefaultInsertSql = true): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setDefaultInsertSql() method.
    }

    public function setDefaultUpdate ($defaultUpdate): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setDefaultUpdate() method.
    }

    public function setDefaultUpdateSql (bool $isDefaultUpdateSql = true): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setDefaultUpdateSql() method.
    }

    public function setDefaultValueSql (bool $isDefaultValueSql = true): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setDefaultValueSql() method.
    }

    public function setPrimary (bool $isPrimary = true): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setPrimary() method.
    }

    public function setRequired (bool $isRequired = true): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setRequired() method.
    }

    public function setRequiredNull (bool $isRequiredNull = true): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setRequiredNull() method.
    }

    public function setUnique (bool $isUnique = true): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setUnique() method.
    }

    public function setValues (array $arValues): \Ms\Core\Interfaces\Db\IField
    {
        // TODO: Implement setValues() method.
    }
}

class TestTable extends \Ms\Core\Entity\Db\Tables\TableAbstract
{
    public function getMap (): \Ms\Core\Entity\Db\Tables\FieldsCollection
    {
        $collection = new \Ms\Core\Entity\Db\Tables\FieldsCollection();
        $collection
            ->addField(new \Ms\Core\Entity\Db\Fields\IntegerField('ID'))
        ;

        return $collection;
    }
}

/**
 * Класс \FieldAbstractTest
 * Тесты класса \Ms\Core\Entity\Db\Fields\FieldAbstract
 */
class FieldAbstractTest extends \PHPUnit\Framework\TestCase
{
    /** @var null|TestField */
    protected $field = null;

    protected function setUp ()
    {
        $this->field = new TestField('TEST');
    }

    /**
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::setLink
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::getLink
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::setTitle
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::getTitle
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::setFetchDataModification
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::getFetchDataModification
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::setSaveDataModification
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::getSaveDataModification
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::setSerialized
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::isSerialized
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::setName
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::getName
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::setDataType
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::getDataType
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::setFieldType
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::getFieldType
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::serialize
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::unserialize
     * @covers \Ms\Core\Entity\Db\Fields\FieldAbstract::getClassName
     */
    public function testFieldMethods ()
    {
        $this->field->setLink(
            new \Ms\Core\Entity\Db\Links\LinkedField(
                new TestTable(),
                'ID',
                null,
                false
            )
        );
        $this->assertTrue(!is_null($this->field->getLink()));
        $this->field->setTitle('Test Title');
        $this->assertEquals('Test Title',$this->field->getTitle());
        $this->field->setFetchDataModification('methodName');
        $this->assertEquals('methodName',$this->field->getFetchDataModification());
        $this->field->setSaveDataModification('saveMethodName');
        $this->assertEquals('saveMethodName',$this->field->getSaveDataModification());
        $this->field->setSerialized();
        $this->assertTrue($this->field->isSerialized());
        $this->field->setName('TEST_NEW_NAME');
        $this->assertEquals('TEST_NEW_NAME',$this->field->getName());
        $this->field->setDataType('varchar');
        $this->assertEquals('varchar',$this->field->getDataType());
        $this->field->setFieldType('string');
        $this->assertEquals('string', $this->field->getFieldType());
        $arr = ['test'=>'data'];
        $arrTest = $this->field->unserialize($this->field->serialize($arr));
        $this->assertTrue(
            is_array($arrTest)
            && array_key_exists('test',$arrTest)
            && $arrTest['test']=='data'
        );
        $this->assertEquals(TestField::class,$this->field->getClassName());
    }
}