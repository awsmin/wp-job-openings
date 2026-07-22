<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class AWSM_Job_Openings_Elementor_Widget extends Widget_Base {

	public function get_name() {
		return 'awsm-job-listings';
	}

	public function get_title() {
		return esc_html__( 'Job Listings', 'wp-job-openings' );
	}

	public function get_icon() {
		return 'awsm-job-listings-widget-icon';
	}

	public function get_categories() {
		return array( 'awsm-job-openings' );
	}

	public function get_keywords() {
		return array( 'job', 'jobs', 'career', 'listing', 'hirezoot' );
	}

	public function get_script_depends() {
		return array( 'wp-job-openings-blocks-view-script' );
	}

	public function get_style_depends() {
		return array( 'wp-job-openings-blocks-style' );
	}

	/**
	 * Returns the site's registered job specifications as a control-ready [key => label] map.
	 *
	 * @return array
	 */
	protected function get_spec_options() {
		$specs   = class_exists( 'AWSM_Job_Openings_Block' ) ? AWSM_Job_Openings_Block::get_block_filter_specifications() : array();
		$options = array();
		foreach ( $specs as $spec ) {
			$options[ $spec['key'] ] = $spec['label'];
		}
		return $options;
	}

	/**
	 * Adds a uniform border-width + color + radius control group for one section of the listing.
	 *
	 * @param string $key           Attribute key prefix, e.g. 'hz_sf'.
	 * @param int    $default_width Default border width in px.
	 * @param string $default_color Default border color (hex).
	 * @param array  $condition     Optional Elementor 'condition' array to gate all three controls.
	 */
	protected function add_border_controls( $key, $default_width = 1, $default_color = '#cccccc', $condition = array() ) {
		$this->add_control(
			"{$key}_border_width",
			array_merge(
				array(
					'label'   => esc_html__( 'Border Width (px)', 'wp-job-openings' ),
					'type'    => Controls_Manager::SLIDER,
					'range'   => array(
						'px' => array(
							'min' => 0,
							'max' => 20,
						),
					),
					'default' => array(
						'unit' => 'px',
						'size' => $default_width,
					),
				),
				$condition ? array( 'condition' => $condition ) : array()
			)
		);

		$this->add_control(
			"{$key}_border_color",
			array_merge(
				array(
					'label'   => esc_html__( 'Border Color', 'wp-job-openings' ),
					'type'    => Controls_Manager::COLOR,
					'default' => $default_color,
				),
				$condition ? array( 'condition' => $condition ) : array()
			)
		);

		$this->add_control(
			"{$key}_border_radius",
			array_merge(
				array(
					'label'      => esc_html__( 'Border Radius', 'wp-job-openings' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'default'    => array(
						'top'    => '5',
						'right'  => '5',
						'bottom' => '5',
						'left'   => '5',
						'unit'   => 'px',
					),
				),
				$condition ? array( 'condition' => $condition ) : array()
			)
		);
	}

	/**
	 * Adds a padding (top/right/bottom/left) control for one section of the listing.
	 *
	 * @param string $key       Attribute key prefix, e.g. 'hz_sf'.
	 * @param array  $defaults  Default top/right/bottom/left values (numbers, px assumed).
	 * @param array  $condition Optional Elementor 'condition' array to gate the control.
	 */
	protected function add_padding_control( $key, $defaults, $condition = array() ) {
		$this->add_control(
			"{$key}_padding",
			array_merge(
				array(
					'label'      => esc_html__( 'Padding', 'wp-job-openings' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'default'    => array_merge( $defaults, array( 'unit' => 'px' ) ),
				),
				$condition ? array( 'condition' => $condition ) : array()
			)
		);
	}

	protected function register_controls() {
		$this->register_content_general_controls();
		$this->register_content_filters_controls();
		$this->register_content_filtered_list_controls();
		$this->register_content_layout_controls();
		$this->register_style_search_filters_controls();
		$this->register_style_job_listing_controls();
		$this->register_style_button_controls();
		$this->register_style_pagination_controls();
		$this->register_style_sidebar_controls();
	}

	protected function register_content_general_controls() {
		$this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'General', 'wp-job-openings' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'search',
			array(
				'label'        => esc_html__( 'Enable Search', 'wp-job-openings' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wp-job-openings' ),
				'label_off'    => esc_html__( 'No', 'wp-job-openings' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'search_placeholder',
			array(
				'label'       => esc_html__( 'Search Placeholder', 'wp-job-openings' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'Search Jobs', 'wp-job-openings' ),
				'condition'   => array(
					'search' => 'yes',
				),
			)
		);

		$this->add_control(
			'hide_expired_jobs',
			array(
				'label'        => esc_html__( 'Hide Expired Jobs', 'wp-job-openings' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wp-job-openings' ),
				'label_off'    => esc_html__( 'No', 'wp-job-openings' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_spec_icon',
			array(
				'label'        => esc_html__( 'Show Specification Icons', 'wp-job-openings' ),
				'description'  => esc_html__( 'Show Spec Icon in the Listing.', 'wp-job-openings' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wp-job-openings' ),
				'label_off'    => esc_html__( 'No', 'wp-job-openings' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'other_options',
			array(
				'label'       => esc_html__( 'Job Specs to Show in the Listing', 'wp-job-openings' ),
				'description' => esc_html__( 'Specifications shown as tags on each job listing row (separate from the filter dropdowns above).', 'wp-job-openings' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->get_spec_options(),
				'default'     => array(),
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label'   => esc_html__( 'Order By', 'wp-job-openings' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'new_to_old',
				'options' => array(
					'new_to_old' => esc_html__( 'Newest First', 'wp-job-openings' ),
					'old_to_new' => esc_html__( 'Oldest First', 'wp-job-openings' ),
				),
			)
		);

		$this->add_control(
			'listing_per_page',
			array(
				'label'   => esc_html__( 'Jobs Per Page', 'wp-job-openings' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 100,
				'default' => 10,
			)
		);

		$this->end_controls_section();
	}

	protected function register_content_filters_controls() {
		$this->start_controls_section(
			'section_filters',
			array(
				'label' => esc_html__( 'Filters', 'wp-job-openings' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'enable_job_filter',
			array(
				'label'        => esc_html__( 'Enable Filters', 'wp-job-openings' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wp-job-openings' ),
				'label_off'    => esc_html__( 'No', 'wp-job-openings' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$options = $this->get_spec_options();

		$this->add_control(
			'filter_options',
			array(
				'label'       => esc_html__( 'Specifications to Show as Filters', 'wp-job-openings' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $options,
				'default'     => array_keys( $options ),
				'condition'   => array(
					'enable_job_filter' => 'yes',
				),
			)
		);

		// Pro Pack registers its own real filter_type_{spec_key} controls via its own
		// Elementor hooks when active (same control IDs, same Single/Multiple CHOOSE
		// UI) — these are only a "Pro" preview for when Pro Pack isn't installed, so
		// they're skipped entirely once Pro Pack is active to avoid colliding with it.
		if ( ! class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ) {
			foreach ( $options as $spec_key => $spec_label ) {
				$this->add_control(
					'filter_type_' . $spec_key,
					array(
						/* translators: %s: specification label, e.g. "Job Category" */
						'label'     => sprintf( esc_html__( '%s Selection', 'wp-job-openings' ), $spec_label ),
						'type'      => Controls_Manager::CHOOSE,
						'options'   => array(
							'dropdown' => array(
								'title' => esc_html__( 'Single', 'wp-job-openings' ),
								'icon'  => 'eicon-dot-circle-o',
							),
							'checkbox' => array(
								'title' => esc_html__( 'Multiple (Pro)', 'wp-job-openings' ),
								'icon'  => 'eicon-atomic-checkbox',
							),
						),
						'default'   => 'dropdown',
						'toggle'    => false,
						'classes'   => 'awsm-filter-type-choose',
						'condition' => array(
							'enable_job_filter' => 'yes',
						),
					)
				);
			}
		}

		$this->add_control(
			'filter_items_order',
			array(
				'label'     => esc_html__( 'Filter Items Order', 'wp-job-openings' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'custom',
				'options'   => array(
					'custom'     => esc_html__( 'Custom (Settings Order)', 'wp-job-openings' ),
					'alpha_asc'  => esc_html__( 'Alphabetical (A-Z)', 'wp-job-openings' ),
					'alpha_desc' => esc_html__( 'Alphabetical (Z-A)', 'wp-job-openings' ),
				),
				'condition' => array(
					'enable_job_filter' => 'yes',
				),
			)
		);

		$this->add_control(
			'placement',
			array(
				'label'   => esc_html__( 'Filter Placement', 'wp-job-openings' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'top'  => esc_html__( 'Top', 'wp-job-openings' ),
					'side' => esc_html__( 'Side', 'wp-job-openings' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * "Filtered List" (list_type) is a Pro Pack feature: Pro Pack's own Elementor hook
	 * (add_filtered_list_section()) registers this exact section/control ID itself,
	 * with a richer per-spec preselected-terms picker, when active. This is only a
	 * "Pro" preview for when Pro Pack isn't installed, so it's skipped entirely once
	 * Pro Pack is active to avoid colliding with Pro Pack's own version.
	 */
	protected function register_content_filtered_list_controls() {
		if ( class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ) {
			return;
		}

		$this->start_controls_section(
			'section_filtered_list',
			array(
				'label' => esc_html__( 'Filtered List', 'wp-job-openings' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'list_type',
			array(
				'label'       => esc_html__( 'Show', 'wp-job-openings' ),
				'description' => esc_html__( 'Display all jobs or filtered by job specifications. Filtering by specifications is a Pro Pack feature.', 'wp-job-openings' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'all',
				'options'     => array(
					'all'      => esc_html__( 'All Jobs', 'wp-job-openings' ),
					'filtered' => esc_html__( 'Filtered List (Pro)', 'wp-job-openings' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_content_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Layout', 'wp-job-openings' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		// Pro Pack's own Elementor hook (extend_layout_section()) calls update_control()
		// on this same 'layout' control to drop the "(Pro)" suffix once active — that
		// only replaces 'options', so the label itself is safe to always include here.
		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'Layout', 'wp-job-openings' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'list',
				'options' => array(
					'list'  => esc_html__( 'List', 'wp-job-openings' ),
					'grid'  => esc_html__( 'Grid', 'wp-job-openings' ),
					'stack' => class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ? esc_html__( 'Stack', 'wp-job-openings' ) : esc_html__( 'Stack (Pro)', 'wp-job-openings' ),
				),
			)
		);

		$this->add_control(
			'number_of_columns',
			array(
				'label'     => esc_html__( 'Columns', 'wp-job-openings' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '3',
				'options'   => array(
					'1' => esc_html__( '1 Column', 'wp-job-openings' ),
					'2' => esc_html__( '2 Columns', 'wp-job-openings' ),
					'3' => esc_html__( '3 Columns', 'wp-job-openings' ),
					'4' => esc_html__( '4 Columns', 'wp-job-openings' ),
				),
				'condition' => array(
					'layout' => 'grid',
				),
			)
		);

		$this->add_control(
			'pagination',
			array(
				'label'   => esc_html__( 'Pagination Style', 'wp-job-openings' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'modern',
				'options' => array(
					'modern'  => esc_html__( 'Modern', 'wp-job-openings' ),
					'classic' => esc_html__( 'Classic', 'wp-job-openings' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_search_filters_controls() {
		$this->start_controls_section(
			'section_style_search_filters',
			array(
				'label' => esc_html__( 'Search & Filters', 'wp-job-openings' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'hz_sf_background_color',
			array(
				'label' => esc_html__( 'Background Color', 'wp-job-openings' ),
				'type'  => Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'hz_sf_text_color',
			array(
				'label' => esc_html__( 'Text Color', 'wp-job-openings' ),
				'type'  => Controls_Manager::COLOR,
			)
		);

		// Uses the hz_ls_* attributes: this is what actually renders the visible
		// border on the search box and filter dropdowns (matches the block, where
		// the "Search & Filters" border control has no visible effect in Top
		// placement and "List Style" is what controls this — folded into one
		// section here to match what users expect from "Search & Filters").
		$this->add_border_controls( 'hz_ls', 1, '#cccccc' );

		$this->end_controls_section();
	}

	protected function register_style_job_listing_controls() {
		$this->start_controls_section(
			'section_style_job_listing',
			array(
				'label' => esc_html__( 'Job Listing', 'wp-job-openings' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'hz_jl_background_color',
			array(
				'label' => esc_html__( 'Background Color', 'wp-job-openings' ),
				'type'  => Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'hz_jl_text_color',
			array(
				'label' => esc_html__( 'Text Color', 'wp-job-openings' ),
				'type'  => Controls_Manager::COLOR,
			)
		);

		$this->add_border_controls( 'hz_jl', 1, '#CBCBCB' );
		$this->add_padding_control(
			'hz_jl',
			array(
				'top'    => '15',
				'right'  => '15',
				'bottom' => '15',
				'left'   => '15',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_button_controls() {
		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => esc_html__( 'More Details Button', 'wp-job-openings' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'hz_button_style',
			array(
				'label'   => esc_html__( 'Button Style', 'wp-job-openings' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'     => esc_html__( 'None', 'wp-job-openings' ),
					'filled'   => esc_html__( 'Fill', 'wp-job-openings' ),
					'outlined' => esc_html__( 'Outline', 'wp-job-openings' ),
				),
			)
		);

		$this->add_control(
			'hz_button_text',
			array(
				'label'   => esc_html__( 'Button Text', 'wp-job-openings' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'More Details →', 'wp-job-openings' ),
			)
		);

		$this->add_control(
			'hz_button_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wp-job-openings' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'hz_button_style!' => 'none',
				),
			)
		);

		$this->add_control(
			'hz_button_text_color',
			array(
				'label' => esc_html__( 'Text Color', 'wp-job-openings' ),
				'type'  => Controls_Manager::COLOR,
			)
		);

		// Border/radius/padding only have a visible effect once a button style is
		// chosen (same as the block, which hides these once hz_button_style !== 'none').
		$button_style_condition = array( 'hz_button_style!' => 'none' );
		$this->add_border_controls( 'hz_bs', 1, '#4e35df', $button_style_condition );
		$this->add_padding_control(
			'hz_bs',
			array(
				'top'    => '13',
				'right'  => '13',
				'bottom' => '13',
				'left'   => '13',
			),
			$button_style_condition
		);

		$this->end_controls_section();
	}

	protected function register_style_pagination_controls() {
		$this->start_controls_section(
			'section_style_pagination',
			array(
				'label' => esc_html__( 'Pagination', 'wp-job-openings' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'hz_pagination_background_color',
			array(
				'label' => esc_html__( 'Background Color', 'wp-job-openings' ),
				'type'  => Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'hz_pagination_text_color',
			array(
				'label' => esc_html__( 'Text Color', 'wp-job-openings' ),
				'type'  => Controls_Manager::COLOR,
			)
		);

		$this->add_border_controls( 'hz_pagination', 1, '#cbcbcb' );
		// No fixed default here: the actual default (5px classic / 20px modern) is
		// applied in render() based on the 'pagination' style, matching the block editor.
		$this->add_padding_control( 'hz_pagination', array() );

		$this->end_controls_section();
	}

	protected function register_style_sidebar_controls() {
		$this->start_controls_section(
			'section_style_sidebar',
			array(
				'label' => esc_html__( 'Sidebar', 'wp-job-openings' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				// Matches the block: shown whenever placement is "side" and either
				// search or filters is enabled (not just filters).
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'  => 'placement',
							'value' => 'side',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'  => 'search',
									'value' => 'yes',
								),
								array(
									'name'  => 'enable_job_filter',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'hz_sidebar_width',
			array(
				'label'   => esc_html__( 'Sidebar Width (%)', 'wp-job-openings' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 33.33,
				'max'     => 80.33,
				'step'    => 0.1,
				'default' => 33.33,
			)
		);

		$this->add_control(
			'hz_sidebar_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'wp-job-openings' ),
				'type'  => Controls_Manager::COLOR,
			)
		);

		$this->add_control(
			'hz_sidebar_tx_color',
			array(
				'label' => esc_html__( 'Text Color', 'wp-job-openings' ),
				'type'  => Controls_Manager::COLOR,
			)
		);

		// hz_sf_border/radius/padding only have a visible effect on the sidebar
		// container in Side placement (same as the block), so they live here
		// rather than in "Search & Filters" where they'd silently do nothing.
		$this->add_border_controls( 'hz_sf', 1, '#cccccc' );
		$this->add_padding_control(
			'hz_sf',
			array(
				'top'    => '15',
				'right'  => '15',
				'bottom' => '15',
				'left'   => '15',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Reads a DIMENSIONS control value and maps it to the corner-based shape
	 * (topLeft/topRight/bottomLeft/bottomRight) the block's render logic expects.
	 * CSS border-radius shorthand order (top, right, bottom, left) already lines up
	 * with the corner order (top-left, top-right, bottom-right, bottom-left).
	 *
	 * @param array $dimensions Raw control value with top/right/bottom/left/unit keys.
	 * @return array
	 */
	protected function to_corner_radius( $dimensions ) {
		$unit = ! empty( $dimensions['unit'] ) ? $dimensions['unit'] : 'px';
		return array(
			'topLeft'     => ( isset( $dimensions['top'] ) && $dimensions['top'] !== '' ? $dimensions['top'] : '5' ) . $unit,
			'topRight'    => ( isset( $dimensions['right'] ) && $dimensions['right'] !== '' ? $dimensions['right'] : '5' ) . $unit,
			'bottomRight' => ( isset( $dimensions['bottom'] ) && $dimensions['bottom'] !== '' ? $dimensions['bottom'] : '5' ) . $unit,
			'bottomLeft'  => ( isset( $dimensions['left'] ) && $dimensions['left'] !== '' ? $dimensions['left'] : '5' ) . $unit,
		);
	}

	/**
	 * Reads a DIMENSIONS control value used for padding into the plain
	 * top/right/bottom/left shape the block's render logic expects.
	 *
	 * @param array  $dimensions Raw control value with top/right/bottom/left/unit keys.
	 * @param string $fallback   Value to use for any side left blank.
	 * @return array
	 */
	protected function to_padding( $dimensions, $fallback = '0' ) {
		$unit = ! empty( $dimensions['unit'] ) ? $dimensions['unit'] : 'px';
		return array(
			'top'    => ( isset( $dimensions['top'] ) && $dimensions['top'] !== '' ? $dimensions['top'] : $fallback ) . $unit,
			'right'  => ( isset( $dimensions['right'] ) && $dimensions['right'] !== '' ? $dimensions['right'] : $fallback ) . $unit,
			'bottom' => ( isset( $dimensions['bottom'] ) && $dimensions['bottom'] !== '' ? $dimensions['bottom'] : $fallback ) . $unit,
			'left'   => ( isset( $dimensions['left'] ) && $dimensions['left'] !== '' ? $dimensions['left'] : $fallback ) . $unit,
		);
	}

	protected function border_value( $settings, $key ) {
		$width = isset( $settings[ "{$key}_border_width" ]['size'] ) ? $settings[ "{$key}_border_width" ]['size'] : 1;
		$unit  = isset( $settings[ "{$key}_border_width" ]['unit'] ) ? $settings[ "{$key}_border_width" ]['unit'] : 'px';
		return array(
			'width' => $width . $unit,
			'color' => $this->resolve_global_color( $settings, "{$key}_border_color" ),
		);
	}

	/**
	 * Color controls have no 'selectors' mapping (colors are applied via this widget's
	 * own --hz-xxx CSS custom properties, shared with the Gutenberg block/shortcode), so
	 * Elementor's own CSS pipeline never resolves a Global Color pick for them. When a
	 * Global Color is selected, Elementor leaves the control's own value blank and instead
	 * records the pick under $settings['__globals__'][$key] (e.g. 'globals/colors?id=primary').
	 * Resolve that reference to the same CSS variable Elementor's kit CSS already defines
	 * on :root, so it can be used as a value directly, exactly like a literal hex value.
	 */
	protected function resolve_global_color( $settings, $key ) {
		if ( ! empty( $settings['__globals__'][ $key ] ) ) {
			$query = wp_parse_url( $settings['__globals__'][ $key ], PHP_URL_QUERY );
			wp_parse_str( (string) $query, $params );

			if ( ! empty( $params['id'] ) ) {
				return 'var(--e-global-color-' . sanitize_html_class( $params['id'] ) . ')';
			}
		}

		return isset( $settings[ $key ] ) ? $settings[ $key ] : '';
	}

	protected function render() {
		if ( ! class_exists( 'Awsm_Job_Guten_Blocks' ) || ! function_exists( 'awsm_jobs_query' ) ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		$block_id = 'awsm-elementor-' . sanitize_html_class( $this->get_id() );

		$filter_options = isset( $settings['filter_options'] ) && is_array( $settings['filter_options'] ) ? array_values( $settings['filter_options'] ) : array();

		// Stack layout and Filtered listing are Pro Pack features, shown here (labeled
		// "Pro") but disabled from actual selection by elementor-widget-pro-lock.js.
		// This is a second, server-side guard in case that JS never ran (e.g. a saved
		// value from before Pro Pack was deactivated) — never trust the client alone.
		$is_pro_pack_active = class_exists( 'AWSM_Job_Openings_Pro_Pack' );

		$layout = isset( $settings['layout'] ) ? $settings['layout'] : 'list';
		if ( 'stack' === $layout && ! $is_pro_pack_active ) {
			$layout = 'list';
		}

		$list_type = isset( $settings['list_type'] ) ? $settings['list_type'] : 'all';
		if ( 'filtered' === $list_type && ! $is_pro_pack_active ) {
			$list_type = 'all';
		}

		$atts = array(
			'blockId'                        => $block_id,
			'anchor'                         => '',
			'search'                         => ! empty( $settings['search'] ),
			'search_placeholder'             => isset( $settings['search_placeholder'] ) ? $settings['search_placeholder'] : '',
			'enable_job_filter'              => ! empty( $settings['enable_job_filter'] ),
			'filter_options'                 => $filter_options,
			'filtersInitialized'             => true,
			// filter_type_{spec_key} controls only exist here when Pro Pack is inactive
			// (see register_content_filters_controls()), and even then only as a "Pro"
			// preview — picking "Multiple (Pro)" never actually applies. When Pro Pack
			// is active, it registers its own real version of these controls and merges
			// the resulting per-spec selection in via its own filter hook, below.
			'filter_types'                   => array(),
			'filter_items_order'             => isset( $settings['filter_items_order'] ) ? $settings['filter_items_order'] : 'custom',
			'placement'                      => isset( $settings['placement'] ) ? $settings['placement'] : 'top',
			'layout'                         => $layout,
			'number_of_columns'              => isset( $settings['number_of_columns'] ) ? intval( $settings['number_of_columns'] ) : 3,
			'list_type'                      => $list_type,
			'order_by'                       => isset( $settings['order_by'] ) ? $settings['order_by'] : 'new_to_old',
			'hide_expired_jobs'              => ! empty( $settings['hide_expired_jobs'] ),
			'listing_per_page'               => isset( $settings['listing_per_page'] ) ? intval( $settings['listing_per_page'] ) : 10,
			'pagination'                     => isset( $settings['pagination'] ) ? $settings['pagination'] : 'modern',
			'show_spec_icon'                 => ! empty( $settings['show_spec_icon'] ),
			'other_options'                  => isset( $settings['other_options'] ) && is_array( $settings['other_options'] ) ? array_values( $settings['other_options'] ) : array(),
			'hz_sidebar_width'               => isset( $settings['hz_sidebar_width'] ) ? floatval( $settings['hz_sidebar_width'] ) : 33.333,

			'hz_sf_background_color'        => $this->resolve_global_color( $settings, 'hz_sf_background_color' ),
			'hz_sf_text_color'               => $this->resolve_global_color( $settings, 'hz_sf_text_color' ),
			'hz_sf_border'                   => $this->border_value( $settings, 'hz_sf' ),
			'hz_sf_border_radius'            => $this->to_corner_radius( isset( $settings['hz_sf_border_radius'] ) ? $settings['hz_sf_border_radius'] : array() ),
			'hz_sf_padding'                  => $this->to_padding( isset( $settings['hz_sf_padding'] ) ? $settings['hz_sf_padding'] : array() ),

			'hz_ls_border'                   => $this->border_value( $settings, 'hz_ls' ),
			'hz_ls_border_radius'            => $this->to_corner_radius( isset( $settings['hz_ls_border_radius'] ) ? $settings['hz_ls_border_radius'] : array() ),

			'hz_jl_background_color'         => $this->resolve_global_color( $settings, 'hz_jl_background_color' ),
			'hz_jl_text_color'               => $this->resolve_global_color( $settings, 'hz_jl_text_color' ),
			'hz_jl_border'                   => $this->border_value( $settings, 'hz_jl' ),
			'hz_jl_border_radius'            => $this->to_corner_radius( isset( $settings['hz_jl_border_radius'] ) ? $settings['hz_jl_border_radius'] : array() ),
			'hz_jl_padding'                  => $this->to_padding( isset( $settings['hz_jl_padding'] ) ? $settings['hz_jl_padding'] : array() ),

			'hz_button_style'                => isset( $settings['hz_button_style'] ) ? $settings['hz_button_style'] : 'none',
			'hz_button_text'                 => isset( $settings['hz_button_text'] ) ? $settings['hz_button_text'] : '',
			'hz_button_background_color'     => $this->resolve_global_color( $settings, 'hz_button_background_color' ),
			'hz_button_text_color'           => $this->resolve_global_color( $settings, 'hz_button_text_color' ),
			'hz_bs_border'                   => $this->border_value( $settings, 'hz_bs' ),
			'hz_bs_border_radius'            => $this->to_corner_radius( isset( $settings['hz_bs_border_radius'] ) ? $settings['hz_bs_border_radius'] : array() ),
			'hz_bs_padding'                  => $this->to_padding( isset( $settings['hz_bs_padding'] ) ? $settings['hz_bs_padding'] : array() ),

			'hz_pagination_background_color' => $this->resolve_global_color( $settings, 'hz_pagination_background_color' ),
			'hz_pagination_text_color'       => $this->resolve_global_color( $settings, 'hz_pagination_text_color' ),
			'hz_pagination_border'           => $this->border_value( $settings, 'hz_pagination' ),
			'hz_pagination_border_radius'    => $this->to_corner_radius( isset( $settings['hz_pagination_border_radius'] ) ? $settings['hz_pagination_border_radius'] : array() ),
			'hz_pagination_padding'          => $this->to_padding(
				isset( $settings['hz_pagination_padding'] ) ? $settings['hz_pagination_padding'] : array(),
				'classic' === ( isset( $settings['pagination'] ) ? $settings['pagination'] : 'modern' ) ? '5' : '20'
			),

			'hz_sidebar_bg_color'            => $this->resolve_global_color( $settings, 'hz_sidebar_bg_color' ),
			'hz_sidebar_tx_color'            => $this->resolve_global_color( $settings, 'hz_sidebar_tx_color' ),
		);

		/**
		 * Filters the attributes passed to the block's render function from the Elementor widget.
		 *
		 * Pro Pack for WP Job Openings hooks in here to inject its own settings (Stack layout,
		 * per-spec multi-select filter types, Filtered List + preselected terms, Featured Image),
		 * registered via its own elementor/element/awsm-job-listings/* control hooks. This widget
		 * has no knowledge of those features — same separation as the Gutenberg block, which is
		 * extended by Pro Pack entirely through WordPress action/filter hooks.
		 *
		 * @param array      $atts     Attributes built from this widget's own (free-tier) settings.
		 * @param array      $settings Full Elementor settings array for this widget instance.
		 * @param Widget_Base $widget  This widget instance.
		 */
		$atts = apply_filters( 'awsm_jobs_elementor_widget_attributes', $atts, $settings, $this );

		echo Awsm_Job_Guten_Blocks::get_instance()->block_render_callback( $atts, '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
