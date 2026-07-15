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
		return 'eicon-post-list';
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
	 */
	protected function add_border_controls( $key, $default_width = 1, $default_color = '#cccccc' ) {
		$this->add_control(
			"{$key}_border_width",
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
			)
		);

		$this->add_control(
			"{$key}_border_color",
			array(
				'label'   => esc_html__( 'Border Color', 'wp-job-openings' ),
				'type'    => Controls_Manager::COLOR,
				'default' => $default_color,
			)
		);

		$this->add_control(
			"{$key}_border_radius",
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
			)
		);
	}

	/**
	 * Adds a padding (top/right/bottom/left) control for one section of the listing.
	 *
	 * @param string $key      Attribute key prefix, e.g. 'hz_sf'.
	 * @param array  $defaults Default top/right/bottom/left values (numbers, px assumed).
	 */
	protected function add_padding_control( $key, $defaults ) {
		$this->add_control(
			"{$key}_padding",
			array(
				'label'      => esc_html__( 'Padding', 'wp-job-openings' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array_merge( $defaults, array( 'unit' => 'px' ) ),
			)
		);
	}

	protected function register_controls() {
		$this->register_content_general_controls();
		$this->register_content_filters_controls();
		$this->register_content_layout_controls();
		$this->register_style_search_filters_controls();
		$this->register_style_list_controls();
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
				'label'     => esc_html__( 'Search Placeholder', 'wp-job-openings' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
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
				'label'     => esc_html__( 'Filter Placement', 'wp-job-openings' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'top',
				'options'   => array(
					'top'  => esc_html__( 'Top', 'wp-job-openings' ),
					'side' => esc_html__( 'Side', 'wp-job-openings' ),
				),
				'condition' => array(
					'enable_job_filter' => 'yes',
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

		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'Layout', 'wp-job-openings' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'list',
				'options' => array(
					'list' => esc_html__( 'List', 'wp-job-openings' ),
					'grid' => esc_html__( 'Grid', 'wp-job-openings' ),
				),
			)
		);

		$this->add_control(
			'number_of_columns',
			array(
				'label'     => esc_html__( 'Columns', 'wp-job-openings' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 6,
				'default'   => 3,
				'condition' => array(
					'layout' => 'grid',
				),
			)
		);

		$this->add_control(
			'hz_sidebar_width',
			array(
				'label'     => esc_html__( 'Sidebar Width (%)', 'wp-job-openings' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 10,
				'max'       => 50,
				'default'   => 33,
				'condition' => array(
					'placement'         => 'side',
					'enable_job_filter' => 'yes',
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

	protected function register_style_list_controls() {
		$this->start_controls_section(
			'section_style_list',
			array(
				'label' => esc_html__( 'List Style', 'wp-job-openings' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

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
				'label'     => esc_html__( 'Button Text', 'wp-job-openings' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'More Details →', 'wp-job-openings' ),
				'condition' => array(
					'hz_button_style!' => 'none',
				),
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
				'label'     => esc_html__( 'Text Color', 'wp-job-openings' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'hz_button_style!' => 'none',
				),
			)
		);

		$this->add_border_controls( 'hz_bs', 1, '#4e35df' );
		$this->add_padding_control(
			'hz_bs',
			array(
				'top'    => '13',
				'right'  => '13',
				'bottom' => '13',
				'left'   => '13',
			)
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
		$this->add_padding_control(
			'hz_pagination',
			array(
				'top'    => '20',
				'right'  => '20',
				'bottom' => '20',
				'left'   => '20',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_sidebar_controls() {
		$this->start_controls_section(
			'section_style_sidebar',
			array(
				'label'     => esc_html__( 'Sidebar', 'wp-job-openings' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'placement' => 'side',
				),
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
	 * @param array $dimensions Raw control value with top/right/bottom/left/unit keys.
	 * @return array
	 */
	protected function to_padding( $dimensions ) {
		$unit = ! empty( $dimensions['unit'] ) ? $dimensions['unit'] : 'px';
		return array(
			'top'    => ( isset( $dimensions['top'] ) && $dimensions['top'] !== '' ? $dimensions['top'] : '0' ) . $unit,
			'right'  => ( isset( $dimensions['right'] ) && $dimensions['right'] !== '' ? $dimensions['right'] : '0' ) . $unit,
			'bottom' => ( isset( $dimensions['bottom'] ) && $dimensions['bottom'] !== '' ? $dimensions['bottom'] : '0' ) . $unit,
			'left'   => ( isset( $dimensions['left'] ) && $dimensions['left'] !== '' ? $dimensions['left'] : '0' ) . $unit,
		);
	}

	protected function border_value( $settings, $key ) {
		$width = isset( $settings[ "{$key}_border_width" ]['size'] ) ? $settings[ "{$key}_border_width" ]['size'] : 1;
		$unit  = isset( $settings[ "{$key}_border_width" ]['unit'] ) ? $settings[ "{$key}_border_width" ]['unit'] : 'px';
		return array(
			'width' => $width . $unit,
			'color' => isset( $settings[ "{$key}_border_color" ] ) ? $settings[ "{$key}_border_color" ] : '',
		);
	}

	protected function render() {
		if ( ! class_exists( 'Awsm_Job_Guten_Blocks' ) || ! function_exists( 'awsm_jobs_query' ) ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		$block_id = 'awsm-elementor-' . sanitize_html_class( $this->get_id() );

		$filter_options = isset( $settings['filter_options'] ) && is_array( $settings['filter_options'] ) ? array_values( $settings['filter_options'] ) : array();

		$atts = array(
			'blockId'                        => $block_id,
			'anchor'                         => '',
			'search'                         => ! empty( $settings['search'] ),
			'search_placeholder'             => isset( $settings['search_placeholder'] ) ? $settings['search_placeholder'] : '',
			'enable_job_filter'              => ! empty( $settings['enable_job_filter'] ),
			'filter_options'                 => $filter_options,
			'filtersInitialized'             => true,
			'filter_types'                   => array(),
			'filter_items_order'             => isset( $settings['filter_items_order'] ) ? $settings['filter_items_order'] : 'custom',
			'placement'                      => isset( $settings['placement'] ) ? $settings['placement'] : 'top',
			'layout'                         => isset( $settings['layout'] ) ? $settings['layout'] : 'list',
			'number_of_columns'              => isset( $settings['number_of_columns'] ) ? intval( $settings['number_of_columns'] ) : 3,
			'list_type'                      => 'all',
			'order_by'                       => isset( $settings['order_by'] ) ? $settings['order_by'] : 'new_to_old',
			'hide_expired_jobs'              => ! empty( $settings['hide_expired_jobs'] ),
			'listing_per_page'               => isset( $settings['listing_per_page'] ) ? intval( $settings['listing_per_page'] ) : 10,
			'pagination'                     => isset( $settings['pagination'] ) ? $settings['pagination'] : 'modern',
			'show_spec_icon'                 => ! empty( $settings['show_spec_icon'] ),
			'other_options'                  => isset( $settings['other_options'] ) && is_array( $settings['other_options'] ) ? array_values( $settings['other_options'] ) : array(),
			'hz_sidebar_width'               => isset( $settings['hz_sidebar_width'] ) ? floatval( $settings['hz_sidebar_width'] ) : 33.333,

			'hz_sf_background_color'        => isset( $settings['hz_sf_background_color'] ) ? $settings['hz_sf_background_color'] : '',
			'hz_sf_text_color'               => isset( $settings['hz_sf_text_color'] ) ? $settings['hz_sf_text_color'] : '',
			'hz_sf_border'                   => $this->border_value( $settings, 'hz_sf' ),
			'hz_sf_border_radius'            => $this->to_corner_radius( isset( $settings['hz_sf_border_radius'] ) ? $settings['hz_sf_border_radius'] : array() ),
			'hz_sf_padding'                  => $this->to_padding( isset( $settings['hz_sf_padding'] ) ? $settings['hz_sf_padding'] : array() ),

			'hz_ls_border'                   => $this->border_value( $settings, 'hz_ls' ),
			'hz_ls_border_radius'            => $this->to_corner_radius( isset( $settings['hz_ls_border_radius'] ) ? $settings['hz_ls_border_radius'] : array() ),

			'hz_jl_background_color'         => isset( $settings['hz_jl_background_color'] ) ? $settings['hz_jl_background_color'] : '',
			'hz_jl_text_color'               => isset( $settings['hz_jl_text_color'] ) ? $settings['hz_jl_text_color'] : '',
			'hz_jl_border'                   => $this->border_value( $settings, 'hz_jl' ),
			'hz_jl_border_radius'            => $this->to_corner_radius( isset( $settings['hz_jl_border_radius'] ) ? $settings['hz_jl_border_radius'] : array() ),
			'hz_jl_padding'                  => $this->to_padding( isset( $settings['hz_jl_padding'] ) ? $settings['hz_jl_padding'] : array() ),

			'hz_button_style'                => isset( $settings['hz_button_style'] ) ? $settings['hz_button_style'] : 'none',
			'hz_button_text'                 => isset( $settings['hz_button_text'] ) ? $settings['hz_button_text'] : '',
			'hz_button_background_color'     => isset( $settings['hz_button_background_color'] ) ? $settings['hz_button_background_color'] : '',
			'hz_button_text_color'           => isset( $settings['hz_button_text_color'] ) ? $settings['hz_button_text_color'] : '',
			'hz_bs_border'                   => $this->border_value( $settings, 'hz_bs' ),
			'hz_bs_border_radius'            => $this->to_corner_radius( isset( $settings['hz_bs_border_radius'] ) ? $settings['hz_bs_border_radius'] : array() ),
			'hz_bs_padding'                  => $this->to_padding( isset( $settings['hz_bs_padding'] ) ? $settings['hz_bs_padding'] : array() ),

			'hz_pagination_background_color' => isset( $settings['hz_pagination_background_color'] ) ? $settings['hz_pagination_background_color'] : '',
			'hz_pagination_text_color'       => isset( $settings['hz_pagination_text_color'] ) ? $settings['hz_pagination_text_color'] : '',
			'hz_pagination_border'           => $this->border_value( $settings, 'hz_pagination' ),
			'hz_pagination_border_radius'    => $this->to_corner_radius( isset( $settings['hz_pagination_border_radius'] ) ? $settings['hz_pagination_border_radius'] : array() ),
			'hz_pagination_padding'          => $this->to_padding( isset( $settings['hz_pagination_padding'] ) ? $settings['hz_pagination_padding'] : array() ),

			'hz_sidebar_bg_color'            => isset( $settings['hz_sidebar_bg_color'] ) ? $settings['hz_sidebar_bg_color'] : '',
			'hz_sidebar_tx_color'            => isset( $settings['hz_sidebar_tx_color'] ) ? $settings['hz_sidebar_tx_color'] : '',
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
