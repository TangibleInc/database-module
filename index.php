<?php
/**
 * Based on: https://github.com/rubengc/CT
 */
new class {

  /**
   * Update module version to ensure latest version gets loaded
   * when multiple module instances exist.
   *
   * Expected format: YYYYMMDD
   */
  const VERSION     = '20220530';
  const ACTION_NAME = 'tangible_database_module_load';

  function __construct() {

    /**
     * Calculate priority converting version into a number, so the highest version
     * gets top priority.
     */
    $priority = 99999999 - absint( self::VERSION );

    add_action( self::ACTION_NAME, [$this, 'load'], $priority );

    /**
     * In the original, it ran the action immediately here to support using the
     * library inside de/activation hooks; however, that prevents the version priority
     * logic from working at all (earliest loaded version will get priority instead).
     */
    // self::ensure_action();

    // Hook in to the earliest hook we have available
    add_action('muplugins_loaded',  [__CLASS__, 'ensure_action'], 0);
    add_action('plugins_loaded',    [__CLASS__, 'ensure_action'], 0);
    add_action('after_setup_theme', [__CLASS__, 'ensure_action'], 0);
  }

  static function ensure_action() {
    if (!did_action(self::ACTION_NAME)) do_action(self::ACTION_NAME);
  }

  function load() {

    if (defined('TDB_VERSION')) return;

    define( 'TDB_VERSION', self::VERSION );
    define( 'TDB_FILE', __FILE__ );
    define( 'TDB_DIR', plugin_dir_path( __FILE__ ) );
    define( 'TDB_URL', plugin_dir_url( __FILE__ ) );
    define( 'TDB_DEBUG', false );

    $dir = TDB_DIR;

    // Table and table meta classes
    require_once $dir . 'table.php';
    require_once $dir . 'table-meta.php';

    // Database and schema related classes
    require_once $dir . 'database.php';
    require_once $dir . 'database-schema.php';
    require_once $dir . 'database-schema-updater.php';

    // Rest API
    require_once $dir . 'rest-controller.php';
    require_once $dir . 'rest-meta-fields.php';

    // Query and list table classes
    require_once $dir . 'query.php';
    if (is_admin()) require_once $dir . 'list-table.php';

    // Views (list and add/edit)
    require_once $dir . 'view.php';
    require_once $dir . 'list-view.php';
    require_once $dir . 'edit-view.php';

    // Rest of includes
    require_once $dir . 'functions.php';
    require_once $dir . 'hooks.php';

    // Extended features
    require_once $dir . 'cmb2.php';
    require_once $dir . 'ajax/index.php';

    $this->load_textdomain();

    // Setup role capabilities - @see ./functions.php
    tdb_populate_roles();

    // Init action hook used to register tables
    add_action('init', function() {

      do_action( 'tdb_init' );

      if (is_admin()) {
        do_action( 'tdb_admin_init' );
      }
    }, 0);
  }

  /**
   * Internationalization
   */
  private function load_textdomain() {
    // Set filter for language directory
    $lang_dir = TDB_DIR . '/languages/';
    $lang_dir = apply_filters( 'tdb_languages_directory', $lang_dir );

    // Traditional WordPress plugin locale filter
    $locale = apply_filters( 'plugin_locale', get_locale(), 'tdb' );
    $mofile = sprintf( '%1$s-%2$s.mo', 'tdb', $locale );

    // Setup paths to current locale file
    $mofile_local   = $lang_dir . $mofile;
    $mofile_global  = WP_LANG_DIR . '/tdb/' . $mofile;

    if( file_exists( $mofile_global ) ) {
      // Look in global /wp-content/languages/tdb/ folder
      load_textdomain( 'tdb', $mofile_global );
    } elseif( file_exists( $mofile_local ) ) {
      // Look in local /wp-content/plugins/tdb/languages/ folder
      load_textdomain( 'tdb', $mofile_local );
    } else {
      // Load the default language files
      load_plugin_textdomain( 'tdb', false, $lang_dir );
    }
  }
};
