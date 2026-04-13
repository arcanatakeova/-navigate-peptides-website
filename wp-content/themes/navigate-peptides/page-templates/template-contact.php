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
            // Use Contact Form 7 or WPForms shortcode if available
            $cf7_form = get_post_meta(get_the_ID(), '_nav_contact_form_shortcode', true);
            if ($cf7_form) {
                echo do_shortcode($cf7_form);
            } else {
            ?>
            <form class="nav-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="nav_contact_form">
                <?php wp_nonce_field('nav_contact_nonce', 'nav_nonce'); ?>

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
