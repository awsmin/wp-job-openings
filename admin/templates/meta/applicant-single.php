<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$applicant_email           = esc_attr( get_post_meta( $post->ID, 'awsm_applicant_email', true ) );
$applicant_details         = $this->get_applicant_meta_details_list( $post->ID, array( 'awsm_applicant_email' => $applicant_email ) );
$attachment_id             = get_post_meta( $post->ID, 'awsm_attachment_id', true );
$tab_applicant_single_view = AWSM_Job_Openings_Meta::set_applicant_single_view_tab();
$applicant_tab_contents               = AWSM_Job_Openings_Meta::get_applicant_single_view_content( $post->ID, $attachment_id );
$resume_details            = $this->get_attached_file_details( $attachment_id );
$full_file_name            = get_post_meta( $attachment_id, 'awsm_actual_file_name', true );
$applicant_job_id          = get_post_meta( $post->ID, 'awsm_job_id', true );
$resume_field_label        = ( new AWSM_Job_Openings_Form() )->dynamic_form_fields( $applicant_job_id )['awsm_file']['label'];
$this->is_main_applicant_viewed( $post->ID );
/**
 * Initialize applicant meta box.
 *
 * @since 1.6.0
 */
do_action( 'awsm_job_applicant_mb_init', $post->ID );
?>

<div class="awsm-application-container awsm-clearfix">
	<div class="awsm-application-main">
		<div class="awsm-application-head">
			<div class="awsm-application-head-main">
				<?php
				/**
				 * Fires before applicant photo content.
				 *
				 * @since 1.6.0
				 */
				do_action( 'before_awsm_job_applicant_mb_photo', $post->ID );

				$avatar = apply_filters( 'awsm_applicant_photo', get_avatar( $applicant_email, 130 ) );
				echo '<div class="awsm-applicant-image">' . $avatar . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
				<div class="awsm-applicant-info">
					<h3><?php echo esc_html( $applicant_details['name'] ); ?></h3>
					<?php $title = esc_html( sprintf( get_post_meta( $post->ID, 'awsm_apply_for', true ) ) ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
					<!-- Translators: %s is the title to display in the paragraph. -->
					<p><?php printf( esc_html__( '%s', 'wp-job-openings' ), esc_html( $title ) ); ?></p>

				</div><!-- .awsm-applicant-info -->
				<?php
				/**
				 * Fires after applicant photo content.
				 *
				 * @since 1.6.0
				 */
				do_action( 'after_awsm_job_applicant_mb_photo', $post->ID );
				?>
			</div><!-- .awsm-application-head-main -->
			<div class="awsm-application-actions <?php echo ! class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ? 'pro-feature' : ''; ?>">
				<span class="pro-ft"><?php esc_html_e( 'Pro Features', 'wp-job-openings' ); ?></span>
				<?php if ( class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ) : ?>
					<?php do_action( 'awsm_application_rating_data' ); ?>
				<?php else : ?>
					<div class="awsm-application-rating-disabled">
						<?php
						wp_star_rating(
							array(
								'rating' => 3,
								'type'   => 'rating',
							)
						);
						?>
					</div>
				<?php endif; ?>
				<div class="awsm-application-action awsm-dropdown">
					<a class="awsm-application-action-btn awsm-dropdown-toggle" href="#">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M6.40008 1.60001C6.40008 2.48367 7.11642 3.20002 8.00009 3.20002C8.88375 3.20002 9.6001 2.48367 9.6001 1.60001C9.6001 0.716349 8.88375 -3.13126e-08 8.00009 -6.99387e-08C7.11642 -1.08565e-07 6.40008 0.716349 6.40008 1.60001Z" fill="#161616"/>
							<path d="M6.40008 7.99997C6.40008 8.88364 7.11642 9.59999 8.00009 9.59999C8.88375 9.59999 9.6001 8.88364 9.6001 7.99997C9.6001 7.11631 8.88375 6.39996 8.00009 6.39996C7.11642 6.39996 6.40008 7.11631 6.40008 7.99997Z" fill="#161616"/>
							<path d="M6.40008 14.4C6.40008 15.2837 7.11642 16 8.00009 16C8.88375 16 9.6001 15.2837 9.6001 14.4C9.6001 13.5163 8.88375 12.8 8.00009 12.8C7.11642 12.8 6.40008 13.5163 6.40008 14.4Z" fill="#161616"/>
						</svg>
					</a>
					<?php do_action( 'after_awsm_job_applicant_mb_details_list', $post->ID ); ?>          
				</div><!-- .awsm-application-action -->
			</div><!-- .awsm-application-head-actions -->
		</div><!-- .awsm-application-head -->
		<div class="application-main-cnt">
			<?php do_action( 'awsm_job_application_edit' ); ?>
			<?php if ( ! isset( $_GET['application'] ) || $_GET['application'] !== 'edit' ) : ?>
				<div class="application-main-cnt-tab-sec">
					<!-- Tabs Navigation -->
					<ul class="application-main-tab">
						<?php foreach ( $tab_applicant_single_view as $key => $tab_data ) : ?>
							<li>
								<a href="#awsm-applicant-<?php echo esc_attr( $key ); ?>" class="<?php echo $key === 'profile' ? 'active' : ''; ?>">
									<?php
									if ( is_array( $tab_data ) && isset( $tab_data['label'] ) ) {
										echo esc_html( $tab_data['label'] );
										if ( isset( $tab_data['count'] ) ) {
											echo '<span>' . intval( $tab_data['count'] ) . '</span>';
										}
									} else {
										echo esc_html( $tab_data );
									}
									?>
								</a>
							</li>
						<?php endforeach; ?>
						<?php if ( ! class_exists( 'AWSM_Job_Openings_Pro_Pack' ) ) : ?>
							<li class="pro-feature">
							<div class="pro-ft">Pro Features</div>
							<a href="#"><?php echo esc_html__( 'Notifications', 'wp-job-openings' ); ?><span>8</span></a>
							<a href="#"><?php echo esc_html__( 'Remarks', 'wp-job-openings' ); ?><span>8</span></a>
							</li>
						<?php endif; ?>
					</ul>
					<!-- Tabs Content -->
					<div class="application-main-tab-items">
						<!-- Profile Tab -->
						<div id="awsm-applicant-profile" class="application-main-tab-item awsm-applicant-profile active">
							<?php
							do_action( 'before_awsm_job_applicant_mb_details_list', $post->ID );
							?>
							<ul class="awsm-applicant-details-list">
								<?php
								echo wp_kses_post( $applicant_details['list'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								if ( ! empty( $resume_details ) ) :
									$file_size_display = isset( $resume_details['file_size']['display'] ) ? $resume_details['file_size']['display'] : '';
									?>
									<li>
										<label>
											<?php echo ! empty( $resume_field_label ) ? esc_html( $resume_field_label ) : esc_html__( 'Resume', 'wp-job-openings' ); ?>
										</label>
										<div class="awsm-applicant-resume">
											<span class="hs-resume-info">
												<span>
													<strong>
														<?php
														echo ! empty( $full_file_name )
															? esc_html( $full_file_name )
															: esc_html__( 'Resume.pdf', 'wp-job-openings' );
														?>
													</strong>
													<?php echo esc_html( $file_size_display ); ?>
												</span>
											</span>
											<div class="awsm-applicant-resume-cta">
												<?php do_action( 'after_awsm_job_applicant_details_list_preview_resume', $post->ID ); ?>
												<a href="<?php echo esc_url( $this->get_attached_file_download_url( $attachment_id ) ); ?>" rel="nofollow" aria-label="<?php esc_attr_e( 'Download Resume', 'wp-job-openings' ); ?>">
													<svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" version="1.1" preserveAspectRatio="xMinYMin">
														<path xmlns="http://www.w3.org/2000/svg" fill="black" d="M8.66667 7.99998H10.6667L8 10.6666L5.33333 7.99998H7.33333V5.33331H8.66667V7.99998ZM10 2.66665H3.33333V13.3333H12.6667V5.33331H10V2.66665ZM2 1.99451C2 1.62935 2.29833 1.33331 2.66567 1.33331H10.6667L13.9998 4.66665L14 13.995C14 14.3659 13.7034 14.6666 13.3377 14.6666H2.66227C2.29651 14.6666 2 14.3631 2 14.0054V1.99451Z"/>
													</svg>
													<?php esc_html_e( 'Download', 'wp-job-openings' ); ?>
												</a>
											</div>
										</div>
									</li>
								<?php endif; ?>
							</ul>
						</div>
						<!-- Additional Tabs -->
						<?php foreach ( $applicant_tab_contents as $applicant_tab_key => $applicant_tab ) : ?>
							<div id="awsm-applicant-<?php echo esc_attr( $applicant_tab_key ); ?>" class="application-main-tab-item awsm-applicant-<?php echo esc_attr( $applicant_tab_key ); ?>-tab">
							<?php echo $applicant_tab['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> 
							</div>
						<?php endforeach; ?>
					</div>
				</div><!-- .application-main-cnt-tab-sec -->
			<?php endif; ?>
		</div><!-- .application-main-cnt -->
	</div><!-- .awsm-application-main -->
	
	<?php
	// Compatibility fix for Pro version.
	if ( defined( 'AWSM_JOBS_PRO_PLUGIN_VERSION' ) && version_compare( AWSM_JOBS_PRO_PLUGIN_VERSION, '1.4.0', '<' ) ) :
		?>
		<div class="submitbox awsm-application-submitbox">
			<div id="major-publishing-actions" class="awsm-application-major-actions awsm-clearfix">
				<?php $this->application_delete_action( $post->ID ); ?>
			</div><!-- #major-publishing-actions -->
		</div><!-- .awsm-application-submitbox -->
		<?php
	endif;
	?>
</div>

