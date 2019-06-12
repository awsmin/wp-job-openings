<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	$hr_email                        = get_option( 'awsm_hr_email_address', '' );
	$hr_notification                 = get_option( 'awsm_jobs_hr_notification', $hr_email );
	$admin_subject                   = get_option( 'awsm_jobs_notification_subject', '' );
	$appplicant_notification_content = get_option( 'awsm_jobs_notification_content', '' );
	$admin_to_mail                   = get_option( 'awsm_jobs_admin_to_notification', get_option( 'admin_email' ) );
	$admin_hr_mail                   = get_option( 'awsm_jobs_admin_hr_notification', $hr_email );
	$admin_notification_subject      = get_option( 'awsm_jobs_admin_notification_subject', '' );
	$admin_notification_content      = get_option( 'awsm_jobs_admin_notification_content', '' );
?>

<div id="settings-awsm-settings-notification" class="awsm-admin-settings">
	<div class="awsm-settings-col-left">
		<?php do_action( 'awsm_settings_form_elem_start', 'notification' ); ?>
		<form method="POST" action="options.php" id="notification_form">
			<?php
				settings_fields( 'awsm-jobs-notification-settings' );

				// display notification subtabs.
				$this->display_subtabs( 'notification' );

				do_action( 'before_awsm_settings_main_content', 'notification' );
			?>

			<div class="awsm-sub-options-container" id="awsm-job-notification-options-container">
				<h2 class="awsm-section-title"><?php esc_html_e( 'Application Notifications', 'wp-job-openings' ); ?></h2>
				<div class="awsm-form-section-main awsm-acc-section-main">
					<div class="awsm-form-section awsm-acc-secton" id="settings-notification">
						<?php do_action( 'before_awsm_notification_settings' ); ?>
						<div class="awsm-acc-main awsm-acc-form-switch">
							<div class="awsm-acc-head on">
								<h3><?php echo esc_html__( 'Application Received - Applicant Notification', 'wp-job-openings' ); ?></h3>
								<label for="awsm_jobs_acknowledgement" class="awsm-toggle-switch">
									<input type="checkbox" class="awsm-settings-switch" id="awsm_jobs_acknowledgement" name="awsm_jobs_acknowledgement" value="acknowledgement" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_jobs_acknowledgement' ), 'acknowledgement' ) ); ?> />
									<span class="awsm-ts-label" data-on="<?php esc_html_e( 'ON', 'wp-job-openings' ); ?>" data-off="<?php esc_html_e( 'OFF', 'wp-job-openings' ); ?>"></span>
								<span class="awsm-ts-inner"></span>
								</label>
							</div><!-- .awsm-acc-head -->
							<div class="awsm-acc-content">
								<div class="awsm-row">
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_applicant_notification"><?php esc_html_e( 'To', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_applicant_notification" id="awsm_jobs_applicant_notification" value="<?php echo esc_attr( '{applicant-email}' ); ?>" disabled />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_hr_notification"><?php esc_html_e( 'CC:', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_hr_notification" id="awsm_jobs_hr_notification" value="<?php echo esc_attr( $hr_notification ); ?>" />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm-notification-subject"><?php esc_html_e( 'Subject ', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" id="awsm-notification-subject" name="awsm_jobs_notification_subject" value="<?php echo esc_attr( $admin_subject ); ?>" required />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm_jobs_notification_content"><?php esc_html_e( 'Content ', 'wp-job-openings' ); ?></label>
											<textarea class="awsm-form-control" id="awsm-notification-content" name="awsm_jobs_notification_content" rows="5" cols="50" required><?php echo esc_textarea( $appplicant_notification_content ); ?></textarea>
									</div><!-- .col -->
								</div><!-- row -->
								<ul class="awsm-list-inline">
									<li><?php echo apply_filters( 'awsm_job_settings_submit_btn', get_submit_button( esc_html__( 'Save', 'wp-job-openings' ) ), 'notification' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
								</ul>
							</div><!-- .awsm-acc-content -->
						</div><!-- .awsm-acc-main -->
						<div class="awsm-acc-main awsm-acc-form-switch">
							<div class="awsm-acc-head">
								<h3><?php esc_html_e( 'Application Received - Admin Notification', 'wp-job-openings' ); ?></h3>
								<label for="awsm_jobs_enable_admin_notification" class="awsm-toggle-switch">
									<input type="checkbox" class="awsm-settings-switch" id="awsm_jobs_enable_admin_notification" name="awsm_jobs_enable_admin_notification" value="enable" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_jobs_enable_admin_notification' ), 'enable' ) ); ?> />
									<span class="awsm-ts-label" data-on="<?php esc_html_e( 'ON', 'wp-job-openings' ); ?>" data-off="<?php esc_html_e( 'OFF', 'wp-job-openings' ); ?>"></span>
								<span class="awsm-ts-inner"></span>
								</label>
							</div><!-- .awsm-acc-head -->
							<div class="awsm-acc-content">
								<div class="awsm-row">
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_admin_to_notification"><?php esc_html_e( 'To', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_admin_to_notification" id="awsm_jobs_admin_to_notification" value="<?php echo esc_attr( $admin_to_mail ); ?>" placeholder="<?php esc_html__( 'Admin Email', 'wp-job-openings' ); ?>" required />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_admin_hr_notification"><?php esc_html_e( 'CC:', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_admin_hr_notification" id="awsm_jobs_admin_hr_notification" value="<?php echo esc_attr( $admin_hr_mail ); ?>" />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm_jobs_admin_notification_subject"><?php esc_html_e( 'Subject ', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" id="awsm_jobs_admin_notification_subject" name="awsm_jobs_admin_notification_subject" value="<?php echo esc_attr( $admin_notification_subject ); ?>" required />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm_jobs_admin_notification_content"><?php esc_html_e( 'Content ', 'wp-job-openings' ); ?></label>
											<textarea class="awsm-form-control" id="awsm_jobs_admin_notification_content" name="awsm_jobs_admin_notification_content" rows="5" cols="50" required><?php echo esc_textarea( $admin_notification_content ); ?></textarea>
									</div><!-- .col -->
								</div><!-- row -->
								<ul class="awsm-list-inline">
									<li><?php echo apply_filters( 'awsm_job_settings_submit_btn', get_submit_button( esc_html__( 'Save', 'wp-job-openings' ) ), 'notification' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
								</ul>
							</div><!-- .awsm-acc-content -->
						</div><!-- .awsm-acc-main -->
						<?php do_action( 'after_awsm_notification_settings' ); ?>
					</div><!-- .awsm-form-section -->
				</div><!-- .awsm-form-section-main -->
			</div><!-- #awsm-job-notification-options-container -->

			<?php do_action( 'after_awsm_settings_main_content', 'notification' ); ?>

		</form>
		<?php do_action( 'awsm_settings_form_elem_end', 'notification' ); ?>
	</div><!-- .awsm-settings-col-left -->

	<?php
		$template_tags = apply_filters(
			'awsm_job_template_tags',
			array(
				'{applicant}'        => __( 'Applicant Name:', 'wp-job-openings' ),
				'{application-id}'   => __( 'Application ID:', 'wp-job-openings' ),
				'{applicant-email}'  => __( 'Applicant Email:', 'wp-job-openings' ),
				'{applicant-phone}'  => __( 'Applicant Phone:', 'wp-job-openings' ),
				'{applicant-resume}' => __( 'Applicant Resume:', 'wp-job-openings' ),
				'{applicant-cover}'  => __( 'Cover letter:', 'wp-job-openings' ),
				'{job-title}'        => __( 'Job Title:', 'wp-job-openings' ),
				'{job-id}'           => __( 'Job ID:', 'wp-job-openings' ),
				'{job-expiry}'       => __( 'Job Expiry Date:', 'wp-job-openings' ),
				'{admin-email}'      => __( 'Site admin email:', 'wp-job-openings' ),
				'{hr-email}'         => __( 'HR Email:', 'wp-job-openings' ),
				'{company}'          => __( 'Company Name:', 'wp-job-openings' ),
			)
		);
		?>
	<div class="awsm-settings-col-right">
		<div class="awsm-settings-aside">
			<h3><?php echo esc_html__( 'Template Tags', 'wp-job-openings' ); ?></h3>
			<ul class="awsm-job-template-tag-list">
				<?php
				foreach ( $template_tags as $template_tag => $tag_label ) {
					printf( '<li><span>%s</span><span>%s</span></li>', esc_html( $tag_label ), esc_html( $template_tag ) );
				}
				?>
			</ul>
		</div><!-- .awsm-settings-aside -->
	</div><!-- .awsm-settings-col-right -->
</div><!-- .awsm-admin-settings -->
