<?php
/**
 * Tangible Ajax List Table
 *
 * Encapsulates "Custom Tables - Ajax List Table" by rubengc <rubengcdev@gamil.com>
 * @see https://github.com/rubengc/ct-ajax-list-table
 *
 * Usage:
 *
 * ```php
 * tdb_render_ajax_list_table( $table, $query_args = array(), $view_args = array() );
 * ```
 */

// Add-on version is same as main module
define( 'TDB_AJAX_LIST_TABLE_VER', TDB_VERSION );
define( 'TDB_AJAX_LIST_TABLE_FILE', __FILE__ );
define( 'TDB_AJAX_LIST_TABLE_DIR', plugin_dir_path( __FILE__ ) );
define( 'TDB_AJAX_LIST_TABLE_URL', plugin_dir_url( __FILE__ ) );

require_once __DIR__ . '/ajax-functions.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/enqueue.php';
