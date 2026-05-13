<?php
namespace HPCMS\CPT;

defined( 'ABSPATH' ) || exit;

class Clients {
    public static function register(): void {
        register_post_type( 'hpcms_client', [
            'labels'       => [
                'name'               => __( 'Clients', 'headless-portfolio-cms' ),
                'singular_name'      => __( 'Client', 'headless-portfolio-cms' ),
                'add_new'            => __( 'Add Client', 'headless-portfolio-cms' ),
                'add_new_item'       => __( 'Add New Client', 'headless-portfolio-cms' ),
                'edit_item'          => __( 'Edit Client', 'headless-portfolio-cms' ),
                'new_item'           => __( 'New Client', 'headless-portfolio-cms' ),
                'view_item'          => __( 'View Client', 'headless-portfolio-cms' ),
                'search_items'       => __( 'Search Clients', 'headless-portfolio-cms' ),
                'not_found'          => __( 'No Clients found', 'headless-portfolio-cms' ),
                'not_found_in_trash' => __( 'No Clients in Trash', 'headless-portfolio-cms' ),
                'all_items'          => __( 'All Clients', 'headless-portfolio-cms' ),
                'menu_name'          => __( 'Clients', 'headless-portfolio-cms' ),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'headless-portfolio-cms',
            'show_in_rest' => true,
            'rest_base'    => 'hpcms-clients',
            'supports'     => [ 'title', 'thumbnail', 'page-attributes' ],
            'menu_icon'    => 'dashicons-groups',
            'rewrite'      => false,
        ] );
    }
}
