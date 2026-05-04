<?php
/**
 * Template Part: Category Grid
 *
 * @package NavigatePeptides
 */

// Literal __() calls so makepot can extract the strings; the dynamic lookup
// below still works because each array value is the extracted literal.
// 6 categories: 3x2 grid on desktop, 2x3 on mobile. Locked to Stephie's
// final spec sheet (compound + color list).
$categories = [
    ['slug' => 'metabolic-research',           'color' => '#3F6A8A', 'name' => __('Metabolic Research', 'navigate-peptides'),           'desc' => __('Peptides studied for metabolic pathway modulation and energy metabolism research.', 'navigate-peptides')],
    ['slug' => 'cellular-research',            'color' => '#4A6F5A', 'name' => __('Cellular Research', 'navigate-peptides'),            'desc' => __('Peptides investigated for cellular signaling pathways and proliferation mechanisms.', 'navigate-peptides')],
    ['slug' => 'tissue-repair-research',       'color' => '#A88E45', 'name' => __('Tissue Repair Research', 'navigate-peptides'),       'desc' => __('Compounds investigated for extracellular matrix, collagen, and growth-factor pathway research.', 'navigate-peptides')],
    ['slug' => 'hormonal-signaling-research',  'color' => '#6B5A7A', 'name' => __('Hormonal Signaling Research', 'navigate-peptides'),  'desc' => __('Compounds studied for endocrine pathway modulation and receptor-signaling research.', 'navigate-peptides')],
    ['slug' => 'cognitive-research',           'color' => '#8A5D6A', 'name' => __('Cognitive Research', 'navigate-peptides'),           'desc' => __('Peptides explored for neurotrophic pathways and synaptic signaling mechanisms.', 'navigate-peptides')],
    ['slug' => 'dermal-research',              'color' => '#5A2E36', 'name' => __('Dermal Research', 'navigate-peptides'),              'desc' => __('Compounds explored for dermal tissue modeling and epidermal pathway analysis.', 'navigate-peptides')],
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
