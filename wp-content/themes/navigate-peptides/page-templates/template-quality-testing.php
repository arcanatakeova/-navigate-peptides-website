<?php
/**
 * Template Name: Quality — Testing & Verification
 *
 * @package NavigatePeptides
 */

get_header();
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <h1 class="nav-page-hero__title">Testing &amp; Verification</h1>
        <p class="nav-page-hero__subtitle">Every batch of every compound undergoes independent analytical testing before release. No exceptions.</p>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container">
        <div class="nav-card-grid nav-card-grid--2">
            <?php
            $tests = [
                ['title' => 'HPLC Analysis', 'desc' => 'High-performance liquid chromatography confirms compound purity and identifies any impurities or degradation products. Each batch must meet our minimum purity threshold.'],
                ['title' => 'Mass Spectrometry', 'desc' => 'Molecular weight verification via mass spectrometry confirms compound identity and structural integrity. Results are documented on every certificate of analysis.'],
                ['title' => 'Endotoxin Screening', 'desc' => 'LAL (Limulus Amebocyte Lysate) testing screens for bacterial endotoxin contamination, ensuring compounds meet research-grade cleanliness standards.'],
                ['title' => 'Sterility Verification', 'desc' => 'Microbial testing protocols verify absence of bacterial and fungal contamination in lyophilized products prior to release.'],
            ];
            foreach ($tests as $t) :
            ?>
                <div class="nav-info-card">
                    <h3 class="nav-info-card__title"><?php echo esc_html($t['title']); ?></h3>
                    <p class="nav-info-card__desc"><?php echo esc_html($t['desc']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
