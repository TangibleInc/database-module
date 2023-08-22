<?php
namespace Tests\TDBEditViewTestCase;

class TDBEditViewTestCase extends \WP_UnitTestCase
{

    function test_edit_view()
    {
        $this->assertTrue(defined('TDB_VERSION'), 'Database module is loaded');

        // TODO: Rename "DataBase" to "Database"
        $this->assertTrue(class_exists('TDB_Edit_View'), 'Database class  exists');

        $dbEditView = new \TDB_Edit_View('test', [

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

        $this->assertTrue(is_object($dbEditView), 'Database Edit View class can be instantiated');

        $tdb_registered_tables = tdb_register_table('test',  ['schema' =>
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
                                                                        ]
                                                                    ]
        );



        // var_dump($tdb_registered_tables); die();

        /**
         * WPDieException: Sorry, you are not allowed to edit items of this type.
         */
        // $dbEditView->init();
    }
}
