<?php
    if( ! defined( 'ABSPATH' ) ) {
        exit;
    }
    $awsm_filters = get_option( 'awsm_jobs_filter' );
    $taxonomy_objects = get_object_taxonomies( 'awsm_job_openings', 'objects' );
    $spec_label_placeholder = 'placeholder="' . esc_html__( 'Enter a specification', 'wp-job-openings' ) . '"';
    $spec_tags_placeholder = 'data-placeholder="' . esc_html__( 'Enter options', 'wp-job-openings' ) . '"';
    $icon_placeholder = 'data-placeholder="' . esc_html__( 'Select icon', 'wp-job-openings' ) . '"';
?>

<div id="settings-awsm-settings-specifications" class="awsm-admin-settings" style="display: none;">
    <form method="POST" action="options.php#settings-awsm-settings-specifications" id="job_specifications_form">
        <?php
            settings_fields( 'awsm-jobs-specifications-settings' );
        ?>
        <div class="awsm-form-section-main">
            <div class="awsm-form-section">
                <table id="awsm-repeatable-specifications" width="100%" class="awsm-specs">
                    <thead>
                         <tr>
                           <th scope="row" colspan="2" class="awsm-form-head-title">
                                <h2><?php _e( 'Manage Job Specifications', 'wp-job-openings' ); ?></h2>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="awsm_job_specifications_settings_body">
                        <?php do_action( 'before_awsm_specifications_settings' ); ?>
                        <tr>
                            <td><?php _e( 'Specifications', 'wp-job-openings' ); ?></td>
                            <td><?php _e( 'Icon (Optional)', 'wp-job-openings' ); ?></td>
                            <td><?php _e( 'Options', 'wp-job-openings' ); ?></td></td>
                            <td></td>
                        </tr>
                        <?php if( empty( $taxonomy_objects ) ) : ?>
                            <tr class="awsm_job_specifications_settings_row">
                                <td>
                                    <input type="text" class="widefat awsm_jobs_filter_title" name="awsm_jobs_filter[0][filter]" value="" required <?php echo $spec_label_placeholder; ?> />
                                </td>
                                <td>
                                    <select class="awsm-font-icon-selector awsm-icon-select-control" name="awsm_jobs_filter[0][icon]" style="width: 100%;" <?php echo $icon_placeholder; ?>></select>
                                </td>
                                <td>
                                    <select class="awsm_jobs_filter_tags" name="awsm_jobs_filter[0][tags][]" multiple="multiple" style="width: 100%;" <?php echo $spec_tags_placeholder; ?>></select>
                                </td>
                                <td><a class="button awsm-text-red awsm-filters-remove-row" href="#" ><?php _e('Delete','wp-job-openings');?></a>
                                </td>
                            </tr>
                        <?php
                            else :
                                $index = 0;
                                foreach( $taxonomy_objects as $taxonomy => $taxonomy_options ) :
                        ?>
                                    <tr class="awsm_job_specifications_settings_row" data-index="<?php echo $index; ?>">
                                        <td>
                                            <input type="hidden" name="awsm_jobs_filter[<?php echo $index; ?>][taxonomy]" value="<?php echo esc_attr( $taxonomy ); ?>" />
                                            <input type="text" class="widefat awsm_jobs_filter_title" name="awsm_jobs_filter[<?php echo $index; ?>][filter]" value="<?php echo esc_attr( $taxonomy_options->label ); ?>" required <?php echo $spec_label_placeholder; ?> />
                                        </td>
                                        <td>
                                           <select class="awsm-font-icon-selector awsm-icon-select-control" name="awsm_jobs_filter[<?php echo $index; ?>][icon]" style="width: 100%;" <?php echo $icon_placeholder; ?>>
                                                <?php
                                                    if( ! empty( $awsm_filters ) ) {
                                                        foreach( $awsm_filters as $filter ) {
                                                            if( $taxonomy == $filter['taxonomy'] ) {
                                                                if( ! empty( $filter['icon'] ) ) {
                                                                    printf( '<option value="%1$s" selected><i class="awsm-job-icon-%1$s"></i> %1$s</option>', $filter['icon'] );
                                                                }
                                                            }
                                                        }
                                                    }
                                                ?>
                                           </select>
                                        </td>
                                        <td>
                                            <select class="awsm_jobs_filter_tags" name="awsm_jobs_filter[<?php echo $index; ?>][tags][]" multiple="multiple" style="width: 100%;" <?php echo $spec_tags_placeholder; ?>>
                                                <?php
                                                    $terms = get_terms( $taxonomy, 'orderby=id&hide_empty=0' );
                                                    if( ! empty( $terms ) ) :
                                                        foreach( $terms as $term ) :
                                                ?>
                                                            <option value="<?php echo esc_attr( $term->name ); ?>" selected><?php echo esc_html( $term->name ); ?></option>
                                                <?php
                                                        endforeach;
                                                    endif;
                                                ?>
                                            </select>
                                        </td>
                                        <td><a class="button awsm-filters-remove-row" href="#" data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>"><?php _e('Delete','wp-job-openings');?></a>
                                        </td>
                                    </tr>
                        <?php
                                    $index++;
                                endforeach;
                            endif;
                        ?>
                        <tr class="awsm-filter-empty-row screen-reader-text" data-next="<?php echo ( ! empty( $taxonomy_objects ) ) ? count( $taxonomy_objects ) : 1; ?>">
                            <td>
                                <input type="text" class="widefat awsm_jobs_filter_title" <?php echo $spec_label_placeholder; ?> />
                            </td>
                            <td>
                                <select class="awsm-font-icon-selector" style="width: 100%;" <?php echo $icon_placeholder; ?>></select>
                            </td>
                            <td>
                                <select class="awsm-empty-spec-select" multiple="multiple" style="width: 100%;" <?php echo $spec_tags_placeholder; ?>></select>
                            </td>
                            <td><a class="button awsm-text-red awsm-filters-remove-row" href="#" ><?php _e('Delete','wp-job-openings');?></a>
                            </td>
                        </tr>
                        <?php do_action( 'after_awsm_specifications_settings' ); ?>
                    </tbody>
                </table>
                <p><a class="button awsm-add-filter-row" href="#"><?php esc_html_e( 'Add new spec','wp-job-openings' );?></a></p>
            </div><!-- .awsm-form-section -->
        </div><!-- .awsm-form-section-main -->
        <div class="awsm-form-footer">
            <?php echo apply_filters( 'awsm_job_settings_submit_btn', get_submit_button(), 'specifications' ); ?>
        </div><!-- .awsm-form-footer -->
    </form>
</div><!-- .awsm-admin-settings -->