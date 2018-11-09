<?php
if ( class_exists( 'TimberSite' ) ) {

	class KiliSite extends TimberSite  {
		function __construct() {

			/**
			* Child theme Class instance
			*/
			global $kili_theme;
			$this->kili_theme = $kili_theme;

			/**
			* Parent theme main Class Construct
			*/
			parent::__construct();

			/**
			* Filters
			*/
			$this->add_filters();

		}

		/**
		 * Add filters to KiliSite Class
		 *
		 * @return void
		 */
		public function add_filters() {
			add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		}

		function add_to_twig( $twig ) {
		    $function = new Twig_SimpleFunction('image_path', function ( $filename ) {
				return $this->kili_theme->asset_path( 'images/' . $filename );
		    });
		    $twig->addFunction($function);

		    return $twig;
		}
	}

	$kili_site = new KiliSite();
}
