<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	$job_id = get_the_ID();
if ( $post->post_type === 'awsm_job_application' ) {
	$job_id = $post->post_parent;
}
	$applications = AWSM_Job_Openings::get_applications( $job_id, 'ids' );
	$post_count   = count( $applications );
	$check_status = get_post_status( $job_id );
	$views_count  = get_post_meta( $job_id, 'awsm_views_count', true );
	$job_title    = get_the_title( $job_id );

	/**
	 * Initialize job status meta box.
	 *
	 * @since 1.6.0
	 *
	 * @param int $job_id  The Job ID.
	 * @param int $post_id Current Post ID (Job or Application).
	 */
	do_action( 'awsm_job_status_mb_init', $job_id, $post->ID );
?>

<table class="awsm-job-stat-table">
	<?php
		$data_rows = array(
			'job_title'       => array(
				esc_html__( 'Job Title', 'wp-job-openings' ),
				wp_strip_all_tags( $job_title ),
			),
			'current_status'  => array(
				esc_html__( 'Current Status:', 'wp-job-openings' ),
				'',
			),
			'views'           => array(
				esc_html__( 'Views:', 'wp-job-openings' ),
				! empty( $views_count ) ? esc_html( $views_count ) : 0,
			),
			'applications'    => array(
				esc_html__( 'Applications:', 'wp-job-openings' ),
				'',
			),
			'last_submission' => array(
				esc_html__( 'Last Submission:', 'wp-job-openings' ),
				'',
			),
		);

		if ( $check_status === 'publish' ) {
			$data_rows['current_status'][1] = '<span class="awsm-text-green">' . esc_html__( 'Active', 'wp-job-openings' ) . '</span>';
		} elseif ( $check_status === 'expired' ) {
			$data_rows['current_status'][1] = '<span class="awsm-text-red">' . esc_html__( 'Expired', 'wp-job-openings' ) . '</span>';
		} else {
			$data_rows['current_status'][1] = '<span>' . esc_html__( 'Pending', 'wp-job-openings' ) . '</span>';
		}

		if ( $post_count > 0 ) {
			$data_rows['applications'][1] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( admin_url( 'edit.php?post_type=awsm_job_application&awsm_filter_posts=' . $job_id ) ), esc_attr( $post_count ) );
		} else {
			$data_rows['applications'][1] = esc_html( $post_count );
		}

		if ( $post_count > 0 ) {
			$applications                    = array_values( $applications );
			$edit_link                       = get_edit_post_link( $applications[0] );
			$data_rows['last_submission'][1] = sprintf( '<a href="%1$s">%2$s %3$s</a>', esc_url( $edit_link ), esc_html( human_time_diff( get_the_time( 'U', $applications[0] ), current_time( 'timestamp' ) ) ), esc_html__( 'ago', 'wp-job-openings' ) );
		} else {
			$data_rows['last_submission'][1] = esc_html__( 'NA', 'wp-job-openings' );
		}

		if ( $post->post_type === 'awsm_job_application' ) {
			$date_format         = get_awsm_jobs_date_format( 'job-status' );
			$job_submission_date = date_i18n( $date_format, get_post_time( 'U', false, $job_id ) );
			$expiry_date         = get_post_meta( $job_id, 'awsm_job_expiry', true );
			$formatted_date      = ! empty( $expiry_date ) ? date_i18n( $date_format, strtotime( $expiry_date ) ) : esc_html__( 'NA', 'wp-job-openings' );

			if ( current_user_can( 'edit_post', $job_id ) ) {
				$data_rows['job_title'][1] = sprintf( '<a href="%2$s">%1$s</a>', $data_rows['job_title'][1], esc_url( get_edit_post_link( $job_id ) ) );
			}

			if ( empty( $data_rows['job_title'][1] ) ) {
				$job_name                       = get_post_meta( $post->ID, 'awsm_apply_for', true );
				$data_rows['current_status'][1] = '<span class="awsm-text-red">' . esc_html__( 'Deleted', 'wp-job-openings' ) . '</span>';
				$data_rows['job_title'][1]      = $job_name;
			}

			$data_rows['date_posted'] = array(
				esc_html__( 'Date Posted:', 'wp-job-openings' ),
				esc_html( $job_submission_date ),
			);

			$data_rows['date_of_expiry'] = array(
				esc_html__( 'Date of Expiry:', 'wp-job-openings' ),
				esc_html( $formatted_date ),
			);

			if ( $post_count > 1 ) {
				// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
				$next = $prev = $current = 0;
				// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
				$prev_btn = $next_btn = '';
				foreach ( $applications as $index => $application_id ) {
					if ( intval( $post->ID ) === $application_id ) {
						$current = $index + 1;
						if ( isset( $applications[ $index + 1 ] ) ) {
							$prev = $applications[ $index + 1 ];
						}
						if ( isset( $applications[ $index - 1 ] ) ) {
							$next = $applications[ $index - 1 ];
						}
					}
				}

				$prev_btn = sprintf( '<a class="button awsm-job-prev-application-btn%3$s" href="%2$s">&larr; %1$s</a>', esc_html__( 'Prev', 'wp-job-openings' ), $prev ? esc_url( get_edit_post_link( $prev ) ) : '#', ! $prev ? ' btn-disabled' : '' );

				$pagination = sprintf( '<span class="awsm-job-status-pagination">%1$s<br />%2$s</span>', esc_html( "{$current}/{$post_count}" ), esc_html__( 'Applications', 'wp-job-openings' ) );

				$next_btn = sprintf( '<a class="button awsm-job-next-application-btn%3$s" href="%2$s">%1$s &rarr;</a>', esc_html__( 'Next', 'wp-job-openings' ), $next ? esc_url( get_edit_post_link( $next ) ) : '#', ! $next ? ' btn-disabled' : '' );

				$data_rows['actions'][0] = '<div class="awsm-job-status-btn-wrapper">' . $prev_btn . $pagination . $next_btn . '</div>';
			}
		}

		/**
		 * Filters job status meta box content rows.
		 *
		 * @since 1.6.0
		 *
		 * @param array $data_rows Rows data array.
		 * @param int $job_id  The Job ID.
		 * @param int $post_id Current Post ID (Job or Application).
		 */
		$data_rows = apply_filters( 'awsm_job_status_mb_data_rows', $data_rows, $job_id, $post->ID );

		foreach ( $data_rows as $data_row ) {
			$column_content = '';
			if ( isset( $data_row[0] ) && ! isset( $data_row[1] ) ) {
				$column_content = '<td colspan="2">' . $data_row[0] . '</td>';
			} else {
				$column_content = sprintf( '<td>%1$s</td><td>%2$s</td>', isset( $data_row[0] ) ? $data_row[0] : '', isset( $data_row[1] ) ? $data_row[1] : '' );
			}

			echo '<tr>' . $column_content . '</tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
</table>
