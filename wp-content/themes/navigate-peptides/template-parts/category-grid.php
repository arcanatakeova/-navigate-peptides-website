<?php
/**
 * Template Part: Category Grid
 *
 * @package NavigatePeptides
 */

$categories = [
    ['name' => 'Metabolic Research',     'slug' => 'metabolic-research',     'color' => '#2F4666', 'desc' => 'Peptides studied for metabolic pathway modulation and energy metabolism research.'],
    ['name' => 'Tissue Repair Research', 'slug' => 'tissue-repair-research', 'color' => '#9C843E', 'desc' => 'Compounds investigated for tissue regeneration and structural repair mechanisms.'],
    ['name' => 'Cognitive Research',     'slug' => 'cognitive-research',     'color' => '#5E507F', 'desc' => 'Peptides explored for neuroprotective pathways and cognitive function research.'],
    ['name' => 'Inflammation Research',  'slug' => 'inflammation-research',  'color' => '#4A141C', 'desc' => 'Compounds studied for inflammatory response modulation and immune signaling.'],
    ['name' => 'Cellular Research',      'slug' => 'cellular-research',      'color' => '#8E5660', 'desc' => 'Peptides investigated for cellular signaling pathways and proliferation mechanisms.'],
    ['name' => 'Dermal Research',        'slug' => 'dermal-research',        'color' => '#4A6B5F', 'desc' => 'Compounds explored for dermal tissue modeling and epidermal pathway analysis.'],
    ['name' => 'Research Blends',        'slug' => 'research-blends',        'color' => '#474C50', 'desc' => 'Multi-peptide formulations designed for synergistic pathway research applications.'],
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
                <span class="nav-category-card__link">View Compounds →</span>
            </div>
        </a>
    <?php endforeach; ?>
</div>
