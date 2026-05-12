<?php
namespace HPCMS\CPT;

defined( 'ABSPATH' ) || exit;

class Education {
    public static function register(): void {
        register_post_type( 'hpcms_education', [
            'labels'       => [
                'name'               => __( 'Education', 'headless-portfolio-cms' ),
                'singular_name'      => __( 'Education', 'headless-portfolio-cms' ),
                'add_new'            => __( 'Add Education', 'headless-portfolio-cms' ),
                'add_new_item'       => __( 'Add New Education', 'headless-portfolio-cms' ),
                'edit_item'          => __( 'Edit Education', 'headless-portfolio-cms' ),
                'new_item'           => __( 'New Education', 'headless-portfolio-cms' ),
                'view_item'          => __( 'View Education', 'headless-portfolio-cms' ),
                'search_items'       => __( 'Search Education', 'headless-portfolio-cms' ),
                'not_found'          => __( 'No Education found', 'headless-portfolio-cms' ),
                'not_found_in_trash' => __( 'No Education in Trash', 'headless-portfolio-cms' ),
                'all_items'          => __( 'All Education', 'headless-portfolio-cms' ),
                'menu_name'          => __( 'Education', 'headless-portfolio-cms' ),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'headless-portfolio-cms',
            'show_in_rest' => true,
            'rest_base'    => 'hpcms-education',
            'supports'     => [ 'title', 'editor', 'page-attributes' ],
            'menu_icon'    => 'dashicons-welcome-learn-more',
            'rewrite'      => false,
        ] );
    }
}
