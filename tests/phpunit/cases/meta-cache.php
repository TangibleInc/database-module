<?php
namespace Tests\TDBMetaCacheTestCase;

/**
 * Object meta must be cached in the same group that writes and deletes
 * invalidate. tdb_update_meta_cache() used to cache under the meta table's
 * database name while tdb_get_object_meta() and every add/update/delete use
 * the meta table's registered name, so a cached empty result outlived the
 * meta written after it.
 */
class TDBMetaCacheTestCase extends \WP_UnitTestCase {

  private $table;

  public function set_up(): void {
    parent::set_up();

    $this->table = tdb_register_table( 'meta_cache_test', [
      'singular' => 'Meta cache test',
      'plural'   => 'Meta cache tests',
      'show_ui'  => false,
      'supports' => [ 'meta' ],
      'schema'   => [
        'id' => [
          'type'           => 'bigint',
          'length'         => '20',
          'auto_increment' => true,
          'primary_key'    => true,
        ],
        'title' => [
          'type'   => 'varchar',
          'length' => '50',
        ],
      ],
    ] );

    $this->table->db->maybe_upgrade();
    $this->table->meta->db->maybe_upgrade();

    tdb_setup_table( $this->table );
  }

  public function tear_down() {
    tdb_reset_setup_table();
    parent::tear_down();

    // Prevent cached database tables from conflicting with tests
    global $tdb_tables_groups, $tdb_registered_tables;
    $tdb_tables_groups = [];
    unset( $tdb_registered_tables['meta_cache_test'] );
  }

  public function test_meta_added_after_an_empty_read_is_returned() {
    $id = tdb_insert_object( [ 'title' => 'One' ] );

    // An object with no meta yet: this read caches an empty result.
    $this->assertSame( '', tdb_get_object_meta( $id, 'colour', true ) );

    tdb_add_object_meta( $id, 'colour', 'blue' );

    $this->assertSame( 'blue', tdb_get_object_meta( $id, 'colour', true ) );
  }

  public function test_updated_meta_replaces_the_cached_value() {
    $id = tdb_insert_object( [ 'title' => 'Two' ] );
    tdb_add_object_meta( $id, 'colour', 'blue' );

    $this->assertSame( 'blue', tdb_get_object_meta( $id, 'colour', true ) );

    tdb_update_object_meta( $id, 'colour', 'green' );

    $this->assertSame( 'green', tdb_get_object_meta( $id, 'colour', true ) );
  }

  public function test_meta_is_cached_under_the_group_writes_invalidate() {
    $id = tdb_insert_object( [ 'title' => 'Three' ] );
    tdb_add_object_meta( $id, 'colour', 'blue' );

    tdb_get_object_meta( $id );

    $this->assertSame( [ 'colour' => [ 'blue' ] ], wp_cache_get( $id, $this->table->meta->name ) );
    $this->assertFalse( wp_cache_get( $id, $this->table->meta->db->table_name ) );
  }
}
