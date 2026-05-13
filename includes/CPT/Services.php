<?php
namespace HPCMS\CPT;

defined( 'ABSPATH' ) || exit;

class Services {
    public static function register(): void {
        register_post_type( 'hpcms_service', [
            'labels'       => [
                'name'               => __( 'Services', 'headless-portfolio-cms' ),
                'singular_name'      => __( 'Service', 'headless-portfolio-cms' ),
                'add_new'            => __( 'Add Service', 'headless-portfolio-cms' ),
                'add_new_item'       => __( 'Add New Service', 'headless-portfolio-cms' ),
                'edit_item'          => __( 'Edit Service', 'headless-portfolio-cms' ),
                'new_item'           => __( 'New Service', 'headless-portfolio-cms' ),
                'view_item'          => __( 'View Service', 'headless-portfolio-cms' ),
                'search_items'       => __( 'Search Services', 'headless-portfolio-cms' ),
                'not_found'          => __( 'No Services found', 'headless-portfolio-cms' ),
                'not_found_in_trash' => __( 'No Services in Trash', 'headless-portfolio-cms' ),
                'all_items'          => __( 'All Services', 'headless-portfolio-cms' ),
                'menu_name'          => __( 'Services', 'headless-portfolio-cms' ),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'headless-portfolio-cms',
            'show_in_rest' => true,
            'rest_base'    => 'hpcms-services',
            'supports'     => [ 'title', 'editor', 'page-attributes', 'thumbnail' ],
            'menu_icon'    => 'dashicons-rest-api',
            'rewrite'      => false,
        ] );
    }
}
