<?php
namespace HPCMS\CPT;

defined( 'ABSPATH' ) || exit;

class Resume {
    public static function register(): void {
        register_post_type( 'hpcms_resume', [
            'labels'       => [
                'name'               => __( 'Resumes', 'headless-portfolio-cms' ),
                'singular_name'      => __( 'Resume', 'headless-portfolio-cms' ),
                'add_new'            => __( 'Add Resume', 'headless-portfolio-cms' ),
                'add_new_item'       => __( 'Add New Resume', 'headless-portfolio-cms' ),
                'edit_item'          => __( 'Edit Resume', 'headless-portfolio-cms' ),
                'new_item'           => __( 'New Resume', 'headless-portfolio-cms' ),
                'view_item'          => __( 'View Resume', 'headless-portfolio-cms' ),
                'search_items'       => __( 'Search Resumes', 'headless-portfolio-cms' ),
                'not_found'          => __( 'No Resumes found', 'headless-portfolio-cms' ),
                'not_found_in_trash' => __( 'No Resumes in Trash', 'headless-portfolio-cms' ),
                'all_items'          => __( 'All Resumes', 'headless-portfolio-cms' ),
                'menu_name'          => __( 'Resumes', 'headless-portfolio-cms' ),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'headless-portfolio-cms',
            'show_in_rest' => true,
            'rest_base'    => 'hpcms-resume',
            'supports'     => [ 'title', 'page-attributes' ],
            'menu_icon'    => 'dashicons-media-document',
            'rewrite'      => false,
        ] );
    }
}
