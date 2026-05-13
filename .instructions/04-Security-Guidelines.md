# Security & Development Guidelines

## WordPress.org Compliance
- **Sanitization**: Always use `sanitize_text_field()`, `sanitize_email()`, `esc_url_raw()`, `absint()` for inputs.
- **Escaping**: Always use `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()` for outputs.
- **Nonces**: Use `wp_create_nonce()` and `check_admin_referer()` / `wp_verify_nonce()` for all form submissions and AJAX.
- **Permissions**: Use `current_user_can('manage_options')` (or appropriate capability) before sensitive operations.
- **No Globals**: Avoid using global variables. Use classes and dependency injection where possible.

## API Security
- Never use `permission_callback => '__return_true'` for endpoints that modify data.
- Read-only endpoints should still use appropriate checks if they expose private data.

## Coding Standards
- Follow PSR-12 for PHP coding style.
- Use strict typing (`declare(strict_types=1);` where possible).
- Use `wp_die()` for terminal errors.
- Ensure all file headers have `defined('ABSPATH') || exit;`.
