<?php


/**
 * This class adds structure of 'reg_schema_property_element_history' table to 'propel' DatabaseMap object.
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
class SchemaPropertyElementHistoryMapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.SchemaPropertyElementHistoryMapBuilder';

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

		$tMap = $this->dbMap->addTable('reg_schema_property_element_history');
		$tMap->setPhpName('SchemaPropertyElementHistory');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('CREATED_AT', 'CreatedAt', 'int', CreoleTypes::TIMESTAMP, false, null);

		$tMap->addColumn('UPDATED_AT', 'UpdatedAt', 'int', CreoleTypes::TIMESTAMP, false, null);

		$tMap->addForeignKey('CREATED_USER_ID', 'CreatedUserId', 'int', CreoleTypes::INTEGER, 'users', 'ID', false, null);

		$tMap->addColumn('ACTION', 'Action', 'string', CreoleTypes::CHAR, false, null);

		$tMap->addForeignKey('SCHEMA_PROPERTY_ELEMENT_ID', 'SchemaPropertyElementId', 'int', CreoleTypes::INTEGER, 'reg_schema_property_element', 'ID', false, null);

		$tMap->addForeignKey('SCHEMA_PROPERTY_ID', 'SchemaPropertyId', 'int', CreoleTypes::INTEGER, 'reg_schema_property', 'ID', false, null);

		$tMap->addForeignKey('SCHEMA_ID', 'SchemaId', 'int', CreoleTypes::INTEGER, 'reg_schema', 'ID', false, null);

		$tMap->addForeignKey('PROFILE_PROPERTY_ID', 'ProfilePropertyId', 'int', CreoleTypes::INTEGER, 'profile_property', 'ID', false, null);

		$tMap->addColumn('OBJECT', 'Object', 'string', CreoleTypes::LONGVARCHAR, false, null);

		$tMap->addForeignKey('RELATED_SCHEMA_PROPERTY_ID', 'RelatedSchemaPropertyId', 'int', CreoleTypes::INTEGER, 'reg_schema_property', 'ID', false, null);

		$tMap->addColumn('LANGUAGE', 'Language', 'string', CreoleTypes::CHAR, true, 6);

		$tMap->addForeignKey('STATUS_ID', 'StatusId', 'int', CreoleTypes::INTEGER, 'reg_status', 'ID', false, null);

		$tMap->addColumn('CHANGE_NOTE', 'ChangeNote', 'string', CreoleTypes::LONGVARCHAR, false, null);

		$tMap->addForeignKey('IMPORT_ID', 'ImportId', 'int', CreoleTypes::INTEGER, 'reg_file_import_history', 'ID', false, null);

	} // doBuild()

} // SchemaPropertyElementHistoryMapBuilder
