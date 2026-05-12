<?php
namespace HPCMS\CPT;

defined( 'ABSPATH' ) || exit;

class Experience {
    public static function register(): void {
        register_post_type( 'hpcms_experience', [
            'labels'       => [
                'name'               => __( 'Experience', 'headless-portfolio-cms' ),
                'singular_name'      => __( 'Experience', 'headless-portfolio-cms' ),
                'add_new'            => __( 'Add Experience', 'headless-portfolio-cms' ),
                'add_new_item'       => __( 'Add New Experience', 'headless-portfolio-cms' ),
                'edit_item'          => __( 'Edit Experience', 'headless-portfolio-cms' ),
                'new_item'           => __( 'New Experience', 'headless-portfolio-cms' ),
                'view_item'          => __( 'View Experience', 'headless-portfolio-cms' ),
                'search_items'       => __( 'Search Experience', 'headless-portfolio-cms' ),
                'not_found'          => __( 'No Experience found', 'headless-portfolio-cms' ),
                'not_found_in_trash' => __( 'No Experience in Trash', 'headless-portfolio-cms' ),
                'all_items'          => __( 'All Experience', 'headless-portfolio-cms' ),
                'menu_name'          => __( 'Experience', 'headless-portfolio-cms' ),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'headless-portfolio-cms',
            'show_in_rest' => true,
            'rest_base'    => 'hpcms-experience',
            'supports'     => [ 'title', 'editor', 'page-attributes' ],
            'menu_icon'    => 'dashicons-businessman',
            'rewrite'      => false,
        ] );
    }
}
