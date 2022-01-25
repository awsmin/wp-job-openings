<?php
/**
 * The header template.
 *
 * This template can be overridden by copying it to currenttheme/wp-job-openings/theme-compat/header.php
 *
 * @package wp-job-openings
 * @since 3.2.0
 * @version 3.2.0
 */

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class( 'awsm-jobs-is-block-theme' ); ?>>

	<?php wp_body_open(); ?>

	<div id="page" class="site">
		<header id="masthead" class="site-header">
			<div class="site-branding">
				<?php the_custom_logo(); ?>

				<p class="site-title"><a href="<?php echo esc_url( home_url() ); ?>/"><?php bloginfo( 'name' ); ?></a></p>
				<p class="description"><?php bloginfo( 'description' ); ?></p>
			</div>
		</header>
		<div id="content" class="site-content">
