<?php
/**
 * Mail Basic Template.
 *
 * Override this by copying it to currenttheme/wp-job-openings/mail/basic.php
 *
 * @package wp-job-openings
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="utf-8">
		<title>{mail-subject}</title>
	</head>

	<body>
		{mail-content}
	</body>
</html>
