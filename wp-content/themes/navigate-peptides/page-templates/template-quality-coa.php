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

            <?php
            $search_query = isset($_GET['coa_search']) ? sanitize_text_field(wp_unslash($_GET['coa_search'])) : '';

            if ($search_query && class_exists('WooCommerce')) :
                // Search by batch number (exact meta match)
                $batch_args = [
                    'post_type'      => 'product',
                    'posts_per_page' => 20,
                    'post_status'    => 'publish',
                    'meta_query'     => [
                        'relation' => 'OR',
                        [
                            'key'     => '_nav_batch_number',
                            'value'   => $search_query,
                            'compare' => 'LIKE',
                        ],
                    ],
                    's' => $search_query,
                ];

                $results = new WP_Query($batch_args);

                if ($results->have_posts()) :
            ?>
                <div class="nav-coa-results">
                    <h4 class="nav-coa-results__title">Results for "<?php echo esc_html($search_query); ?>"</h4>
                    <div class="nav-coa-results__grid">
                        <?php while ($results->have_posts()) : $results->the_post();
                            $product_obj = wc_get_product(get_the_ID());
                            if (! $product_obj) continue;

                            $batch   = get_post_meta(get_the_ID(), '_nav_batch_number', true);
                            $purity  = get_post_meta(get_the_ID(), '_nav_purity', true);
                            $lab     = get_post_meta(get_the_ID(), '_nav_testing_lab', true);
                            $coa_url = get_post_meta(get_the_ID(), '_nav_coa_pdf', true);
                        ?>
                        <div class="nav-coa-result-card">
                            <h5 class="nav-coa-result-card__name"><?php echo esc_html($product_obj->get_name()); ?></h5>
                            <?php if ($batch) : ?>
                                <p class="nav-coa-result-card__meta"><span>Batch:</span> <?php echo esc_html($batch); ?></p>
                            <?php endif; ?>
                            <?php if ($purity) : ?>
                                <p class="nav-coa-result-card__meta"><span>Purity:</span> <?php echo esc_html($purity); ?></p>
                            <?php endif; ?>
                            <?php if ($lab) : ?>
                                <p class="nav-coa-result-card__meta"><span>Lab:</span> <?php echo esc_html($lab); ?></p>
                            <?php endif; ?>
                            <?php if ($coa_url) : ?>
                                <a href="<?php echo esc_url($coa_url); ?>" class="nav-btn nav-btn--outline nav-btn--sm" target="_blank" rel="noopener">
                                    View Certificate
                                </a>
                            <?php else : ?>
                                <p class="nav-text-muted">Certificate pending publication</p>
                            <?php endif; ?>
                        </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>
            <?php else : ?>
                <div class="nav-coa-no-results">
                    <p>No certificates found for "<?php echo esc_html($search_query); ?>". Please verify your batch number or contact our team for assistance.</p>
                </div>
            <?php endif;

            elseif ($search_query) : ?>
                <p class="nav-coa-lookup__note">WooCommerce is required for COA lookup functionality.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
