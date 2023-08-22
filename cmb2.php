<?php defined( 'ABSPATH' ) || exit;
/**
 * Compatibility with CMB2
 */

if (!class_exists('CMB2_Boxes')) return;

/**
 * Check if should override on admin init action
 *
 * @since 1.0.0
 *
 * @param $tdb_table_name
 * @param $object
 */
function tdb_cmb2_admin_init() {

    global $tdb_registered_tables, $tdb_table, $tdb_cmb2_override, $pagenow;

    // Setup a custom global to meet that we need to override it
    $tdb_cmb2_override = false;

    // Check if is on admin.php
    if( $pagenow !== 'admin.php' ) {
        return;
    }

    // Check if isset page query parameter
    if( ! isset( $_GET['page'] ) ) {
        return;
    }

    foreach( $tdb_registered_tables as $tdb_registered_table ) {

        // Check if is edit page slug
        if( $_GET['page'] === $tdb_registered_table->views->edit->get_slug() ) {
            // Let know to this compatibility module it needs to operate
            $tdb_cmb2_override = true;
        }
    }

}
add_action( 'admin_init', 'tdb_cmb2_admin_init', 1 );

/**
 * Check if should override on add meta boxes action
 *
 * @since 1.0.0
 *
 * @param $tdb_table_name
 * @param $object
 */
function tdb_cmb2_add_meta_boxes( $tdb_table_name, $object ) {

    global $tdb_registered_tables, $tdb_table, $tdb_cmb2_override;

    // If not is a registered table, return
    if( ! isset( $tdb_registered_tables[$tdb_table_name] ) ) {
        return;
    }

    // If not object given, return
    if( ! $object ) {
        return;
    }

    $primary_key = $tdb_table->db->primary_key;

    // Setup a false post var to allow CMB2 trigger cmb2_override_meta_value hook
    $_REQUEST['post'] = $object->$primary_key;

    // Let know to this compatibility module it needs to operate
    $tdb_cmb2_override = true;

    // Fix: CMB2 stop enqueuing their assets so need to add it again
    CMB2_Hookup::enqueue_cmb_css();
    CMB2_Hookup::enqueue_cmb_js();

}
add_action( 'add_meta_boxes', 'tdb_cmb2_add_meta_boxes', 10, 2 );

/**
 * On save an object, let it know to CMB2
 *
 * @since 1.0.0
 *
 * @param $object_id
 * @param $object
 */
function tdb_cmb2_save_object( $object_id, $object ) {

    global $tdb_registered_tables, $tdb_table, $tdb_cmb2_override;

    // Return if CMB2 not exists
    if( ! class_exists( 'CMB2' ) ) {
        return;
    }

    // Return if user is not allowed
    if ( ! current_user_can( $tdb_table->cap->edit_item, $object_id ) ) {
        return;
    }

    // Setup a custom global to meet that we need to override it
    $tdb_cmb2_override = true;

    // Loop all registered boxes
    foreach( CMB2_Boxes::get_all() as $cmb ) {

        // Skip meta boxes that do not support this TDB_Table
        if( ! in_array( $tdb_table->name, $cmb->meta_box['object_types'] ) ) {
            continue;
        }

        // Take a trip to reading railroad – if you pass go collect $200
        $cmb->save_fields( $object_id, 'post', $_POST );
    }

}
add_action( 'tdb_save_object', 'tdb_cmb2_save_object', 10, 2 );

/**
 * Override the CMB2 field value
 *
 * @since 1.0.0
 *
 * @param $value
 * @param $object_id
 * @param $args
 * @param $field
 *
 * @return mixed|string
 */
function tdb_cmb2_override_meta_value( $value, $object_id, $args, $field ) {

    global $tdb_registered_tables, $tdb_table, $tdb_cmb2_override;

    if( ! is_a( $tdb_table, 'TDB_Table' ) ) {
        return $value;
    }

    if( $tdb_cmb2_override !== true ) {
        return $value;
    }

    $object = (array) tdb_get_object( $object_id );

    // Check if is a main field
    if( isset( $object[$args['field_id']] ) ) {
        return $object[$args['field_id']];
    }

    // If not is a main field and TDB_Table supports meta data, then try to get its value from meta table
    if( in_array( 'meta', $tdb_table->supports ) ) {
        return tdb_get_object_meta( $object_id, $args['field_id'], ( $args['single'] || $args['repeat'] ) );
    }

    return '';
}
add_filter( 'cmb2_override_meta_value', 'tdb_cmb2_override_meta_value', 10, 4 );

/**
 * Override the CMB2 field value save
 *
 * @since 1.0.0
 *
 * @param $check
 * @param $args
 * @param $field_args
 * @param $field
 *
 * @return bool|false|int
 */
function tdb_cmb2_override_meta_save( $check, $args, $field_args, $field ) {

    global $tdb_registered_tables, $tdb_table, $tdb_cmb2_override;

    if( $tdb_cmb2_override !== true ) {
        return $check;
    }

    $object = (array) tdb_get_object( $args['id'] );

    // If not is a main field and TDB_Table supports meta data, then try to save the given value to the meta table
    // Note: Main fields are automatically stored by the save method on the TDB_Edit_View edit screen
    if( ! isset( $object[$args['field_id']] ) && in_array( 'meta', $tdb_table->supports ) ) {

        // Add metadata if not single
        if ( ! $args['single'] ) {
            return tdb_add_object_meta( $args['id'], $args['field_id'], $args['value'], false );
        }

        // Delete meta if we have an empty array
        if ( is_array( $args['value'] ) && empty( $args['value'] ) ) {
            return tdb_delete_object_meta( $args['id'], $args['field_id'], $field->value );
        }

        // Update metadata
        return tdb_update_object_meta( $args['id'], $args['field_id'], $args['value'] );

    }

    return $check;

}
add_filter( 'cmb2_override_meta_save', 'tdb_cmb2_override_meta_save', 10, 4 );

/**
 * Override the CMB2 field value remove
 *
 * @since 1.0.0
 *
 * @param $check
 * @param $args
 * @param $field_args
 * @param $field
 *
 * @return bool|false|int
 */
function tdb_cmb2_override_meta_remove( $check, $args, $field_args, $field ) {

    global $tdb_registered_tables, $tdb_table, $tdb_cmb2_override, $wpdb;

    if( $tdb_cmb2_override !== true ) {
        return $check;
    }

    $object = (array) tdb_get_object( $args['id'] );

    // If not is a main field and TDB_Table supports meta data, then try to remove the given value to the meta table
    // Note: Main fields are automatically managed by the save method on the TDB_Edit_View edit screen
    if( ! isset( $object[$args['field_id']] ) && in_array( 'meta', $tdb_table->supports ) ) {

      if( $field_args['multiple'] && ! $field_args['repeatable'] ) {
        // Delete multiple entries
        $meta_table_name = $tdb_table->meta->db->table_name;
        $primary_key = $tdb_table->db->primary_key;

        $where = array(
            'meta_key' => $args['field_id']
        );

        $where[$primary_key] = $args['id'];

        return $wpdb->delete( $meta_table_name, $where );

      } else {
        // Delete single entry
        return tdb_delete_object_meta( $args['id'], $args['field_id'], $field->value );
      }
    }

    return $check;

}
add_filter( 'cmb2_override_meta_remove', 'tdb_cmb2_override_meta_remove', 10, 4 );
