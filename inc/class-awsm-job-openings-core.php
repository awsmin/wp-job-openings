<?php

if( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AWSM_Job_Openings_Core {
    private static $_instance = null;

	public function __construct(  ) {
        add_action( 'init', array( $this, 'register_post_types' ) );
    }

    public static function init() {
        if( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function register() {
        $this->unregister_awsm_job_openings_post_type();
        $this->register_post_types();
        $this->manage_default_roles_caps();
        $this->add_custom_role();
        $this->create_upload_directory();
    }

    public function unregister() {
        $this->unregister_awsm_job_openings_post_type();
        $this->manage_default_roles_caps( 'remove' );
        $this->remove_custom_role();
    }

	public function register_post_types() {

        if( post_type_exists( 'awsm_job_openings' ) ) {
            return;
        }

        $labels = array(
            'name'               => __( 'Job Openings', 'wp-job-openings' ),
            'singular_name'      => __( 'Job', 'wp-job-openings' ),
            'add_new'            => __( 'New Opening', 'wp-job-openings' ),
            'add_new_item'       => __( 'Add New Job', 'wp-job-openings' ),
            'edit_item'          => __( 'Edit Job', 'wp-job-openings' ),
            'new_item'           => __( 'New job', 'wp-job-openings' ),
            'search_items'       => __( 'Search Jobs', 'wp-job-openings' ),
            'not_found'          => __( 'No Jobs found', 'wp-job-openings' ),
            'not_found_in_trash' => __( 'No Jobs found in Trash', 'wp-job-openings' ),
            'parent_item_colon'  => __( 'Parent Job :', 'wp-job-openings' ),
            'menu_name'          => __( 'Job Openings', 'wp-job-openings' )
        );

        $args = array(
            'has_archive'     => true,
            'labels'          => $labels,
            'hierarchical'    => false,
            'map_meta_cap'    => true,
            'taxonomies'      => array(),
            'public'          => true,
            'show_ui'         => true,
            'show_in_rest'    => true,
            'show_in_menu'    => true,
            'rewrite'         => array( 'slug' => get_option( 'awsm_permalink_slug', 'jobs' ) ),
            'capability_type' => 'job',
            'menu_icon'       => esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/nav-icon.png'),
            'supports'        => array( 'title', 'editor' )
        );

        register_post_type( 'awsm_job_openings', $args );

        if( post_type_exists( 'awsm_job_application' ) ) {
            return;
        }

         $labels = array(
            'name'               => __( 'Applications',  'wp-job-openings' ),
            'singular_name'      => __( 'Application', 'wp-job-openings' ),
            'menu_name'          => __( 'Applications', 'wp-job-openings' ),
            'edit_item'          => __( 'Applications', 'wp-job-openings' ),
            'view_item'          => __( 'View Application', 'wp-job-openings' ),
            'search_items'       => __( 'Search Applications', 'wp-job-openings' ),
            'not_found'          => __( 'No Applications found', 'wp-job-openings' ),
            'not_found_in_trash' => __( 'No Applications found in Trash', 'wp-job-openings' )
        );

        $args = array(
            'labels'          => $labels,
            'public'          => false,
            'show_ui'         => true,
            'map_meta_cap'    => true,
            'show_in_menu'    => 'edit.php?post_type=awsm_job_openings',
            'capability_type' => 'application',
            'capabilities'    => array(
                'create_posts' => false
            ),
            'supports'        => false,
            'rewrite'         => false
        );

        register_post_type( 'awsm_job_application', $args );
    }

    private function get_caps() {
        $caps = array(
            'level_1' => array(
                'edit_jobs'                     => true,
                'delete_jobs'                   => true,
                'edit_applications'             => true,
                'delete_applications'           => true,
            ),
            'level_2' => array(
                'edit_published_jobs'           => true,
                'delete_published_jobs'         => true,
                'publish_jobs'                  => true,
                'edit_published_applications'   => true,
                'delete_published_applications' => true,
                'publish_applications'          => true,
            ),
            'level_3' => array(
                'edit_others_jobs'              => true,
                'read_private_jobs'             => true,
                'delete_private_jobs'           => true,
                'delete_others_jobs'            => true,
                'edit_private_jobs'             => true,
                'edit_others_applications'      => true,
                'read_private_applications'     => true,
                'delete_private_applications'   => true,
                'delete_others_applications'    => true,
                'edit_private_applications'     => true
            ),
            'level_4' => array(
                'manage_awsm_jobs'              => true
            )
        );
        return $caps;
    }

    private function manage_default_roles_caps( $action = 'add' ) {
        $caps = $this->get_caps();
        $role_caps = array(
            'administrator' => array_merge( $caps['level_1'], $caps['level_2'], $caps['level_3'], $caps['level_4'] ),
            'editor'        => array_merge( $caps['level_1'], $caps['level_2'], $caps['level_3'] ),
            'author'        => array_merge( $caps['level_1'], $caps['level_2'] ),
            'contributor'   => $caps['level_1']
        );
        foreach( $role_caps as $slug => $current_caps ) {
            $role = get_role( $slug );
            if( $role ) {
                foreach( $current_caps as $current_cap => $value ) {
                    if( $action === 'remove' ) {
                        $role->remove_cap( $current_cap );
                    } else {
                        $role->add_cap( $current_cap );
                    }
                }
            }
        }
    }

    private function add_custom_role() {
        $caps = $this->get_caps();
        $hr_caps = array_merge( $caps['level_1'], $caps['level_2'], $caps['level_3'], $caps['level_4'] );
        $hr_caps['read'] = true;
        add_role( 'hr', __( 'HR' ), $hr_caps );
    }

    private function remove_custom_role() {
        if( get_role( 'hr' ) ) {
            remove_role( 'hr' );
        }
    }

    public function unregister_awsm_job_openings_post_type() {
        global $wp_post_types;
        if( isset( $wp_post_types[ 'awsm_job_openings' ] ) ) {
            unset( $wp_post_types[ 'awsm_job_openings' ] );
            return true;
        }
        return false;
    }

    private function create_upload_directory() {
        $upload_dir = wp_upload_dir();
        $base_dir = trailingslashit( $upload_dir['basedir'] );
        $upload_dir = $base_dir . AWSM_JOBS_UPLOAD_DIR_NAME;
        $files = array(
            array(
                'name'    => 'index.html',
                'content' => ''
            ),
            array(
                'name'    => '.htaccess',
                'content' => 'Options -Indexes'
            )
        );
        if( wp_mkdir_p( $upload_dir ) ) {
            foreach( $files as $file ) {
                $current_file = trailingslashit( $upload_dir ) . $file['name'];
                if( ! file_exists( $current_file ) ) {
                    $handle = @fopen( $current_file, 'w' );
                    if( $handle ) {
                        fwrite( $handle, $file['content'] );
                        fclose( $handle );
                    }
                }
            }
        }
    }
}