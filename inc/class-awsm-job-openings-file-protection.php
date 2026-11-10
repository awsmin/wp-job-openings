<?php
/**
 * Encrypts applicant uploads at rest when "Secure uploaded files" is enabled.
 *
 * The .htaccess rule written for this setting is ignored by NGINX and by hosts that do not allow
 * per-directory server configuration (e.g. WordPress.com, WP Engine, Kinsta). Encrypting the files
 * keeps them unreadable through their public URL on any server, while authorised downloads and
 * notification mail attachments are decrypted by PHP.
 *
 * @package wp-job-openings
 * @since 4.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.WP.AlternativeFunctions, WordPress.PHP.NoSilencedErrors.Discouraged
// Sodium functions and constants are provided by sodium_compat (bundled with WordPress) below PHP 7.2.
// phpcs:disable PHPCompatibility.Constants.NewConstants, PHPCompatibility.FunctionUse.NewFunctions

class AWSM_Job_Openings_File_Protection {
	/**
	 * Encrypted file signature.
	 */
	const MAGIC = 'AWSMENC1';

	/**
	 * Encrypted file format version.
	 */
	const FORMAT_VERSION = 1;

	/**
	 * Length of the file header: magic (8) + version (1) + key id (4) + plain size (4) + stream header (24).
	 */
	const HEADER_LENGTH = 41;

	/**
	 * Plaintext chunk size.
	 */
	const CHUNK_SIZE = 65536;

	/**
	 * Option holding the generated encryption key.
	 */
	const KEY_OPTION = 'awsm_jobs_file_encryption_key';

	/**
	 * Cron hook for encrypting existing files (or decrypting them once the setting is disabled).
	 */
	const SWEEP_HOOK = 'awsm_jobs_file_protection_sweep';

	/**
	 * Suffix for files being written.
	 */
	const TEMP_SUFFIX = '.awsm-tmp';

	private static $instance = null;

	/**
	 * Decrypted temporary files created during this request.
	 *
	 * @var array
	 */
	private static $temp_files = array();

	public function __construct() {
		add_action( 'awsm_job_application_submitted', array( $this, 'protect_application_files' ), 999 );
		add_action( self::SWEEP_HOOK, array( $this, 'sweep' ) );
		add_action( 'init', array( $this, 'schedule_sweep' ) );
		add_action( 'add_option_awsm_hide_uploaded_files', array( $this, 'schedule_immediate_sweep' ) );
		add_action( 'update_option_awsm_hide_uploaded_files', array( $this, 'schedule_immediate_sweep' ) );
		add_filter( 'fallback_intermediate_image_sizes', array( $this, 'disable_pdf_previews' ), 999 );
		add_filter( 'wp_mail', array( $this, 'decrypt_mail_attachments' ), 999 );
		add_action( 'wp_mail_succeeded', array( __CLASS__, 'cleanup_temp_files' ) );
		add_action( 'wp_mail_failed', array( __CLASS__, 'cleanup_temp_files' ) );
		add_action( 'shutdown', array( __CLASS__, 'cleanup_temp_files' ) );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Whether the server can encrypt files (ext-sodium or the sodium_compat library bundled with WordPress).
	 *
	 * @return bool
	 */
	public static function is_supported() {
		return function_exists( 'sodium_crypto_secretstream_xchacha20poly1305_init_push' ) && defined( 'SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_TAG_FINAL' );
	}

	/**
	 * Whether new and existing applicant uploads should be encrypted.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return get_option( 'awsm_hide_uploaded_files' ) === 'hide_files' && self::is_supported();
	}

	/**
	 * Absolute path of the HireZoot upload directory.
	 *
	 * @return string
	 */
	public static function get_base_dir() {
		$upload_dir = wp_upload_dir( null, false );
		return wp_normalize_path( trailingslashit( $upload_dir['basedir'] ) . AWSM_JOBS_UPLOAD_DIR_NAME );
	}

	/**
	 * Whether the file is stored encrypted.
	 *
	 * @param string $file Absolute file path.
	 * @return bool
	 */
	public static function is_encrypted_file( $file ) {
		if ( ! is_string( $file ) || $file === '' || ! is_file( $file ) || filesize( $file ) < self::HEADER_LENGTH ) {
			return false;
		}
		$handle = @fopen( $file, 'rb' );
		if ( ! $handle ) {
			return false;
		}
		$magic = fread( $handle, strlen( self::MAGIC ) );
		fclose( $handle );
		return $magic === self::MAGIC;
	}

	/**
	 * Size of the original (decrypted) file.
	 *
	 * @param string $file Absolute file path.
	 * @return int|false
	 */
	public static function get_file_size( $file ) {
		if ( ! self::is_encrypted_file( $file ) ) {
			return is_file( $file ) ? filesize( $file ) : false;
		}
		$header = self::read_header( $file );
		return $header ? $header['size'] : false;
	}

	/**
	 * Get the decrypted contents of an applicant file. Works for both encrypted and plain files.
	 *
	 * Add-ons that read applicant files should use this instead of file_get_contents().
	 *
	 * @param string $file Absolute file path.
	 * @return string|false
	 */
	public static function get_file_contents( $file ) {
		if ( ! self::is_encrypted_file( $file ) ) {
			return is_readable( $file ) ? file_get_contents( $file ) : false;
		}
		$contents = '';
		$success  = self::decrypt_stream(
			$file,
			function ( $chunk ) use ( &$contents ) {
				$contents .= $chunk;
				return true;
			}
		);
		return $success ? $contents : false;
	}

	/**
	 * Send the decrypted file to the output buffer.
	 *
	 * @param string $file Absolute file path.
	 * @return bool
	 */
	public static function output_file( $file ) {
		if ( ! self::is_encrypted_file( $file ) ) {
			return readfile( $file ) !== false;
		}
		return self::decrypt_stream(
			$file,
			function ( $chunk ) {
				echo $chunk; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				flush();
				return true;
			}
		);
	}

	/**
	 * Create a decrypted copy of the file in the system temp directory. The copy keeps the original file name
	 * and is removed at the end of the request.
	 *
	 * @param string $file Absolute file path.
	 * @return string|false Temporary file path.
	 */
	public static function get_decrypted_temp_file( $file ) {
		if ( ! self::is_encrypted_file( $file ) ) {
			return false;
		}
		$temp_dir = trailingslashit( get_temp_dir() ) . 'awsm-jobs-' . wp_generate_password( 20, false, false );
		if ( ! wp_mkdir_p( $temp_dir ) ) {
			return false;
		}
		$temp_file = $temp_dir . '/' . wp_basename( $file );
		$handle    = @fopen( $temp_file, 'wb' );
		$success   = false;
		if ( $handle ) {
			$success = self::decrypt_stream(
				$file,
				function ( $chunk ) use ( $handle ) {
					return fwrite( $handle, $chunk ) !== false;
				}
			);
			fclose( $handle );
		}
		self::$temp_files[] = $temp_file;
		if ( ! $success ) {
			self::cleanup_temp_files();
			return false;
		}
		return $temp_file;
	}

	public static function cleanup_temp_files() {
		foreach ( self::$temp_files as $temp_file ) {
			if ( file_exists( $temp_file ) ) {
				@unlink( $temp_file );
			}
			@rmdir( dirname( $temp_file ) );
		}
		self::$temp_files = array();
	}

	/**
	 * Encrypt a file in place. The original is only replaced after the encrypted copy is verified.
	 *
	 * @param string $file Absolute file path.
	 * @return bool
	 */
	public static function encrypt_file( $file ) {
		if ( ! self::is_supported() || ! is_file( $file ) || self::is_encrypted_file( $file ) ) {
			return false;
		}
		$key  = self::get_primary_key();
		$size = filesize( $file );
		if ( ! $key || $size === false || $size > 0xFFFFFFFF ) {
			return false;
		}

		$temp_file = $file . self::TEMP_SUFFIX;
		$in        = @fopen( $file, 'rb' );
		$out       = @fopen( $temp_file, 'wb' );
		$success   = $in && $out;
		if ( $success ) {
			list( $state, $stream_header ) = sodium_crypto_secretstream_xchacha20poly1305_init_push( $key );

			$success = fwrite( $out, self::MAGIC . chr( self::FORMAT_VERSION ) . self::get_key_id( $key ) . pack( 'N', $size ) . $stream_header ) !== false;
			$buffer  = (string) fread( $in, self::CHUNK_SIZE );
			while ( $success ) {
				$next    = (string) fread( $in, self::CHUNK_SIZE );
				$tag     = $next === '' ? SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_TAG_FINAL : SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_TAG_MESSAGE;
				$success = fwrite( $out, sodium_crypto_secretstream_xchacha20poly1305_push( $state, $buffer, '', $tag ) ) !== false;
				if ( $next === '' ) {
					break;
				}
				$buffer = $next;
			}
		}
		if ( $in ) {
			fclose( $in );
		}
		if ( $out ) {
			fclose( $out );
		}

		// Never replace the original unless the encrypted copy decrypts back to the same content.
		if ( $success ) {
			$hash    = hash_init( 'sha256' );
			$success = self::decrypt_stream(
				$temp_file,
				function ( $chunk ) use ( $hash ) {
					hash_update( $hash, $chunk );
					return true;
				}
			) && hash_equals( hash_file( 'sha256', $file ), hash_final( $hash ) );
		}

		return self::replace_file( $file, $temp_file, $success, 'encrypt' );
	}

	/**
	 * Decrypt a file in place.
	 *
	 * @param string $file Absolute file path.
	 * @return bool
	 */
	public static function decrypt_file( $file ) {
		if ( ! self::is_encrypted_file( $file ) ) {
			return false;
		}
		$header    = self::read_header( $file );
		$temp_file = $file . self::TEMP_SUFFIX;
		$out       = @fopen( $temp_file, 'wb' );
		$success   = false;
		if ( $header && $out ) {
			$success = self::decrypt_stream(
				$file,
				function ( $chunk ) use ( $out ) {
					return fwrite( $out, $chunk ) !== false;
				}
			);
		}
		if ( $out ) {
			fclose( $out );
		}
		$success = $success && filesize( $temp_file ) === $header['size'];

		return self::replace_file( $file, $temp_file, $success, 'decrypt' );
	}

	/**
	 * Encrypt the files of a newly submitted application, including generated image sizes.
	 *
	 * @param int $application_id Application ID.
	 */
	public function protect_application_files( $application_id ) {
		if ( ! self::is_enabled() ) {
			return;
		}
		$attachment_ids = get_children(
			array(
				'post_parent' => $application_id,
				'post_type'   => 'attachment',
				'post_status' => 'any',
				'fields'      => 'ids',
			)
		);
		$resume_id      = get_post_meta( $application_id, 'awsm_attachment_id', true );
		if ( ! empty( $resume_id ) ) {
			$attachment_ids[] = (int) $resume_id;
		}
		foreach ( array_unique( array_map( 'intval', $attachment_ids ) ) as $attachment_id ) {
			foreach ( self::get_attachment_files( $attachment_id ) as $file ) {
				if ( self::should_encrypt( $file ) ) {
					self::encrypt_file( $file );
				}
			}
		}
	}

	/**
	 * Keep the recurring sweep scheduled while the setting is enabled. It encrypts existing files and catches
	 * files written outside the submission flow.
	 */
	public function schedule_sweep() {
		if ( self::is_enabled() && ! wp_next_scheduled( self::SWEEP_HOOK ) ) {
			wp_schedule_event( time() + MINUTE_IN_SECONDS, 'hourly', self::SWEEP_HOOK );
		}
	}

	/**
	 * Run a sweep as soon as possible after the setting is toggled.
	 */
	public function schedule_immediate_sweep() {
		if ( ! wp_next_scheduled( self::SWEEP_HOOK, array( true ) ) ) {
			wp_schedule_single_event( time(), self::SWEEP_HOOK, array( true ) );
		}
	}

	/**
	 * Encrypt every unprotected file in the upload directory, or decrypt them all once the setting is disabled.
	 * Work is time-boxed; a follow-up run is scheduled when files remain.
	 */
	public function sweep() {
		if ( ! self::is_supported() ) {
			return;
		}
		$enabled = self::is_enabled();
		/**
		 * Filters the maximum time in seconds a single file protection sweep may run.
		 *
		 * @since 4.2.1
		 *
		 * @param int $time_limit Time limit in seconds.
		 */
		$deadline  = time() + (int) apply_filters( 'awsm_jobs_file_protection_sweep_time_limit', 20 );
		$remaining = false;
		foreach ( self::get_candidate_files() as $file ) {
			if ( time() >= $deadline ) {
				$remaining = true;
				break;
			}
			$encrypted = self::is_encrypted_file( $file );
			if ( $enabled && ! $encrypted && self::should_encrypt( $file ) ) {
				self::encrypt_file( $file );
			} elseif ( ! $enabled && $encrypted ) {
				self::decrypt_file( $file );
			}
		}
		if ( $remaining ) {
			wp_schedule_single_event( time() + MINUTE_IN_SECONDS, self::SWEEP_HOOK, array( true ) );
		} elseif ( ! $enabled ) {
			wp_clear_scheduled_hook( self::SWEEP_HOOK );
		}
	}

	/**
	 * Applicant uploads are not displayed as images anywhere, so skip PDF page previews while secure uploads
	 * is enabled. The previews are full-page renders of the CV and would otherwise sit unprotected beside it.
	 *
	 * @param array $sizes Fallback sizes for non-image files.
	 * @return array
	 */
	public function disable_pdf_previews( $sizes ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$action = isset( $_POST['action'] ) ? sanitize_key( $_POST['action'] ) : '';
		if ( in_array( $action, array( 'awsm_applicant_form_submission', 'awsm_applicant_attachments_handler' ), true ) && self::is_enabled() ) {
			return array();
		}
		return $sizes;
	}

	/**
	 * Swap encrypted mail attachments for decrypted temporary copies.
	 *
	 * @param array $atts wp_mail() arguments.
	 * @return array
	 */
	public function decrypt_mail_attachments( $atts ) {
		if ( empty( $atts['attachments'] ) ) {
			return $atts;
		}
		$attachments = $atts['attachments'];
		if ( ! is_array( $attachments ) ) {
			$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
		}
		$base_dir = self::get_base_dir();
		foreach ( $attachments as $name => $file ) {
			if ( is_string( $file ) && strpos( wp_normalize_path( $file ), $base_dir ) === 0 && self::is_encrypted_file( $file ) ) {
				$temp_file = self::get_decrypted_temp_file( $file );
				if ( $temp_file ) {
					$attachments[ $name ] = $temp_file;
				}
			}
		}
		$atts['attachments'] = $attachments;
		return $atts;
	}

	/**
	 * Whether a file in the upload directory should be encrypted.
	 *
	 * @param string $file Absolute file path.
	 * @return bool
	 */
	public static function should_encrypt( $file ) {
		$file    = wp_normalize_path( $file );
		$encrypt = strpos( $file, self::get_base_dir() . '/' ) === 0 && ! self::is_excluded( $file );

		// Pro Pack reads applicant photos directly from disk, so leave images readable unless it declares support.
		if ( $encrypt && class_exists( 'AWSM_Job_Openings_Pro_Pack' ) && preg_match( '/\.(jpe?g|png|gif|webp|bmp|heic|avif)$/i', $file ) && ! preg_match( '/-pdf(-\d+x\d+)?\.jpe?g$/i', $file ) ) {
			/**
			 * Filters whether image uploads are encrypted when Pro Pack is active.
			 *
			 * @since 4.2.1
			 *
			 * @param bool $encrypt Whether to encrypt image uploads.
			 */
			$encrypt = (bool) apply_filters( 'awsm_jobs_encrypt_image_uploads', false );
		}

		/**
		 * Filters whether an applicant upload is encrypted at rest.
		 *
		 * @since 4.2.1
		 *
		 * @param bool $encrypt Whether to encrypt the file.
		 * @param string $file Absolute file path.
		 */
		return (bool) apply_filters( 'awsm_jobs_encrypt_uploaded_file', $encrypt, $file );
	}

	private static function is_excluded( $file ) {
		if ( in_array( wp_basename( $file ), array( 'index.php', 'index.html', '.htaccess' ), true ) ) {
			return true;
		}
		if ( substr( $file, -strlen( self::TEMP_SUFFIX ) ) === self::TEMP_SUFFIX ) {
			return true;
		}
		// Pro Pack's pending uploads (validated again on submission) and the add-on CSV export directory.
		$excluded_dirs = array( 'temp', 'batch-export' );
		$relative_path = substr( $file, strlen( self::get_base_dir() ) + 1 );
		$first_dir     = strtok( $relative_path, '/' );
		return $first_dir !== $relative_path && in_array( $first_dir, $excluded_dirs, true );
	}

	/**
	 * All files that belong to an attachment: the main file, generated sizes and the original image.
	 *
	 * @param int $attachment_id Attachment ID.
	 * @return array
	 */
	private static function get_attachment_files( $attachment_id ) {
		$file = get_attached_file( $attachment_id, true );
		if ( empty( $file ) ) {
			return array();
		}
		$files    = array( $file );
		$dir      = trailingslashit( dirname( $file ) );
		$metadata = wp_get_attachment_metadata( $attachment_id, true );
		if ( is_array( $metadata ) ) {
			if ( ! empty( $metadata['sizes'] ) && is_array( $metadata['sizes'] ) ) {
				foreach ( $metadata['sizes'] as $size ) {
					if ( ! empty( $size['file'] ) ) {
						$files[] = $dir . wp_basename( $size['file'] );
					}
				}
			}
			if ( ! empty( $metadata['original_image'] ) ) {
				$files[] = $dir . wp_basename( $metadata['original_image'] );
			}
		}
		return array_unique( $files );
	}

	private static function get_candidate_files() {
		$files    = array();
		$base_dir = self::get_base_dir();
		if ( ! is_dir( $base_dir ) ) {
			return $files;
		}
		try {
			$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $base_dir, FilesystemIterator::SKIP_DOTS ) );
			foreach ( $iterator as $item ) {
				if ( $item->isFile() ) {
					$file = wp_normalize_path( $item->getPathname() );
					if ( ! self::is_excluded( $file ) ) {
						$files[] = $file;
					}
				}
			}
		} catch ( Exception $e ) {
			AWSM_Job_Openings::log( $e->getMessage() );
		}
		return $files;
	}

	private static function replace_file( $file, $temp_file, $success, $operation ) {
		if ( $success ) {
			$perms   = fileperms( $file );
			$success = @rename( $temp_file, $file );
			if ( $success && $perms !== false ) {
				@chmod( $file, $perms & 0777 );
			}
		}
		if ( ! $success ) {
			if ( file_exists( $temp_file ) ) {
				@unlink( $temp_file );
			}
			AWSM_Job_Openings::log( sprintf( 'HireZoot: unable to %s file: %s', $operation, $file ) );
		}
		return $success;
	}

	private static function read_header( $file ) {
		$handle = @fopen( $file, 'rb' );
		if ( ! $handle ) {
			return false;
		}
		$header = fread( $handle, self::HEADER_LENGTH );
		fclose( $handle );
		if ( strlen( $header ) !== self::HEADER_LENGTH || substr( $header, 0, 8 ) !== self::MAGIC || ord( $header[8] ) !== self::FORMAT_VERSION ) {
			return false;
		}
		$size = unpack( 'N', substr( $header, 13, 4 ) );
		return array(
			'key_id'        => substr( $header, 9, 4 ),
			'size'          => $size[1],
			'stream_header' => substr( $header, 17, 24 ),
		);
	}

	/**
	 * Decrypt a file chunk by chunk. Only authenticated chunks are passed to the callback.
	 *
	 * @param string   $file Absolute file path.
	 * @param callable $callback Receives each decrypted chunk; return false to abort.
	 * @return bool True when the whole file was decrypted.
	 */
	private static function decrypt_stream( $file, $callback ) {
		if ( ! self::is_supported() ) {
			return false;
		}
		$header = self::read_header( $file );
		$key    = $header ? self::get_key_by_id( $header['key_id'] ) : false;
		if ( ! $key ) {
			AWSM_Job_Openings::log( sprintf( 'HireZoot: no encryption key available for file: %s', $file ) );
			return false;
		}
		$handle = @fopen( $file, 'rb' );
		if ( ! $handle ) {
			return false;
		}
		fseek( $handle, self::HEADER_LENGTH );
		$state    = sodium_crypto_secretstream_xchacha20poly1305_init_pull( $header['stream_header'], $key );
		$complete = false;
		while ( ! feof( $handle ) ) {
			$chunk = fread( $handle, self::CHUNK_SIZE + SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_ABYTES );
			if ( $chunk === false || $chunk === '' ) {
				break;
			}
			$result = sodium_crypto_secretstream_xchacha20poly1305_pull( $state, $chunk );
			if ( ! is_array( $result ) || call_user_func( $callback, $result[0] ) === false ) {
				break;
			}
			if ( $result[1] === SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_TAG_FINAL ) {
				$complete = true;
				break;
			}
		}
		fclose( $handle );
		return $complete;
	}

	/**
	 * Available keys, primary first. AWSM_JOBS_FILE_ENCRYPTION_KEY (base64, 32 bytes) in wp-config.php takes
	 * precedence over the generated key; the generated key is kept so older files remain readable.
	 *
	 * @param bool $generate Generate and store a key when none exists.
	 * @return array
	 */
	private static function get_keys( $generate = false ) {
		$keys = array();
		if ( defined( 'AWSM_JOBS_FILE_ENCRYPTION_KEY' ) ) {
			$key = base64_decode( AWSM_JOBS_FILE_ENCRYPTION_KEY, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			if ( is_string( $key ) && strlen( $key ) === SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_KEYBYTES ) {
				$keys[] = $key;
			}
		}
		$stored = get_option( self::KEY_OPTION );
		if ( empty( $stored ) && $generate && empty( $keys ) ) {
			$new_key = base64_encode( sodium_crypto_secretstream_xchacha20poly1305_keygen() ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			// Keep a key created by a concurrent request instead of overwriting it.
			$stored = add_option( self::KEY_OPTION, $new_key, '', 'no' ) ? $new_key : get_option( self::KEY_OPTION );
		}
		if ( ! empty( $stored ) ) {
			$key = base64_decode( $stored, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			if ( is_string( $key ) && strlen( $key ) === SODIUM_CRYPTO_SECRETSTREAM_XCHACHA20POLY1305_KEYBYTES ) {
				$keys[] = $key;
			}
		}
		return $keys;
	}

	private static function get_primary_key() {
		$keys = self::get_keys( true );
		return ! empty( $keys ) ? $keys[0] : false;
	}

	private static function get_key_by_id( $key_id ) {
		foreach ( self::get_keys() as $key ) {
			if ( hash_equals( self::get_key_id( $key ), $key_id ) ) {
				return $key;
			}
		}
		return false;
	}

	private static function get_key_id( $key ) {
		return substr( hash( 'sha256', 'awsm-jobs-file-key|' . $key, true ), 0, 4 );
	}
}
