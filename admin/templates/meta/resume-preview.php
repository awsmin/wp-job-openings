<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Initialize applicant resume preview meta box.
 *
 * @since 4.0.0
 */
do_action( 'awsm_resume_preview_mb_init', $post->ID );
?>
<div class="awsm-resume-preview">
	<?php
		$awsm_attachment_id    = get_post_meta( $post->ID, 'awsm_attachment_id', true );
		$secure_upload_enabled = get_option( 'awsm_hide_uploaded_files' ) === 'hide_files';
		$attachment_url        = ( ! $secure_upload_enabled ) ? wp_get_attachment_url( intval( $awsm_attachment_id ) ) : false;
	if ( $secure_upload_enabled && ! empty( $awsm_attachment_id ) ) :
		?>
			<div class="awsm-resume-secure">
				<h4><strong><?php esc_html_e( 'Preview not available', 'wp-job-openings' ); ?></strong></h4>
				<p><?php esc_html_e( 'Secure file upload is enabled, which disables resume preview. You can still download and view the file.', 'wp-job-openings' ); ?></p>
			</div>
		<?php
		elseif ( $attachment_url ) :
			$file_extension = strtolower( pathinfo( $attachment_url, PATHINFO_EXTENSION ) );
			?>
			<div class="awsm-document-preview">
				<div class="awsm-preview-loader">
					<div class="awsm-preview-spinner"></div>
					<button type="button" class="awsm-preview-reload-btn">
						&#8635; <?php esc_html_e( 'Reload Preview', 'wp-job-openings' ); ?>
					</button>
				</div>
			<?php if ( $file_extension === 'pdf' ) : ?>
					<iframe
						data-src="<?php echo esc_url( 'https://docs.google.com/viewer?embedded=true&url=' . urlencode( $attachment_url ) ); ?>"
						class="awsm-preview-frame"
						style="width: 100%; height: 400px; border: none;"
						frameborder="0"
						sandbox="allow-scripts allow-same-origin allow-popups">
					</iframe>
				<?php elseif ( in_array( $file_extension, array( 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx' ) ) ) : ?>
					<iframe
						data-src="<?php echo esc_url( 'https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode( $attachment_url ) ); ?>"
						class="awsm-preview-frame"
						style="width: 100%; height: 400px; border: none;"
						frameborder="0">
					</iframe>
				<?php else : ?>
					<div class="awsm-preview-unsupported">
						<h2><strong><?php esc_html_e( 'This file type cannot be previewed. Please download the file to view it.', 'wp-job-openings' ); ?></strong></h2>
					</div>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<div class="awsm-resume-none">
				<h2><strong><?php esc_html_e( 'No resume to preview. File not found!', 'wp-job-openings' ); ?></strong></h2>
			</div>
			<?php
		endif;
		?>
</div>
