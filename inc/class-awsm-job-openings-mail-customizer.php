<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Mail_Customizer {
	private static $instance = null;

	public function __construct() {
		add_action( 'awsm_jobs_notification_html_template_head', array( $this, 'template_head' ) );
		add_action( 'awsm_jobs_notification_html_template_footer', array( $this, 'template_footer' ) );
		add_filter( 'awsm_jobs_notification_customizer_fields', array( $this, 'remove_legacy_customizer_fields' ), 100 );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function template_head( $settings ) {
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo AWSM_Job_Openings_UI_Builder::generate_css( self::get_reset_styles() );
		echo AWSM_Job_Openings_UI_Builder::generate_css( self::get_main_styles( $settings ) );
		// phpcs:enable
	}

	public function template_footer( $settings ) {
		?>
			<td class="awsm-jobs-mail-footer-text">
				<?php echo nl2br( self::sanitize_content( $settings['footer_text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</td>
		<?php
	}

	public static function get_default_logo() {
		return AWSM_JOBS_PLUGIN_URL . '/assets/img/hirezoot-logo.png';
	}

	public static function sanitize_content( $content ) {
		/**
		 * Filters the notification customizer allowed html for the content.
		 *
		 * @since 2.2.0
		 *
		 * @param string|array $allowed_html Allowed HTML elements and attributes, or a context name.
		 */
		$allowed_html = apply_filters( 'awsm_jobs_notification_customizer_allowed_html', 'post' );

		return wp_kses( $content, $allowed_html );
	}

	public static function get_settings() {
		if ( ! class_exists( 'AWSM_Job_Openings_Settings' ) ) {
			require_once AWSM_JOBS_PLUGIN_DIR . '/admin/class-awsm-job-openings-settings.php';
		}
		$default_from_email = AWSM_Job_Openings_Settings::awsm_from_email( true );
		/**
		 * Filters the notification customizer default values.
		 *
		 * @since 2.2.0
		 *
		 * @param array $default_values Default values.
		 */
		$default_values = apply_filters(
			'awsm_jobs_notification_customizer_default_values',
			array(
				'logo'        => 'default',
				'base_color'  => '#96F5E3',
				'from_email'  => $default_from_email,
				/* translators: %1$s: Site link, %2$s: Plugin website link */
				'footer_text' => sprintf( esc_html__( 'Sent from %1$s by %2$s Plugin', 'wp-job-openings' ), '<a href="{site-url}">{site-title}</a>', '<a href="' . AWSM_JOBS_SITE_URL . '">' . esc_html__( 'HireZoot', 'wp-job-openings' ) . '</a>' ),
			)
		);

		$settings            = get_option( 'awsm_jobs_notification_customizer' );
		$customizer_settings = wp_parse_args( $settings, $default_values );
		// The background color option is removed. Older Pro Pack versions still apply 'bg_color' when it is set.
		unset( $customizer_settings['bg_color'] );
		return $customizer_settings;
	}

	public function remove_legacy_customizer_fields( $customizer_fields ) {
		foreach ( $customizer_fields as $index => $field ) {
			if ( isset( $field['id'] ) && $field['id'] === 'awsm_jobs_notification_customizer_bg_color' ) {
				unset( $customizer_fields[ $index ] );
			}
		}
		return array_values( $customizer_fields );
	}

	public static function validate_template() {
		$unsupported_versions = array( '2.0.0' );

		$is_valid_header_template = awsm_jobs_is_valid_template_file( AWSM_Job_Openings::get_template_path( 'header.php', 'mail' ), $unsupported_versions );
		$is_valid_footer_template = awsm_jobs_is_valid_template_file( AWSM_Job_Openings::get_template_path( 'footer.php', 'mail' ), $unsupported_versions );

		if ( ! $is_valid_header_template || ! $is_valid_footer_template ) :
			?>
				<div class="awsm-jobs-error-container">
					<div class="awsm-jobs-error">
						<p>
							<?php esc_html_e( 'It looks like you have overridden the mail HTML template files. This version is unsupported with the notification customizer. Please update template files for full support.', 'wp-job-openings' ); ?>
						</p>
					</div>
				</div>
			<?php
		endif;
	}

	public static function get_logo( $settings = array() ) {
		$logo = '';
		if ( empty( $settings ) ) {
			$settings = self::get_settings();
		}

		if ( ! empty( $settings['logo'] ) ) {
			$image_url  = self::get_default_logo();
			$img_alt    = esc_html__( 'HireZoot', 'wp-job-openings' );
			$extra_attr = '';

			if ( $settings['logo'] === 'default' ) {
				$extra_attr = ' width="174" height="43"';
			} else {
				$image_url    = awsm_jobs_get_original_image_url( $settings['logo'] );
				$company_name = get_option( 'awsm_job_company_name' );
				$img_alt      = ! empty( $company_name ) ? $company_name : get_bloginfo( 'name', 'display' );
			}
			$logo = sprintf( '<a href="%1$s" target="_blank"><h1><img src="%2$s" alt="%3$s"%4$s style="max-width: 100%%; height:auto;"></h1></a>', esc_url( site_url( '/' ) ), esc_url( $image_url ), esc_attr( $img_alt ), $extra_attr );
		}
		/**
		 * Filters the notification HTML mail template logo.
		 *
		 * @since 2.2.0
		 *
		 * @param string $logo Mail logo HTML content.
		 * @param array $settings Notification mail customizer settings.
		 */
		$logo = apply_filters( 'awsm_jobs_notification_html_template_logo', $logo, $settings );

		return $logo;
	}

	public static function get_reset_styles() {
		$styles = array(
			array(
				'selector'    => 'html, body',
				'declaration' => array(
					'margin'     => '0 auto !important',
					'padding'    => '0 !important',
					'height'     => '100% !important',
					'width'      => '100% !important',
					'background' => '#ffffff',
				),
			),
			/* What it does: Stops email clients resizing small text. */
			array(
				'selector'    => '*',
				'declaration' => array(
					'-ms-text-size-adjust'     => '100%',
					'-webkit-text-size-adjust' => '100%',
				),
			),
			/* What it does: Centers email on Android 4.4. */
			array(
				'selector'    => 'div[style*="margin: 16px 0"]',
				'declaration' => array(
					'margin' => '0 !important',
				),
			),
			/* What it does: Stops Outlook from adding extra spacing to tables. */
			array(
				'selector'    => 'table, td',
				'declaration' => array(
					'mso-table-lspace' => '0pt !important',
					'mso-table-rspace' => '0pt !important',
				),
			),
			/* What it does: Fixes webkit padding issue. */
			array(
				'selector'    => 'table',
				'declaration' => array(
					'border-spacing'  => '0 !important',
					'border-collapse' => 'collapse !important',
					'table-layout'    => 'fixed !important',
					'margin'          => '0 auto !important',
				),
			),
			/* What it does: Uses a better rendering method when resizing images in IE. */
			array(
				'selector'    => 'img',
				'declaration' => array(
					'-ms-interpolation-mode' => 'bicubic',
				),
			),
			/* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
			array(
				'selector'    => 'a',
				'declaration' => array(
					'text-decoration' => 'none',
				),
			),
			/* What it does: A work-around for email clients meddling in triggered links. */
			array(
				'selector'    => '*[x-apple-data-detectors], .unstyle-auto-detected-links *, .aBn',
				'declaration' => array(
					'border-bottom'   => '0 !important',
					'cursor'          => 'default !important',
					'color'           => 'inherit !important',
					'text-decoration' => 'none !important',
					'font-size'       => 'inherit !important',
					'font-family'     => 'inherit !important',
					'font-weight'     => 'inherit !important',
					'line-height'     => 'inherit !important',
				),
			),
			/* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
			array(
				'selector'    => '.a6S',
				'declaration' => array(
					'display' => 'none !important',
					'opacity' => '0.01 !important',
				),
			),
			/* What it does: Prevents Gmail from changing the text color in conversation threads. */
			array(
				'selector'    => '.im',
				'declaration' => array(
					'color' => 'inherit !important',
				),
			),
			/* If the above doesn't work, add a .g-img class to any image in question. */
			array(
				'selector'    => 'img.g-img + div',
				'declaration' => array(
					'display' => 'none !important',
				),
			),
			/* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
			/* Create one of these media queries for each additional viewport size you'd like to fix */
			/* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
			array(
				'media_query' => '@media only screen and (min-device-width: 320px) and (max-device-width: 374px)',
				'css'         => array(
					array(
						'selector'    => 'u ~ div .email-container',
						'declaration' => array(
							'min-width' => '320px !important',
						),
					),
				),
			),
			/* iPhone 6, 6S, 7, 8, and X */
			array(
				'media_query' => '@media only screen and (min-device-width: 375px) and (max-device-width: 413px)',
				'css'         => array(
					array(
						'selector'    => 'u ~ div .email-container',
						'declaration' => array(
							'min-width' => '375px !important',
						),
					),
				),
			),
			/* iPhone 6+, 7+, and 8+ */
			array(
				'media_query' => '@media only screen and (min-device-width: 414px)',
				'css'         => array(
					array(
						'selector'    => 'u ~ div .email-container',
						'declaration' => array(
							'min-width' => '414px !important',
						),
					),
				),
			),
		);
		/**
		 * Filters the notification HTML mail template reset styles.
		 *
		 * @since 2.2.0
		 *
		 * @param array $styles Reset styles.
		 */
		$styles = apply_filters( 'awsm_jobs_notification_html_template_reset_styles', $styles );

		return $styles;
	}

	public static function get_main_styles( $settings = array() ) {
		if ( empty( $settings ) ) {
			$settings = self::get_settings();
		}

		$styles = array(
			array(
				'selector'    => '.primary',
				'declaration' => array(
					'background' => '#f3a333',
				),
			),
			array(
				'selector'    => '.bg_white',
				'declaration' => array(
					'background' => '#ffffff',
				),
			),
			array(
				'selector'    => '.bg_light',
				'declaration' => array(
					'background' => '#fafafa',
				),
			),
			array(
				'selector'    => '.bg_black',
				'declaration' => array(
					'background' => '#000000',
				),
			),
			array(
				'selector'    => '.bg_dark',
				'declaration' => array(
					'background' => 'rgba(0,0,0,.8)',
				),
			),
			array(
				'selector'    => '.btn',
				'declaration' => array(
					'padding' => '14px 40px',
				),
			),
			array(
				'selector'    => '.btn.btn-primary',
				'declaration' => array(
					'border-radius' => '3px',
					'background'    => $settings['base_color'],
					'color'         => '#ffffff',
					'border'        => '1px solid #207E76',
					'font-weight'   => 'bold',
				),
			),
			array(
				'selector'    => 'h1, h2, h3, h4, h5, h6',
				'declaration' => array(
					'font-family' => '"Helvetica Neue", Helvetica, Arial, sans-serif',
					'color'       => '#1F3130',
					'margin-top'  => '0',
				),
			),
			array(
				'selector'    => 'body',
				'declaration' => array(
					'font-family' => '"Helvetica Neue", Helvetica, Arial, sans-serif',
					'font-weight' => '400',
					'font-size'   => '16px',
					'line-height' => '1.125',
					'color'       => '#4F5F5E',
				),
			),
			array(
				'selector'    => 'a',
				'declaration' => array(
					'color' => $settings['base_color'],
				),
			),
			array(
				'selector'    => 'table',
				'declaration' => array(
					'color' => '#4F5F5E',
				),
			),
			array(
				'selector'    => '.logo',
				'declaration' => array(
					'padding'    => '40px 20px 10px',
					'text-align' => 'center',
				),
			),
			array(
				'selector'    => '.email-container-body',
				'declaration' => array(
					'border-top' => '9px solid ' . $settings['base_color'],
				),
			),
			array(
				'selector'    => '.email-main-container',
				'declaration' => array(
					'padding' => '20px 0',
				),
			),
			array(
				'selector'    => '.logo h1',
				'declaration' => array(
					'margin' => '0',
				),
			),
			array(
				'selector'    => '.logo h1 a',
				'declaration' => array(
					'color'          => '#000',
					'font-size'      => '20px',
					'font-weight'    => '700',
					'text-transform' => 'uppercase',
					'font-family'    => '"Montserrat", sans-serif',
				),
			),
			array(
				'selector'    => '.main-content',
				'declaration' => array(
					'padding' => '20px 0 10px',
				),
			),
			array(
				'selector'    => '.main-content h2',
				'declaration' => array(
					'font-size'     => '25px',
					'margin-bottom' => '17px',
				),
			),
			array(
				'selector'    => '.main-content h3',
				'declaration' => array(
					'font-size'      => '16px',
					'text-transform' => 'uppercase',
					'margin-bottom'  => '20px',
				),
			),
			array(
				'selector'    => '.mail-content-stats ul',
				'declaration' => array(
					'list-style' => 'none',
					'padding'    => '0',
					'margin'     => '0',
				),
			),
			array(
				'selector'    => '.mail-content-stats li',
				'declaration' => array(
					'display' => 'inline-block',
					'margin'  => '0 25px',
				),
			),
			array(
				'selector'    => '.mail-content-stats li span',
				'declaration' => array(
					'display'   => 'block',
					'font-size' => '43px',
					'color'     => $settings['base_color'],
				),
			),
			array(
				'selector'    => '.job-table td, .job-table th',
				'declaration' => array(
					'text-align' => 'left',
					'padding'    => '13px 20px',
				),
			),
			array(
				'selector'    => '.main-content-in-2',
				'declaration' => array(
					'padding'       => '30px 0',
					'border-bottom' => '1px solid #D7DFDF',
				),
			),
			array(
				'selector'    => '.main-content-in-3',
				'declaration' => array(
					'padding-top' => '30px',
				),
			),
			array(
				'selector'    => '.footer a',
				'declaration' => array(
					'color'       => '#3A3A3A',
					'font-weight' => '600',
				),
			),
			array(
				'selector'    => '.awsm-jobs-mail-footer-text',
				'declaration' => array(
					'padding'     => '20px 40px 40px',
					'text-align'  => 'center',
					'font-size'   => '13px',
					'line-height' => '1.6',
					'color'       => '#5F6368',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-intro',
				'declaration' => array(
					'padding'    => '0 20px',
					'text-align' => 'center',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-title',
				'declaration' => array(
					'max-width'   => '420px',
					'margin'      => '0 auto 14px',
					'font-size'   => '30px',
					'line-height' => '1.3',
					'font-weight' => '700',
					'color'       => '#1A1A1A',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-subtitle',
				'declaration' => array(
					'max-width'   => '300px',
					'margin'      => '0 auto',
					'font-size'   => '16px',
					'line-height' => '1.5',
					'color'       => '#4B4B4B',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-stats-wrap',
				'declaration' => array(
					'padding' => '28px 20px',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-stats',
				'declaration' => array(
					'width'     => '100%',
					'max-width' => '504px',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-stat',
				'declaration' => array(
					'width'          => '33.33%',
					'padding'        => '0 7px',
					'vertical-align' => 'top',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-stat-box',
				'declaration' => array(
					'padding'          => '30px 8px',
					'background-color' => $settings['base_color'],
					'border-radius'    => '6px',
					'text-align'       => 'center',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-stat-count',
				'declaration' => array(
					'display'       => 'block',
					'margin-bottom' => '10px',
					'font-size'     => '28px',
					'line-height'   => '1.2',
					'font-weight'   => '700',
					'color'         => '#1A1A1A',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-stat-label',
				'declaration' => array(
					'display'     => 'block',
					'font-size'   => '13px',
					'line-height' => '1.4',
					'color'       => '#2B2B2B',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-recent-wrap',
				'declaration' => array(
					'padding' => '16px 20px 0',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-recent',
				'declaration' => array(
					'max-width'     => '518px',
					'margin'        => '0 auto',
					'padding'       => '18px 10px 10px',
					'border'        => '1px solid #D9DCE1',
					'border-radius' => '12px',
					'text-align'    => 'center',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-recent-title',
				'declaration' => array(
					'margin'         => '0 0 16px',
					'font-size'      => '16px',
					'font-weight'    => '600',
					'text-transform' => 'none',
					'color'          => '#2B2B2B',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-table',
				'declaration' => array(
					'width'     => '100%',
					'font-size' => '13px',
					'color'     => '#1A1A1A',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-table th',
				'declaration' => array(
					'padding'          => '9px 8px',
					'background-color' => '#F0F2F5',
					'font-weight'      => '400',
					'color'            => '#2B2B2B',
					'text-align'       => 'left',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-table td',
				'declaration' => array(
					'padding'       => '15px 8px',
					'border-bottom' => '1px solid #E6E8EB',
					'text-align'    => 'left',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-table td a',
				'declaration' => array(
					'color' => '#1A1A1A',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-action',
				'declaration' => array(
					'margin' => '16px 0 2px',
				),
			),
			array(
				'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-btn',
				'declaration' => array(
					'display'          => 'inline-block',
					'padding'          => '11px 16px',
					'background-color' => '#262626',
					'border-radius'    => '6px',
					'font-size'        => '15px',
					'font-weight'      => '600',
					'color'            => '#FFFFFF',
				),
			),
			array(
				'media_query' => '@media screen and (max-width: 550px)',
				'css'         => array(
					array(
						'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-title',
						'declaration' => array(
							'font-size' => '24px',
						),
					),
					array(
						'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-stat',
						'declaration' => array(
							'padding' => '0 4px',
						),
					),
					array(
						'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-stat-box',
						'declaration' => array(
							'padding' => '20px 4px',
						),
					),
					array(
						'selector'    => '.awsm-jobs-digest .awsm-jobs-digest-stat-count',
						'declaration' => array(
							'font-size' => '22px',
						),
					),
				),
			),
			array(
				'media_query' => '@media screen and (max-width: 550px)',
				'css'         => array(
					array(
						'selector'    => '.logo',
						'declaration' => array(
							'padding' => '25px 15px 5px',
						),
					),
					array(
						'selector'    => '.main-content',
						'declaration' => array(
							'padding' => '15px 0 5px',
						),
					),
					array(
						'selector'    => '.awsm-jobs-mail-footer-text',
						'declaration' => array(
							'padding' => '15px 20px 25px',
						),
					),
					array(
						'selector'    => '.job-table td, .job-table th',
						'declaration' => array(
							'padding' => '10px',
						),
					),
					array(
						'selector'    => '.main-content li',
						'declaration' => array(
							'margin' => '0 10px',
						),
					),
					array(
						'selector'    => '.main-content li span',
						'declaration' => array(
							'font-size' => '34px',
						),
					),
					array(
						'selector'    => '.main-content-in-2',
						'declaration' => array(
							'padding' => '25px 0',
						),
					),
					array(
						'selector'    => '.main-content-in-3',
						'declaration' => array(
							'padding-top' => '25px',
						),
					),
				),
			),
		);
		/**
		 * Filters the notification HTML mail template main styles.
		 *
		 * @since 2.2.0
		 *
		 * @param array $styles Main styles.
		 * @param array $settings Notification mail customizer settings.
		 */
		$styles = apply_filters( 'awsm_jobs_notification_html_template_main_styles', $styles, $settings );

		return $styles;
	}
}
