<?php
/**
 * Template Name: Quality — COA Lookup
 *
 * @package NavigatePeptides
 */

get_header();
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <h1 class="nav-page-hero__title">Certificates of Analysis</h1>
        <p class="nav-page-hero__subtitle">Every compound ships with a batch-specific certificate of analysis. Search by product name or batch number.</p>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container nav-section--center">
        <div class="nav-coa-lookup">
            <h3 class="nav-coa-lookup__title">COA Lookup</h3>
            <form class="nav-coa-lookup__form" method="get">
                <label for="nav-coa-search" class="nav-form-label">Batch Number or Product Name</label>
                <input
                    type="text"
                    id="nav-coa-search"
                    name="coa_search"
                    class="nav-form-input"
                    placeholder="e.g., BPC-157 or NP-2024-0142"
                    value="<?php echo esc_attr($_GET['coa_search'] ?? ''); ?>"
                >
                <button type="submit" class="nav-btn nav-btn--primary nav-btn--full">Search Certificates</button>
            </form>
            <p class="nav-coa-lookup__note">
                COA database is being populated. All certificates will be searchable here once published.
            </p>
        </div>
    </div>
</section>

<?php get_footer(); ?>
