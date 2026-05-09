<?php
defined( 'ABSPATH' ) || exit;

/**
 * Contact form business logic:
 * - Rate limiting (5 requests/IP/hour via transients)
 * - Input validation & sanitisation
 * - Saves to Contact Log CPT
 * - Sends notification email to admin
 * - Sends auto-reply to submitter
 */
class Adil_Contact_Handler {

    private const RATE_LIMIT      = 5;    // max submissions
    private const RATE_WINDOW_SEC = 3600; // 1 hour

    /**
     * Main entry point — called from the REST endpoint.
     * Returns an array [ 'success' => bool, 'message' => string ].
     */
    public static function handle( array $data ): array {

        // 1. Rate limit
        $rate_check = self::check_rate_limit();
        if ( ! $rate_check['allowed'] ) {
            return [
                'success' => false,
                'message' => 'Rate limit exceeded. Please try again in an hour.',
                'code'    => 429,
            ];
        }

        // 2. Validate required fields
        $errors = self::validate( $data );
        if ( ! empty( $errors ) ) {
            return [
                'success' => false,
                'message' => implode( ' ', $errors ),
                'code'    => 422,
            ];
        }

        // 3. Save to WP admin inbox
        $log_id = Adil_Contact_Log::save( $data );
        if ( is_wp_error( $log_id ) ) {
            return [
                'success' => false,
                'message' => 'Could not save your message. Please email directly.',
                'code'    => 500,
            ];
        }

        // 4. Increment rate limit counter
        self::increment_rate_limit();

        // 5. Notify admin
        self::send_admin_notification( $data );

        // 6. Auto-reply to submitter
        self::send_auto_reply( $data );

        return [
            'success' => true,
            'message' => "Thanks {$data['name']}! Your message has been received. I'll get back to you shortly.",
            'code'    => 200,
        ];
    }

    // ── Validation ────────────────────────────────────────────────────────────

    private static function validate( array $data ): array {
        $errors = [];

        if ( empty( trim( $data['name'] ?? '' ) ) ) {
            $errors[] = 'Name is required.';
        }
        if ( empty( trim( $data['email'] ?? '' ) ) || ! is_email( $data['email'] ) ) {
            $errors[] = 'A valid email address is required.';
        }
        if ( empty( trim( $data['subject'] ?? '' ) ) ) {
            $errors[] = 'Subject is required.';
        }
        if ( empty( trim( $data['message'] ?? '' ) ) || strlen( trim( $data['message'] ) ) < 10 ) {
            $errors[] = 'Please include a message (at least 10 characters).';
        }

        return $errors;
    }

    // ── Rate limiting via WP transients ───────────────────────────────────────

    private static function rate_key(): string {
        $ip = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' );
        return 'adil_contact_rate_' . md5( $ip );
    }

    private static function check_rate_limit(): array {
        $count = (int) get_transient( self::rate_key() );
        return [
            'allowed'   => $count < self::RATE_LIMIT,
            'remaining' => max( 0, self::RATE_LIMIT - $count ),
        ];
    }

    private static function increment_rate_limit(): void {
        $key   = self::rate_key();
        $count = (int) get_transient( $key );
        set_transient( $key, $count + 1, self::RATE_WINDOW_SEC );
    }

    // ── Emails ────────────────────────────────────────────────────────────────

    private static function send_admin_notification( array $data ): void {
        $to      = get_option( 'adil_contact_email', get_option( 'admin_email' ) );
        $subject = '[Portfolio] ' . sanitize_text_field( $data['subject'] );

        $name    = sanitize_text_field( $data['name'] );
        $email   = sanitize_email( $data['email'] );
        $budget  = sanitize_text_field( $data['budget'] ?? 'Not specified' );
        $message = sanitize_textarea_field( $data['message'] );
        $ip      = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? 'Unknown' );
        $time    = current_time( 'F j, Y \a\t g:i a' );

        $body = "New contact form submission from adilashrafi.com\n\n"
              . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
              . "Name:    {$name}\n"
              . "Email:   {$email}\n"
              . "Subject: {$data['subject']}\n"
              . "Budget:  {$budget}\n"
              . "Date:    {$time}\n"
              . "IP:      {$ip}\n"
              . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
              . "Message:\n{$message}\n\n"
              . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
              . "Reply directly to: {$email}\n"
              . "View in WP Admin: " . admin_url( 'edit.php?post_type=adil_contact_log' );

        wp_mail( $to, $subject, $body, [
            'Reply-To: ' . $name . ' <' . $email . '>',
            'Content-Type: text/plain; charset=UTF-8',
        ] );
    }

    private static function send_auto_reply( array $data ): void {
        $to      = sanitize_email( $data['email'] );
        $name    = sanitize_text_field( $data['name'] );
        $subject = 'Got your message! — Al Adil Ashrafi';
        $from    = get_option( 'adil_contact_email', get_option( 'admin_email' ) );

        $body = "Hi {$name},\n\n"
              . "Thanks for reaching out! I've received your message and will get back to you within 24–48 hours.\n\n"
              . "Here's a copy of what you sent:\n"
              . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
              . "Subject: " . sanitize_text_field( $data['subject'] ) . "\n"
              . sanitize_textarea_field( $data['message'] ) . "\n"
              . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
              . "In the meantime, you can:\n"
              . "· View my work:    https://adilashrafi.com\n"
              . "· Connect on LinkedIn: https://www.linkedin.com/in/al-adil-ashrafi/\n\n"
              . "Best,\n"
              . "Al Adil Ashrafi\n"
              . "The Marketing Alchemist\n"
              . "Mohammadpur, Dhaka, Bangladesh";

        wp_mail( $to, $subject, $body, [
            'From: Al Adil Ashrafi <' . $from . '>',
            'Reply-To: Al Adil Ashrafi <' . $from . '>',
            'Content-Type: text/plain; charset=UTF-8',
        ] );
    }
}
