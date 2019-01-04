<?php
  if( ! defined( 'ABSPATH' ) ) {
    exit;
  }
  $the_ID = get_the_ID();
  if( $post->post_type == 'awsm_job_application' ) {
    $the_ID = $post->post_parent;
  }
  $applications = AWSM_Job_Openings::get_applications( $the_ID );
  $post_count = count( $applications );
  $check_status = get_post_status( $the_ID );
  $views_count = get_post_meta( $the_ID , 'awsm_views_count', true );
  $job_title = get_the_title( $the_ID );
?>

<table class="awsm-job-stat-table">
  <tr>
    <td><?php esc_html_e( 'Job Title' );?></td>
    <td><?php echo wp_strip_all_tags( $job_title ); ?></td>
  </tr>
  <tr>
    <td><?php esc_html_e( 'Current Status:', 'wp-job-openings' ); ?></td>
    <td>
    <?php
      if ( $check_status == 'publish' ) {
        echo '<span class="awsm-text-green">'. esc_html__( 'Active', 'wp-job-openings' ) . '</span>';
      } elseif( $check_status == 'expired' ) {
        echo '<span class="awsm-text-red">'. esc_html__( 'Expired', 'wp-job-openings' ) . '</span>';
      } else {
        echo '<span>'. esc_html__( 'Pending', 'wp-job-openings' ) . '</span>';
      }
    ?>
    </td>
  </tr>
  <tr>
    <td><?php esc_html_e( 'Views:', 'wp-job-openings' ); ?></td>
    <td><?php echo ( ! empty( $views_count ) ) ? $views_count : 0; ?></td>
  </tr>
  <tr>
    <td><?php esc_html_e( 'Applications:', 'wp-job-openings' ); ?></td>
    <td>
      <?php
        if( $post_count > 0 ) {
          printf( '<a href="%1$s">%2$s</a>', esc_url( admin_url( 'edit.php?post_type=awsm_job_application&awsm_filter_posts=' . $the_ID ) ), $post_count );
        } else {
          echo $post_count;
        }
      ?>
    </td>
  </tr>
  <tr>
    <td><?php esc_html_e( 'Last Submission:', 'wp-job-openings' ); ?></td>
    <td>
      <?php
        if( $post_count > 0 ) {
          $recent_application = array_values( $applications )[0];
          $link = get_edit_post_link( $recent_application->ID );
          printf( '<a href="%1$s">%2$s %3$s</a>', esc_url( $link ), human_time_diff( get_the_time( 'U', $recent_application->ID ), current_time( 'timestamp' ) ), esc_html__( 'ago', 'wp-job-openings' ) );
        } else {
          esc_html_e( 'NA', 'wp-job-openings' );
        }
      ?>
    </td>
  </tr>
  <?php
    if( $post->post_type == 'awsm_job_application' ) :
      $date_format = __( get_option( 'date_format' ) );
      $job_submission_date = date_i18n( $date_format, strtotime( get_the_date( '', $the_ID ) ) );
      $expiry_date = get_post_meta( $the_ID, 'awsm_job_expiry', true);
      $formatted_date = date_i18n( $date_format, strtotime( $expiry_date ) );
  ?>
      <tr>
        <td><?php esc_html_e( 'Date Posted:', 'wp-job-openings' ); ?></td>
        <td><?php echo $job_submission_date; ?></td>
      </tr>
      <tr>
        <td><?php esc_html_e( 'Date of Expiry:', 'wp-job-openings' ); ?></td>
        <td><?php echo $formatted_date; ?></td>
      </tr>
      <?php if( current_user_can( 'edit_post', $the_ID ) ) : ?>
        <tr>
          <td><?php printf( '<div class="awsm-job-edit-btn-wrapper"><a class="button awsm-job-edit-btn" href="%2$s">%1$s</a></div>', esc_html__( 'Edit Job', 'wp-job-openings' ), esc_url( get_edit_post_link( $the_ID ) ) ); ?></td>
          <td></td>
        </tr>
      <?php endif; ?>
  <?php
    endif;
  ?>
</table>