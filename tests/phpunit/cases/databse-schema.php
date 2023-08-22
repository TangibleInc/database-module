<?php
namespace Tests\TDBDataBaseSchemaTestCase;

class TDBDataBaseSchemaTestCase extends \WP_UnitTestCase {

    private $db = null;
    protected static $schema;


    public function set_up(): void {
        parent::set_up();

                self::$schema =
                     [
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
                    ];


        $this->db = new \TDB_DataBase_Schema( self::$schema  );

    }

    function tear_down() {
      parent::tear_down();
    
      // Prevent cached database tables from conflicting with tests
      global $tdb_tables_groups;
      $tdb_tables_groups = [];
    }
    
    function test_database_schema() {

        $this->assertTrue(class_exists('TDB_DataBase_Schema'), 'Database class  exists');



        $this->assertTrue(is_object($this->db), 'Database class can be instantiated');


    }

    public function test_field_array_to_schema()
    {
        $field_args = [
            'type' => 'INT',
            'length' => 11,
            'nullable' => false,
            'default' => 0,
            'unique' => true,
            'auto_increment' => true,
            'primary_key' => true,
            'key' => false,
            'unsigned' => true,
            'zerofill' => false
        ];
        $field_id = 'id';
        $expected_schema = '`'.$field_id.'` INT(11) UNSIGNED NOT NULL DEFAULT 0 UNIQUE AUTO_INCREMENT ';

        $field_array_to_schema = $this->db->field_array_to_schema($field_id, $field_args);

        $this->assertEquals($expected_schema, $field_array_to_schema);

        return $field_array_to_schema;
    }

    /**
     * @depends test_field_array_to_schema
     */
    public function test_string_schema_to_array($schemaString) {

        // Call the method to convert the schema string into an array
        $actualSchemaArray = $this->db->string_schema_to_array($schemaString);

        $this->assertIsArray($actualSchemaArray);

    }

    public function test_allowed_field_types(){

            // Call the method to get the actual array of allowed field types
            $actualAllowedTypes = $this->db->allowed_field_types();

            // Define the expected array of allowed field types
            $expectedAllowedTypes = array(
                'BIT',
                'TINYINT',
                'SMALLINT',
                'MEDIUMINT',
                'INT',
                'INTEGER',
                'BIGINT',
                'REAL',
                'DOUBLE',
                'FLOAT',
                'DECIMAL',
                'NUMERIC',
                'DATE',
                'TIME',
                'TIMESTAMP',
                'DATETIME',
                'YEAR',
                'CHAR',
                'VARCHAR',
                'BINARY',
                'VARBINARY',
                'TINYBLOB',
                'BLOB',
                'MEDIUMBLOB',
                'LONGBLOB',
                'TINYTEXT',
                'TEXT',
                'JSON',
            );

            // Perform the test by comparing the expected and actual arrays
            $this->assertEquals($expectedAllowedTypes, $actualAllowedTypes);
    }

    public function test_is_numeric(){
        // Test numeric types
        $this->assertTrue($this->db->is_numeric('TINYINT'));
        $this->assertTrue($this->db->is_numeric('INT'));
        $this->assertTrue($this->db->is_numeric('BIGINT'));
        $this->assertTrue($this->db->is_numeric('REAL'));
        $this->assertTrue($this->db->is_numeric('DOUBLE'));
        $this->assertTrue($this->db->is_numeric('FLOAT'));
        $this->assertTrue($this->db->is_numeric('DECIMAL'));
        $this->assertTrue($this->db->is_numeric('NUMERIC'));

        // Test non-numeric types
        $this->assertFalse($this->db->is_numeric('VARCHAR'));
        $this->assertFalse($this->db->is_numeric('CHAR'));
        $this->assertFalse($this->db->is_numeric('TEXT'));
        $this->assertFalse($this->db->is_numeric('DATE'));
        $this->assertFalse($this->db->is_numeric('TIME'));
        $this->assertFalse($this->db->is_numeric('JSON'));
    }
}