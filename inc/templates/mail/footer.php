<?php
/**
 * Footer template part for mail.
 *
 * Override this by copying it to currenttheme/wp-job-openings/mail/footer.php
 *
 * @package wp-job-openings
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$customizer_settings = AWSM_Job_Openings_Mail_Customizer::get_settings();

?>
							<?php
								/**
								 * after_awsm_jobs_notification_html_template_main_content hook.
								 *
								 * @since 2.2.0
								 *
								 * @param array $settings Notification mail customizer settings.
								 */
								do_action( 'after_awsm_jobs_notification_html_template_main_content', $customizer_settings );
							?>
						</td>
					</tr>
					<tr>
						<td valign="middle" class="footer email-section">
							<table style="width: 100%;">
								<tr>
									<?php
										/**
										 * Notification mail HTML template footer.
										 *
										 * @hooked AWSM_Job_Openings_Mail_Customizer::template_footer()
										 *
										 * @since 2.2.0
										 *
										 * @param array $settings Notification mail customizer settings.
										 */
										do_action( 'awsm_jobs_notification_html_template_footer', $customizer_settings );
									?>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</center>
	</body>
</html>
