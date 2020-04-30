<?php
/**
 * Header template part for mail.
 *
 * Override this by copying it to currenttheme/wp-job-openings/mail/header.php
 *
 * @package wp-job-openings
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
	<meta charset="utf-8"> <!-- utf-8 works for most cases -->
	<meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
	<meta name="x-apple-disable-message-reformatting">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
	<title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->

	<?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet ?>
	<link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,400i,700,700i" rel="stylesheet">

	<!-- CSS Reset : BEGIN -->
<style>

html,
body {
	margin: 0 auto !important;
	padding: 0 !important;
	height: 100% !important;
	width: 100% !important;
	background: #f1f1f1;
}

/* What it does: Stops email clients resizing small text. */
* {
	-ms-text-size-adjust: 100%;
	-webkit-text-size-adjust: 100%;
}

/* What it does: Centers email on Android 4.4 */
div[style*="margin: 16px 0"] {
	margin: 0 !important;
}

/* What it does: Stops Outlook from adding extra spacing to tables. */
table,
td {
	mso-table-lspace: 0pt !important;
	mso-table-rspace: 0pt !important;
}

/* What it does: Fixes webkit padding issue. */
table {
	border-spacing: 0 !important;
	border-collapse: collapse !important;
	table-layout: fixed !important;
	margin: 0 auto !important;
}

/* What it does: Uses a better rendering method when resizing images in IE. */
img {
	-ms-interpolation-mode:bicubic;
}

/* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
a {
	text-decoration: none;
}

/* What it does: A work-around for email clients meddling in triggered links. */
*[x-apple-data-detectors],  /* iOS */
.unstyle-auto-detected-links *,
.aBn {
	border-bottom: 0 !important;
	cursor: default !important;
	color: inherit !important;
	text-decoration: none !important;
	font-size: inherit !important;
	font-family: inherit !important;
	font-weight: inherit !important;
	line-height: inherit !important;
}

/* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
.a6S {
	display: none !important;
	opacity: 0.01 !important;
}

/* What it does: Prevents Gmail from changing the text color in conversation threads. */
.im {
	color: inherit !important;
}

/* If the above doesn't work, add a .g-img class to any image in question. */
img.g-img + div {
	display: none !important;
}

/* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
/* Create one of these media queries for each additional viewport size you'd like to fix */

/* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
@media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
	u ~ div .email-container {
		min-width: 320px !important;
	}
}
/* iPhone 6, 6S, 7, 8, and X */
@media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
	u ~ div .email-container {
		min-width: 375px !important;
	}
}
/* iPhone 6+, 7+, and 8+ */
@media only screen and (min-device-width: 414px) {
	u ~ div .email-container {
		min-width: 414px !important;
	}
}

</style>

	<!-- CSS Reset : END -->

	<!-- Progressive Enhancements : BEGIN -->
<style>

.primary{
	background: #f3a333;
}

.bg_white{
	background: #ffffff;
}
.bg_light{
	background: #fafafa;
}
.bg_black{
	background: #000000;
}
.bg_dark{
	background: rgba(0,0,0,.8);
}


/*BUTTON*/
.btn{
	padding: 14px 40px;
}
.btn.btn-primary{
	border-radius: 3px;
	background: #05BC9C;
	color: #ffffff;
	border: 1px solid #207E76;
	font-weight: bold;
}

h1,h2,h3,h4,h5,h6{
	font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
	color: #1F3130;
	margin-top: 0;
}

body{
	font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
	font-weight: 400;
	font-size: 16px;
	line-height: 1.125;
	color: #4F5F5E;
}

a{
	color: #05BC9C;
}

table{
	color: #4F5F5E;
}
/*LOGO*/
.logo{
	padding: 46px 0;
	text-align: center;
}
.logo h1{
	margin: 0;
}
.logo h1 a{
	color: #000;
	font-size: 20px;
	font-weight: 700;
	text-transform: uppercase;
	font-family: 'Montserrat', sans-serif;
}

/*main-content*/

.main-content {
	padding: 60px 0;
	border-width: 9px 1px 1px;
	border-style: solid;
	border-color: #05BC9C #C6CCD2 #C6CCD2;
}
.main-content h2{
	font-size: 25px;
	margin-bottom: 17px;
}
.main-content h3{
	font-size: 16px;
	text-transform: uppercase;
	margin-bottom: 38px;
}
.main-content ul{
	list-style: none;
	padding: 0;
	margin: 0;
}
.main-content li{
	display: inline-block;
	margin: 0 25px;
}
.main-content li span{
	display: block;
	font-size: 43px;
	color: #05BC9C;
}
.job-table td, .job-table th{
	text-align: left;
	padding: 13px 20px;
}
.main-content-in-2{
	padding: 50px 0;
	border-bottom: 1px solid #D7DFDF;
}
.main-content-in-3{
	padding-top: 50px;
}

/*FOOTER*/

.footer a{
	color: #4F5F5E;
}

@media screen and (max-width: 550px) {
	.logo{
		padding: 30px 0;
	}
	.main-content{
		padding: 35px 0;
	}
	.job-table td, .job-table th{
		padding: 10px;
	}
	.main-content li{
		margin: 0 10px;
	}
	.main-content li span{
		font-size: 34px;
	}
	.main-content-in-2{
		padding: 35px 0;
	}
	.main-content-in-3{
		padding-top: 35px;
	}
}

</style>

</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #F1F4F7;">
	<center style="width: 100%; background-color: #F1F4F7;">
		<div style="max-width: 770px; margin: 0 auto;" class="email-container">
			<!-- BEGIN BODY -->
		<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
			<tr>
				<td class="logo">
					<h1><img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/logo.png' ); ?>" width="284" height="35" alt="<?php esc_html_e( 'WP Job Openings', 'wp-job-openings' ); ?>"></h1>
				</td>
			</tr><!-- end tr -->
			<tr>
				<td valign="middle" class="main-content bg_white">
