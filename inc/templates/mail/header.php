<?php
/**
 * Header template part for mail.
 *
 * Override this by copying it to currenttheme/wp-job-openings/mail/header.php
 *
 * @package wp-job-openings
 * @version 3.3.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$customizer_settings = AWSM_Job_Openings_Mail_Customizer::get_settings();
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="x-apple-disable-message-reformatting">
	<title>{mail-subject}</title>

	<?php
		/**
		 * Prints styles or data in the head tag of the notification mail HTML template.
		 *
		 * @hooked AWSM_Job_Openings_Mail_Customizer::template_head()
		 *
		 * @since 2.2.0
		 *
		 * @param array $settings Notification mail customizer settings.
		 */
		do_action( 'awsm_jobs_notification_html_template_head', $customizer_settings );
	?>
</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #F1F4F7;" class="email-container-body">
	<center style="width: 100%; background-color: #F1F4F7;" class="email-main-container">
		<div style="max-width: 640px; margin: 0 auto;" class="email-container">

		<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
			<tr>
				<td class="logo">
					<?php echo AWSM_Job_Openings_Mail_Customizer::get_logo( $customizer_settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
			</tr>
			<tr>
				<td valign="middle" class="main-content bg_white">
					<?php
						/**
						 * before_awsm_jobs_notification_html_template_main_content hook.
						 *
						 * @since 2.2.0
						 *
						 * @param array $settings Notification mail customizer settings.
						 */
						do_action( 'before_awsm_jobs_notification_html_template_main_content', $customizer_settings );
					?>
