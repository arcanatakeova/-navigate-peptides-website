<?php
/**
 * Template Name: About — Standards
 *
 * @package NavigatePeptides
 */

get_header();
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <h1 class="nav-page-hero__title">Our Standards</h1>
        <p class="nav-page-hero__subtitle">The principles that define our approach to research peptide supply.</p>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container">
        <div class="nav-stack">
            <?php
            $standards = [
                ['title' => 'Quality Without Compromise', 'desc' => 'Every batch is independently tested. Every certificate of analysis is published. We do not ship compounds that fail to meet our purity specifications, regardless of cost implications.'],
                ['title' => 'Regulatory Compliance', 'desc' => 'Our operations, content, and marketing practices comply with FDA regulations for research-use-only compounds, payment processor requirements, and applicable state and federal guidelines.'],
                ['title' => 'Scientific Integrity', 'desc' => 'Product descriptions reference published preclinical research and describe molecular mechanisms. We do not make health claims, therapeutic suggestions, or consumer-oriented marketing statements.'],
                ['title' => 'Transparency', 'desc' => 'Public certificates of analysis, documented testing methodologies, and clear research-use disclaimers on every product. No hidden information, no misleading language.'],
                ['title' => 'Researcher-First Approach', 'desc' => 'Our catalog, documentation, and quality processes are designed for research professionals. Scientific context, pathway documentation, and verified purity data — not marketing copy.'],
            ];
            foreach ($standards as $s) :
            ?>
                <div class="nav-info-card">
                    <h3 class="nav-info-card__title"><?php echo esc_html($s['title']); ?></h3>
                    <p class="nav-info-card__desc"><?php echo esc_html($s['desc']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
