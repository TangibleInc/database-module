# Custom Tables - Ajax List Table #

Utility to render a Custom Tables List Table with ajax searching and pagination.

Encapsulates "Custom Tables - Ajax List Table" developed by [rubengc](rubengcdev@gamil.com)

[https://github.com/rubengc/ct-ajax-list-table](https://github.com/rubengc/ct-ajax-list-table)

To render an ajax list table use the main function with appropriate arguments:

 ```php
/**
 * Render an ajax list table
 *
 *
 * @param TDB_Table|string   $table  TDB_Table object or TDB_Table name
 * @param array              $query_args Query parameters
 * @param array              $view_args  View parameters
 */
tdb_render_ajax_list_table ($table, $query_args = array(), $view_args = array() );
 
 ```

### An example ###
Providing that a **'user_related_data'** Custom Database Table with User related data has been defined, it can be rendered on User profile page using **'show_user_profile'** and **'edit_user_profile'** WP action hooks:

Callback function:

```php
/**
 * Generate markup to list user related data
 *
 *
 * @param  object $user         The current user's $user object
 *
 * @return string               concatenated markup
 */
function user_related_data_profile_page_list( $user = null ) {

  /**
   * Filter to allow set the number of user earnings to show on user profile
   *
   *
   * @param int $items_per_page
   *
   * @return int
   */
  $items_per_page = apply_filters( 'user_related_data_per_page', 10 );
  ?>
  <h2 class="user-related-data-profile-page-list">
   <span>
   <?php echo current_user_can( 'manage_options' ) ? __( 'User Earned Accreditations:', 'text-domain' ) : __( 'Your Accreditations:', 'text-domain' ); 
   ?>  
   </span>
 </h2>
  <?php
  tdb_render_ajax_list_table(
    'user_related_data',
    [
      'user_id' => absint( $user->ID ),
      'items_per_page' => $items_per_page,
      'orderby'=>'created_date',
      'order'=>'asc' // force asc
    ],
    [
      'views' => false,
      'search_box' => false
    ]
  );
  ?>
  <hr>
  <?php
};
```

WP action hooks:

```php
add_action('show_user_profile', 'user_related_data_profile_page_list');
add_action('edit_user_profile', 'user_related_data_profile_page_list');
```
