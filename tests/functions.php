<?php
namespace Tests\Database;

class Functions_TestCase extends \WP_UnitTestCase {

  function test_functions() {

    $this->assertTrue(function_exists('tdb_register_table'));

  }
}
