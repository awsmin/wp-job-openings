<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">
	<h1><?php esc_html_e( 'Help', 'wp-job-openings' ); ?></h1>
	<div id="awsm-job-help" class="awsm-admin-settings">
		<div class="awsm-row awsm-help-main">
			<div class="awsm-col awsm-help-left">
				<?php do_action( 'before_awsm_help_left' ); ?>

				<div class="awsm-help-section">
					<p>
						<?php
							/* translators: %s: link to plugin support */
							printf( esc_html__( 'Thank you for trying out Job Openings plugin by AWSM Innovations. The plugin lets you add and manage job openings in a company easily. We primarily offer support through our support portal at %s. Sign up and open a ticket to get help. Be sure to mention your WordPress version and give as much additional information as possible.  Please feel free to reach us out through our website for customizations and feature requests.', 'wp-job-openings' ), '<a href="https://awsm.in/support" target="_blank">https://awsm.in/support</a>' );
						?>
					</p>
					<p>
						<?php
							/* translators: %1$s: opening anchor tag, %2$s: closing anchor tag */
							printf( esc_html__( 'If you liked our plugin please leave us a %1$s rating%2$s. A huge thanks in advance!', 'wp-job-openings' ), '<a class="awsm-ratings" href="https://wordpress.org/support/plugin/wp-job-openings/reviews/?rate=5#new-post" target="__blank"><i class="awsm-job-icon-star"></i><i class="awsm-job-icon-star"></i><i class="awsm-job-icon-star"></i><i class="awsm-job-icon-star"></i><i class="awsm-job-icon-star"></i>', '</a>' );
						?>
					</p>
				</div><!-- .awsm-help-section -->
				<div class="awsm-help-section">
					<h2><?php esc_html_e( 'Short codes', 'wp-job-openings' ); ?></h2>
					<p><?php esc_html_e( 'We like to keep things simple. WP Job Openings Plugin has only one shortcode you have to note. The shortcode will display the list of job openings you have in your website. ', 'wp-job-openings' ); ?></p>

					<p><?php esc_html_e( 'Example of a shortcode supported by WP Job Openings plugin is below', 'wp-job-openings' ); ?></p>
					<?php
						$example = htmlentities( '[awsmjobs listings="5" filters="no" loadmore="no"]' );
						printf( '<p><code>%1$s</code><button id="awsm-copy-clip" type="button" data-clipboard-text="%2$s" class="button">%3$s</button></p>', esc_html( $example ), esc_attr( $example ), esc_html__( 'Copy', 'wp-job-openings' ) );
					?>
					<p><?php esc_html_e( 'Attributes are explained below', 'wp-job-openings' ); ?></p>
					<?php
						$shortcode_atts = array(
							'filters'  => array(
								'description' => esc_html__( 'Whether to Show or Hide Job filters.', 'wp-job-openings' ),
								'arguments'   => 'yes/no ' . sprintf( '(yes - %s, no - %s)', esc_html__( 'Show filters', 'wp-job-openings' ), esc_html__( 'Hide filters', 'wp-job-openings' ) ),
								'default'     => esc_html__( "'Enable job filters in job listing' option in Settings", 'wp-job-openings' ),
							),
							'listings' => array(
								'description' => esc_html__( 'Default Number of Job Listings to display.', 'wp-job-openings' ),
								'arguments'   => esc_html__( 'Value that is no less than 1.', 'wp-job-openings' ),
								'default'     => esc_html__( "'Listings per page' option in Settings", 'wp-job-openings' ),
							),
							'loadmore' => array(
								'description' => esc_html__( "Whether to Show or Hide 'Load more...' button in Job Listings.", 'wp-job-openings' ),
								'arguments'   => 'yes/no ' . sprintf( '(yes - %s, no - %s)', esc_html__( 'Show button', 'wp-job-openings' ), esc_html__( 'Hide button', 'wp-job-openings' ) ),
								'default'     => 'yes',
							),
						);
						?>
					<table class="awsm-job-description-table">
						<?php foreach ( $shortcode_atts as $attr => $attr_details ) : ?>
								<tr>
									<td><code><?php echo esc_html( $attr ); ?></code></td>
									<td>
										<p><?php printf( '<strong>%s:</strong> %s', esc_html__( 'Description', 'wp-job-openings' ), esc_html( $attr_details['description'] ) ); ?></p>
										<p><?php printf( '<strong>%s:</strong> %s', esc_html__( 'Accepted Arguments', 'wp-job-openings' ), esc_html( $attr_details['arguments'] ) ); ?></p>
										<p><?php printf( '<strong>%s:</strong> %s', esc_html__( 'Default', 'wp-job-openings' ), esc_html( $attr_details['default'] ) ); ?></p>
									</td>
								</tr>
						<?php endforeach; ?>
					</table>
				</div><!-- .awsm-help-section -->
				<div class="awsm-help-section">
					<h2><?php esc_html_e( 'Have a feature request?', 'wp-job-openings' ); ?></h2>
					<p>
						<?php
							/* translators: %1$s: opening anchor tag, %2$s: closing anchor tag */
							printf( esc_html__( 'We are just getting started. Any suggestions you may have would help us to build this plugin to a powerful recruiting tool for your company. Please share your suggestions and feature requests through %1$s our website %2$s.', 'wp-job-openings' ), '<a href="https://awsm.in/contact/" target="_blank">', '</a>' );
						?>
					</p>
				</div><!-- .awsm-help-section -->
				<div class="awsm-help-section">
					<h2><?php esc_html_e( 'Thanks to.. ', 'wp-job-openings' ); ?></h2>
					<?php printf( '<p>%1$s: <a href="https://fontawesome.com/" target="_blank">%2$s</a>, <a href="https://www.flaticon.com/" target="_blank">%3$s</a>, <a href="http://animaticons.co/" target="_blank">%4$s</a></p>', esc_html__( 'Icons', 'wp-job-openings' ), 'Font Awesome', 'FlatIcons', 'Animaticons.co' ); ?>
					<?php printf( '<p>%1$s: <a href="https://clipboardjs.com/" target="_blank">%2$s</a>, <a href="https://select2.org/" target="_blank">%3$s</a>, <a href="http://selectric.js.org/" target="_blank">%4$s</a>, <a href="https://jqueryvalidation.org/" target="_blank">%5$s</a></p>', esc_html__( 'jQuery Plugins', 'wp-job-openings' ), 'clipboard.js', 'Select2', 'Selectric', 'Jquery Validation' ); ?>
				</div>

				<?php do_action( 'after_awsm_help_left' ); ?>
			</div><!-- .awsm-help-left -->
			<div class="awsm-col awsm-help-right">
				<?php do_action( 'before_awsm_help_right' ); ?>

				<div class="awsm-author-details">
					<h2><?php esc_html_e( 'Designed and developed by', 'wp-job-openings' ); ?></h2>
					<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/awsm-logo.png' ); ?>" width="68" height="68" alt="awsm innovations">
					<h3><?php esc_html_e( 'awsm innovations', 'wp-job-openings' ); ?></h3>
					<ul class="awsm-social">
						<li><a href="https://www.facebook.com/awsminnovations" target="_blank" title="awsm innovations"><span class="awsm-job-icon-facebook"></span></a></li>
						<li><a href="https://twitter.com/awsmin" target="_blank" title="awsm innovations"><span class="awsm-job-icon-twitter"></span></a></li>
						<li><a href="https://github.com/awsmin" target="_blank" title="awsm innovations"><span class="awsm-job-icon-github"></span></a></li>
						<li><a href="https://codecanyon.net/user/awsmin" target="_blank" title="awsm innovations"><span class="awsm-icon awsm-job-icon-envato"><?php esc_html_e( 'Envato', 'wp-job-openings' ); ?></span></a></li>
						<li><a href="https://www.instagram.com/awsmin/" target="_blank" title="awsm innovations"><span class="awsm-job-icon-instagram"></span></a></li>
					</ul>
				</div><!-- .awsm-author-details -->

				<?php do_action( 'after_awsm_help_right' ); ?>
			</div><!-- .awsm-help-right -->
		</div><!-- .row -->
	</div><!-- .awsm-admin-settings -->
</div>
