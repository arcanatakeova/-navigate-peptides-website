<?php
/**
 * Template Name: Research Hub
 *
 * @package NavigatePeptides
 */

get_header();
?>

<section class="nav-page-hero">
    <div class="nav-container">
        <h1 class="nav-page-hero__title">Research Hub</h1>
        <p class="nav-page-hero__subtitle">
            Scientific resources for peptide research. Pathway analysis, mechanism-of-action documentation,
            and referenced preclinical studies — organized for research professionals.
        </p>
    </div>
</section>

<section class="nav-section">
    <div class="nav-container">
        <div class="nav-card-grid nav-card-grid--2">
            <?php
            $sections = [
                ['title' => 'Research Intelligence', 'slug' => 'intelligence', 'tag' => 'Analysis', 'desc' => 'Curated analysis of peptide research developments, regulatory updates, and preclinical findings from peer-reviewed sources.'],
                ['title' => 'Research Library', 'slug' => 'library', 'tag' => 'Database', 'desc' => 'Comprehensive database of compound profiles, mechanism-of-action summaries, and referenced preclinical studies.'],
                ['title' => 'Research Framework', 'slug' => 'framework', 'tag' => 'Methodology', 'desc' => 'Methodological guidelines for peptide research including handling protocols, storage requirements, and documentation standards.'],
                ['title' => 'Emerging Research', 'slug' => 'emerging', 'tag' => 'Frontier', 'desc' => 'Coverage of novel peptide compounds and newly published research exploring uncharacterized signaling pathways.'],
            ];
            foreach ($sections as $s) :
                $link = get_term_link($s['slug'], 'research_category');
                if (is_wp_error($link)) $link = home_url('/research/');
            ?>
                <a href="<?php echo esc_url($link); ?>" class="nav-link-card">
                    <span class="nav-link-card__tag"><?php echo esc_html($s['tag']); ?></span>
                    <h3 class="nav-link-card__title"><?php echo esc_html($s['title']); ?></h3>
                    <p class="nav-link-card__desc"><?php echo esc_html($s['desc']); ?></p>
                    <span class="nav-link-card__action">Explore →</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
