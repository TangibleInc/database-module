<?php
/**
* Custom Tables Loader
*
* Handles checking for and smartly loading the newest version of this library.
*/

/**
* Loader versioning: http://jtsternberg.github.io/wp-lib-loader/
*/

if ( ! class_exists( 'CT_Loader_200', false ) ) {

  /**
  * Versioned loader class-name
  *
  * This ensures each version is loaded/checked.
  *
  * @category WordPressLibrary
  * @package  CT_Loader
  * @author   GamiPress <contact@gamipress.com>
  * @license  GPL-2.0+
  * @version  1.0.0
  * @link     https://gamipress.com
  * @since    1.0.0
  */
  class CT_Loader_200 {

    /**
    * CT_Loader version number
    * @var   string
    * @since 1.0.0
    */
    const VERSION = '2.0.0';

    /**
    * Setup constants
    *
    * @since       1.0.0
    */
    private function constants() {

      // Version
      define( 'CT_VER', self::VERSION );

      // File
      define( 'CT_FILE', __FILE__ );

      // Path
      define( 'CT_DIR', plugin_dir_path( __FILE__ ) );

      // URL
      define( 'CT_URL', plugin_dir_url( __FILE__ ) );

      // Debug
      define( 'CT_DEBUG', false );

    }

    /**
    * Starts the version checking process.
    * Creates CT_LOADED definition for early detection by other scripts.
    *
    * Hooks CT_Loader inclusion to the ct_loader_load hook
    * on a high priority which decrements (increasing the priority) with
    * each version release.
    *
    * @since 1.0.0
    */
    public function __construct() {

      if ( ! defined( 'CT_LOADER_PRIORITY' ) ) {
        // Calculate priority converting version into a number (eg: 1.0.0 to 100)
        define( 'CT_LOADER_PRIORITY', 99999 - absint( str_replace( '.', '', self::VERSION ) ) );
      }

      if ( ! defined( 'CT_LOADED' ) ) {
        // A constant you can use to check if Custom Tables (CT) is loaded for your plugins/themes with CT dependency.
        // Can also be used to determine the priority of the hook in use for the currently loaded version.
        define( 'CT_LOADED', CT_LOADER_PRIORITY );
      }

      // Use the hook system to ensure only the newest version is loaded.
      add_action( 'ct_loader_load', array( $this, 'include_lib' ), CT_LOADER_PRIORITY );

      // Try to fire our hook as soon as possible,including right now (required for activation hooks).
      self::fire_hook();

      // Hook in to the first hook we have available and fire our `ct_loader_load' hook.
      add_action( 'muplugins_loaded', array( __CLASS__, 'fire_hook' ), 9 );
      add_action( 'plugins_loaded', array( __CLASS__, 'fire_hook' ), 9 );
      add_action( 'after_setup_theme', array( __CLASS__, 'fire_hook' ), 9 );
    }

    /**
    * Fires the ct_loader_load action hook.
    *
    * @since 1.0.0
    */
    public static function fire_hook() {
      if ( ! did_action( 'ct_loader_load' ) ) {
        // Then fire our hook.
        do_action( 'ct_loader_load' );
      }
    }

    /**
    * A final check if CT_Loader exists before kicking off
    * our CT_Loader loading.
    *
    * @since  1.0.0
    */
    public function include_lib() {
      if ( class_exists( 'CT', false ) ) {
        return;
      }

      $this->constants();

      // Include and initiate Custom Tables (CT) class.
      require_once CT_DIR . 'includes/class-ct.php';
    }

  }

  // Kick it off.
  new CT_Loader_200;
}
