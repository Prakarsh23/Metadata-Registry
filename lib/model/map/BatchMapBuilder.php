<?php


/**
 * This class adds structure of 'reg_batch' table to 'propel' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.model.map
 */
class BatchMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.BatchMapBuilder';

	/**
	 * The database map.
	 */
	private $dbMap;

	/**
	 * Tells us if this DatabaseMapBuilder is built so that we
	 * don't have to re-build it every time.
	 *
	 * @return     boolean true if this DatabaseMapBuilder is built, false otherwise.
	 */
	public function isBuilt()
	{
		return ($this->dbMap !== null);
	}

	/**
	 * Gets the databasemap this map builder built.
	 *
	 * @return     the databasemap
	 */
	public function getDatabaseMap()
	{
		return $this->dbMap;
	}

	/**
	 * The doBuild() method builds the DatabaseMap
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function doBuild()
	{
		$this->dbMap = Propel::getDatabaseMap('propel');

		$tMap = $this->dbMap->addTable('reg_batch');
		$tMap->setPhpName('Batch');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('RUN_TIME', 'RunTime', 'int', CreoleTypes::TIMESTAMP, false, null);

		$tMap->addColumn('RUN_DESCRIPTION', 'RunDescription', 'string', CreoleTypes::LONGVARCHAR, false, null);

		$tMap->addColumn('OBJECT_TYPE', 'ObjectType', 'string', CreoleTypes::VARCHAR, false, 20);

		$tMap->addColumn('OBJECT_ID', 'ObjectId', 'int', CreoleTypes::INTEGER, false, null);

		$tMap->addColumn('EVENT_TIME', 'EventTime', 'int', CreoleTypes::TIMESTAMP, false, null);

		$tMap->addColumn('EVENT_TYPE', 'EventType', 'string', CreoleTypes::VARCHAR, false, 20);

		$tMap->addColumn('EVENT_DESCRIPTION', 'EventDescription', 'string', CreoleTypes::LONGVARCHAR, false, null);

		$tMap->addColumn('REGISTRY_URI', 'RegistryUri', 'string', CreoleTypes::VARCHAR, false, 191);

	} // doBuild()

} // BatchMapBuilder
