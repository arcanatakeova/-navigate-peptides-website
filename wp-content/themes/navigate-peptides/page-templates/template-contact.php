<?php
/**
 * Template Name: Contact / Request Access
 *
 * @package NavigatePeptides
 */

get_header();
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <h1 class="nav-page-hero__title">Request Access</h1>
        <p class="nav-page-hero__subtitle">Contact our team for research inquiries, wholesale pricing, or to request an account.</p>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container nav-section--center">
        <div class="nav-contact-form">
            <?php
            // Success / error messages
            if (isset($_GET['sent']) && $_GET['sent'] === '1') : ?>
                <div class="nav-form-success">
                    <p>Your inquiry has been submitted. Our team will respond within 1-2 business days.</p>
                </div>
            <?php elseif (isset($_GET['error'])) :
                $error_messages = [
                    'required' => 'Please fill in all required fields.',
                    'email'    => 'Please enter a valid email address.',
                    'rate'     => 'You\'ve submitted recently from this network — please wait 60 seconds and try again.',
                    'send'     => 'Your message could not be sent automatically. Please email us directly at ' . antispambot(get_option('admin_email')) . '.',
                ];
                $error_key = sanitize_text_field(wp_unslash($_GET['error']));
                $error_msg = $error_messages[$error_key] ?? 'An error occurred. Please try again.';
            ?>
                <div class="nav-form-error">
                    <p><?php echo esc_html($error_msg); ?></p>
                </div>
            <?php endif; ?>

            <?php
            // Use Contact Form 7 or WPForms shortcode if configured.
            // Only accept the two shortcodes we support — never execute arbitrary
            // admin-supplied shortcodes, to prevent shortcode-as-XSS vectors.
            $cf7_form = trim((string) get_post_meta(get_the_ID(), '_nav_contact_form_shortcode', true));
            $allowed_shortcode = (bool) preg_match('/^\[(contact-form-7|wpforms)\b[^\]]*\]$/i', $cf7_form);
            if ($cf7_form && $allowed_shortcode) {
                echo do_shortcode($cf7_form);
            } else {
            ?>
            <form class="nav-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="nav_contact_form">
                <?php wp_nonce_field('nav_contact_nonce', 'nav_nonce'); ?>

                <!-- Honeypot anti-spam field — name chosen to avoid password-manager autofill -->
                <div style="position:absolute;left:-9999px;" aria-hidden="true">
                    <label for="nav-fax-url-hp">Leave this empty</label>
                    <input type="text" id="nav-fax-url-hp" name="nav_fax_url" tabindex="-1" autocomplete="off">
                </div>

                <div class="nav-form__row nav-form__row--2">
                    <div class="nav-form__field">
                        <label for="nav-first-name" class="nav-form-label">First Name</label>
                        <input type="text" id="nav-first-name" name="first_name" class="nav-form-input" required>
                    </div>
                    <div class="nav-form__field">
                        <label for="nav-last-name" class="nav-form-label">Last Name</label>
                        <input type="text" id="nav-last-name" name="last_name" class="nav-form-input" required>
                    </div>
                </div>

                <div class="nav-form__field">
                    <label for="nav-email" class="nav-form-label">Email Address</label>
                    <input type="email" id="nav-email" name="email" class="nav-form-input" required>
                </div>

                <div class="nav-form__field">
                    <label for="nav-org" class="nav-form-label">Organization / Institution</label>
                    <input type="text" id="nav-org" name="organization" class="nav-form-input">
                </div>

                <div class="nav-form__field">
                    <label for="nav-inquiry" class="nav-form-label">Inquiry Type</label>
                    <select id="nav-inquiry" name="inquiry_type" class="nav-form-select">
                        <option value="general">General Inquiry</option>
                        <option value="wholesale">Wholesale / Bulk Pricing</option>
                        <option value="account">Account Access Request</option>
                        <option value="technical">Technical / Product Question</option>
                        <option value="coa">COA Request</option>
                    </select>
                </div>

                <div class="nav-form__field">
                    <label for="nav-message" class="nav-form-label">Message</label>
                    <textarea id="nav-message" name="message" class="nav-form-textarea" rows="5" required></textarea>
                </div>

                <button type="submit" class="nav-btn nav-btn--primary nav-btn--full">Submit Inquiry</button>
            </form>
            <?php } ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
