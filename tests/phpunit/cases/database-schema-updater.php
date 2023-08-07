<?php
namespace Tests\TDBDataBaseSchemaUpdaterTestCase;

class TDBDataBaseSchemaUpdaterTestCase extends \WP_UnitTestCase
{

    public $tdb_db = null;
    public $schema = null;

    private $db_schema_updater = null;


    public function setUp(): void
    {
        parent::setUp();

        $this->tdb_db = new \TDB_DataBase('test', [
            'schema' => [
                'id' => [
                    'type' => 'bigint',
                    'length' => '20',
                    'auto_increment' => true,
                    'primary_key' => true,
                ],
                'title' => [
                    'type' => 'varchar',
                    'length' => '50',
                ],
            ]
        ]);

        $this->db_schema_updater = new \TDB_DataBase_Schema_Updater($this->tdb_db);

//        $this->schema = $this->tdb_db->schema;
    }

    function test_database_schema_updater() {

        global $wpdb;

        $this->assertTrue(class_exists('TDB_DataBase_Schema_Updater'), 'Database Schema Updater class  exists');


        $this->assertTrue(is_object(  $this->db_schema_updater  ), 'Database Schema Updater class can be instantiated');


        $result = $this->db_schema_updater->run();

        // Check if the schema update was successful
        $this->assertTrue($result, 'Database schema update was successful');


    }

   /* public function test_object_field_to_array()
    {
        // Example field definition similar to the one returned by DESCRIBE


        $field = (object) [
            'Field' => 'test_field',
            'Type' => 'varchar(255)',
            'Null' => 'NO',
            'Key' => 'MUL',
            'Default' => 'default_value',
            'Extra' => 'auto_increment',
        ];

        $expectedOutput = [
            'type' => 'varchar(255)',
            'length' => '255',
            'decimals' => 0,
            'format' => '',
            'options' => [],
            'nullable' => false,
            'unsigned' => null,
            'zerofill' => null,
            'binary' => null,
            'charset' => false,
            'collate' => false,
            'default' => 'default_value',
            'auto_increment' => true,
            'unique' => false,
            'primary_key' => false,
            'key' => true,
        ];

        // Assuming $this->db_schema_updater is an instance of TDB_DataBase_Schema_Updater
        $result = $this->db_schema_updater->object_field_to_array($field);

//        print_r($expectedOutput);
//        print_r($result);
//        die();

        $this->assertEquals($expectedOutput, $result);
    }*/
    
}