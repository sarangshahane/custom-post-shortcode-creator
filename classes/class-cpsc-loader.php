<?php
/**
 * Cpsc Loader.
 *
 * @package custom-post-shortcode-creator
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Cpsc_Loader' ) ) {

	/**
	 * Class Cpsc_Loader.
	 */
	final class Cpsc_Loader {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance = null;


		/**
		 *  Initiator
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {

				self::$instance = new self();

				/**
				 * Custom Post Shortcode Creator loaded.
				 *
				 * Fires when Custom Post Shortcode Creator was fully loaded and instantiated.
				 *
				 * @since 1.0.0
				 */
				do_action( 'cpsc_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->define_constants();

			// Activation hook.
			register_activation_hook( CPSC_FILE, array( $this, 'activation_reset' ) );

			// deActivation hook.
			register_deactivation_hook( CPSC_FILE, array( $this, 'deactivation_reset' ) );

			add_action( 'plugins_loaded', array( $this, 'init' ), 99 );
			add_action( 'plugins_loaded', array( $this, 'load_cf_textdomain' ) );

		}

		/**
		 * Defines all constants
		 *
		 * @since 1.0.0
		 */
		public function define_constants() {

			define( 'CPSC_BASE', plugin_basename( CPSC_FILE ) );
			define( 'CPSC_DIR', plugin_dir_path( CPSC_FILE ) );
			define( 'CPSC_URL', plugins_url( '/', CPSC_FILE ) );
			define( 'CPSC_VER', '1.0.1' );
			define( 'CPSC_SLUG', 'custom-post-shortcode-creator' );
			define( 'CPSC_NAME', 'Custom Post Shortcode Creator' );

		}

		/**
		 * Loads plugin files.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function init() {
			$this->include_required_files();

			/**
			 * Custom Post Shortcode Creator Init.
			 *
			 * Fires when Custom Post Shortcode Creator is instantiated.
			 *
			 * @since 1.0.0
			 */
			do_action( 'cpsc_init' );
		}

		/**
		 * Load Core Files.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function include_required_files() {
			require_once CPSC_DIR . 'classes/class-cpsc-shortcodes.php';
			require_once CPSC_DIR . 'classes/class-cpsc-meta-boxes.php';
		}


		/**
		 * Load Custom Post Shortcode Creator Text Domain.
		 * This will load the translation textdomain depending on the file priorities.
		 *      1. Global Languages /wp-content/languages/custom-post-shortcode-creator/ folder
		 *      2. Local dorectory /wp-content/plugins/custom-post-shortcode-creator/languages/ folder
		 *
		 * @since 1.0.3
		 * @return void
		 */
		public function load_cf_textdomain() {
			// Default languages directory for custom-post-shortcode-creator.
			$lang_dir = CPSC_DIR . 'languages/';

			/**
			 * Filters the languages directory path to use for custom-post-shortcode-creator.
			 *
			 * @param string $lang_dir The languages directory path.
			 */
			$lang_dir = apply_filters( 'cpsc_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter.
			global $wp_version;

			$get_locale = get_locale();

			if ( $wp_version >= 4.7 ) {
				$get_locale = get_user_locale();
			}

			/**
			 * Language Locale for custom-post-shortcode-creator
			 *
			 * @var $get_locale The locale to use.
			 * Uses get_user_locale()` in WordPress 4.7 or greater,
			 * otherwise uses `get_locale()`.
			 */
			$locale = apply_filters( 'plugin_locale', $get_locale, 'custom-post-shortcode-creator' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'custom-post-shortcode-creator', $locale );

			// Setup paths to current locale file.
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/custom-post-shortcode-creator/ folder.
				load_textdomain( 'custom-post-shortcode-creator', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/custom-post-shortcode-creator/languages/ folder.
				load_textdomain( 'custom-post-shortcode-creator', $mofile_local );
			} else {
				// Load the default language files.
				load_plugin_textdomain( 'custom-post-shortcode-creator', false, $lang_dir );
			}
		}


		/**
		 * Activation Reset
		 */
		public function activation_reset() {
			
		}

		/**
		 * Deactivation Reset
		 */
		public function deactivation_reset() {      }

	}

	/**
	 *  Prepare if class 'Cpsc_Loader' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	Cpsc_Loader::get_instance();
}
