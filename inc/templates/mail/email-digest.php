<?php
/**
 * Mail template for daily email digest.
 *
 * Override this by copying it to currenttheme/wp-job-openings/mail/email-digest.php
 *
 * @package wp-job-openings
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require AWSM_Job_Openings::get_template_path( 'header.php', 'mail' );

$overview_data = AWSM_Job_Openings::get_overview_data();
$digest_stats  = array(
	array(
		'count' => $overview_data['active_jobs'],
		'label' => __( 'Active Jobs', 'wp-job-openings' ),
	),
	array(
		'count' => isset( $overview_data['unread_applications'] ) ? $overview_data['unread_applications'] : 0,
		'label' => __( 'New Applications', 'wp-job-openings' ),
	),
	array(
		'count' => isset( $overview_data['active_applications'] ) ? $overview_data['active_applications'] : 0,
		'label' => __( 'Total Applications', 'wp-job-openings' ),
	),
);
?>

<table role="presentation" cellspacing="0" cellpadding="0" border="0" class="awsm-jobs-digest" style="width: 100%;">
	<tr>
		<td class="awsm-jobs-digest-intro">
			<h2 class="awsm-jobs-digest-title"><?php esc_html_e( 'Here’s a quick overview of your job listings', 'wp-job-openings' ); ?></h2>
			<p class="awsm-jobs-digest-subtitle">
				<?php
					/* translators: %s: Site title */
					printf( esc_html__( 'A snapshot of how your job listings in %s performed', 'wp-job-openings' ), '{site-title}' );
				?>
			</p>
		</td>
	</tr>
	<tr>
		<td class="awsm-jobs-digest-stats-wrap">
			<table role="presentation" cellspacing="0" cellpadding="0" border="0" class="awsm-jobs-digest-stats">
				<tr>
					<?php foreach ( $digest_stats as $digest_stat ) : ?>
						<td class="awsm-jobs-digest-stat">
							<div class="awsm-jobs-digest-stat-box">
								<span class="awsm-jobs-digest-stat-count"><?php echo esc_html( $digest_stat['count'] ); ?></span>
								<span class="awsm-jobs-digest-stat-label"><?php echo esc_html( $digest_stat['label'] ); ?></span>
							</div>
						</td>
					<?php endforeach; ?>
				</tr>
			</table>
		</td>
	</tr>
	<?php
	if ( ! empty( $applications ) ) :
		?>
		<tr>
			<td class="awsm-jobs-digest-recent-wrap">
				<div class="awsm-jobs-digest-recent">
					<h3 class="awsm-jobs-digest-recent-title"><?php esc_html_e( 'Recent applications', 'wp-job-openings' ); ?></h3>
					<table role="presentation" cellspacing="0" cellpadding="0" border="0" class="awsm-jobs-digest-table">
						<thead>
							<tr>
								<th style="width: 30%;"><?php esc_html_e( 'Name', 'wp-job-openings' ); ?></th>
								<th style="width: 38%;"><?php esc_html_e( 'Job', 'wp-job-openings' ); ?></th>
								<th style="width: 32%;"><?php esc_html_e( 'Applied on', 'wp-job-openings' ); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php
						foreach ( $applications as $application ) :
							$job_name = get_post_meta( $application->ID, 'awsm_apply_for', true );
							?>
							<tr>
								<td><a href="<?php echo esc_url( AWSM_Job_Openings::get_application_edit_link( $application->ID ) ); ?>"><?php echo esc_html( $application->post_title ); ?></a></td>
								<td><?php echo esc_html( $job_name ); ?></td>
								<td><?php echo esc_html( date_i18n( get_awsm_jobs_date_format( 'email-digest', __( 'j F Y', 'wp-job-openings' ) ), get_post_time( 'U', false, $application->ID ) ) ); ?></td>
							</tr>
							<?php
						endforeach;
						?>
						</tbody>
					</table>
					<p class="awsm-jobs-digest-action">
						<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_openings&page=awsm-jobs-overview' ) ); ?>" class="awsm-jobs-digest-btn"><?php esc_html_e( 'View All Applications', 'wp-job-openings' ); ?> &rarr;</a>
					</p>
				</div>
			</td>
		</tr>
		<?php
	endif;
	?>
</table>

<?php
require AWSM_Job_Openings::get_template_path( 'footer.php', 'mail' );
