<?php
namespace Tests\TDBEditViewTestCase;

class TDBEditViewTestCase extends \WP_UnitTestCase
{

  function tear_down() {
    parent::tear_down();
  
    // Prevent cached database tables from conflicting with tests
    global $tdb_tables_groups;
    $tdb_tables_groups = [];
  }
  
    function test_edit_view()
    {
        $this->assertTrue(defined('TDB_VERSION'), 'Database module is loaded');

        $this->assertTrue(class_exists('TDB_Edit_View'), 'Database class  exists');

        $db_edit_view = new \TDB_Edit_View('test', [

            'columns' => [
                'title' => [
                    'label' => __('Title'),
                    'sortable' => 'title', // ORDER BY title ASC
                ],
                'status' => [
                    'label' => __('Status'),
                    'sortable' => ['status', false], // ORDER BY status ASC
                ],
                'date' => [
                    'label' => __('Date'),
                    'sortable' => ['date', true], // ORDER BY date DESC
                ],
            ]

        ]);

        $this->assertTrue(is_object($db_edit_view), 'TDB_Edit_View can be instantiated');

        $test_table = tdb_register_table('test',  [

          /**
           * NOTE: The option "show_ui" is required and must be true. Otherwise,
           * TDB_Edit_View will throw and error, "Sorry, you are not allowed to
           * edit items of this type.".
           * 
           * @see edit-view.php, init()
           */
          'show_ui' => true,

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

        // IMPORTANT: Ensure database is created
        $test_table->db->maybe_upgrade();

        /**
         * TDB_Edit_View requires a user with capability to create and edit
         * items of this type. 
         */
        $user_id = $this->factory->user->create([
          'role' => 'administrator'
        ]);
        wp_set_current_user( $user_id );

        /**
         * On success, TDB_Edit_View will redirect to the edit screen. It is
         * necessary to catch and prevent this. Otherwise we get an error,
         * "Cannot modify header information", because PHPUnit has already sent
         * headers by this point.
         */

        $result = null;
        add_filter('wp_redirect', function($location, $status) use (&$result) {
          $result = [
            'location' => $location,
            'status' => $status,
          ];
          return null;
        }, 10, 2);

        $db_edit_view->init();

        $this->assertTrue(is_array($result), 'TDB_Edit_View called wp_redirect');

        $this->assertTrue($result['status'] === 302, 'TDB_Edit_View set redirect status 302');

        // @see tdb_get_edit_link()
        $edit_link_base = $test_table->views->edit->get_link();

        // var_dump($edit_link_base);

        $this->assertTrue(strpos($result['location'], $edit_link_base) === 0, 'TDB_Edit_View set redirect location to edit link');
      }
}
