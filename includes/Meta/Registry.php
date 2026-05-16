<?php
namespace HPCMS\Meta;

defined( 'ABSPATH' ) || exit;

class Registry {
    public static function init(): void {
        add_action( 'init',           [ __CLASS__, 'register_all_meta' ] );
        add_action( 'add_meta_boxes', [ __CLASS__, 'add_all_meta_boxes' ] );
        add_action( 'save_post',      [ __CLASS__, 'save_all_meta' ] );
    }

    public static function register_all_meta(): void {
        Projects::register();
        Experience::register();
        Education::register();
        Resume::register();
        Skills::register();
        Testimonials::register();
        Services::register();
    }

    public static function add_all_meta_boxes(): void {
        add_meta_box( 'hpcms_project_meta',     'Project Details',     [ Projects::class, 'render_box' ],     'hpcms_project',     'normal', 'high' );
        add_meta_box( 'hpcms_experience_meta',  'Experience Details',  [ Experience::class, 'render_box' ],   'hpcms_experience',  'normal', 'high' );
        add_meta_box( 'hpcms_education_meta',   'Education Details',   [ Education::class, 'render_box' ],    'hpcms_education',   'normal', 'high' );
        add_meta_box( 'hpcms_resume_meta',      'Resume Details',      [ Resume::class, 'render_box' ],       'hpcms_resume',      'normal', 'high' );
        add_meta_box( 'hpcms_skill_meta',       'Skill Details',       [ Skills::class, 'render_box' ],       'hpcms_skill',       'normal', 'high' );
        add_meta_box( 'hpcms_testimonial_meta', 'Testimonial Details', [ Testimonials::class, 'render_box' ], 'hpcms_testimonial', 'normal', 'high' );
        add_meta_box( 'hpcms_service_meta',     'Service Details',     [ Services::class, 'render_box' ],     'hpcms_service',     'normal', 'high' );
    }

    public static function save_all_meta( int $post_id ): void {
        if ( ! isset( $_POST['hpcms_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['hpcms_nonce'] ), 'hpcms_save_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $url_fields = [
            '_hpcms_project_url', '_hpcms_github_url', '_hpcms_company_url',
            '_hpcms_certificate_url', '_hpcms_resume_file', '_hpcms_skill_url',
            '_hpcms_client_image',
        ];
        foreach ( $url_fields as $key ) {
            if ( isset( $_POST[ $key ] ) ) {
                update_post_meta( $post_id, $key, esc_url_raw( wp_unslash( $_POST[ $key ] ) ) );
            }
        }

        $text_fields = [
            '_hpcms_client_name', '_hpcms_completion_date', '_hpcms_tech_stack',
            '_hpcms_seo_title', '_hpcms_position', '_hpcms_employment_type',
            '_hpcms_start_date', '_hpcms_end_date', '_hpcms_location',
            '_hpcms_institution', '_hpcms_degree', '_hpcms_field_of_study',
            '_hpcms_grade', '_hpcms_resume_version', '_hpcms_resume_type',
            '_hpcms_last_updated', '_hpcms_skill_level',
            '_hpcms_client_position', '_hpcms_company', '_hpcms_company_name', '_hpcms_service_num',
        ];
        foreach ( $text_fields as $key ) {
            if ( isset( $_POST[ $key ] ) ) {
                update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
            }
        }

        $html_fields = [ '_hpcms_service_icon', '_hpcms_skill_icon' ];
        foreach ( $html_fields as $key ) {
            if ( isset( $_POST[ $key ] ) ) {
                $allowed = wp_kses_allowed_html( 'post' );
                $allowed['svg'] = [
                    'xmlns'       => true,
                    'viewbox'     => true,
                    'fill'        => true,
                    'width'       => true,
                    'height'      => true,
                    'class'       => true,
                    'style'       => true,
                    'stroke'      => true,
                    'stroke-width' => true,
                    'stroke-linecap' => true,
                    'stroke-linejoin' => true,
                ];
                $allowed['path'] = [
                    'd'      => true,
                    'fill'   => true,
                    'stroke' => true,
                    'stroke-width' => true,
                    'stroke-linecap' => true,
                    'stroke-linejoin' => true,
                    'class'  => true,
                    'style'  => true,
                ];
                $allowed['circle']   = [ 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'class' => true, 'style' => true ];
                $allowed['rect']     = [ 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'class' => true, 'style' => true ];
                $allowed['line']     = [ 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true, 'stroke-width' => true, 'class' => true, 'style' => true ];
                $allowed['polyline'] = [ 'points' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'class' => true, 'style' => true ];
                $allowed['polygon']  = [ 'points' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'class' => true, 'style' => true ];
                $allowed['ellipse']  = [ 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'class' => true, 'style' => true ];
                $allowed['g']        = [ 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'class' => true, 'style' => true, 'transform' => true ];
                $allowed['defs']     = [];
                $allowed['lineargradient'] = [ 'id' => true, 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'gradientunits' => true ];
                $allowed['stop']     = [ 'offset' => true, 'stop-color' => true, 'stop-opacity' => true ];
                $allowed['mask']     = [ 'id' => true, 'maskunits' => true ];
                $allowed['use']      = [ 'href' => true, 'xlink:href' => true, 'x' => true, 'y' => true ];
                $allowed['text']     = [ 'x' => true, 'y' => true, 'fill' => true, 'font-size' => true, 'font-family' => true, 'text-anchor' => true, 'class' => true, 'style' => true ];
                $allowed['tspan']    = [ 'x' => true, 'y' => true, 'fill' => true, 'class' => true, 'style' => true ];
                
                update_post_meta( $post_id, $key, wp_kses( wp_unslash( $_POST[ $key ] ), $allowed ) );
            }
        }

        $textarea_fields = [ '_hpcms_seo_description', '_hpcms_gallery', '_hpcms_key_results' ];
        foreach ( $textarea_fields as $key ) {
            if ( isset( $_POST[ $key ] ) ) {
                update_post_meta( $post_id, $key, sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) );
            }
        }

        $int_fields = [ '_hpcms_experience_years', '_hpcms_skill_percentage', '_hpcms_rating' ];
        foreach ( $int_fields as $key ) {
            if ( isset( $_POST[ $key ] ) ) {
                update_post_meta( $post_id, $key, absint( $_POST[ $key ] ) );
            }
        }

        $bool_fields = [ '_hpcms_featured', '_hpcms_current_position' ];
        foreach ( $bool_fields as $key ) {
            update_post_meta( $post_id, $key, isset( $_POST[ $key ] ) );
        }
    }
}
