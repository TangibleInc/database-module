<?php defined( 'ABSPATH' ) || exit;

/**
 * Register REST API routes.
 */
add_action( 'rest_api_init', function() {

  global $tdb_registered_tables;

  if (!isset($tdb_registered_tables)) {
    $tdb_registered_tables = [];
  }

  foreach( $tdb_registered_tables as $tdb_table ) {

    // Skip tables that are not setup to being shown in rest
    if( ! $tdb_table->show_in_rest ) {
      continue;
    }

    $class = ! empty( $tdb_table->rest_controller_class ) ? $tdb_table->rest_controller_class : 'TDB_REST_Controller';

    // Skip if rest controller class doesn't exists
    if ( ! class_exists( $class ) ) {
      continue;
    }

    $controller = new $class( $tdb_table->name );

    // Check if controller is subclass of WP_REST_Controller to check if should call to the register_routes() function
    if ( ! is_subclass_of( $controller, 'WP_REST_Controller' ) ) {
      continue;
    }

    $controller->register_routes();
  }

  // Trigger custom rest API init hook
  do_action( 'tdb_rest_api_init' );
}, 9 );
