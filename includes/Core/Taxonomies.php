<?php
namespace HPCMS\Core;

defined( 'ABSPATH' ) || exit;

class Taxonomies {
    public static function init(): void {
        add_action( 'init', [ __CLASS__, 'register_all' ] );
    }

    public static function register_all(): void {
        self::register_project_category();
        self::register_technology();
        self::register_industry();
        self::register_skill_category();
    }

    private static function register_project_category(): void {
        register_taxonomy( 'hpcms_project_category', [ 'hpcms_project' ], [
            'labels'       => [
                'name'              => __( 'Project Categories', 'headless-portfolio-cms' ),
                'singular_name'     => __( 'Project Category', 'headless-portfolio-cms' ),
                'search_items'      => __( 'Search Project Categories', 'headless-portfolio-cms' ),
                'all_items'         => __( 'All Project Categories', 'headless-portfolio-cms' ),
                'parent_item'       => __( 'Parent Project Category', 'headless-portfolio-cms' ),
                'parent_item_colon' => __( 'Parent Project Category:', 'headless-portfolio-cms' ),
                'edit_item'         => __( 'Edit Project Category', 'headless-portfolio-cms' ),
                'update_item'       => __( 'Update Project Category', 'headless-portfolio-cms' ),
                'add_new_item'      => __( 'Add New Project Category', 'headless-portfolio-cms' ),
                'new_item_name'     => __( 'New Project Category Name', 'headless-portfolio-cms' ),
                'menu_name'         => __( 'Project Categories', 'headless-portfolio-cms' ),
            ],
            'hierarchical' => true,
            'show_in_rest' => true,
            'rest_base'    => 'hpcms-project-categories',
        ] );
    }

    private static function register_technology(): void {
        register_taxonomy( 'hpcms_technology', [ 'hpcms_project' ], [
            'labels'       => [
                'name'              => __( 'Technologies', 'headless-portfolio-cms' ),
                'singular_name'     => __( 'Technology', 'headless-portfolio-cms' ),
                'search_items'      => __( 'Search Technologies', 'headless-portfolio-cms' ),
                'all_items'         => __( 'All Technologies', 'headless-portfolio-cms' ),
                'parent_item'       => __( 'Parent Technology', 'headless-portfolio-cms' ),
                'parent_item_colon' => __( 'Parent Technology:', 'headless-portfolio-cms' ),
                'edit_item'         => __( 'Edit Technology', 'headless-portfolio-cms' ),
                'update_item'       => __( 'Update Technology', 'headless-portfolio-cms' ),
                'add_new_item'      => __( 'Add New Technology', 'headless-portfolio-cms' ),
                'new_item_name'     => __( 'New Technology Name', 'headless-portfolio-cms' ),
                'menu_name'         => __( 'Technologies', 'headless-portfolio-cms' ),
            ],
            'hierarchical' => false,
            'show_in_rest' => true,
            'rest_base'    => 'hpcms-technologies',
        ] );
    }

    private static function register_industry(): void {
        register_taxonomy( 'hpcms_industry', [ 'hpcms_project' ], [
            'labels'       => [
                'name'              => __( 'Industries', 'headless-portfolio-cms' ),
                'singular_name'     => __( 'Industry', 'headless-portfolio-cms' ),
                'search_items'      => __( 'Search Industries', 'headless-portfolio-cms' ),
                'all_items'         => __( 'All Industries', 'headless-portfolio-cms' ),
                'parent_item'       => __( 'Parent Industry', 'headless-portfolio-cms' ),
                'parent_item_colon' => __( 'Parent Industry:', 'headless-portfolio-cms' ),
                'edit_item'         => __( 'Edit Industry', 'headless-portfolio-cms' ),
                'update_item'       => __( 'Update Industry', 'headless-portfolio-cms' ),
                'add_new_item'      => __( 'Add New Industry', 'headless-portfolio-cms' ),
                'new_item_name'     => __( 'New Industry Name', 'headless-portfolio-cms' ),
                'menu_name'         => __( 'Industries', 'headless-portfolio-cms' ),
            ],
            'hierarchical' => false,
            'show_in_rest' => true,
            'rest_base'    => 'hpcms-industries',
        ] );
    }

    private static function register_skill_category(): void {
        register_taxonomy( 'hpcms_skill_category', [ 'hpcms_skill' ], [
            'labels'       => [
                'name'              => __( 'Skill Categories', 'headless-portfolio-cms' ),
                'singular_name'     => __( 'Skill Category', 'headless-portfolio-cms' ),
                'search_items'      => __( 'Search Skill Categories', 'headless-portfolio-cms' ),
                'all_items'         => __( 'All Skill Categories', 'headless-portfolio-cms' ),
                'parent_item'       => __( 'Parent Skill Category', 'headless-portfolio-cms' ),
                'parent_item_colon' => __( 'Parent Skill Category:', 'headless-portfolio-cms' ),
                'edit_item'         => __( 'Edit Skill Category', 'headless-portfolio-cms' ),
                'update_item'       => __( 'Update Skill Category', 'headless-portfolio-cms' ),
                'add_new_item'      => __( 'Add New Skill Category', 'headless-portfolio-cms' ),
                'new_item_name'     => __( 'New Skill Category Name', 'headless-portfolio-cms' ),
                'menu_name'         => __( 'Skill Categories', 'headless-portfolio-cms' ),
            ],
            'hierarchical' => true,
            'show_in_rest' => true,
            'rest_base'    => 'hpcms-skill-categories',
        ] );
    }
}
