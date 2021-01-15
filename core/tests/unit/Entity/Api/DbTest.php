<?php
require_once (dirname(__FILE__).'/../../../autoloader.php');

use Ms\Core\Api\Db;

class DbTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp ()
	{

	}

    /**
     * @covers \Ms\Core\Api\Db::getTableOrm
     */
	public function testGetTableOrm ()
	{
		$res = Db::getInstance()->getTableOrm (new \Ms\Core\Tables\UsersTable ());
		$this->assertInstanceOf ('Ms\Core\Entity\Db\Tables\ORMController', $res);
	}

    /**
     * @covers Db::getTableOrmByClass
     */
	public function testGetTableOrmByClass ()
	{
		$res = Db::getInstance()->getTableOrmByClass (\Ms\Core\Tables\UsersTable::class);
		$this->assertInstanceOf ('Ms\Core\Entity\Db\Tables\ORMController', $res);
	}

    /**
     * @covers Db::getTableHelper
     */
	public function testGetTableHelper ()
	{
		$res = Db::getInstance()->getTableHelper ();
		$this->assertInstanceOf ('Ms\Core\Entity\Helpers\TableHelper',$res);
	}
}
