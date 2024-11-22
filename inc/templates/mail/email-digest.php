<?php
/**
 * Mail template for daily email digest.
 *
 * Override this by copying it to currenttheme/wp-job-openings/mail/email-digest.php
 *
 * @package wp-job-openings
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require AWSM_Job_Openings::get_template_path( 'header.php', 'mail' );

// Array of strings for translation
$texts = array(
	'overview_heading'         => 'Here’s a awsm tested listings',
	'overview_paragraph'       => 'A snapshot of how your job listings in %s performed',
	'active_jobs'              => 'Active Jobs',
	'new_applications'         => 'New Applications',
	'total_applications'       => 'Total Applications',
	'recent_applications'      => 'Recent Applications',
	'name_column'              => 'Name',
	'job_column'               => 'Job',
	'applied_on_column'        => 'Applied on',
	'view_button'              => 'View',
	'view_more_button'         => 'View More',
);

// Register strings with esc_html__ for translation
foreach ($texts as $key => $text) {
	esc_html__( $text, 'wp-job-openings' );
	error_log( json_encode( $text, JSON_PRETTY_PRINT ) );
}

?>

<table style="width: 100%;">
	<tr>
		<td class="main-content-in-1">
			<div style="padding: 0 10px; text-align: center; max-width: 576px; margin: 0 auto;">
				<h2><?php echo AWSM_Job_Openings::translate_string( $texts['overview_heading'], $current_language ); ?>
				<?php if( function_exists('pll_translate_string')) {
					echo pll_translate_string( 'this is test listings polylang', $lang );
				}  else {
						esc_html_e( 'Here’s a quick overview of your job listings', 'wp-job-openings' );
						esc_html_e( 'this is test listings', 'wp-job-openings' );
					}
				
			?>
			</h2>
				<p>
					<?php
						/* translators: %s: Site title */
						printf( esc_html__( 'A snapshot of how your job listings in %s performed', 'wp-job-openings' ), '{site-title}' );
					?>
				</p>
			</div>
		</td>
	</tr>
	<tr>
		<td class="main-content-in-2"<?php echo empty( $applications ) ? ' style="border-bottom: 0;"' : ''; ?>>
			<div style="padding: 0 15px; text-align: center; max-width: 512px; margin: 0 auto;">
				<?php
					$overview_data = AWSM_Job_Openings::get_overview_data();
				?>
				<ul class="mail-content-stats">
					<li>
						<span><?php echo esc_html( $overview_data['active_jobs'] ); ?></span>
						<?php echo AWSM_Job_Openings::translate_string( $texts['active_jobs'], $current_language ); ?>
					</li>
					<li>
						<span><?php echo esc_html( $overview_data['new_applications'] ); ?></span>
						<?php echo AWSM_Job_Openings::translate_string( $texts['new_applications'], $current_language ); ?>
					</li>
					<li>
						<span><?php echo esc_html( $overview_data['total_applications'] ); ?></span>
						<?php echo AWSM_Job_Openings::translate_string( $texts['total_applications'], $current_language ); ?>
					</li>
				</ul>
			</div>
		</td>
	</tr>
	<?php if ( ! empty( $applications ) ) : ?>
		<tr>
			<td class="main-content-in-3">
				<div style="text-align: center; max-width: 576px; margin: 0 auto;">
					<h3><?php echo AWSM_Job_Openings::translate_string( $texts['recent_applications'], $current_language ); ?></h3>
					<table class="job-table" style="font-size: 14px; width: 100%;">
						<thead>
							<tr style="background-color: #F3F5F8; color: #1F3130;">
								<th style="width:25%;"><?php echo AWSM_Job_Openings::translate_string( $texts['name_column'], $current_language ); ?></th>
								<th style="width:35%;"><?php echo AWSM_Job_Openings::translate_string( $texts['job_column'], $current_language ); ?></th>
								<th style="width:25%;"><?php echo AWSM_Job_Openings::translate_string( $texts['applied_on_column'], $current_language ); ?></th>
								<th style="width:15%;"></th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ( $applications as $application ) :
							$job_name = get_post_meta( $application->ID, 'awsm_apply_for', true );
							?>
							<tr style="border-bottom: 1px solid #D7DFDF;">
								<td><?php echo esc_html( $application->post_title ); ?></td>
								<td><?php echo esc_html( $job_name ); ?></td>
								<td><?php echo esc_html( date_i18n( get_awsm_jobs_date_format( 'email-digest', __( 'j F Y', 'wp-job-openings' ) ), get_post_time( 'U', false, $application->ID ) ) ); ?></td>
								<td><a href="<?php echo esc_url( AWSM_Job_Openings::get_application_edit_link( $application->ID ) ); ?>"><strong><?php echo AWSM_Job_Openings::translate_string( $texts['view_button'], $current_language ); ?></strong></a></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
					<p style="margin-top: 40px;"><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_openings&page=awsm-jobs-overview' ) ); ?>" class="btn btn-primary"><?php echo AWSM_Job_Openings::translate_string( $texts['view_more_button'], $current_language ); ?></a></p>
				</div>
			</td>
		</tr>
	<?php endif; ?>
</table>

<?php
require AWSM_Job_Openings::get_template_path( 'footer.php', 'mail' );