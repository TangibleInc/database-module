<?php
namespace Tests\Database;

/**
 * @see ../database.php
 * @see https://developer.wordpress.org/reference/classes/wpdb
 */
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

    // IMPORTANT: Ensure database is created
    $db->maybe_upgrade();



    // Insert

    $data = [
      'title' => 'Hello, world'
    ];

    $id = $db->insert($data);

    $this->assertTrue($id !== false, 'Insert data');
    $this->assertTrue(is_numeric($id), 'Insert data returns row ID');


    // Get

    $row = $db->get( $id );
    $this->assertTrue(!is_null($row), 'Get row by ID');

    foreach ($data as $key => $value) {
      $this->assertEquals($row->$key, $data[$key], 'Row has the same properties as inserted data');
    }


    // Update

    $new_data = [
      'title' => 'Good morning, starshine'
    ];

    $result = $db->update($new_data, [
      'id' => $id
    ]);

    $this->assertTrue($result !== false, 'Update data');
    $this->assertEquals( 1, $result, 'Update data returns number of changed rows');

    $row = $db->get( $id );
    $this->assertTrue(!is_null($row), 'Get row by ID');

    foreach ($new_data as $key => $value) {
      $this->assertEquals($row->$key, $new_data[$key], 'Row has the same properties as updated data');
    }


    // Delete

    $result = $db->delete([
      'id' => $id
    ]);

    $this->assertTrue($result !== false, 'Delete data');
    $this->assertTrue($result===1, 'Delete data returns number of deleted rows');

    $row = $db->get( $id );
    $this->assertTrue(is_null($row), 'Delete row should not exist');
  }
}
