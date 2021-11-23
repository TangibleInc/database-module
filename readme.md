# Database module

This module is based on the Custom Tables library ([source](https://github.com/rubengc/CT)).

It has been converted to use a unique prefix `tdb_`, to prevent conflict with other plugins that may be using the original library. This lets us customize and extend this module as needed.

## Install

Create or add to the plugin's `composer.json`

```json
{
  "repositories": [{
    "type": "vcs",
    "url": "git@bitbucket.org:/tangibleinc/tangible-database-module.git"
  }],
  "require": {
    "tangible/database": "dev-master"
  },
  "minimum-stability": "dev"
}
```

Run on command line

```sh
composer install
```

Later, regularly keep module up to date

```sh
composer update
```


## Load

At the top of the plugin entry file

```php
require __DIR__ . '/vendor/tangible/database/index.php';
```


## Usage

The following are updated examples from the original library.

### Initialization

Use the `tdb_init` action hook to register the table.

See the method `TDB_Table::set_props` in `table.php` for all available properties.

```php
add_action('tdb_init', function() {

  $log = tdb_register_table('log', [
    'singular'      => 'Log',
    'plural'        => 'Logs',

    // Make custom table visible on admin area (check 'views' parameter)
    'show_ui'       => true,
    // Make custom table visible on rest API
    'show_in_rest'  => true,
    // Rest base URL, if not defined will user the table name
    //'rest_base'  => 'logs',
    // Change the version on schema changes to run the schema auto-updater
    'version'       => 1,
    // If not defined will be checked on the field that hsa primary_key as true on schema
    //'primary_key' => 'log_id',
    'schema'        => [
      'log_id' => [
        'type' => 'bigint',
        'length' => '20',
        'auto_increment' => true,
        'primary_key' => true,
      ],
      'title' => [
        'type' => 'varchar',
        'length' => '50',
      ],
      'status' => [
        'type' => 'varchar',
        'length' => '50',
      ],
      'date' => [
        'type' => 'datetime',
      ]
    ],
    // Schema can also be defined as string
    // 'schema' => '
    //   log_id bigint(20) NOT NULL AUTO_INCREMENT,
    //   title varchar(50) NOT NULL,
    //   status varchar(50) NOT NULL,
    //   date datetime NOT NULL,
    //   PRIMARY KEY  (log_id)
    // ',

    // Database engine (default to InnoDB)
    'engine' => 'InnoDB',

    // View args
    'views' => [
      'add' => [
        // 'columns' => 1 // This will force to the add view just to one column, default is 2
      ],
      'list' => [
        // 'per_page' => 40 // This will force the per page initial value

        // The columns arg is a shortcut of the manage_columns and manage_sortable_columns commonly required hooks
        'columns' => [
          'title'   => [
            'label'     => __( 'Title' ),
            'sortable'  => 'title', // ORDER BY title ASC
          ],
          'status'  => [
            'label' => __( 'Status' ),
            'sortable' => [ 'status', false ], // ORDER BY status ASC
          ],
          'date'    => [
            'label' => __( 'Date' ),
            'sortable' => [ 'date', true ], // ORDER BY date DESC
          ],
        ]
      ]
    ],
    'supports' => [
      // This support automatically generates a new DB table with {table_name}_meta with a similar structure like WP post meta
      'meta',
    ]
  ]);

  // Add some data
  /*
  $log->db->insert( [ 'title' => 'Log 1' ] );
  $log->db->insert( [ 'title' => 'Log 2' ] );
  $log->db->insert( [ 'title' => 'Log 3' ] );
  */

});
```


### Default data

Default data when creating a new item. See `tdb_insert_object()` in `functions.php`.

```php
add_filter("tdb_{$table_name}_default_data", function( $default_data = [] ) {

  $default_data['title'] = 'Auto draft';
  $default_data['status'] = 'pending';
  $default_data['date'] = date( 'Y-m-d H:i:s' );

  return $default_data;
});
```


### Metabox

Add metabox to the edit screen

```php
add_action('add_meta_boxes', function() use ($table_name) {

  add_meta_box(
    'demo-meta-box-id',
    __( 'Demo Meta Box', 'textdomain' ),
    'yourprefix_demo_metabox_callback',
    $table_name,
    'normal'
  );
});
```

Metabox render callback

```php
function yourprefix_demo_metabox_callback( $object ) {

  // Turn stdObject into an array
  $object_data = (array) $object;

  ?>
  <table class="form-table">
  <?php foreach( $object_data as $field => $value ) :

    if ( $field === 'log_id' ) continue; // Ingore ID field

    ?>
    <tr>
      <th>
        <?php echo ucfirst( $field ); ?>
      </th>
      <td>
        <input type="text" name="<?php echo $field; ?>" value="<?php echo $value; ?>">
      </td>
    </tr>

  <?php endforeach; ?>
  </table>
  <?php
}
```


### CMB2

Meta box initialization

```php
add_action('cmb2_admin_init', function() {

  $cmb = new_cmb2_box([
    'id'           	=> 'cmb-demo-meta-box-id',
    'title'        	=> __( 'CMB2 Demo Meta Box', 'textdomain' ),
    'object_types' 	=> [ 'demo_logs' ],
  ]);

  $cmb->add_field([
    'id'         => 'title',
    'name'       => esc_html__( 'Title', 'textdomain' ),
    'desc'       => esc_html__( 'field description (optional)', 'textdomain' ),
    'type'       => 'text',
  ]);

  $cmb->add_field([
    'id'         => 'status',
    'name'       => esc_html__( 'Status', 'textdomain' ),
    'desc'       => esc_html__( 'field description (optional)', 'textdomain' ),
    'type'       => 'text',
  ]);

  // This fields just work if you defined meta as supports on tdb_register_table()
  $cmb->add_field([
    'id'         => 'yourprefix_meta_field',
    'name'       => esc_html__( 'Meta field', 'textdomain' ),
    'desc'       => esc_html__( 'field description (optional)', 'textdomain' ),
    'type'       => 'text',
  ]);

  $cmb->add_field([
    'id'         => 'yourprefix_meta_field_2',
    'name'       => esc_html__( 'Meta field 2', 'textdomain' ),
    'desc'       => esc_html__( 'field description (optional)', 'textdomain' ),
    'type'       => 'text',
  ]);
});
```


### Query

Similar to `WP_Query`, there's a class named `TDB_Query` to apply (cached) searches on custom tables.

---

Fields to apply a search, used on searches (query var "s")

```php
add_filter("tdb_query_{$table_name}_search_fields", function( $search_fields = [] ) {

  $search_fields[] = 'title';
  $search_fields[] = 'status';

  return $search_fields;
});
```

An example adding support for `log__in` and `log__not_in` query vars

```php
add_filter('tdb_query_where', function( $where, $tdb_query ) {

  global $tdb_table;

  // Apply to custom table only
  if( $tdb_table->name !== 'demo_logs' ) return $where;

  $table_name = $tdb_table->db->table_name;

  // Shorthand
  $qv = $tdb_query->query_vars;

  // Include
  if( isset( $qv['log__in'] ) && ! empty( $qv['log__in'] ) ) {

    if( is_array( $qv['log__in'] ) ) {
      $include = implode( ", ", $qv['log__in'] );
    } else {
      $include = $qv['log__in'];
    }

    if( ! empty( $include ) ) {
      $where .= " AND {$table_name}.log_id IN ( {$include} )";
    }
  }

  // Exclude
  if( isset( $qv['log__not_in'] ) && ! empty( $qv['log__not_in'] ) ) {

    if( is_array( $qv['log__not_in'] ) ) {
      $exclude = implode( ", ", $qv['log__not_in'] );
    } else {
      $exclude = $qv['log__not_in'];
    }

    if( ! empty( $exclude ) ) {
      $where .= " AND {$table_name}.log_id NOT IN ( {$exclude} )";
    }
  }

  return $where;
}, 10, 2);
```


### REST API

Register the item schema properties - used on create and update endpoints

```php
add_filter("tdb_rest_{$table_name}_schema", function( $schema ) {

  // Properties
  $schema['properties'] = array_merge( [
    'log_id'            => [
      'description'   => __( 'Unique identifier for the object.', 'textdomain' ),
      'type'          => 'integer',
      'context'       => [ 'view', 'edit', 'embed' ],
    ],
    'title'             => [
      'description'   => __( 'The title for the object.', 'textdomain' ),
      'type'          => 'string',
      'context'       => [ 'view', 'edit', 'embed' ],
    ],
    'status'            => [
      'description'   => __( 'Status of log for the object.', 'textdomain' ),
      'type'          => 'string',
      'context'       => [ 'view', 'edit', 'embed' ],
      'readonly'      => true,
    ],
    'date'              => [
      'description'   => __( 'The date the object was created, in the site\'s timezone.', 'textdomain' ),
      'type'          => 'string',
      'format'        => 'date-time',
      'context'       => [ 'view', 'edit', 'embed' ],
    ],
  ], $schema['properties'] );

  return $schema;
});
```

Custom collection params

In this example, the collection params are called `exclude` and `include`. These are mapped by the next example of "parameter mapping" to the real query vars.

```php
add_filter("tdb_rest_{$table_name}_collection_params", function( $query_params, $tdb_table ) {

  // Exclude
  $query_params['exclude'] = [
    'description'        => __( 'Ensure result set excludes specific IDs.', 'textdomain' ),
    'type'               => 'array',
    'items'              => [
      'type'           => 'integer',
    ],
    'default'            => [],
  ];

  // Include
  $query_params['include'] = [
    'description'        => __( 'Limit result set to specific IDs.', 'textdomain' ),
    'type'               => 'array',
    'items'              => [
      'type'           => 'integer',
    ],
    'default'            => [],
  ];

  return $query_params;
}, 10, 2);
```

Map custom parameters to real query vars

This example depends on the "custom query vars" example above.

```php
add_filter("tdb_rest_{$table_name}_parameter_mappings", function( $parameter_mappings, $tdb_table, $request ) {

  $parameter_mappings['exclude'] = 'log__not_in';
  $parameter_mappings['include'] = 'log__in';

  return $parameter_mappings;
}, 10, 3);
```

Custom field sanitization on rest API updates

```php
add_filter("tdb_rest_{$table_name}_sanitize_field_value", function( $value, $field, $request ) {

switch( $field ) {
  case 'date':
    // Validate date.
    $mm = substr( $value, 5, 2 );
    $jj = substr( $value, 8, 2 );
    $aa = substr( $value, 0, 4 );
    $valid_date = wp_checkdate( $mm, $jj, $aa, $value );

    if ( ! $valid_date ) {
      return new WP_Error( 'rest_invalid_field', __( 'Invalid date.', 'textdomain' ), [ 'status' => 400 ] );
    }
    break;
  }

  return $value;
}, 10, 3 );
```

Register REST custom fields

```php
add_action( 'tdb_rest_api_init', function() use ($table_name) {

  register_rest_field(
    $table_name,
    'yourprefix_meta_field',
    [
      'get_callback'    => 'yourprefix_common_get_object_meta',
      'update_callback' => 'yourprefix_common_update_object_meta',
      'schema'          => null,
    ]
  );

  register_rest_field(
    $table_name,
    'yourprefix_meta_field_2',
    [
      'get_callback'    => 'yourprefix_common_get_object_meta',
      'update_callback' => 'yourprefix_common_update_object_meta',
      'schema'          => null,
    ]
  );
});

// Get object meta callback
function yourprefix_common_get_object_meta( $object, $field_name, $request ) {
  return tdb_get_object_meta( $object[ 'id' ], $field_name, true );
}

// Update object meta callback
function yourprefix_common_update_object_meta( $value, $object, $field_name ) {
  return tdb_update_object_meta( $object[ 'id' ], $field_name, $value );
}
```
