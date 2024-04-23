<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AWSM_Job_Openings_Uninstall {

	public static function uninstall() {
		self::clear_cron_jobs();
		self::remove_terms();
		self::remove_posts();
		self::remove_role_caps();
		self::delete_options();
	}

	private static function get_all_options() {
		$options = array(
			'awsm_jobs_plugin_version',
			'awsm_current_general_subtab',
			'awsm_select_page_listing',
			'awsm_job_company_name',
			'awsm_hr_email_address',
			'awsm_jobs_timezone',
			'awsm_permalink_slug',
			'awsm_jobs_remove_permalink_front_base',
			'awsm_default_msg',
			'awsm_jobs_email_digest',
			'awsm_jobs_disable_archive_page',
			'awsm_jobs_enable_featured_image',
			'awsm_hide_uploaded_files',
			'awsm_delete_data_on_uninstall',
			'awsm_current_appearance_subtab',
			'awsm_jobs_listing_view',
			'awsm_jobs_list_per_page',
			'awsm_jobs_number_of_columns',
			'awsm_enable_job_search',
			'awsm_enable_job_filter_listing',
			'awsm_jobs_listing_available_filters',
			'awsm_jobs_listing_specs',
			'awsm_jobs_details_page_template',
			'awsm_jobs_details_page_layout',
			'awsm_jobs_expired_jobs_listings',
			'awsm_jobs_specification_job_detail',
			'awsm_jobs_show_specs_icon',
			'awsm_jobs_make_specs_clickable',
			'awsm_jobs_specs_position',
			'awsm_jobs_expired_jobs_content_details',
			'awsm_jobs_expired_jobs_block_search',
			'awsm_jobs_hide_expiry_date',
			'awsm_current_specifications_subtab',
			'awsm_jobs_filter',
			'awsm_jobs_remove_filters',
			'awsm_current_form_subtab',
			'awsm_jobs_admin_upload_file_ext',
			'awsm_enable_gdpr_cb',
			'awsm_gdpr_cb_text',
			'awsm_jobs_enable_recaptcha',
			'awsm_jobs_recaptcha_site_key',
			'awsm_jobs_recaptcha_secret_key',
			'awsm_current_notification_subtab',
			'awsm_jobs_applicant_notification',
			'awsm_jobs_from_email_notification',
			'awsm_jobs_reply_to_notification',
			'awsm_jobs_hr_notification',
			'awsm_jobs_acknowledgement',
			'awsm_jobs_notification_subject',
			'awsm_jobs_notification_content',
			'awsm_jobs_notification_mail_template',
			'awsm_jobs_admin_to_notification',
			'awsm_jobs_enable_admin_notification',
			'awsm_jobs_admin_from_email_notification',
			'awsm_jobs_admin_reply_to_notification',
			'awsm_jobs_admin_hr_notification',
			'awsm_jobs_admin_notification_subject',
			'awsm_jobs_admin_notification_content',
			'awsm_jobs_notification_admin_mail_template',
			'awsm_jobs_notification_customizer',
			'awsm_jobs_default_listing_page_id',
			'awsm_jobs_insert_default_specs_terms',
			'awsm_register_default_settings',
			'awsm_gdpr_policies',
			'awsm_plugin_rating_job_count',
			'awsm_plugin_rating_application_count',
			'awsm_jobs_plugin_rating',
			'awsm_jobs_enable_expiry_notification',
			'awsm_jobs_author_from_email_notification',
			'awsm_jobs_author_to_notification',
			'awsm_jobs_author_hr_notification',
			'awsm_jobs_author_notification_subject',
			'awsm_jobs_author_notification_content',
			'awsm_jobs_notification_author_mail_template',
			'awsm_jobs_author_reply_to_notification',
		);
		return $options;
	}

	private static function get_custom_caps() {
		$custom_caps = array(
			'edit_jobs',
			'delete_jobs',
			'edit_applications',
			'delete_applications',
			'edit_published_jobs',
			'delete_published_jobs',
			'publish_jobs',
			'edit_published_applications',
			'delete_published_applications',
			'publish_applications',
			'edit_others_jobs',
			'read_private_jobs',
			'delete_private_jobs',
			'delete_others_jobs',
			'edit_private_jobs',
			'edit_others_applications',
			'read_private_applications',
			'delete_private_applications',
			'delete_others_applications',
			'edit_private_applications',
			'manage_awsm_jobs',
		);
		return $custom_caps;
	}

	private static function clear_cron_jobs() {
		wp_clear_scheduled_hook( 'awsm_check_for_expired_jobs' );
		wp_clear_scheduled_hook( 'awsm_jobs_email_digest' );
	}

	private static function remove_terms() {
		global $wpdb;
		$filters = get_option( 'awsm_jobs_filter' );
		if ( ! empty( $filters ) ) {
			foreach ( $filters as $filter ) {
				$taxonomy = sanitize_text_field( $filter['taxonomy'] );
				$terms    = $wpdb->get_results( $wpdb->prepare( "SELECT t.term_id, tt.term_taxonomy_id FROM {$wpdb->terms} t INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ( %s )", $taxonomy ) );
				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
						$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $term->term_taxonomy_id ), array( '%d' ) );
						$wpdb->delete( $wpdb->term_relationships, array( 'term_taxonomy_id' => $term->term_taxonomy_id ), array( '%d' ) );
						$wpdb->delete( $wpdb->terms, array( 'term_id' => $term->term_id ), array( '%d' ) );
					}
				}
				$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ) );
			}
		}
	}

	private static function remove_attachments() {
		global $wpdb;
		$attachments = $wpdb->get_results( "SELECT posts.ID FROM {$wpdb->posts} posts INNER JOIN {$wpdb->posts} parent ON posts.post_parent = parent.ID WHERE posts.post_type = 'attachment' AND parent.post_type = 'awsm_job_application'" );
		if ( ! empty( $attachments ) ) {
			foreach ( $attachments as $attachment ) {
				wp_delete_attachment( $attachment->ID, true );
			}
		}
	}

	private static function remove_posts() {
		global $wpdb;
		self::remove_attachments();
		$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN( 'awsm_job_openings', 'awsm_job_application' );" );
		$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );
	}

	private static function remove_role_caps() {
		global $wp_roles;
		$caps       = self::get_custom_caps();
		$role_slugs = array_keys( $wp_roles->roles );
		foreach ( $role_slugs as $slug ) {
			$role = get_role( $slug );
			foreach ( $caps as $cap ) {
				$role->remove_cap( $cap );
			}
		}
		// Now, remove the custom role
		if ( get_role( 'hr' ) ) {
			remove_role( 'hr' );
		}
	}

	private static function delete_options() {
		$options = self::get_all_options();
		foreach ( $options as $option ) {
			delete_option( $option );
		}
	}
}
