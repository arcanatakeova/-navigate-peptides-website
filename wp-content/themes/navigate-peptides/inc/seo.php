<?php
/**
 * SEO Foundation — Navigate Peptides
 *
 * Compliance constraint: processor prohibits human-use framing; all meta
 * copy and schema must be molecular/research-oriented. See docs/COMPLIANCE.
 *
 * Outputs:
 *   - Title tag (via document_title_parts filter)
 *   - Meta description, robots, viewport already in header
 *   - Canonical, pagination rel=prev/next
 *   - Open Graph + Twitter Card
 *   - JSON-LD: Organization + WebSite w/ SearchAction (sitewide),
 *     Product (single product), BreadcrumbList (all non-home),
 *     Article (single research post), CollectionPage (archives)
 *
 * @package NavigatePeptides
 */

defined('ABSPATH') || exit;

/* ------------------------------------------------------------------
 * Constants + helpers
 * ----------------------------------------------------------------*/

define('NAV_SEO_SITE_NAME', 'Navigate Peptides');
define('NAV_SEO_TAGLINE', 'Precision Peptide Research');
define('NAV_SEO_TWITTER_HANDLE', '@navigatepeptides');
// SERP target: ~155 chars (Google truncates ~160). Earlier copy was
// 202 chars — long-tail of the snippet was dropped on display.
define('NAV_SEO_DEFAULT_DESC', 'Research-grade peptide compounds with third-party verified certificates of analysis. Supplied for controlled laboratory research only.');

/**
 * JSON-LD script encoder — forces HEX escaping so user-controllable strings
 * containing </script>, &, quotes, or angle brackets can never break out of
 * the <script type="application/ld+json"> context. Defence-in-depth XSS.
 *
 * On encode failure (non-UTF-8 byte in a title, deeply recursive schema),
 * logs the failure with enough context to diagnose rather than silently
 * dropping the schema emitter.
 */
function nav_seo_json_ld(array $schema): string {
    $flags = JSON_UNESCAPED_SLASHES
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT;
    $json = wp_json_encode($schema, $flags);
    if ($json === false) {
        $type = $schema['@type'] ?? 'unknown';
        $url  = $_SERVER['REQUEST_URI'] ?? '';
        error_log(sprintf(
            '[nav_seo] JSON-LD encode failed: err=%s @type=%s url=%s',
            json_last_error_msg(), $type, $url
        ));
        // Surface to admins — silent JSON-LD drop on the public site is a
        // compliance-relevant regression (processor scanners check the
        // schema's RUO disambiguatingDescription).
        if (function_exists('nav_admin_warn')) {
            nav_admin_warn(
                'json_ld_encode_' . $type,
                sprintf(
                    'JSON-LD encode failed for @type=%s on %s — schema dropped from page output.',
                    $type, $url
                )
            );
        }
        return '';
    }
    return '<script type="application/ld+json">' . $json . '</script>' . "\n";
}

/**
 * Compliance scrubber for user-editable strings that flow into meta
 * description and schema descriptions. The admin-save scanner warns but
 * does not block; this is a belt-and-braces final pass.
 *
 * Replaces flagged keywords with neutral synonyms rather than stripping
 * them outright — preserves sentence structure so fallbacks aren't needed.
 */
function nav_seo_scrub(string $text): string {
    if ($text === '') return $text;

    // Pairs: prohibited pattern => compliant replacement.
    // Case-insensitive; \b boundaries to avoid partial matches.
    // Expanded 2026-04: audit caught blind spots (sub-q / SC inject / IM
    // inject / bioavailable / potency / efficacy / cycle / stack /
    // muscle growth / clinically proven / FDA approved) that the original
    // map didn't cover. Processors read every rendered string.
    $map = [
        '/\bdos(?:e|ing|age)\b/i'                  => 'reconstitution concentration',
        '/\binject(?:ion|ing|ions|able)?\b/i'      => 'laboratory handling',
        '/\bsubcutaneous\b/i'                      => 'laboratory application',
        '/\bintramuscular\b/i'                     => 'laboratory application',
        '/\bsub[- ]?q(?:s)?\b/i'                   => 'laboratory application',
        '/\b(?:SC|IM)[ -]?inject(?:ion|ing|ions)?\b/i' => 'laboratory application',
        '/\bheal(?:ing|s)?\b/i'                    => 'pathway modulation',
        '/\brecover(?:y|ing)?\b/i'                 => 'pathway response',
        '/\btreat(?:ment|s|ing)?\b/i'              => 'investigation',
        '/\btherap(?:y|ies|eutic)\b/i'             => 'investigational',
        '/\bpatients?\b/i'                         => 'research subjects',
        '/\bwellness\b/i'                          => 'molecular research',
        '/\bperformance\b/i'                       => 'mechanism',
        '/\banti[- ]aging\b/i'                     => 'longevity pathway',
        '/\b(fat|weight)[- ]loss\b/i'              => 'metabolic pathway research',
        '/\bmuscle[- ]growth\b/i'                  => 'myocellular pathway research',
        '/\bpharmaceutical grade\b/i'              => 'research grade',
        '/\bFDA[- ]approved\b/i'                   => 'research grade',
        '/\bclinically[- ]proven\b/i'              => 'investigational',
        '/\bbefore and after\b/i'                  => 'pre- and post-study',
        '/\btestimonial\b/i'                       => 'research citation',
        '/\bcure[sd]?\b/i'                         => 'investigational',
        '/\bbenefits?\b/i'                         => 'mechanisms',
        '/\bprotocol\b/i'                          => 'procedure',
        // cycle/cycling/cycled and stack/stacking/stacked needed explicit
        // entries — the earlier `(s|d)?` and `(s|ed|ing)?` captures
        // produced ungrammatical phased/combinationing output. Order
        // matters: longer alternations evaluated first.
        '/\bcycling\b/i'                           => 'phasing',
        '/\bcycled\b/i'                            => 'phased',
        '/\bcycle(s)?\b/i'                         => 'phase$1',
        '/\bstacking\b/i'                          => 'combining',
        '/\bstacked\b/i'                           => 'combined',
        '/\bstack(s)?\b/i'                         => 'combination$1',
        '/\bbioavailab(le|ility)\b/i'              => 'structural propert$1',
        '/\bpotenc(y|ies)\b/i'                     => 'molecular activit$1',
        '/\befficacy\b/i'                          => 'mechanism profile',
        // Wellness/supplement adjacent phrasing — common processor scanner
        // flags on competitor storefronts. Expand the perimeter so any
        // marketing copy that drifts toward these terms gets reframed
        // before it reaches a rendered string.
        '/\bboost(?:s|ed|ing|er|ers)?\b/i'         => 'mechanism research',
        '/\benhanc(?:e|ed|ing|ement|ements|es)\b/i' => 'modulation study',
        '/\bgains\b/i'                             => 'observed responses',
        '/\blean[- ]mass\b/i'                      => 'somatotype composition research',
        '/\bbody[- ]composition\b/i'               => 'somatotype composition research',
        '/\bvitality\b/i'                          => 'cellular metabolism research',
        '/\byouth(?:ful)?\b/i'                     => 'longevity pathway',
        '/\bsupplement(?:s|ation|ed|ing)?\b/i'     => 'research compound',
        '/\bnutraceutical(?:s)?\b/i'               => 'research compound',
        '/\bpeptide[- ]therap(?:y|ies)\b/i'        => 'peptide research',
        '/\brejuvenat(?:e|ed|ing|ion)\b/i'         => 'cellular pathway study',
        '/\brestor(?:e|ed|ing|ation)\b/i'          => 'mechanism investigation',
        '/\bregenerat(?:e|ed|ing|ion|ive)\b/i'     => 'pathway investigation',
        '/\bbiohack(?:s|ing|er|ers)?\b/i'          => 'mechanism research',
        '/\blibido\b/i'                            => 'endocrine pathway',
        '/\benerg(?:y|izing|ize)\b/i'              => 'metabolic activity',
    ];
    $result = preg_replace(array_keys($map), array_values($map), $text);
    if ($result === null) {
        // preg_replace returns null on regex error — log and bail with
        // the original input so callers don't quietly receive an empty
        // string (the prior `(string) preg_replace(...)` cast hid this).
        error_log('[nav_seo_scrub] preg_replace failed for input: ' . substr($text, 0, 120));
        return $text;
    }
    return $result;
}

/**
 * Scrub a short identifier-like string (product name, term name, tag name)
 * for use in schema/OG/Twitter output. Same map as nav_seo_scrub but
 * short-circuits empty input and returns the original on regex failure.
 */
function nav_seo_scrub_name($text): string {
    $text = (string) $text;
    if ($text === '') return $text;
    // nav_seo_scrub now handles regex failure internally and returns
    // the original input on error, so the prior `?: $text` rescue tail
    // (unreachable through the success path due to a `(string)` cast)
    // is no longer needed.
    return nav_seo_scrub($text);
}

/**
 * Is a competing SEO plugin active? When one is, our meta/OG/Twitter/JSON-LD
 * emitters bail — otherwise every page ships duplicate tags, which Google
 * treats as a conflict signal (last one wins, and order is plugin-dependent).
 * Detected via constants/classes the major plugins define on load.
 */
function nav_seo_has_competing_plugin(): bool {
    static $has = null;
    if ($has !== null) return $has;
    $has = defined('WPSEO_VERSION')                    // Yoast SEO
        || defined('WPSEO_FILE')
        || class_exists('RankMath', false)             // Rank Math
        || defined('RANK_MATH_VERSION')
        || defined('AIOSEO_VERSION')                   // All in One SEO
        || defined('AIOSEO_PHP_VERSION_DIR')
        || defined('SEOPRESS_VERSION')                 // SEOPress
        || defined('THE_SEO_FRAMEWORK_VERSION');       // The SEO Framework
    return (bool) apply_filters('nav_seo_has_competing_plugin', $has);
}

/*
 * Suppress Jetpack / wpcom Open Graph emitters. On Atomic (wp.com Business)
 * Jetpack auto-emits its own og:image via the "shareable image" service at
 * s0.wp.com/_si/ — that produces a low-quality grey card that shows as a
 * SECOND link preview in iMessage alongside our branded card. We emit a
 * full OG block of our own below, so silence JP's output entirely.
 */
add_filter('jetpack_enable_open_graph', '__return_false');
add_filter('jetpack_enable_opengraph',  '__return_false'); // older JP alias
add_filter('jetpack_open_graph_output_tags', '__return_empty_array');
add_filter('jetpack_photon_skip_image',  '__return_true');  // don't rewrite our og-social-share.png to Photon CDN
add_filter('jetpack_shareable_image_url', '__return_empty_string');

/**
 * Strip Jetpack Sharing + Likes UI on the front-end. Both inject hard-
 * coded light-theme inline CSS (#sharedaddy, #likes-other-gravatars
 * with white backgrounds + #2ea2cc blue stars) that clashes hard with
 * the dark research-bench palette. Also strips the Jetpack Carousel
 * lightbox comment form which renders as a default browser modal.
 */
add_filter('sharing_show', '__return_false');
add_filter('jetpack_likes_master_iframe', '__return_empty_string');
add_filter('jetpack_sharing_counts',     '__return_false');
add_action('wp_enqueue_scripts', function () {
    wp_dequeue_style('jetpack_likes');
    wp_dequeue_style('jetpack-sharing-buttons');
    wp_dequeue_style('jetpack_sharing_css');
    wp_dequeue_style('sharedaddy');
    wp_dequeue_script('jetpack_likes');
    wp_dequeue_script('sharing-js');
}, 100);
add_filter('jp_carousel_load', '__return_false');

/**
 * Append the paged suffix (/page/N/) to a canonical URL when the current
 * request is page 2+ of a paginated archive or search. Without this, page
 * 2 of a category would canonical to page 1 — Google deduplicates and the
 * later pages drop out of the index.
 */
function nav_seo_maybe_append_paged(string $url): string {
    if (!$url) return $url;
    $paged = (int) get_query_var('paged');
    if ($paged < 2) return $url;
    // get_pagenum_link() respects the site's permalink structure + any
    // query string already present, so it's the safe way to build page-N
    // URLs for any archive type.
    $paged_url = get_pagenum_link($paged, false);
    return $paged_url ?: $url;
}

/**
 * Get the canonical URL for the current request.
 * Handles singular, category, tag, search, paged archives, and front page.
 */
function nav_seo_canonical_url(): string {
    if (is_front_page()) {
        return home_url('/');
    }
    if (is_singular()) {
        $permalink = get_permalink();
        return $permalink ?: home_url('/');
    }
    if (is_category() || is_tag() || is_tax()) {
        $term = get_queried_object();
        if ($term && isset($term->term_id)) {
            $link = get_term_link($term);
            if (!is_wp_error($link) && $link) return nav_seo_maybe_append_paged($link);
        }
    }
    if (is_post_type_archive()) {
        $link = get_post_type_archive_link(get_post_type());
        if ($link) return nav_seo_maybe_append_paged($link);
    }
    if (is_search()) {
        return nav_seo_maybe_append_paged(home_url('/?s=' . urlencode(get_search_query())));
    }
    if (is_home()) {
        $posts_page = get_permalink(get_option('page_for_posts'));
        return nav_seo_maybe_append_paged($posts_page ?: home_url('/'));
    }
    // Fallback: reconstruct from request path without misusing add_query_arg.
    $request = $GLOBALS['wp']->request ?? '';
    return nav_seo_maybe_append_paged(home_url('/' . ltrim((string) $request, '/')));
}

/**
 * Clamp a string to N words with ellipsis — meta description length safe.
 */
function nav_seo_trim(string $text, int $words = 30): string {
    $text = wp_strip_all_tags($text);
    $text = preg_replace('/\s+/', ' ', trim($text));
    return wp_trim_words($text, $words, '…');
}

/**
 * Meta description per page type. Compliance-safe defaults + scrub pass.
 * Never returns an empty string — falls back to NAV_SEO_DEFAULT_DESC.
 */
function nav_seo_description(): string {
    $desc = '';

    // Static front page (front-page.php) — must be evaluated BEFORE
    // is_home() because both return true when show_on_front='posts'.
    // The blog-archive copy belongs at /research/ only.
    if (is_front_page()) {
        // ~150 chars — fits in SERP without truncation while still
        // surfacing the brand+RUO posture in the first 120 chars.
        $desc = 'High-purity research peptides with third-party verified COAs on every batch. Supplied for controlled laboratory research only.';
    }
    // WooCommerce shop archive (/compounds/ -> page-managed, but
    // is_shop() catches the auto-generated archive too).
    elseif (function_exists('is_shop') && is_shop()) {
        $desc = NAV_SEO_DEFAULT_DESC;
    }
    // Single product: compound-specific scientific description
    elseif (is_singular('product') && function_exists('wc_get_product')) {
        $product = wc_get_product(get_the_ID());
        if ($product) {
            $title    = $product->get_name();
            $subtitle = get_post_meta($product->get_id(), '_nav_technical_subtitle', true);
            $mw       = get_post_meta($product->get_id(), '_nav_molecular_weight', true);
            $purity   = get_post_meta($product->get_id(), '_nav_purity', true);
            $short    = $product->get_short_description();

            if ($short) {
                $desc = nav_seo_trim($short, 26);
            } elseif ($subtitle) {
                $desc = sprintf(
                    '%s — %s. %s%sResearch-use only compound with third-party verified COA.',
                    $title,
                    $subtitle,
                    $mw ? "MW: {$mw}. " : '',
                    $purity ? "Purity: {$purity}. " : ''
                );
            } else {
                $desc = sprintf(
                    '%s research-grade peptide compound. Third-party verified certificate of analysis. Supplied for controlled laboratory environments.',
                    $title
                );
            }
        }
    } elseif (is_product_category()) {
        $term = get_queried_object();
        if ($term) {
            if (!empty($term->description)) {
                $desc = nav_seo_trim($term->description, 30);
            } else {
                $desc = sprintf(
                    '%s peptide compounds for scientific investigation. Every batch includes molecular identity verification, HPLC purity data, and a lot-specific certificate of analysis. Research-use only.',
                    $term->name
                );
            }
        }
    } elseif (is_singular(['post', 'nav_research'])) {
        $excerpt = get_the_excerpt();
        if ($excerpt) {
            $desc = nav_seo_trim($excerpt, 28);
        }
    } elseif (is_singular()) {
        $manual = get_post_meta(get_the_ID(), '_nav_meta_description', true);
        if ($manual) {
            $desc = nav_seo_trim($manual, 30);
        } else {
            $excerpt = get_the_excerpt();
            if ($excerpt) $desc = nav_seo_trim($excerpt, 28);
        }
    } elseif (is_post_type_archive('post') || is_home()) {
        $desc = 'Mechanism deep-dives, emerging research, and analytical methodology for research peptide compounds. Purely scientific content — no human-use framing.';
    } elseif (is_search()) {
        // Strip HTML + cap length so malicious search strings don't leak
        // ugly raw characters into Google's SERP preview.
        $q = wp_strip_all_tags((string) get_search_query());
        $q = mb_substr($q, 0, 80);
        $desc = sprintf(
            'Search results for %s — Navigate Peptides research compounds and analytical resources.',
            $q
        );
    }

    // Compliance scrub — ship the cleaned text rather than clobbering to a
    // generic default. The previous behavior fell through to
    // NAV_SEO_DEFAULT_DESC whenever a single keyword was matched, which
    // ended up shipping identical meta descriptions for multiple products
    // (duplicate-content SEO penalty). Now: scrub, log the mutation so the
    // offending source can be fixed, emit the scrubbed text.
    $scrubbed = nav_seo_scrub($desc);
    if ($scrubbed !== $desc && $desc !== '') {
        error_log(sprintf(
            '[nav_seo] scrub mutated meta description. url=%s original=%s',
            $_SERVER['REQUEST_URI'] ?? '',
            mb_substr($desc, 0, 120)
        ));
    }
    if (trim($scrubbed) === '') {
        return NAV_SEO_DEFAULT_DESC;
    }
    return $scrubbed;
}

/**
 * Resolve a shareable OG image with fallbacks.
 * Priority: post featured image → site icon → theme hero asset.
 */
function nav_seo_og_image(): array {
    $width = 1200;
    $height = 630;

    // Per-page override — admin meta box on every page/post sets
    // `_nav_og_image_id` to an attachment ID. Wins over featured-image
    // and fallback so editors can give About/Quality/etc. dedicated
    // social-share renders instead of the generic theme fallback.
    if (is_singular()) {
        $override = (int) get_post_meta(get_the_ID(), '_nav_og_image_id', true);
        if ($override > 0) {
            $src = wp_get_attachment_image_src($override, 'large');
            if ($src) {
                return [
                    'url'    => $src[0],
                    'width'  => $src[1],
                    'height' => $src[2],
                    'alt'    => get_post_meta($override, '_wp_attachment_image_alt', true) ?: get_the_title(),
                ];
            }
        }
    }

    if (is_singular() && has_post_thumbnail()) {
        $id  = get_post_thumbnail_id();
        $src = wp_get_attachment_image_src($id, 'large');
        if ($src) {
            return [
                'url'    => $src[0],
                'width'  => $src[1],
                'height' => $src[2],
                'alt'    => get_post_meta($id, '_wp_attachment_image_alt', true) ?: get_the_title(),
            ];
        }
    }

    if (is_product_category()) {
        $term  = get_queried_object();
        $thumb = $term ? get_term_meta($term->term_id, 'thumbnail_id', true) : 0;
        if ($thumb) {
            $src = wp_get_attachment_image_src($thumb, 'large');
            if ($src) {
                return [
                    'url'    => $src[0],
                    'width'  => $src[1],
                    'height' => $src[2],
                    'alt'    => $term->name,
                ];
            }
        }
    }

    // Theme fallback — dedicated on-brand social share image (1200×630).
    // Dark bg, wordmark, "Engineered. Intelligent." display, branded vial
    // with full label — replaces the generic "Made in the USA" placeholder
    // that was showing in iMessage/Slack/FB previews.
    return [
        'url'    => get_template_directory_uri() . '/assets/images/og-social-share.png',
        'width'  => 1200,
        'height' => 630,
        'alt'    => 'Navigate Peptides — precision peptide research',
    ];
}

/**
 * Whether this response should be noindexed.
 * Default: noindex search results + paginated archive deep pages + 404.
 */
function nav_seo_is_noindex(): bool {
    if (is_404() || is_search()) return true;

    // Noindex deep paginated archives — they're mostly duplicate content.
    $paged = (int) get_query_var('paged');
    if ($paged > 5) return true;

    return (bool) apply_filters('nav_seo_noindex', false);
}

/* ------------------------------------------------------------------
 * Title tag — customize document_title_parts
 * ----------------------------------------------------------------*/
add_filter('document_title_parts', function (array $parts) {
    if (nav_seo_has_competing_plugin()) return $parts;
    // Home: "Navigate Peptides — Precision Peptide Research"
    if (is_front_page()) {
        return [
            'title'   => NAV_SEO_SITE_NAME,
            'tagline' => NAV_SEO_TAGLINE,
        ];
    }

    // Single product: "BPC-157 5mg — Synthetic Pentadecapeptide | Navigate Peptides"
    if (is_singular('product') && function_exists('wc_get_product')) {
        $product  = wc_get_product(get_the_ID());
        if ($product) {
            $subtitle = nav_seo_scrub_name(get_post_meta($product->get_id(), '_nav_technical_subtitle', true));
            $title    = nav_seo_scrub_name($product->get_name());
            if ($subtitle) {
                $parts['title'] = $title . ' — ' . $subtitle;
            } else {
                $parts['title'] = $title . ' — Research Peptide Compound';
            }
            $parts['site'] = NAV_SEO_SITE_NAME;
        }
        return $parts;
    }

    // Product category: "Metabolic Research Peptides | Navigate Peptides"
    if (is_product_category()) {
        $term = get_queried_object();
        if ($term) {
            $parts['title'] = nav_seo_scrub_name($term->name) . ' Peptide Compounds';
        }
        $parts['site'] = NAV_SEO_SITE_NAME;
        return $parts;
    }

    // Research category/tag: "Research Library | Navigate Peptides"
    if (is_category() || is_tag() || is_tax()) {
        $term = get_queried_object();
        if ($term) {
            $parts['title'] = nav_seo_scrub_name($term->name);
        }
        $parts['site'] = NAV_SEO_SITE_NAME;
        return $parts;
    }

    $parts['site'] = NAV_SEO_SITE_NAME;
    return $parts;
});

// Em-dash matches the title-formatter's internal subtitle separation
// (seo.php:445) — keeps the SERP title rendered in one consistent
// punctuation style across page types.
add_filter('document_title_separator', fn() => '—');

/* ------------------------------------------------------------------
 * Meta description, robots, canonical, prev/next, preconnect
 * ----------------------------------------------------------------*/
add_action('wp_head', function () {
    if (nav_seo_has_competing_plugin()) return;
    // Description
    $desc = nav_seo_description();
    if ($desc) {
        echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
    }

    // Robots
    if (nav_seo_is_noindex()) {
        echo '<meta name="robots" content="noindex,follow">' . "\n";
    } else {
        echo '<meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">' . "\n";
    }

    // Canonical
    $canonical = nav_seo_canonical_url();
    if ($canonical) {
        echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
    }

    // Paginated archives — prev/next
    if (is_archive() || is_home() || is_search()) {
        global $wp_query;
        $paged = max(1, (int) get_query_var('paged'));
        $max   = (int) ($wp_query->max_num_pages ?? 1);
        if ($paged > 1) {
            echo '<link rel="prev" href="' . esc_url(get_pagenum_link($paged - 1)) . '">' . "\n";
        }
        if ($paged < $max) {
            echo '<link rel="next" href="' . esc_url(get_pagenum_link($paged + 1)) . '">' . "\n";
        }
    }

    // Performance — preconnect / dns-prefetch for third-party hosts the
    // theme actually contacts on a typical request:
    //   - googletagmanager.com  (GA4 gtag.js bootstrap — inc/analytics.php)
    //   - google-analytics.com  (GA4 collect endpoint)
    //   - ajax.googleapis.com   (model-viewer ESM — header.php, on
    //                            home, quality, about, all PDPs, archive)
    // preconnect opens the TCP+TLS handshake speculatively; dns-prefetch
    // is a cheaper hint for hosts we may or may not hit. Fonts hosts are
    // already preconnected in header.php so we don't duplicate them.
    echo '<link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>' . "\n";
    echo '<link rel="preconnect" href="https://www.google-analytics.com" crossorigin>' . "\n";
    // model-viewer ESM is loaded from ajax.googleapis.com on pages with
    // 3D content. Mirrors the conditional in header.php so the handshake
    // hint only fires when we'll actually open that connection.
    global $template;
    $tpl = isset($template) ? basename((string) $template) : '';
    $needs_mv = is_front_page()
        || in_array($tpl, ['template-quality.php', 'template-about.php'], true)
        || (function_exists('is_product') && is_product())
        || (function_exists('is_shop') && (is_shop() || is_product_category() || is_product_tag()));
    if ($needs_mv) {
        echo '<link rel="preconnect" href="https://ajax.googleapis.com" crossorigin>' . "\n";
    }

    // Search engine verification meta tags. Set the constants in
    // inc/business.php (or wp-config.php) once you've claimed the
    // property in each console. Each console accepts the meta-tag
    // verification method as an alternative to DNS TXT records.
    if (defined('NAV_GSC_VERIFICATION') && NAV_GSC_VERIFICATION !== '') {
        echo '<meta name="google-site-verification" content="' . esc_attr(NAV_GSC_VERIFICATION) . '">' . "\n";
    }
    if (defined('NAV_BING_VERIFICATION') && NAV_BING_VERIFICATION !== '') {
        echo '<meta name="msvalidate.01" content="' . esc_attr(NAV_BING_VERIFICATION) . '">' . "\n";
    }
    if (defined('NAV_YANDEX_VERIFICATION') && NAV_YANDEX_VERIFICATION !== '') {
        echo '<meta name="yandex-verification" content="' . esc_attr(NAV_YANDEX_VERIFICATION) . '">' . "\n";
    }
}, 6);

/* ------------------------------------------------------------------
 * Open Graph + Twitter Card
 * ----------------------------------------------------------------*/
add_action('wp_head', function () {
    if (nav_seo_has_competing_plugin()) return;
    $title = wp_get_document_title();
    $desc  = nav_seo_description();
    $url   = nav_seo_canonical_url();
    $image = nav_seo_og_image();

    // Open Graph
    echo '<meta property="og:site_name" content="' . esc_attr(NAV_SEO_SITE_NAME) . '">' . "\n";
    echo '<meta property="og:locale" content="en_US">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";

    // og:type
    $type = 'website';
    if (is_singular('product')) {
        $type = 'product';
    } elseif (is_singular(['post', 'nav_research'])) {
        $type = 'article';
    }
    echo '<meta property="og:type" content="' . esc_attr($type) . '">' . "\n";

    // og:image
    if ($image['url']) {
        echo '<meta property="og:image" content="' . esc_url($image['url']) . '">' . "\n";
        echo '<meta property="og:image:width" content="' . esc_attr((string) $image['width']) . '">' . "\n";
        echo '<meta property="og:image:height" content="' . esc_attr((string) $image['height']) . '">' . "\n";
        if (!empty($image['alt'])) {
            echo '<meta property="og:image:alt" content="' . esc_attr($image['alt']) . '">' . "\n";
        }
    }

    // Article-specific
    if (is_singular(['post', 'nav_research'])) {
        $published = get_the_date('c');
        $modified  = get_the_modified_date('c');
        echo '<meta property="article:published_time" content="' . esc_attr($published) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr($modified) . '">' . "\n";

        // Article sections + tags — pull from built-in taxonomy for
        // posts, from the theme's research_category for nav_research.
        $cats = is_singular('nav_research')
            ? (get_the_terms(get_the_ID(), 'research_category') ?: [])
            : (get_the_category() ?: []);
        if (!is_wp_error($cats)) {
            foreach ($cats as $cat) {
                echo '<meta property="article:section" content="' . esc_attr(nav_seo_scrub_name($cat->name)) . '">' . "\n";
            }
        }

        $tags = is_singular('nav_research')
            ? (get_the_terms(get_the_ID(), 'research_tag') ?: get_the_tags() ?: [])
            : (get_the_tags() ?: []);
        if ($tags && !is_wp_error($tags)) {
            foreach ($tags as $tag) {
                echo '<meta property="article:tag" content="' . esc_attr(nav_seo_scrub_name($tag->name)) . '">' . "\n";
            }
        }

        $author_id = (int) get_post_field('post_author', get_the_ID());
        if ($author_id) {
            $author_name = get_the_author_meta('display_name', $author_id);
            if ($author_name) {
                echo '<meta property="article:author" content="' . esc_attr($author_name) . '">' . "\n";
            }
        }
    }

    // Product-specific
    if (is_singular('product') && function_exists('wc_get_product')) {
        $product = wc_get_product(get_the_ID());
        if ($product) {
            echo '<meta property="product:price:amount" content="' . esc_attr((string) $product->get_price()) . '">' . "\n";
            echo '<meta property="product:price:currency" content="' . esc_attr(get_woocommerce_currency()) . '">' . "\n";
            $stock = $product->is_in_stock() ? 'in stock' : 'out of stock';
            echo '<meta property="product:availability" content="' . esc_attr($stock) . '">' . "\n";
            echo '<meta property="product:brand" content="' . esc_attr(NAV_SEO_SITE_NAME) . '">' . "\n";
            echo '<meta property="product:condition" content="new">' . "\n";
            // Facebook / OG product spec expects retailer_item_id when SKU
            // is available — most catalog ingestion tools key on it for
            // dedupe across feeds and ads.
            $sku = $product->get_sku();
            if ($sku) {
                echo '<meta property="product:retailer_item_id" content="' . esc_attr($sku) . '">' . "\n";
            }
        }
    }

    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:site" content="' . esc_attr(NAV_SEO_TWITTER_HANDLE) . '">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($desc) . '">' . "\n";
    if ($image['url']) {
        echo '<meta name="twitter:image" content="' . esc_url($image['url']) . '">' . "\n";
        if (!empty($image['alt'])) {
            echo '<meta name="twitter:image:alt" content="' . esc_attr($image['alt']) . '">' . "\n";
        }
    }
}, 7);

/* ------------------------------------------------------------------
 * JSON-LD: Organization + WebSite (sitewide, priority 5)
 * ----------------------------------------------------------------*/
add_action('wp_head', function () {
    if (nav_seo_has_competing_plugin()) return;
    $home       = home_url('/');
    $logo_url   = '';

    if (has_custom_logo()) {
        $logo_id  = get_theme_mod('custom_logo');
        $logo_src = wp_get_attachment_image_url($logo_id, 'full');
        if ($logo_src) $logo_url = $logo_src;
    }
    if (!$logo_url) {
        $logo_url = get_template_directory_uri() . '/assets/images/logo.svg';
    }

    // Organization — DBA + address + email only. Legal-entity name,
    // phone number, and ContactPoint(telephone) are DELIBERATELY
    // OMITTED for processor-compliance privacy posture (Argyle/NMI
    // RUO storefront — phone leaks user-facing, legal-entity name
    // is private). Anyone touching this block: do NOT restore those
    // fields; inc/business.php has matching deletion notes.
    $organization = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Organization',
        '@id'         => $home . '#organization',
        'name'        => NAV_SEO_SITE_NAME,
        'url'         => $home,
        'logo'        => [
            '@type'  => 'ImageObject',
            'url'    => $logo_url,
            'width'  => 400,
            'height' => 80,
        ],
        'description' => NAV_SEO_DEFAULT_DESC,
        'slogan'      => NAV_SEO_TAGLINE,
        // Topical authority anchors — six research category names so
        // the knowledge graph associates the brand with these subjects.
        'knowsAbout'  => [
            'Metabolic Research',
            'Cellular Research',
            'Tissue Repair Research',
            'Hormonal Signaling Research',
            'Cognitive Research',
            'Dermal Research',
        ],
        'sameAs'      => apply_filters('nav_seo_same_as', []),
    ];
    if (function_exists('nav_business_schema_address')) {
        $organization['address'] = nav_business_schema_address();
    }
    if (function_exists('nav_has_business_email') && nav_has_business_email()) {
        $organization['email'] = NAV_BIZ_EMAIL;
        $organization['contactPoint'] = [
            '@type'       => 'ContactPoint',
            'email'       => NAV_BIZ_EMAIL,
            'contactType' => 'customer support',
            'areaServed'  => 'US',
            'availableLanguage' => ['English'],
        ];
    }

    echo nav_seo_json_ld($organization);

    // WebSite + SearchAction (sitelinks searchbox)
    $website = [
        '@context'        => 'https://schema.org',
        '@type'           => 'WebSite',
        '@id'             => $home . '#website',
        'url'             => $home,
        'name'            => NAV_SEO_SITE_NAME,
        'description'     => NAV_SEO_TAGLINE,
        'publisher'       => ['@id' => $home . '#organization'],
        'inLanguage'      => get_locale() ?: 'en-US',
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'       => 'EntryPoint',
                'urlTemplate' => $home . '?s={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];

    echo nav_seo_json_ld($website);
}, 5);

/* ------------------------------------------------------------------
 * JSON-LD: BreadcrumbList — emits on every non-home page.
 * ----------------------------------------------------------------*/
add_action('wp_head', function () {
    if (nav_seo_has_competing_plugin()) return;
    if (is_front_page()) return;

    $items = [];
    $position = 1;

    $add = function (string $name, string $url = '') use (&$items, &$position) {
        // Scrub every breadcrumb label — user-editable term/post names flow
        // through this helper and end up in Google's rich-result output.
        $item = [
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => nav_seo_scrub_name($name),
        ];
        if ($url) $item['item'] = $url;
        $items[] = $item;
    };

    $add('Home', home_url('/'));

    if (is_singular('product') && function_exists('wc_get_product')) {
        $product = wc_get_product(get_the_ID());
        if ($product) {
            $add('Compounds', home_url('/compounds/'));
            $terms = get_the_terms($product->get_id(), 'product_cat');
            if ($terms && !is_wp_error($terms)) {
                $cat = $terms[0];
                $add($cat->name, (string) get_term_link($cat));
            }
            $add(get_the_title());
        }
    } elseif (is_product_category()) {
        $add('Compounds', home_url('/compounds/'));
        $term = get_queried_object();
        if ($term) $add($term->name);
    } elseif (is_shop() || is_post_type_archive('product')) {
        $add('Compounds');
    } elseif (is_singular(['post', 'nav_research'])) {
        $add('Research', home_url('/research/'));
        // Posts use the built-in category taxonomy; nav_research articles
        // use the theme's research_category taxonomy. Pick whichever
        // applies to this post.
        $cats = is_singular('nav_research')
            ? get_the_terms(get_the_ID(), 'research_category')
            : get_the_category();
        if ($cats && !is_wp_error($cats) && !empty($cats[0])) {
            $term_link = is_singular('nav_research')
                ? get_term_link($cats[0])
                : get_category_link($cats[0]->term_id);
            if (!is_wp_error($term_link)) {
                $add($cats[0]->name, (string) $term_link);
            }
        }
        $add(get_the_title());
    } elseif (is_category() || is_tag() || is_tax()) {
        $term = get_queried_object();
        if ($term) {
            $add('Research', home_url('/research/'));
            $add($term->name);
        }
    } elseif (is_singular('page')) {
        $title = get_the_title();
        $ancestors = get_post_ancestors(get_the_ID());
        if ($ancestors) {
            foreach (array_reverse($ancestors) as $ancestor_id) {
                $add(get_the_title($ancestor_id), (string) get_permalink($ancestor_id));
            }
        }
        $add($title);
    } elseif (is_search()) {
        $add('Search results');
    } elseif (is_404()) {
        $add('Page not found');
    }

    if (count($items) < 2) return;

    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];

    echo nav_seo_json_ld($schema);
}, 9);

/**
 * Build the Product.offers value. Variable products emit AggregateOffer
 * with lowPrice/highPrice/offerCount + an inner offers[] array per
 * variation; simple products emit a single Offer. Variations carry
 * their own SKU + price so search engines + shopping feeds can map
 * each variant to a distinct catalog entry.
 */
function nav_seo_product_offers(WC_Product $product, string $url): array {
    $currency = get_woocommerce_currency();
    $valid_until = gmdate('Y-m-d', strtotime('+1 year'));
    $availability = $product->is_in_stock()
        ? 'https://schema.org/InStock'
        : 'https://schema.org/OutOfStock';
    $seller = ['@id' => home_url('/') . '#organization'];

    if ($product->is_type('variable')) {
        $variations = $product->get_available_variations();
        $offers = [];
        $prices = [];
        foreach ($variations as $variation) {
            if (!isset($variation['display_price'])) continue;
            $price = (string) $variation['display_price'];
            $prices[] = (float) $variation['display_price'];
            $offer = [
                '@type'           => 'Offer',
                'url'             => $url,
                'priceCurrency'   => $currency,
                'price'           => $price,
                'priceValidUntil' => $valid_until,
                'availability'    => !empty($variation['is_in_stock'])
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'itemCondition'   => 'https://schema.org/NewCondition',
                'seller'          => $seller,
            ];
            if (!empty($variation['sku'])) {
                $offer['sku'] = $variation['sku'];
            }
            $offers[] = $offer;
        }
        if ($prices) {
            return [
                '@type'         => 'AggregateOffer',
                'priceCurrency' => $currency,
                'lowPrice'      => (string) min($prices),
                'highPrice'     => (string) max($prices),
                'offerCount'    => count($offers),
                'offers'        => $offers,
            ];
        }
    }

    // Simple / fallback path — single Offer.
    return [
        '@type'           => 'Offer',
        'url'             => $url,
        'priceCurrency'   => $currency,
        'price'           => (string) $product->get_price(),
        'priceValidUntil' => $valid_until,
        'availability'    => $availability,
        'itemCondition'   => 'https://schema.org/NewCondition',
        'seller'          => $seller,
    ];
}

/* ------------------------------------------------------------------
 * JSON-LD: Product (single product page)
 * Emits a Navigate-flavored Product schema with compound properties.
 * ----------------------------------------------------------------*/
add_action('wp_head', function () {
    if (nav_seo_has_competing_plugin()) return;
    if (!is_singular('product') || !function_exists('wc_get_product')) return;

    $product = wc_get_product(get_the_ID());
    if (!$product) return;

    $url       = get_permalink($product->get_id());
    $raw_sku   = $product->get_sku();
    $cas       = get_post_meta($product->get_id(), '_nav_cas_number', true);
    $mw        = get_post_meta($product->get_id(), '_nav_molecular_weight', true);
    $sequence  = get_post_meta($product->get_id(), '_nav_sequence', true);
    $purity    = get_post_meta($product->get_id(), '_nav_purity', true);
    $form      = get_post_meta($product->get_id(), '_nav_form', true);

    // Google Product rich-result guidelines recommend image as an
    // array with multiple renditions. We emit every WP-registered size
    // that yields a URL — typically 'medium', 'large', 'full'. Falling
    // back to the branded vial SVG when no thumbnail is set.
    $image_urls = [];
    if (has_post_thumbnail($product->get_id())) {
        foreach (['large', 'medium_large', 'full'] as $size) {
            $u = get_the_post_thumbnail_url($product->get_id(), $size);
            if ($u && !in_array($u, $image_urls, true)) {
                $image_urls[] = $u;
            }
        }
    }
    if (empty($image_urls)) {
        $image_urls[] = get_template_directory_uri() . '/assets/images/vial-brand.svg';
    }

    // additionalProperty — chemical/physical attributes
    $additional = [];
    if ($cas)      $additional[] = ['@type' => 'PropertyValue', 'name' => 'CAS Number',          'value' => $cas];
    if ($mw)       $additional[] = ['@type' => 'PropertyValue', 'name' => 'Molecular Weight',    'value' => $mw];
    if ($sequence) $additional[] = ['@type' => 'PropertyValue', 'name' => 'Amino Acid Sequence', 'value' => $sequence];
    if ($purity)   $additional[] = ['@type' => 'PropertyValue', 'name' => 'Purity',              'value' => $purity];
    if ($form)     $additional[] = ['@type' => 'PropertyValue', 'name' => 'Form',                'value' => $form];

    $subtitle = get_post_meta($product->get_id(), '_nav_technical_subtitle', true);

    // Description: run through compliance scrub so risky short_description
    // text never ships in the Product schema (matches meta description path).
    $raw_desc = $product->get_short_description()
        ?: $product->get_description()
        ?: ($subtitle ?: 'Research-grade peptide compound for laboratory investigation.');
    $description = nav_seo_scrub(nav_seo_trim($raw_desc, 60));
    if (trim($description) === '') {
        $description = 'Research-grade peptide compound for laboratory investigation.';
    }

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Product',
        '@id'         => $url . '#product',
        'name'        => nav_seo_scrub_name($product->get_name()),
        'url'         => $url,
        'image'       => $image_urls,
        'description' => $description,
        // Brand chains to the Organization node via @id so the
        // knowledge graph sees one entity, not two with matching names.
        'brand'       => [
            '@type' => 'Brand',
            '@id'   => home_url('/') . '#organization',
            'name'  => NAV_SEO_SITE_NAME,
        ],
        'category'    => 'Research Peptide Compound',
        'offers'      => nav_seo_product_offers($product, $url),
    ];

    // Only ship SKU + MPN when admin supplied them. Our synthesized
    // 'NAV-{id}' fallback is useful for internal references but Google
    // can downrank Product rich results that carry a synthesized SKU
    // (it looks identifier-y but isn't manufacturer-assigned).
    if ($raw_sku) {
        $schema['sku'] = $raw_sku;
        $schema['mpn'] = $raw_sku;
    }

    if ($additional) {
        $schema['additionalProperty'] = $additional;
    }

    // Disambiguating description — pull from the single source of truth
    // so the schema-embedded reminder matches the canonical RUO line we
    // render on every page. Drift here trips processor scanners that
    // cross-check page text against JSON-LD.
    $schema['disambiguatingDescription'] = nav_get_disclaimer('sitewide');

    echo nav_seo_json_ld($schema);
}, 10);

/* ------------------------------------------------------------------
 * Suppress WooCommerce's own Product schema on single product pages —
 * we emit a richer, compliance-scrubbed one at priority 10 above.
 * Leaving Woo's schema in place causes Google to see two Product nodes
 * with different shapes, which trips warnings in rich-result testing.
 * ----------------------------------------------------------------*/
add_filter('woocommerce_structured_data_type_for_page', function ($type) {
    if (!is_array($type)) {
        return [];
    }
    if (is_singular('product')) {
        // Drop the 'product' + 'review' entries so Woo's default Product
        // emitter stays silent; keep everything else (breadcrumb, order).
        return array_values(array_diff($type, ['product', 'review']));
    }
    return $type;
});

/* ------------------------------------------------------------------
 * Scrub WooCommerce's default Product schema for compliance.
 * WooCommerce emits its own; ensure description is stripped of health claims
 * and category is research-appropriate.
 * ----------------------------------------------------------------*/
add_filter('woocommerce_structured_data_product', function ($markup) {
    if (!empty($markup['description'])) {
        $markup['description'] = wp_strip_all_tags($markup['description']);
    }
    $markup['category'] = 'Research Peptide Compound';
    return $markup;
});

/* ------------------------------------------------------------------
 * JSON-LD: Article (single research post)
 * ----------------------------------------------------------------*/
add_action('wp_head', function () {
    if (nav_seo_has_competing_plugin()) return;
    if (!is_singular(['post', 'nav_research'])) return;

    $url       = get_permalink();
    $title     = get_the_title();
    $published = get_the_date('c');
    $modified  = get_the_modified_date('c');

    $author_id = (int) get_post_field('post_author', get_the_ID());

    // If post_author is 0 (imported / orphaned content), attribute to the
    // Organization rather than synthesizing a Person named after the site.
    if ($author_id > 0) {
        $author = [
            '@type' => 'Person',
            'name'  => get_the_author_meta('display_name', $author_id) ?: NAV_SEO_SITE_NAME,
            'url'   => get_author_posts_url($author_id),
        ];
    } else {
        error_log('[nav_seo] article ' . get_the_ID() . ' has no author; attributing to Organization');
        $author = ['@id' => home_url('/') . '#organization'];
    }

    // Article image — emit array of available sizes (Google's Article
    // rich-result guidelines recommend multiple aspect renditions). When
    // there's no featured image, fall back to the OG social-share render
    // at 1200x630 (replaces the legacy 1.9MB hero-three-vials.png).
    $image_urls = [];
    if (has_post_thumbnail()) {
        foreach (['large', 'medium_large', 'full'] as $size) {
            $u = get_the_post_thumbnail_url(null, $size);
            if ($u && !in_array($u, $image_urls, true)) {
                $image_urls[] = $u;
            }
        }
    }
    if (empty($image_urls)) {
        $image_urls[] = get_template_directory_uri() . '/assets/images/og-social-share.png';
    }

    // Guard against empty description — Google's Article guidelines require it.
    $desc = nav_seo_description();
    if (trim($desc) === '') $desc = NAV_SEO_DEFAULT_DESC;

    // Strip Gutenberg block comments before counting so wordCount isn't inflated.
    $raw_content  = (string) get_post_field('post_content', get_the_ID());
    $stripped     = preg_replace('/<!--\s*\/?wp:[^>]*-->/', '', $raw_content);
    $word_count   = str_word_count(wp_strip_all_tags((string) $stripped));
    $reading_time = max(1, (int) round($word_count / 200));

    $schema = [
        '@context'         => 'https://schema.org',
        '@type'            => 'Article',
        '@id'              => $url . '#article',
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url],
        'headline'         => $title,
        'description'      => $desc,
        'image'            => $image_urls,
        'datePublished'    => $published,
        'dateModified'     => $modified,
        'author'           => $author,
        'publisher'        => ['@id' => home_url('/') . '#organization'],
        'wordCount'        => $word_count,
        'timeRequired'     => 'PT' . $reading_time . 'M',
        'inLanguage'       => get_locale() ?: 'en-US',
        // RUO context for processor scanners that crawl schema bodies —
        // matches what Product schema carries on the storefront side.
        'disambiguatingDescription' => function_exists('nav_get_disclaimer')
            ? nav_get_disclaimer('sitewide')
            : '',
    ];

    // Resolve articleSection + keywords from whichever taxonomy applies.
    if (is_singular('nav_research')) {
        $cats = get_the_terms(get_the_ID(), 'research_category');
        $tags = get_the_terms(get_the_ID(), 'research_tag') ?: get_the_tags();
    } else {
        $cats = get_the_category();
        $tags = get_the_tags();
    }
    if ($cats && !is_wp_error($cats) && !empty($cats[0])) {
        $schema['articleSection'] = nav_seo_scrub_name($cats[0]->name);
    }
    if ($tags && !is_wp_error($tags)) {
        $scrubbed_tags = array_map('nav_seo_scrub_name', wp_list_pluck($tags, 'name'));
        $schema['keywords'] = implode(', ', $scrubbed_tags);
    }

    echo nav_seo_json_ld($schema);
}, 11);

/* ------------------------------------------------------------------
 * JSON-LD: CollectionPage (product category + research archives)
 * ----------------------------------------------------------------*/
add_action('wp_head', function () {
    if (nav_seo_has_competing_plugin()) return;
    if (!is_product_category() && !is_shop() && !is_post_type_archive('product')
        && !is_category() && !is_tax()) {
        return;
    }

    $term = get_queried_object();
    $url  = nav_seo_canonical_url();
    if (!$url) return;  // Don't emit a schema with an empty @id.

    $name = nav_seo_scrub_name($term && isset($term->name) ? $term->name : 'Compounds');

    // Include paged suffix in @id so page 2, 3, ... don't collide with page 1.
    $paged = max(1, (int) get_query_var('paged'));
    $id    = $url . '#collection' . ($paged > 1 ? '-' . $paged : '');

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'CollectionPage',
        '@id'         => $id,
        'url'         => $url,
        'name'        => $name,
        'description' => nav_seo_description(),
        'isPartOf'    => ['@id' => home_url('/') . '#website'],
        // RUO context for processor scanners — present on every
        // collection node so the storefront's category index pages
        // carry the same compliance signal as individual PDPs.
        'disambiguatingDescription' => function_exists('nav_get_disclaimer')
            ? nav_get_disclaimer('sitewide')
            : '',
    ];

    // Build a lightweight ItemList of the products on this page so
    // search engines can crawl the collection's contents inline.
    // Skips work on /shop/ root + on archives with no posts.
    if (function_exists('is_product_category') && (is_product_category() || is_shop() || is_post_type_archive('product'))) {
        global $wp_query;
        if (!empty($wp_query->posts)) {
            $list_items = [];
            $position   = 1;
            foreach ($wp_query->posts as $post) {
                $list_items[] = [
                    '@type'    => 'ListItem',
                    'position' => $position++,
                    'url'      => get_permalink($post->ID),
                    'name'     => nav_seo_scrub_name(get_the_title($post)),
                ];
                if ($position > 30) break;  // cap so the schema stays small
            }
            if ($list_items) {
                $schema['mainEntity'] = [
                    '@type'           => 'ItemList',
                    'itemListElement' => $list_items,
                    'numberOfItems'   => count($list_items),
                ];
            }
        }
    }

    echo nav_seo_json_ld($schema);
}, 12);

/* ------------------------------------------------------------------
 * Image SEO: auto-fill alt text where missing, from post title context.
 * ----------------------------------------------------------------*/
add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment) {
    if (!empty($attr['alt'])) return $attr;

    $parent_id = (int) $attachment->post_parent;
    if ($parent_id) {
        $parent = get_post($parent_id);
        if ($parent) {
            $attr['alt'] = $parent->post_title;
        }
    }
    if (empty($attr['alt']) && $attachment->post_title) {
        $attr['alt'] = $attachment->post_title;
    }
    return $attr;
}, 10, 2);

/* ------------------------------------------------------------------
 * robots.txt additions — sitemap + friendly crawl rules.
 * WordPress core handles basic robots; we append the sitemap hint.
 * ----------------------------------------------------------------*/
add_filter('robots_txt', function ($output, $public) {
    if (!$public) return $output;

    $output .= "\n";
    $output .= "# Block admin + checkout + cart — not useful for indexing\n";
    $output .= "Disallow: /wp-admin/\n";
    $output .= "Disallow: /checkout/\n";
    $output .= "Disallow: /cart/\n";
    $output .= "Disallow: /my-account/\n";
    $output .= "Disallow: /?s=\n";
    $output .= "Disallow: /*?add-to-cart=\n";
    $output .= "Disallow: /*?orderby=\n";
    $output .= "\n";
    $output .= "# Allow all static assets\n";
    $output .= "Allow: /wp-content/uploads/\n";
    $output .= "Allow: /wp-content/themes/navigate-peptides/assets/\n";
    $output .= "\n";

    // Advertise whatever sitemap actually ships. WP core provides
    // /wp-sitemap.xml when enabled; common SEO plugins (Yoast, RankMath)
    // install their own at /sitemap_index.xml and disable the core one.
    // Emit both hints and let crawlers pick — extra sitemap lines are
    // valid per the robots.txt spec.
    //
    // Always advertise /wp-sitemap.xml as the canonical hint even if
    // wp_sitemaps_get_server() returns falsy at this moment — the
    // server can re-enable later via a plugin or filter, and a stale
    // 404 hint is harmless while a missing hint loses crawl signal.
    $sitemaps = [home_url('/wp-sitemap.xml')];
    // Filterable so plugins (or Ian) can override / add more.
    $sitemaps = apply_filters('nav_seo_robots_sitemaps', $sitemaps);
    foreach ($sitemaps as $url) {
        $output .= "Sitemap: {$url}\n";
    }
    return $output;
}, 10, 2);

/* ------------------------------------------------------------------
 * Meta description field in post editor (for per-page override).
 * Appears on posts + pages + products.
 * ----------------------------------------------------------------*/
add_action('add_meta_boxes', function () {
    $screens = ['post', 'page', 'product'];
    foreach ($screens as $screen) {
        add_meta_box(
            'nav_seo_meta',
            __('SEO — Navigate Peptides', 'navigate-peptides'),
            'nav_seo_meta_box_render',
            $screen,
            'normal',
            'low'
        );
    }
});

function nav_seo_meta_box_render(WP_Post $post): void {
    wp_nonce_field('nav_seo_meta_save', 'nav_seo_meta_nonce');
    $value = get_post_meta($post->ID, '_nav_meta_description', true);
    $length = strlen($value);
    ?>
    <p>
        <label for="nav_meta_description" style="display:block;font-weight:600;margin-bottom:6px;">
            <?php esc_html_e('Meta Description (overrides auto-generated)', 'navigate-peptides'); ?>
        </label>
        <textarea
            id="nav_meta_description"
            name="nav_meta_description"
            rows="3"
            style="width:100%;max-width:640px;"
            maxlength="220"
            placeholder="<?php esc_attr_e('Leave blank to auto-generate from excerpt / product subtitle.', 'navigate-peptides'); ?>"
        ><?php echo esc_textarea($value); ?></textarea>
        <span class="description" style="display:block;margin-top:4px;">
            <?php
            printf(
                /* translators: %d: current character count */
                esc_html__('Current length: %d / 220 characters. Target 150–160 for optimal SERP display.', 'navigate-peptides'),
                (int) $length
            );
            ?>
        </span>
    </p>
    <p>
        <label for="nav_og_image_id" style="display:block;font-weight:600;margin-bottom:6px;">
            <?php esc_html_e('Social Share Image (Open Graph)', 'navigate-peptides'); ?>
        </label>
        <?php
        $og_image_id = (int) get_post_meta($post->ID, '_nav_og_image_id', true);
        $og_preview  = $og_image_id ? wp_get_attachment_image($og_image_id, [120, 63], false, ['style' => 'border:1px solid #ccd0d4;border-radius:3px;']) : '';
        ?>
        <input type="hidden" id="nav_og_image_id" name="nav_og_image_id" value="<?php echo esc_attr((string) $og_image_id); ?>">
        <span id="nav-og-image-preview" style="display:inline-block;vertical-align:middle;margin-right:8px;"><?php echo $og_preview; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image returns safe HTML ?></span>
        <button type="button" class="button" id="nav-og-image-select"><?php esc_html_e('Choose image', 'navigate-peptides'); ?></button>
        <button type="button" class="button-link" id="nav-og-image-clear" <?php if (!$og_image_id) echo 'style="display:none;"'; ?>><?php esc_html_e('Remove', 'navigate-peptides'); ?></button>
        <span class="description" style="display:block;margin-top:4px;">
            <?php esc_html_e('1200×630 recommended. Leave blank to use the featured image or theme fallback.', 'navigate-peptides'); ?>
        </span>
        <script>
        (function () {
            if (typeof wp === 'undefined' || !wp.media) return;
            var btn = document.getElementById('nav-og-image-select');
            var clr = document.getElementById('nav-og-image-clear');
            var hid = document.getElementById('nav_og_image_id');
            var pre = document.getElementById('nav-og-image-preview');
            if (!btn || !hid) return;
            var frame;
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (frame) { frame.open(); return; }
                frame = wp.media({ title: 'Select Social Share Image', multiple: false, library: { type: 'image' } });
                frame.on('select', function () {
                    var att = frame.state().get('selection').first().toJSON();
                    hid.value = att.id;
                    pre.innerHTML = '<img src="' + att.sizes.thumbnail.url + '" style="max-width:120px;height:auto;border:1px solid #ccd0d4;border-radius:3px;">';
                    clr.style.display = '';
                });
                frame.open();
            });
            clr.addEventListener('click', function (e) {
                e.preventDefault();
                hid.value = '';
                pre.innerHTML = '';
                clr.style.display = 'none';
            });
        })();
        </script>
    </p>
    <p>
        <strong><?php esc_html_e('Compliance reminder:', 'navigate-peptides'); ?></strong>
        <?php esc_html_e('No human-use framing, no health claims, no performance/wellness language.', 'navigate-peptides'); ?>
    </p>
    <?php
}

add_action('save_post', function (int $post_id) {
    if (!isset($_POST['nav_seo_meta_nonce'])) return;
    if (!wp_verify_nonce(
        sanitize_text_field(wp_unslash($_POST['nav_seo_meta_nonce'])),
        'nav_seo_meta_save'
    )) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['nav_meta_description'])) {
        $value = sanitize_textarea_field(wp_unslash($_POST['nav_meta_description']));
        if ($value === '') {
            delete_post_meta($post_id, '_nav_meta_description');
        } else {
            $ok = update_post_meta($post_id, '_nav_meta_description', $value);
            if ($ok === false && function_exists('nav_admin_warn')) {
                nav_admin_warn(
                    'seo_meta_description_' . $post_id,
                    sprintf(
                        'SEO meta description failed to save for post #%d — page will fall back to the site-wide default.',
                        $post_id
                    )
                );
            }
        }
    }
    if (isset($_POST['nav_og_image_id'])) {
        $og_id = (int) $_POST['nav_og_image_id'];
        if ($og_id > 0 && get_post_type($og_id) === 'attachment') {
            update_post_meta($post_id, '_nav_og_image_id', $og_id);
        } else {
            delete_post_meta($post_id, '_nav_og_image_id');
        }
    }
});

/* Load the WP media-frame JS on the post editor screens where the
   SEO meta box ships an "Choose image" button. */
add_action('admin_enqueue_scripts', function ($hook) {
    if (in_array($hook, ['post.php', 'post-new.php'], true)) {
        wp_enqueue_media();
    }
});

/* ------------------------------------------------------------------
 * Preload hero images on single product + homepage for LCP.
 * Prefers WebP — matches the <picture> source order in templates.
 * ----------------------------------------------------------------*/
add_action('wp_head', function () {
    if (is_front_page()) {
        $theme_uri = get_template_directory_uri();
        $theme_dir = get_template_directory();
        // Preload the vial GLB so WebGL can start decoding it before the
        // model-viewer script even registers. The SVG preload here used to
        // hand the branded SVG to the browser for the old hero composition
        // — that asset isn't used on the homepage anymore (we ship the
        // rotating 3D vial directly), so preloading it was wasteful AND it
        // left the SVG cached in browsers that had seen the old markup.
        $glb_rel = '/assets/models/vial-ghk-cu.glb';
        $glb_ver = function_exists('nav_asset_version')
            ? nav_asset_version(ltrim($glb_rel, '/'))
            : '';
        $glb_url = $theme_uri . $glb_rel . ($glb_ver ? '?v=' . $glb_ver : '');
        echo '<link rel="preload" as="fetch" type="model/gltf-binary" '
            . 'href="' . esc_url($glb_url) . '" crossorigin="anonymous" '
            . 'fetchpriority="high">' . "\n";
        return;
    }

    if (is_singular('product')) {
        // Preload the product's 3D model so WebGL can start decoding it
        // before model-viewer registers — parity with the home hero.
        // Only emit for https URLs (mixed-content blocks would waste the
        // preload budget on a request the browser will refuse to use).
        $glb = get_post_meta(get_the_ID(), '_nav_3d_model_url', true);
        if (function_exists('nav_safe_glb_url')) {
            $glb = nav_safe_glb_url($glb, (int) get_the_ID());
        }
        if ($glb) {
            echo '<link rel="preload" as="fetch" type="model/gltf-binary" '
                . 'href="' . esc_url($glb) . '" crossorigin="anonymous" '
                . 'fetchpriority="high">' . "\n";
        }

        if (has_post_thumbnail()) {
            $src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'product-hero');
            if ($src) {
                echo '<link rel="preload" as="image" href="' . esc_url($src[0]) . '" fetchpriority="high">' . "\n";
            }
        }
    }
}, 3);
