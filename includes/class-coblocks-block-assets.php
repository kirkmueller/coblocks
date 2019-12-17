<?php
/**
 * Load assets for our blocks.
 *
 * @package CoBlocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load general assets for our blocks.
 *
 * @since 1.0.0
 */
class CoBlocks_Block_Assets {


	/**
	 * This plugin's instance.
	 *
	 * @var CoBlocks_Block_Assets
	 */
	private static $instance;

	/**
	 * Registers the plugin.
	 */
	public static function register() {
		if ( null === self::$instance ) {
			self::$instance = new CoBlocks_Block_Assets();
		}
	}

	/**
	 * The base URL path (without trailing slash).
	 *
	 * @var string $url
	 */
	private $url;

	/**
	 * The plugin version.
	 *
	 * @var string $slug
	 */
	private $slug;

	/**
	 * The Constructor.
	 */
	public function __construct() {
		$this->slug = 'coblocks';
		$this->url  = untrailingslashit( plugins_url( '/', dirname( __FILE__ ) ) );

		add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );
		add_action( 'init', array( $this, 'editor_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_action( 'the_post', array( $this, 'frontend_scripts' ) );
	}

	/**
	 * Enqueue block assets for use within Gutenberg.
	 *
	 * @access public
	 */
	public function block_assets() {

		// Styles.
		wp_enqueue_style(
			$this->slug . '-frontend',
			$this->url . '/dist/coblocks-style.css',
			array(),
			COBLOCKS_VERSION
		);
	}

	/**
	 * Enqueue block assets for use within Gutenberg.
	 *
	 * @param array $asset_file Passed asset file data for unit testing.
	 *
	 * @access public
	 */
	public function editor_assets( $asset_file = array() ) {

		if ( empty( $asset_file ) ) {
			$asset_file = include COBLOCKS_PLUGIN_DIR . 'dist/coblocks.asset.php';
		}

		// Styles.
		wp_register_style(
			$this->slug . '-editor',
			$this->url . '/dist/coblocks-editor.css',
			array(),
			COBLOCKS_VERSION
		);

		// Scripts.
		wp_register_script(
			$this->slug . '-editor',
			$this->url . '/dist/coblocks.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		$post_id    = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
		$post_title = get_bloginfo( 'name' ) . ( ( false === $post_id ) ? '' : sprintf( ' - %s', get_the_title( $post_id ) ) );

		/**
		 * Filter the default block email address value
		 *
		 * @param string  $to      Admin email.
		 * @param integer $post_id Current post ID.
		 */
		$email_to = (string) apply_filters( 'coblocks_form_default_email', get_option( 'admin_email' ), $post_id );

		wp_localize_script(
			$this->slug . '-editor',
			'coblocksBlockData',
			array(
				'form'                           => array(
					'adminEmail'   => $email_to,
					'emailSubject' => $post_title,
				),
				'cropSettingsOriginalImageNonce' => wp_create_nonce( 'cropSettingsOriginalImageNonce' ),
				'cropSettingsNonce'              => wp_create_nonce( 'cropSettingsNonce' ),
			)
		);

	}

	/**
	 * Enqueue front-end assets for blocks.
	 *
	 * @access public
	 * @since 1.9.5
	 */
	public function frontend_scripts() {

		// Custom scripts are not allowed in AMP, so short-circuit.
		if ( CoBlocks()->is_amp() ) {
			return;
		}

		// Define where the asset is loaded from.
		$dir = CoBlocks()->asset_source( 'js' );

		// Define where the vendor asset is loaded from.
		$vendors_dir = CoBlocks()->asset_source( 'js', 'vendors' );

		// Masonry block.
		if ( has_block( $this->slug . '/gallery-masonry' ) ) {
			wp_enqueue_script(
				$this->slug . '-masonry',
				$dir . $this->slug . '-masonry.js',
				array( 'jquery', 'masonry', 'imagesloaded' ),
				COBLOCKS_VERSION,
				true
			);
		}

		// Carousel block.
		if ( has_block( $this->slug . '/gallery-carousel' ) ) {
			wp_enqueue_script(
				$this->slug . '-flickity',
				$vendors_dir . '/flickity.js',
				array( 'jquery' ),
				COBLOCKS_VERSION,
				true
			);
		}

		// Post Carousel block.
		if ( has_block( $this->slug . '/post-carousel' ) ) {
			wp_enqueue_script(
				$this->slug . '-slick',
				$vendors_dir . '/slick.js',
				array( 'jquery' ),
				COBLOCKS_VERSION,
				true
			);
			wp_enqueue_script(
				$this->slug . '-slick-initializer-front',
				$dir . $this->slug . '-slick-initializer-front.js',
				array( 'jquery' ),
				COBLOCKS_VERSION,
				true
			);
		}

		// Lightbox.
		if ( has_block( $this->slug . '/gallery-masonry' ) || has_block( $this->slug . '/gallery-stacked' ) || has_block( $this->slug . '/gallery-collage' ) || has_block( $this->slug . '/gallery-carousel' ) || has_block( $this->slug . '/gallery-offset' ) ) {
			wp_enqueue_script(
				$this->slug . '-lightbox',
				$dir . $this->slug . '-lightbox.js',
				array( 'jquery' ),
				COBLOCKS_VERSION,
				true
			);
		}
	}

}

CoBlocks_Block_Assets::register();
