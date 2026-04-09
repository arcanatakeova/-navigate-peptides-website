<?php
/**
 * Template Name: Quality — Manufacturing Standards
 *
 * @package NavigatePeptides
 */

get_header();
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <h1 class="nav-page-hero__title">Manufacturing Standards</h1>
        <p class="nav-page-hero__subtitle">Research-grade peptide synthesis requires controlled environments, validated processes, and rigorous documentation at every stage.</p>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container">
        <div class="nav-stack">
            <?php
            $items = [
                ['title' => 'GMP-Compliant Facilities', 'desc' => 'All compounds are synthesized in facilities following Good Manufacturing Practice guidelines. Controlled environments with validated HVAC, cleanroom classification, and environmental monitoring.'],
                ['title' => 'Solid-Phase Peptide Synthesis', 'desc' => 'Automated SPPS with Fmoc chemistry protocols ensures consistent, high-purity peptide production. Each synthesis run follows validated standard operating procedures.'],
                ['title' => 'Purification Protocols', 'desc' => 'Reverse-phase HPLC purification removes truncated sequences, deletion products, and synthesis byproducts. Multiple purification cycles are used when required to meet purity specifications.'],
                ['title' => 'Lyophilization & Packaging', 'desc' => 'Controlled freeze-drying preserves compound stability and integrity. Nitrogen-flushed vials prevent oxidative degradation during storage and shipping.'],
            ];
            foreach ($items as $item) :
            ?>
                <div class="nav-info-card">
                    <h3 class="nav-info-card__title"><?php echo esc_html($item['title']); ?></h3>
                    <p class="nav-info-card__desc"><?php echo esc_html($item['desc']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
