<?php defined( 'ABSPATH' ) || exit;

class TDB_Table_Meta {

    /**
     * Table key.
     *
     * @since 1.0.0
     * @access public
     * @var string $name
     */
    public $name;

    /**
     * Table Meta database.
     *
     * @since 1.0.0
     * @access public
     * @var TDB_DataBase $db
     */
    public $db;

    /**
     * Table Meta table object.
     *
     * @since 1.0.0
     * @access public
     * @var TDB_Table $table
     */
    public $table;

    /**
     * TDB_Table_Meta constructor.
     * @param TDB_Table $table
     */
    public function __construct( $table ) {

        $this->table = $table;
        $this->name = $this->table->name . '_meta';

        $this->db = new TDB_DataBase( $this->name, array(
            'version' => 1,
            'global' => $this->table->db->global,
            'schema' => array(
                'meta_id' => array(
                    'type' => 'bigint',
                    'length' => 20,
                    'unsigned' => true,
                    'nullable' => false,
                    'auto_increment' => true,
                    'primary_key' => true
                ),
                $this->table->db->primary_key => array(
                    'type' => 'bigint',
                    'length' => 20,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 0,
                    'key' => true
                ),
                'meta_key' => array(
                    'type' => 'varchar',
                    'length' => 255,
                    'nullable' => true,
                    'default' => null,
                    'key' => true
                ),
                'meta_value' => array(
                    'type' => 'longtext',
                    'nullable' => true,
                    'default' => null
                ),
            )
        ) );

    }

}
