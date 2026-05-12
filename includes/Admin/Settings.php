<?php
namespace HPCMS\Admin;

defined( 'ABSPATH' ) || exit;

class Settings {
    public static function render(): void {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ( isset( $_GET['settings-updated'] ) ) {
            add_settings_error( 'hpcms_messages', 'hpcms_message', __( 'Settings Saved', 'headless-portfolio-cms' ), 'updated' );
        }
        settings_errors( 'hpcms_messages' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'profile';
        ?>
        <div class="wrap hpcms-admin-wrap">
            <header class="hpcms-admin-header">
                <h1><?php esc_html_e( 'Settings', 'headless-portfolio-cms' ); ?></h1>
            </header>

            <h2 class="nav-tab-wrapper">
                <a href="?page=hpcms-settings&tab=profile" class="nav-tab <?php echo $active_tab === 'profile' ? 'nav-tab-active' : ''; ?>">Profile Info</a>
                <a href="?page=hpcms-settings&tab=social" class="nav-tab <?php echo $active_tab === 'social' ? 'nav-tab-active' : ''; ?>">Social Links</a>
                <a href="?page=hpcms-settings&tab=seo" class="nav-tab <?php echo $active_tab === 'seo' ? 'nav-tab-active' : ''; ?>">SEO</a>
                <a href="?page=hpcms-settings&tab=api" class="nav-tab <?php echo $active_tab === 'api' ? 'nav-tab-active' : ''; ?>">API & CORS</a>
            </h2>

            <form method="post" action="options.php" class="hpcms-settings-form">
                <?php
                settings_fields( 'hpcms_settings_group' );

                if ( $active_tab === 'profile' ) {
                    self::render_profile_tab();
                } elseif ( $active_tab === 'social' ) {
                    self::render_social_tab();
                } elseif ( $active_tab === 'seo' ) {
                    self::render_seo_tab();
                } elseif ( $active_tab === 'api' ) {
                    self::render_api_tab();
                }

                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    private static function render_profile_tab(): void {
        ?>
        <table class="form-table">
            <tr><th scope="row">Full Name</th><td><input type="text" name="hpcms_full_name" value="<?php echo esc_attr( get_option( 'hpcms_full_name' ) ); ?>" class="regular-text"></td></tr>
            <tr><th scope="row">Tagline</th><td><input type="text" name="hpcms_tagline" value="<?php echo esc_attr( get_option( 'hpcms_tagline' ) ); ?>" class="regular-text"></td></tr>
            <tr><th scope="row">Bio</th><td><textarea name="hpcms_bio" class="large-text" rows="5"><?php echo esc_textarea( get_option( 'hpcms_bio' ) ); ?></textarea></td></tr>
            <tr><th scope="row">Email</th><td><input type="email" name="hpcms_email" value="<?php echo esc_attr( get_option( 'hpcms_email' ) ); ?>" class="regular-text"></td></tr>
            <tr><th scope="row">Phone</th><td><input type="text" name="hpcms_phone" value="<?php echo esc_attr( get_option( 'hpcms_phone' ) ); ?>" class="regular-text"></td></tr>
            <tr><th scope="row">Location</th><td><input type="text" name="hpcms_location" value="<?php echo esc_attr( get_option( 'hpcms_location' ) ); ?>" class="regular-text"></td></tr>
            <tr><th scope="row">Avatar URL</th><td><input type="url" name="hpcms_avatar_url" value="<?php echo esc_attr( get_option( 'hpcms_avatar_url' ) ); ?>" class="regular-text"></td></tr>
        </table>
        <?php
    }

    private static function render_social_tab(): void {
        ?>
        <table class="form-table">
            <tr><th scope="row">GitHub</th><td><input type="url" name="hpcms_github" value="<?php echo esc_attr( get_option( 'hpcms_github' ) ); ?>" class="regular-text"></td></tr>
            <tr><th scope="row">LinkedIn</th><td><input type="url" name="hpcms_linkedin" value="<?php echo esc_attr( get_option( 'hpcms_linkedin' ) ); ?>" class="regular-text"></td></tr>
            <tr><th scope="row">X / Twitter</th><td><input type="url" name="hpcms_twitter" value="<?php echo esc_attr( get_option( 'hpcms_twitter' ) ); ?>" class="regular-text"></td></tr>
            <tr><th scope="row">YouTube</th><td><input type="url" name="hpcms_youtube" value="<?php echo esc_attr( get_option( 'hpcms_youtube' ) ); ?>" class="regular-text"></td></tr>
            <tr><th scope="row">Behance</th><td><input type="url" name="hpcms_behance" value="<?php echo esc_attr( get_option( 'hpcms_behance' ) ); ?>" class="regular-text"></td></tr>
            <tr><th scope="row">Dribbble</th><td><input type="url" name="hpcms_dribbble" value="<?php echo esc_attr( get_option( 'hpcms_dribbble' ) ); ?>" class="regular-text"></td></tr>
        </table>
        <?php
    }

    private static function render_seo_tab(): void {
        ?>
        <table class="form-table">
            <tr><th scope="row">Default Meta Title</th><td><input type="text" name="hpcms_meta_title" value="<?php echo esc_attr( get_option( 'hpcms_meta_title' ) ); ?>" class="regular-text"></td></tr>
            <tr><th scope="row">Default Meta Description</th><td><textarea name="hpcms_meta_description" class="large-text" rows="3"><?php echo esc_textarea( get_option( 'hpcms_meta_description' ) ); ?></textarea></td></tr>
            <tr><th scope="row">Default OG Image URL</th><td><input type="url" name="hpcms_og_image" value="<?php echo esc_attr( get_option( 'hpcms_og_image' ) ); ?>" class="regular-text"></td></tr>
        </table>
        <?php
    }

    private static function render_api_tab(): void {
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">Enable REST API</th>
                <td>
                    <label><input type="radio" name="hpcms_enable_api" value="1" <?php checked( get_option( 'hpcms_enable_api', '1' ), '1' ); ?>> Yes</label><br>
                    <label><input type="radio" name="hpcms_enable_api" value="0" <?php checked( get_option( 'hpcms_enable_api' ), '0' ); ?>> No</label>
                </td>
            </tr>
            <tr>
                <th scope="row">Enable CORS</th>
                <td>
                    <label><input type="radio" name="hpcms_enable_cors" value="1" <?php checked( get_option( 'hpcms_enable_cors', '1' ), '1' ); ?>> Yes</label><br>
                    <label><input type="radio" name="hpcms_enable_cors" value="0" <?php checked( get_option( 'hpcms_enable_cors' ), '0' ); ?>> No</label>
                    <p class="description">Required if your frontend is hosted on a different domain.</p>
                </td>
            </tr>
            <tr>
                <th scope="row">Allowed Origins</th>
                <td>
                    <textarea name="hpcms_allowed_origins" class="large-text" rows="3"><?php echo esc_textarea( get_option( 'hpcms_allowed_origins' ) ); ?></textarea>
                    <p class="description">One URL per line. Use <code>*</code> to allow all (not recommended for production).</p>
                </td>
            </tr>
            <tr>
                <th scope="row">Frontend URL</th>
                <td>
                    <input type="url" name="hpcms_frontend_url" value="<?php echo esc_attr( get_option( 'hpcms_frontend_url' ) ); ?>" class="regular-text">
                    <p class="description">Used for ISR revalidation webhooks (e.g., Next.js).</p>
                </td>
            </tr>
            <tr>
                <th scope="row">API Cache Duration (s)</th>
                <td><input type="number" name="hpcms_cache_duration" value="<?php echo esc_attr( get_option( 'hpcms_cache_duration', 3600 ) ); ?>" class="small-text"></td>
            </tr>
            <tr>
                <th scope="row">API Secret Token</th>
                <td>
                    <input type="text" name="hpcms_api_token" value="<?php echo esc_attr( get_option( 'hpcms_api_token' ) ); ?>" class="regular-text" readonly>
                    <p class="description">Generated automatically on activation. Use this to authenticate webhooks.</p>
                </td>
            </tr>
        </table>
        <?php
    }
}
