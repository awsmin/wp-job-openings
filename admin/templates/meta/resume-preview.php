<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Initialize applicant resume preview meta box.
 *
 * @since 3.5.0
 */
do_action( 'awsm_resume_preview_mb_init', $post->ID );
?>
<div class="awsm-resume-preview">
<?php
		$awsm_attachment_id = get_post_meta( $post->ID, 'awsm_attachment_id', true );
		$attachment_url     = wp_get_attachment_url( intval( $awsm_attachment_id ) );
if ( $attachment_url ) :
	$file_extension = strtolower( pathinfo( $attachment_url, PATHINFO_EXTENSION ) );

	// Supported office formats
	$supported_office_formats = array(
		'doc',
		'docx',      // Word documents
		'ppt',
		'pptx',      // PowerPoint presentations
		'xls',
		'xlsx',       // Excel spreadsheets
	);

	// Formats supported by Google Docs Viewer
	$google_docs_formats = array(
		'csv',              // CSV files
		'odt',
		'ods',
		'odp', // OpenDocument formats
	);

	// Supported image formats
	$supported_image_formats = array(
		'jpg',
		'jpeg',
		'png',
		'gif',
		'bmp',
		'webp', // Common image formats
	);

	?>
			<div class="awsm-document-preview">
		<?php if ( $file_extension === 'pdf' ) : ?>
				<iframe 
					src="<?php echo esc_url( $attachment_url ); ?>" 
					style="width: 100%; height: 400px; border: none;" 
					frameborder="0">
				</iframe>
			<?php elseif ( in_array( $file_extension, $supported_office_formats ) ) : ?>
				<iframe 
					src="<?php echo esc_url( 'https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode( $attachment_url ) ); ?>" 
					style="width: 100%; height: 400px; border: none;" 
					frameborder="0">
				</iframe>
			<?php elseif ( in_array( $file_extension, $google_docs_formats ) ) : ?>
				<iframe 
					src="https://docs.google.com/viewer?embedded=true&url=<?php echo urlencode( $attachment_url ); ?>"
					style="width: 100%; height: 400px; border: none;" 
					frameborder="0">
				</iframe>
			<?php elseif ( in_array( $file_extension, $supported_image_formats ) ) : ?>
				<img 
					src="<?php echo esc_url( $attachment_url ); ?>" 
					style="width: 100%; height: auto; border: none;" 
					alt="<?php echo esc_attr( basename( $attachment_url ) ); ?>">
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
