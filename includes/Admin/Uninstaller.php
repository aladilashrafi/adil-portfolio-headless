<?php
namespace HPCMS\Admin;

defined( 'ABSPATH' ) || exit;

class Uninstaller {
    public static function init(): void {
        add_action( 'wp_ajax_hpcms_save_deactivation_choice', [ __CLASS__, 'save_choice' ] );
        add_action( 'admin_footer', [ __CLASS__, 'render_modal' ] );
    }

    public static function save_choice(): void {
        check_ajax_referer( 'hpcms_deactivation_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }

        $delete_data = isset( $_POST['delete_data'] ) && '1' === $_POST['delete_data'] ? '1' : '0';
        update_option( 'hpcms_delete_data_on_uninstall', $delete_data );

        wp_send_json_success();
    }

    public static function render_modal(): void {
        $screen = get_current_screen();
        if ( ! $screen || 'plugins' !== $screen->id ) {
            return;
        }
        ?>
        <div id="hpcms-deactivation-modal" class="hpcms-modal-overlay" style="display:none;">
            <div class="hpcms-modal-content">
                <div class="hpcms-modal-header">
                    <h3><?php esc_html_e( 'Wait! We have a question.', 'headless-portfolio-cms' ); ?></h3>
                </div>
                <div class="hpcms-modal-body">
                    <p><?php esc_html_e( 'You are deactivating Headless Portfolio CMS. If you plan to uninstall and delete the plugin, would you like to keep your portfolio data or delete everything?', 'headless-portfolio-cms' ); ?></p>
                    <div class="hpcms-choice-box">
                        <label class="hpcms-choice-item">
                            <input type="radio" name="hpcms_uninstall_choice" value="0" checked>
                            <span class="hpcms-choice-text">
                                <strong><?php esc_html_e( 'Keep Data', 'headless-portfolio-cms' ); ?></strong>
                                <span><?php esc_html_e( 'Recommended. Keep all projects, settings, and media.', 'headless-portfolio-cms' ); ?></span>
                            </span>
                        </label>
                        <label class="hpcms-choice-item">
                            <input type="radio" name="hpcms_uninstall_choice" value="1">
                            <span class="hpcms-choice-text">
                                <strong><?php esc_html_e( 'Delete Everything', 'headless-portfolio-cms' ); ?></strong>
                                <span class="hpcms-warning"><?php esc_html_e( 'Permanent. Removes all portfolio content and settings.', 'headless-portfolio-cms' ); ?></span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="hpcms-modal-footer">
                    <button type="button" class="button hpcms-btn-cancel"><?php esc_html_e( 'Cancel', 'headless-portfolio-cms' ); ?></button>
                    <button type="button" class="button button-primary hpcms-btn-confirm"><?php esc_html_e( 'Confirm & Deactivate', 'headless-portfolio-cms' ); ?></button>
                </div>
            </div>
        </div>

        <style>
            .hpcms-modal-overlay {
                position: fixed;
                top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.7);
                z-index: 999999;
                display: flex;
                align-items: center;
                justify-content: center;
                backdrop-filter: blur(4px);
            }
            .hpcms-modal-content {
                background: #fff;
                width: 100%;
                max-width: 500px;
                border-radius: 12px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                overflow: hidden;
                animation: hpcms-fade-up 0.3s ease-out;
            }
            @keyframes hpcms-fade-up {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .hpcms-modal-header {
                padding: 24px;
                background: #f8f9fa;
                border-bottom: 1px solid #eee;
            }
            .hpcms-modal-header h3 { margin: 0; color: #1d2327; font-size: 1.25rem; }
            .hpcms-modal-body { padding: 24px; }
            .hpcms-modal-body p { margin-bottom: 20px; color: #50575e; line-height: 1.5; }
            .hpcms-choice-box { display: flex; flex-direction: column; gap: 12px; }
            .hpcms-choice-item {
                display: flex;
                gap: 12px;
                padding: 16px;
                border: 2px solid #eee;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.2s;
            }
            .hpcms-choice-item:hover { border-color: #2271b1; background: #f0f6fb; }
            .hpcms-choice-item input[type="radio"] { margin-top: 4px; }
            .hpcms-choice-text { display: flex; flex-direction: column; }
            .hpcms-choice-text strong { color: #1d2327; margin-bottom: 4px; }
            .hpcms-choice-text span { font-size: 0.85rem; color: #646970; }
            .hpcms-warning { color: #d63638 !important; }
            .hpcms-modal-footer {
                padding: 16px 24px;
                background: #f8f9fa;
                border-top: 1px solid #eee;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }
            .hpcms-btn-confirm { min-height: 40px; padding: 0 24px !important; }
        </style>
        <?php
    }
}
