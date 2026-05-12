<?php
namespace HPCMS\Admin;

defined( 'ABSPATH' ) || exit;

class Dashboard {
    public static function render(): void {
        ?>
        <div class="wrap hpcms-admin-wrap">
            <header class="hpcms-admin-header">
                <h1><?php esc_html_e( 'Portfolio CMS Dashboard', 'headless-portfolio-cms' ); ?></h1>
                <p><?php esc_html_e( 'Manage your headless portfolio content and settings.', 'headless-portfolio-cms' ); ?></p>
            </header>

            <div class="hpcms-dashboard-grid">
                <?php self::render_card( 'Projects', 'dashicons-portfolio', 'hpcms_project', 'projects' ); ?>
                <?php self::render_card( 'Experience', 'dashicons-businessman', 'hpcms_experience', 'experience' ); ?>
                <?php self::render_card( 'Education', 'dashicons-welcome-learn-more', 'hpcms_education', 'education' ); ?>
                <?php self::render_card( 'Resume', 'dashicons-media-document', 'hpcms_resume', 'resume' ); ?>
                <?php self::render_card( 'Skills', 'dashicons-chart-bar', 'hpcms_skill', 'skills' ); ?>
                <?php self::render_card( 'Testimonials', 'dashicons-format-quote', 'hpcms_testimonial', 'testimonials' ); ?>
            </div>
        </div>
        <?php
    }

    private static function render_card( string $title, string $icon, string $post_type, string $endpoint ): void {
        $count = wp_count_posts( $post_type )->publish ?? 0;
        $add_url = admin_url( "post-new.php?post_type={$post_type}" );
        $list_url = admin_url( "edit.php?post_type={$post_type}" );
        $api_url = rest_url( HPCMS_API_NS . "/{$endpoint}" );

        ?>
        <div class="hpcms-card">
            <div class="hpcms-card-header">
                <span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
                <h2><?php echo esc_html( $title ); ?></h2>
            </div>
            <div class="hpcms-card-body">
                <p class="hpcms-stat"><span class="hpcms-number"><?php echo esc_html( (string) $count ); ?></span> Published</p>
                <div class="hpcms-card-actions">
                    <a href="<?php echo esc_url( $list_url ); ?>" class="button">View All</a>
                    <a href="<?php echo esc_url( $add_url ); ?>" class="button button-primary">Add New</a>
                </div>
            </div>
            <div class="hpcms-card-footer">
                <small>API: <a href="<?php echo esc_url( $api_url ); ?>" target="_blank">/<?php echo esc_html( $endpoint ); ?></a></small>
            </div>
        </div>
        <?php
    }
}
