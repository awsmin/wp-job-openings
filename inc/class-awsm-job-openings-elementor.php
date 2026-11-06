<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Elementor {
	private static $instance = null;

	public function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widget' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_widget_icon_style' ) );

		add_action( 'deactivate_plugin', array( $this, 'refresh_fallback_content' ) );

		if ( ! class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ) {
			add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_widget_pro_lock_style' ) );
			add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_widget_pro_lock_script' ) );
		}
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Rewrites the post_content fallback of every Elementor page using the Job Listings
	 * widget, right before Elementor is deactivated (its classes are still loaded here).
	 *
	 * @param string $plugin Basename of the plugin being deactivated.
	 *
	 * Pages saved in Elementor before the widget had render_plain_content() hold stripped,
	 * unstyled listing HTML in post_content, which is what WordPress displays once Elementor
	 * is off. Regenerating it stores the equivalent Job Listings block instead.
	 */
	public function refresh_fallback_content( $plugin ) {
		if ( ! defined( 'ELEMENTOR_PLUGIN_BASE' ) || ELEMENTOR_PLUGIN_BASE !== $plugin ) {
			return;
		}
		if ( ! class_exists( '\Elementor\Plugin' ) || empty( \Elementor\Plugin::$instance->db ) ) {
			return;
		}

		$post_ids = get_posts(
			array(
				'post_type'        => 'any',
				'post_status'      => array( 'publish', 'private', 'draft', 'pending', 'future' ),
				'posts_per_page'   => -1,
				'fields'           => 'ids',
				'no_found_rows'    => true,
				'suppress_filters' => true,
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- One-off, runs only on Elementor deactivation.
				'meta_query'       => array(
					array(
						'key'   => '_elementor_edit_mode',
						'value' => 'builder',
					),
					array(
						'key'     => '_elementor_data',
						'value'   => '"widgetType":"awsm-job-listings"',
						'compare' => 'LIKE',
					),
				),
			)
		);

		foreach ( $post_ids as $post_id ) {
			try {
				\Elementor\Plugin::$instance->db->save_plain_text( $post_id );
			} catch ( \Throwable $e ) {
				// Never block Elementor's deactivation; the page keeps its previous content.
				continue;
			}
		}
	}

	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'awsm-job-openings',
			array(
				'title' => esc_html__( 'HireZoot', 'wp-job-openings' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	public function register_widget( $widgets_manager ) {
		if ( ! class_exists( 'Awsm_Job_Guten_Blocks' ) ) {
			return;
		}

		require_once __DIR__ . '/class-awsm-job-openings-elementor-widget.php';
		$widgets_manager->register( new AWSM_Job_Openings_Elementor_Widget() );
	}

	/**
	 * The widget icon is our own HireZoot mark (same SVG already used for the wp-admin
	 * menu icon), not an Elementor icon-font glyph, so it's applied as a CSS mask
	 * (background-color: currentColor) rather than a font ligature. This lets it inherit
	 * Elementor's own icon color in every state (panel list, hover, navigator) for free.
	 */
	public function enqueue_widget_icon_style() {
		$icon_url = esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/hirezoot-icon.svg' );

		wp_add_inline_style(
			'elementor-editor',
			"i.awsm-job-listings-widget-icon {
				display: inline-block;
				width: 1em;
				height: 1em;
				background-color: currentColor;
				-webkit-mask-image: url({$icon_url});
				mask-image: url({$icon_url});
				-webkit-mask-repeat: no-repeat;
				mask-repeat: no-repeat;
				-webkit-mask-position: center;
				mask-position: center;
				-webkit-mask-size: contain;
				mask-size: contain;
			}"
		);
	}

	/**
	 * Styles the Pro-locked choices that elementor-widget-pro-lock.js disables:
	 * the "Multiple" Choose option (grayed out, not-allowed cursor, small "Pro" badge).
	 * The disabled <option> in the Layout/Job Listing Type selects needs no extra CSS —
	 * native <option> elements can't be styled further than this in most browsers, and
	 * the JS-applied "disabled" state already grays them out on its own.
	 */
	public function enqueue_widget_pro_lock_style() {
		wp_add_inline_style(
			'elementor-editor',
			// Elementor's Choose control shows its option labels only as a hover
			// tooltip by default; this makes them always visible instead (same
			// treatment Pro Pack applies to its own real version of this control).
			'.awsm-filter-type-choose .elementor-choices {
				height: auto !important;
				overflow: visible !important;
				line-height: normal !important;
			}
			.awsm-filter-type-choose .elementor-choices-label,
			.awsm-filter-type-choose .elementor-choices label {
				flex-direction: column !important;
				overflow: visible !important;
				padding: 8px 4px !important;
				gap: 4px !important;
				width: auto !important;
				flex: 1 1 0 !important;
			}
			.awsm-filter-type-choose .elementor-screen-only {
				position: static !important;
				width: auto !important;
				height: auto !important;
				margin: 0 !important;
				padding: 0 !important;
				overflow: visible !important;
				clip: auto !important;
				top: auto !important;
				font-size: 10px !important;
				white-space: nowrap !important;
				display: block !important;
			}
			.elementor-choices-label.awsm-pro-locked {
				cursor: not-allowed;
				opacity: 0.5;
			}
			.elementor-choices-label.awsm-pro-locked .awsm-pro-badge {
				display: block;
				font-size: 9px;
				line-height: 1;
				text-transform: uppercase;
				letter-spacing: 0.5px;
				margin-top: 2px;
			}'
		);
	}

	/**
	 * Disables the Pro-only choices in this widget's own controls (Stack layout,
	 * Filtered listing, Multiple-select filters) so they're visible with a "Pro" badge
	 * but can't be selected — see assets/js/admin/elementor-widget-pro-lock.js. Only
	 * enqueued when Pro Pack is inactive (see __construct()); once Pro Pack registers
	 * its own real versions of these controls, this script no longer loads at all.
	 */
	public function enqueue_widget_pro_lock_script() {
		wp_enqueue_script(
			'awsm-job-openings-elementor-widget-pro-lock',
			AWSM_JOBS_PLUGIN_URL . '/assets/js/admin/elementor-widget-pro-lock.js',
			array( 'jquery', 'elementor-editor' ),
			AWSM_JOBS_PLUGIN_VERSION,
			true
		);
	}
}

AWSM_Job_Openings_Elementor::init();
