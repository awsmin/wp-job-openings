<?php
/**
 * Plugin Name:       Job List Block
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       job-list-block
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Awsm_Job_Guten_Blocks {

	private static $instance = null;
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_dynamic_block' ) );
		add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );
	}

	public static function get_instance() {
		// If an instance hasn't been created and set to $instance create an instance and set it to $instance.
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function register_dynamic_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type( __DIR__ . '/build' );
	}

	public function block_render_callback( $atts, $content ) {

	}

	public function block_assets() {
	}
}

Awsm_Job_Guten_Blocks::get_instance();