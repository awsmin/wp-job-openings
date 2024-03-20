<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	$applicant_options = AWSM_Job_Openings_Form::get_notification_options( 'applicant' );
	$admin_options     = AWSM_Job_Openings_Form::get_notification_options( 'admin' );
	$author_options    = AWSM_Job_Openings_Form::get_notification_options( 'author' );
	$from_email        = $applicant_options['from'];
	$admin_from_email  = $admin_options['from'];
	$author_from_email = $author_options['from'];

	$from_email_error_msg = __( "The provided 'From' email address does not belong to this site domain and may lead to issues in email delivery.", 'wp-job-openings' );
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
									<input type="checkbox" class="awsm-settings-switch" id="awsm_jobs_acknowledgement" name="awsm_jobs_acknowledgement" value="acknowledgement" <?php checked( $applicant_options['acknowledgement'], 'acknowledgement' ); ?> />
									<span class="awsm-ts-label" data-on="<?php esc_html_e( 'ON', 'wp-job-openings' ); ?>" data-off="<?php esc_html_e( 'OFF', 'wp-job-openings' ); ?>"></span>
								<span class="awsm-ts-inner"></span>
								</label>
							</div><!-- .awsm-acc-head -->
							<div class="awsm-acc-content">
								<div class="awsm-row">
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_from_email_notification"><?php esc_html_e( 'From', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_from_email_notification" id="awsm_jobs_from_email_notification" value="<?php echo esc_attr( $from_email ); ?>" required />
											<?php
											if ( $this->validate_from_email_id( $from_email ) === false ) {
												printf( '<p class="description awsm-jobs-invalid">%s</p>', esc_html( $from_email_error_msg ) );
											}
											?>
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_reply_to_notification"><?php esc_html_e( 'Reply-To', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_reply_to_notification" id="awsm_jobs_reply_to_notification" value="<?php echo esc_attr( $applicant_options['reply_to'] ); ?>" />
									</div><!-- .col -->
								</div>
								<div class="awsm-row">
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_applicant_notification"><?php esc_html_e( 'To', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_applicant_notification" id="awsm_jobs_applicant_notification" value="<?php echo esc_attr( '{applicant-email}' ); ?>" disabled />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_hr_notification"><?php esc_html_e( 'CC:', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_hr_notification" id="awsm_jobs_hr_notification" value="<?php echo esc_attr( $applicant_options['cc'] ); ?>" />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm-notification-subject"><?php esc_html_e( 'Subject ', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" id="awsm-notification-subject" name="awsm_jobs_notification_subject" value="<?php echo esc_attr( $applicant_options['subject'] ); ?>" required />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm_jobs_notification_content"><?php esc_html_e( 'Content ', 'wp-job-openings' ); ?></label>
										<?php awsm_jobs_wp_editor( $applicant_options['content'], 'awsm_jobs_notification_content' ); ?>
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm_jobs_notification_mail_template"><input type="checkbox" name="awsm_jobs_notification_mail_template" id="awsm_jobs_notification_mail_template" value="enable" <?php checked( $applicant_options['html_template'], 'enable' ); ?>><?php esc_html_e( 'Use HTML Template', 'wp-job-openings' ); ?></label>
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
									<input type="checkbox" class="awsm-settings-switch" id="awsm_jobs_enable_admin_notification" name="awsm_jobs_enable_admin_notification" value="enable" <?php checked( $admin_options['enable'], 'enable' ); ?> />
									<span class="awsm-ts-label" data-on="<?php esc_html_e( 'ON', 'wp-job-openings' ); ?>" data-off="<?php esc_html_e( 'OFF', 'wp-job-openings' ); ?>"></span>
								<span class="awsm-ts-inner"></span>
								</label>
							</div><!-- .awsm-acc-head -->
							<div class="awsm-acc-content">
								<div class="awsm-row">
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_admin_from_email_notification"><?php esc_html_e( 'From', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_admin_from_email_notification" id="awsm_jobs_admin_from_email_notification" value="<?php echo esc_attr( $admin_from_email ); ?>" required />
											<?php
											if ( $this->validate_from_email_id( $admin_from_email ) === false ) {
												printf( '<p class="description awsm-jobs-invalid">%s</p>', esc_html( $from_email_error_msg ) );
											}
											?>
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_admin_reply_to_notification"><?php esc_html_e( 'Reply-To', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_admin_reply_to_notification" id="awsm_jobs_admin_reply_to_notification" value="<?php echo esc_attr( $admin_options['reply_to'] ); ?>" />
									</div><!-- .col -->
								</div>
								<div class="awsm-row">
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_admin_to_notification"><?php esc_html_e( 'To', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_admin_to_notification" id="awsm_jobs_admin_to_notification" value="<?php echo esc_attr( $admin_options['to'] ); ?>" placeholder="<?php esc_html__( 'Admin Email', 'wp-job-openings' ); ?>" required />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_admin_hr_notification"><?php esc_html_e( 'CC:', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_admin_hr_notification" id="awsm_jobs_admin_hr_notification" value="<?php echo esc_attr( $admin_options['cc'] ); ?>" />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm_jobs_admin_notification_subject"><?php esc_html_e( 'Subject ', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" id="awsm_jobs_admin_notification_subject" name="awsm_jobs_admin_notification_subject" value="<?php echo esc_attr( $admin_options['subject'] ); ?>" required />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm_jobs_admin_notification_content"><?php esc_html_e( 'Content ', 'wp-job-openings' ); ?></label>
										<?php awsm_jobs_wp_editor( $admin_options['content'], 'awsm_jobs_admin_notification_content' ); ?>
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm_jobs_notification_admin_mail_template"><input type="checkbox" name="awsm_jobs_notification_admin_mail_template" id="awsm_jobs_notification_admin_mail_template" value="enable" <?php checked( $admin_options['html_template'], 'enable' ); ?>><?php esc_html_e( 'Use HTML Template', 'wp-job-openings' ); ?></label>
									</div><!-- .col -->
								</div><!-- row -->
								<ul class="awsm-list-inline">
									<li><?php echo apply_filters( 'awsm_job_settings_submit_btn', get_submit_button( esc_html__( 'Save', 'wp-job-openings' ) ), 'notification' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
								</ul>
							</div><!-- .awsm-acc-content -->
						</div><!-- .awsm-acc-main -->
						<div class="awsm-acc-main awsm-acc-form-switch">
							<div class="awsm-acc-head">
								<h3><?php esc_html_e( 'Job Expiry Notification', 'wp-job-openings' ); ?></h3>
								<label for="awsm_jobs_enable_expiry_notification" class="awsm-toggle-switch">
									<input type="checkbox" class="awsm-settings-switch" id="awsm_jobs_enable_expiry_notification" name="awsm_jobs_enable_expiry_notification" value="enable" <?php checked( $author_options['enable'], 'enable' ); ?> />
									<span class="awsm-ts-label" data-on="<?php esc_html_e( 'ON', 'wp-job-openings' ); ?>" data-off="<?php esc_html_e( 'OFF', 'wp-job-openings' ); ?>"></span>
								<span class="awsm-ts-inner"></span>
								</label>
							</div><!-- .awsm-acc-head -->
							<div class="awsm-acc-content">
								<div class="awsm-row">
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_author_from_email_notification"><?php esc_html_e( 'From', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_author_from_email_notification" id="awsm_jobs_author_from_email_notification" value="<?php echo esc_attr( $author_from_email ); ?>" required />
											<?php
											if ( $this->validate_from_email_id( $author_from_email ) === false ) {
												printf( '<p class="description awsm-jobs-invalid">%s</p>', esc_html( $from_email_error_msg ) );
											}
											?>
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_author_reply_to_notification"><?php esc_html_e( 'Reply-To', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_author_reply_to_notification" id="awsm_jobs_author_reply_to_notification" value="<?php echo esc_attr( $author_options['reply_to'] ); ?>" />
									</div><!-- .col -->
								</div>
								<div class="awsm-row">
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_author_to_notification"><?php esc_html_e( 'To', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_author_to_notification" id="awsm_jobs_author_to_notification" value="<?php echo esc_attr( $author_options['to'] ); ?>" placeholder="<?php esc_html__( 'Author Email', 'wp-job-openings' ); ?>" required />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-half">
										<label for="awsm_jobs_author_hr_notification"><?php esc_html_e( 'CC:', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" name="awsm_jobs_author_hr_notification" id="awsm_jobs_author_hr_notification" value="<?php echo esc_attr( $author_options['cc'] ); ?>" />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm_jobs_author_notification_subject"><?php esc_html_e( 'Subject ', 'wp-job-openings' ); ?></label>
											<input type="text" class="awsm-form-control" id="awsm_jobs_author_notification_subject" name="awsm_jobs_author_notification_subject" value="<?php echo esc_attr( $author_options['subject'] ); ?>" required />
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm_jobs_author_notification_content"><?php esc_html_e( 'Content ', 'wp-job-openings' ); ?></label>
										<?php awsm_jobs_wp_editor( $author_options['content'], 'awsm_jobs_author_notification_content' ); ?>
									</div><!-- .col -->
									<div class="awsm-col awsm-form-group awsm-col-full">
										<label for="awsm_jobs_notification_author_mail_template"><input type="checkbox" name="awsm_jobs_notification_author_mail_template" id="awsm_jobs_notification_author_mail_template" value="enable" <?php checked( $author_options['html_template'], 'enable' ); ?>><?php esc_html_e( 'Use HTML Template', 'wp-job-openings' ); ?></label>
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

			<div class="awsm-sub-options-container" id="awsm-customize-notification-options-container" style="display: none;">
				<?php
					$customizer_settings = AWSM_Job_Openings_Mail_Customizer::get_settings();
					$validation_msg      = '';
				if ( $this->validate_from_email_id( $customizer_settings['from_email'] ) === false ) {
					$validation_msg = $from_email_error_msg;
				}
					/**
					 * Filters the notification customizer fields.
					 *
					 * @since 2.2.0
					 *
					 * @param array $customizer_fields Notification customizer fields.
					 */
					$customizer_fields = apply_filters(
						'awsm_jobs_notification_customizer_fields',
						array(
							array(
								'id'    => 'awsm-notification-customize-html-template-title',
								'label' => __( 'Customize HTML Template', 'wp-job-openings' ),
								'type'  => 'title',
							),
							array(
								'id'    => 'awsm_jobs_notification_customizer_logo',
								'name'  => 'awsm_jobs_notification_customizer[logo]',
								'type'  => 'image',
								'label' => __( 'Logo', 'wp-job-openings' ),
								'value' => $customizer_settings['logo'],
							),
							array(
								'id'          => 'awsm_jobs_notification_customizer_from_email',
								'name'        => 'awsm_jobs_notification_customizer[from_email]',
								'type'        => 'email',
								'label'       => __( 'Default "From" Email Address: ', 'wp-job-openings' ),
								'value'       => $customizer_settings['from_email'],
								'description' => $validation_msg ? '<p class="description awsm-jobs-invalid">' . $validation_msg . '</p>' : '',
							),
							array(
								'id'          => 'awsm_jobs_notification_customizer_base_color',
								'name'        => 'awsm_jobs_notification_customizer[base_color]',
								'label'       => __( 'Base Color', 'wp-job-openings' ),
								'type'        => 'colorpicker',
								'value'       => $customizer_settings['base_color'],
								'other_attrs' => array(
									'data-default-color' => '#05BC9C',
								),
							),
							array(
								'id'          => 'awsm_jobs_notification_customizer_footer_text',
								'name'        => 'awsm_jobs_notification_customizer[footer_text]',
								'type'        => 'editor',
								'label'       => __( 'Footer Text', 'wp-job-openings' ),
								'value'       => $customizer_settings['footer_text'],
								'other_attrs' => array(
									'editor_height' => 100,
								),
							),
						)
					);

					AWSM_Job_Openings_Mail_Customizer::validate_template();
					?>

				<table class="form-table">
					<tbody>
						<?php
							do_action( 'before_awsm_notification_customizer_settings' );

							$this->display_settings_fields( $customizer_fields );

							do_action( 'after_awsm_notification_customizer_settings' );
						?>
					</tbody>
				</table>

				<div class="awsm-form-footer">
					<?php echo apply_filters( 'awsm_job_settings_submit_btn', get_submit_button(), 'notification' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div><!-- .awsm-form-footer -->
			</div><!-- .awsm-customize-notification-options-container -->

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
