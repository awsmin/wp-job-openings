<?php
  if( ! defined( 'ABSPATH' ) ) {
    exit;
  }
  $the_ID = get_the_ID();
  $args = array(
    'numberposts' => 1,
    'post_type'   => 'awsm_job_application',
    'post_parent' => $the_ID,
    'post_status' => 'publish'
  );
  $recent_application = wp_get_recent_posts( $args );
  $post_count = get_post_meta( $the_ID, 'awsm_application_count', true );
  $post_count = ( ! empty( $post_count ) ) ? $post_count : 0;
  $check_status = get_post_status( $the_ID );
  $views_count = get_post_meta( $the_ID , 'awsm_views_count', true );
?>

<table class="awsm-job-stat-table">
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
    <td><?php esc_html_e( 'Applications Received:', 'wp-job-openings' ); ?></td>
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
        if( $post_count > 0 && ! empty( $recent_application ) ) {
          $app_id = $recent_application[0]['ID'];
          $link = get_edit_post_link( $app_id );
          printf( '<a href="%1$s">%2$s %3$s</a>', $link, human_time_diff( get_the_time( 'U', $app_id ), current_time( 'timestamp' ) ), esc_html__( 'ago', 'wp-job-openings' ) );
        } else {
          esc_html_e( 'NA', 'wp-job-openings' );
        }
      ?>
    </td>
  </tr>
</table>