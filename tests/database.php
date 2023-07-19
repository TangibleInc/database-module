<?php
namespace Tests\Database;

class Database_TestCase extends \WP_UnitTestCase {

  function test_database() {

    $this->assertTrue(defined('TDB_VERSION'), 'Database module is loaded');

    // TODO: Rename "DataBase" to "Database"
    $this->assertTrue(class_exists('TDB_DataBase'), 'Database class  exists');

    $db = new \TDB_DataBase('test', [
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

    $this->assertTrue(is_object($db), 'Database class can be instantiated');

    $data = [
      'title' => 'Hello, world'
    ];

    $id = $db->insert($data);

    $this->assertTrue($id !== false, 'Insert data');
    $this->assertTrue(is_numeric($id), 'Insert data returns row ID');

    $row = $db->get( $id );

    $this->assertTrue(!empty($row), 'Get row by ID');
    $this->assertEquals($row, $data, 'Row is the same as inserted data');

  }
}
