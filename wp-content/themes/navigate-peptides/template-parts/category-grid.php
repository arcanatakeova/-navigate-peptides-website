<?php
/**
 * Template Part: Category Grid
 *
 * @package NavigatePeptides
 */

// Literal __() calls so makepot can extract the strings; the dynamic lookup
// below still works because each array value is the extracted literal.
// 8 categories: 4x2 grid on desktop, 2x4 on mobile. The 8th (Longevity)
// was added alongside Metabolic to both cover Stephie's v1 mockup naming
// and avoid the 7-card orphan row at the end of 3- or 4-col layouts.
$categories = [
    ['slug' => 'metabolic-research',     'color' => '#2F4666', 'name' => __('Metabolic Research', 'navigate-peptides'),     'desc' => __('Peptides studied for metabolic pathway modulation and energy metabolism research.', 'navigate-peptides')],
    ['slug' => 'tissue-repair-research', 'color' => '#9C843E', 'name' => __('Tissue Repair Research', 'navigate-peptides'), 'desc' => __('Compounds investigated for tissue regeneration and structural repair mechanisms.', 'navigate-peptides')],
    ['slug' => 'cognitive-research',     'color' => '#5E507F', 'name' => __('Cognitive Research', 'navigate-peptides'),     'desc' => __('Peptides explored for neuroprotective pathways and cognitive function research.', 'navigate-peptides')],
    ['slug' => 'inflammation-research',  'color' => '#4A141C', 'name' => __('Inflammation Research', 'navigate-peptides'),  'desc' => __('Compounds studied for inflammatory response modulation and immune signaling.', 'navigate-peptides')],
    ['slug' => 'cellular-research',      'color' => '#8E5660', 'name' => __('Cellular Research', 'navigate-peptides'),      'desc' => __('Peptides investigated for cellular signaling pathways and proliferation mechanisms.', 'navigate-peptides')],
    ['slug' => 'dermal-research',        'color' => '#4A6B5F', 'name' => __('Dermal Research', 'navigate-peptides'),        'desc' => __('Compounds explored for dermal tissue modeling and epidermal pathway analysis.', 'navigate-peptides')],
    ['slug' => 'longevity-research',     'color' => '#2E5C6A', 'name' => __('Longevity Research', 'navigate-peptides'),     'desc' => __('Peptides investigated for cellular aging, senescence, and lifespan extension pathways.', 'navigate-peptides')],
    ['slug' => 'research-blends',        'color' => '#474C50', 'name' => __('Research Blends', 'navigate-peptides'),        'desc' => __('Multi-peptide formulations designed for synergistic pathway research applications.', 'navigate-peptides')],
];
?>

<div class="nav-category-grid">
    <?php foreach ($categories as $cat) :
        $link = get_term_link($cat['slug'], 'product_cat');
        if (is_wp_error($link)) $link = home_url('/compounds/');
    ?>
        <a href="<?php echo esc_url($link); ?>" class="nav-category-card" style="--cat-color: <?php echo esc_attr($cat['color']); ?>">
            <div class="nav-category-card__bar"></div>
            <div class="nav-category-card__body">
                <h3 class="nav-category-card__title"><?php echo esc_html($cat['name']); ?></h3>
                <p class="nav-category-card__desc"><?php echo esc_html($cat['desc']); ?></p>
                <span class="nav-category-card__link"><?php esc_html_e('View Compounds →', 'navigate-peptides'); ?></span>
            </div>
        </a>
    <?php endforeach; ?>
</div>
