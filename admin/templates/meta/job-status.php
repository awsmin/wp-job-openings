<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
  $job_id = get_the_ID();
if ( $post->post_type === 'awsm_job_application' ) {
	$job_id = $post->post_parent;
}
  $applications = AWSM_Job_Openings::get_applications( $job_id );
  $post_count   = count( $applications );
  $check_status = get_post_status( $job_id );
  $views_count  = get_post_meta( $job_id, 'awsm_views_count', true );
  $job_title    = get_the_title( $job_id );
?>

<table class="awsm-job-stat-table">
  <tr>
	<td><?php esc_html_e( 'Job Title', 'wp-job-openings' ); ?></td>
	<td><?php echo wp_strip_all_tags( $job_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
  </tr>
  <tr>
	<td><?php esc_html_e( 'Current Status:', 'wp-job-openings' ); ?></td>
	<td>
	<?php
	if ( $check_status === 'publish' ) {
		echo '<span class="awsm-text-green">' . esc_html__( 'Active', 'wp-job-openings' ) . '</span>';
	} elseif ( $check_status === 'expired' ) {
		echo '<span class="awsm-text-red">' . esc_html__( 'Expired', 'wp-job-openings' ) . '</span>';
	} else {
		echo '<span>' . esc_html__( 'Pending', 'wp-job-openings' ) . '</span>';
	}
	?>
	</td>
  </tr>
  <tr>
	<td><?php esc_html_e( 'Views:', 'wp-job-openings' ); ?></td>
	<td><?php echo ( ! empty( $views_count ) ) ? esc_html( $views_count ) : 0; ?></td>
  </tr>
  <tr>
	<td><?php esc_html_e( 'Applications:', 'wp-job-openings' ); ?></td>
	<td>
		<?php
		if ( $post_count > 0 ) {
			printf( '<a href="%1$s">%2$s</a>', esc_url( admin_url( 'edit.php?post_type=awsm_job_application&awsm_filter_posts=' . $job_id ) ), esc_attr( $post_count ) );
		} else {
			echo esc_html( $post_count );
		}
		?>
	</td>
  </tr>
  <tr>
	<td><?php esc_html_e( 'Last Submission:', 'wp-job-openings' ); ?></td>
	<td>
		<?php
		if ( $post_count > 0 ) {
			$applications       = array_values( $applications );
			$recent_application = $applications[0];
			$edit_link          = get_edit_post_link( $recent_application->ID );
			printf( '<a href="%1$s">%2$s %3$s</a>', esc_url( $edit_link ), esc_html( human_time_diff( get_the_time( 'U', $recent_application->ID ), current_time( 'timestamp' ) ) ), esc_html__( 'ago', 'wp-job-openings' ) );
		} else {
			esc_html_e( 'NA', 'wp-job-openings' );
		}
		?>
	</td>
  </tr>
	<?php
	if ( $post->post_type === 'awsm_job_application' ) :
		$date_format         = get_option( 'date_format' );
		$job_submission_date = date_i18n( $date_format, strtotime( get_the_date( '', $job_id ) ) );
		$expiry_date         = get_post_meta( $job_id, 'awsm_job_expiry', true );
		$formatted_date      = date_i18n( $date_format, strtotime( $expiry_date ) );
		?>
	  <tr>
		<td><?php esc_html_e( 'Date Posted:', 'wp-job-openings' ); ?></td>
		<td><?php echo esc_html( $job_submission_date ); ?></td>
	  </tr>
	  <tr>
		<td><?php esc_html_e( 'Date of Expiry:', 'wp-job-openings' ); ?></td>
		<td><?php echo esc_html( $formatted_date ); ?></td>
	  </tr>
		<?php if ( current_user_can( 'edit_post', $job_id ) ) : ?>
		<tr>
		  <td><?php printf( '<div class="awsm-job-edit-btn-wrapper"><a class="button awsm-job-edit-btn" href="%2$s">%1$s</a></div>', esc_html__( 'Edit Job', 'wp-job-openings' ), esc_url( get_edit_post_link( $job_id ) ) ); ?></td>
		  <td></td>
		</tr>
		<?php endif; ?>
		<?php
	endif;
	?>
</table>
