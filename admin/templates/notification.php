<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	$admin_email                     = get_option( 'admin_email' );
	$hr_email                        = get_option( 'awsm_hr_email_address', '' );
	$hr_notification                 = get_option( 'awsm_jobs_hr_notification', $hr_email );
	$admin_subject                   = get_option( 'awsm_jobs_notification_subject', '' );
	$appplicant_notification_content = get_option( 'awsm_jobs_notification_content', '' );
	$admin_to_mail                   = get_option( 'awsm_jobs_admin_to_notification', $hr_email );
	$admin_cc_mail                   = get_option( 'awsm_jobs_admin_hr_notification' );
	$admin_notification_subject      = get_option( 'awsm_jobs_admin_notification_subject', '' );
	$admin_notification_content      = get_option( 'awsm_jobs_admin_notification_content', '' );
	$from_email                      = get_option( 'awsm_jobs_from_email_notification', $admin_email );
	$reply_to                        = get_option( 'awsm_jobs_reply_to_notification' );
	$admin_reply_to                  = get_option( 'awsm_jobs_admin_reply_to_notification', '{applicant-email}' );
	$admin_from_email                = get_option( 'awsm_jobs_admin_from_email_notification', $admin_email );
	$applicant_mail_template         = get_option( 'awsm_jobs_notification_mail_template' );
	$admin_mail_template             = get_option( 'awsm_jobs_notification_admin_mail_template' );
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
										<label for="awsm_jobs_from_email_notification"><?php esc_html_e( 'From', 'wp-job-openings' ); ?></label>
											<input type="email" class="awsm-form-control" name="awsm_jobs_from_email_notification" id="awsm_jobs_from_email_notification" value="<?php echo esc_attr( $from_email ); ?>" required />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_reply_to_notification"><?php esc_html_e( 'Reply-To', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_reply_to_notification" id="awsm_jobs_reply_to_notification" value="<?php echo esc_attr( $reply_to ); ?>" />
									</div><!-- .col -->
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
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm_jobs_notification_mail_template"><input type="checkbox" name="awsm_jobs_notification_mail_template" id="awsm_jobs_notification_mail_template" value="enable" <?php checked( $applicant_mail_template, 'enable' ); ?>><?php esc_html_e( 'Use HTML Template', 'wp-job-openings' ); ?></label>
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
										<label for="awsm_jobs_admin_from_email_notification"><?php esc_html_e( 'From', 'wp-job-openings' ); ?></label>
											<input type="email" class="awsm-form-control" name="awsm_jobs_admin_from_email_notification" id="awsm_jobs_admin_from_email_notification" value="<?php echo esc_attr( $admin_from_email ); ?>" required />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_admin_reply_to_notification"><?php esc_html_e( 'Reply-To', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_admin_reply_to_notification" id="awsm_jobs_admin_reply_to_notification" value="<?php echo esc_attr( $admin_reply_to ); ?>" />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_admin_to_notification"><?php esc_html_e( 'To', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_admin_to_notification" id="awsm_jobs_admin_to_notification" value="<?php echo esc_attr( $admin_to_mail ); ?>" placeholder="<?php esc_html__( 'Admin Email', 'wp-job-openings' ); ?>" required />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_admin_hr_notification"><?php esc_html_e( 'CC:', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_admin_hr_notification" id="awsm_jobs_admin_hr_notification" value="<?php echo esc_attr( $admin_cc_mail ); ?>" />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm_jobs_admin_notification_subject"><?php esc_html_e( 'Subject ', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" id="awsm_jobs_admin_notification_subject" name="awsm_jobs_admin_notification_subject" value="<?php echo esc_attr( $admin_notification_subject ); ?>" required />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm_jobs_admin_notification_content"><?php esc_html_e( 'Content ', 'wp-job-openings' ); ?></label>
											<textarea class="awsm-form-control" id="awsm_jobs_admin_notification_content" name="awsm_jobs_admin_notification_content" rows="5" cols="50" required><?php echo esc_textarea( $admin_notification_content ); ?></textarea>
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm_jobs_notification_admin_mail_template"><input type="checkbox" name="awsm_jobs_notification_admin_mail_template" id="awsm_jobs_notification_admin_mail_template" value="enable" <?php checked( $admin_mail_template, 'enable' ); ?>><?php esc_html_e( 'Use HTML Template', 'wp-job-openings' ); ?></label>
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

	<?php $template_tags = $this->get_template_tags(); ?>

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
