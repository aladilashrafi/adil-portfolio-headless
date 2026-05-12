<?php
namespace HPCMS\CPT;

defined( 'ABSPATH' ) || exit;

class Skills {
    public static function register(): void {
        register_post_type( 'hpcms_skill', [
            'labels'       => [
                'name'               => __( 'Skills', 'headless-portfolio-cms' ),
                'singular_name'      => __( 'Skill', 'headless-portfolio-cms' ),
                'add_new'            => __( 'Add Skill', 'headless-portfolio-cms' ),
                'add_new_item'       => __( 'Add New Skill', 'headless-portfolio-cms' ),
                'edit_item'          => __( 'Edit Skill', 'headless-portfolio-cms' ),
                'new_item'           => __( 'New Skill', 'headless-portfolio-cms' ),
                'view_item'          => __( 'View Skill', 'headless-portfolio-cms' ),
                'search_items'       => __( 'Search Skills', 'headless-portfolio-cms' ),
                'not_found'          => __( 'No Skills found', 'headless-portfolio-cms' ),
                'not_found_in_trash' => __( 'No Skills in Trash', 'headless-portfolio-cms' ),
                'all_items'          => __( 'All Skills', 'headless-portfolio-cms' ),
                'menu_name'          => __( 'Skills', 'headless-portfolio-cms' ),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'headless-portfolio-cms',
            'show_in_rest' => true,
            'rest_base'    => 'hpcms-skills',
            'supports'     => [ 'title', 'page-attributes' ],
            'menu_icon'    => 'dashicons-chart-bar',
            'taxonomies'   => [ 'hpcms_skill_category' ],
            'rewrite'      => false,
        ] );
    }
}
