<?php
/**
 * Sets the theme functions
 *
 * @package Kili
 */

// Load the parent theme config and class.
include get_template_directory() . '/config/load.php';

if ( ! class_exists( 'KiliTheme' ) ) {
	/**
	* Child theme main Class
	*/
	class KiliTheme extends Kili_Framework {
		/**
		 * Class constructor
		 */
		function __construct() {
			// Parent theme main Class Construct.
			parent::__construct();

			// Child theme Actions.
			$this->add_actions();

			// Child theme Filters.
			$this->add_filters();

			// Child theme Setup
			$this->theme_setup();
		}

		/**
		 * Add actions to theme
		 *
		 * @return void
		 */
		public function add_actions() {
			add_action( 'widgets_init', array( $this, 'kili_widgets_init' ) );

			if ( ! is_admin() ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
			}
		}

		/**
		 * Load the theme assets
		 *
		 * @return void
		 */
		public function load_assets() {
			wp_enqueue_style( 'theme-style', $this->asset_path( 'styles/main.css' ), false, null );
			wp_enqueue_script( 'theme-scripts', $this->asset_path( 'scripts/main.js' ), [], null, true );
		}

		/**
		 * Add filters to theme
		 *
		 * @return void
		 */
		public function add_filters() {
			add_filter( 'timber_context', array( $this, 'theme_context' ) );
		}

		/**
		 * Theme setup
		 *
		 * @return void
		 */
		public function theme_setup() {
			// Initialize child theme pages renderer.
			$this->render_pages();

		}

		/**
		 * Get the asset path for enqueue style and scripts files.
		 *
		 * @param string $file the asset file path.
		 * @return string asset path
		 */
		public function asset_path( $file ) {
			$dist_path = THEME_URL . 'dist/';
			$directory = $file;
			$file = $file;
			static $manifest;

			if ( empty( $manifest ) ) {
				$manifest_path = THEME_DIR . 'dist/assets.json';
				$manifest = new Kili_Asset_Manifest( $manifest_path );
			}

			if ( array_key_exists( $file, $manifest->get() ) ) {
				return $dist_path . $manifest->get()[ $file ];
			}
			return $dist_path . $file;
		}

		/**
		 * Options for using in page context
		 *
		 * @param array $context The timber context.
		 * @return array Theme context array
		 */
	    public function theme_context( $context ) {
	    	// Add custom sidebar widgets to theme context.
	    	if ( is_active_sidebar( 'kili-sidebar' ) ) {
	    	    $context['dynamic_sidebar'] = Timber::get_widgets( 'kili-sidebar' );
	    	}
	    	return $context;
	    }

		/**
		 * Register widget area.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
		 */
		public function kili_widgets_init() {
			register_sidebar( array(
				'name'          => __( 'Kili Sidebar', 'kiliframework' ),
				'id'            => 'kili-sidebar',
				'description'   => __( 'Add widgets here to appear in your dynamic_sidebar on context.', 'kiliframework' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget__title">',
				'after_title'   => '</h2>',
			) );

		}
	}
}

// Start the main class.
$kili_theme = new KiliTheme();

// Autoload kiliframework Includes.
foreach ( glob( __DIR__ . '/inc/*.php' ) as $module ) {
	if ( ! $modulepath = $module ) {
		trigger_error( sprintf( __( 'Error locating %s for inclusion', 'kiliframework' ), $module ), E_USER_ERROR );
	}
	require_once( $modulepath );
}
