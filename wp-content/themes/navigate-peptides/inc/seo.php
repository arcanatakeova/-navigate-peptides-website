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
define('NAV_SEO_DEFAULT_DESC', 'Research-grade peptide compounds with third-party verified certificates of analysis. Supplied for controlled laboratory environments. All products intended for research and identification purposes only.');

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
        error_log(sprintf(
            '[nav_seo] JSON-LD encode failed: err=%s @type=%s url=%s',
            json_last_error_msg(),
            $schema['@type'] ?? 'unknown',
            $_SERVER['REQUEST_URI'] ?? ''
        ));
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
        '/\bcycle(s|d)?\b/i'                       => 'phase$1',
        '/\bstack(s|ed|ing)?\b/i'                  => 'combination$1',
        '/\bbioavailab(le|ility)\b/i'              => 'structural propert$1',
        '/\bpotenc(y|ies)\b/i'                     => 'molecular activit$1',
        '/\befficacy\b/i'                          => 'mechanism profile',
    ];
    return (string) preg_replace(array_keys($map), array_values($map), $text);
}

/**
 * Scrub a short identifier-like string (product name, term name, tag name)
 * for use in schema/OG/Twitter output. Same map as nav_seo_scrub but
 * short-circuits empty input and returns the original on regex failure.
 */
function nav_seo_scrub_name($text): string {
    $text = (string) $text;
    if ($text === '') return $text;
    $scrubbed = nav_seo_scrub($text);
    return $scrubbed ?: $text;
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

    // Single product: compound-specific scientific description
    if (is_singular('product') && function_exists('wc_get_product')) {
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
    } elseif (is_singular('post')) {
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

    // Theme fallback — branded hero. WebP first for modern, PNG for OG crawlers.
    return [
        'url'    => get_template_directory_uri() . '/assets/images/hero-three-vials.png',
        'width'  => 1200,
        'height' => 630,
        'alt'    => 'Navigate Peptides — research-grade peptide vials',
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

add_filter('document_title_separator', fn() => '|');

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

    // Performance — dns-prefetch / preconnect for analytical data + fonts
    // (fonts.googleapis already preconnected in header.php)
    echo '<link rel="dns-prefetch" href="//www.google-analytics.com">' . "\n";
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
    } elseif (is_singular('post')) {
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
    if (is_singular('post')) {
        $published = get_the_date('c');
        $modified  = get_the_modified_date('c');
        echo '<meta property="article:published_time" content="' . esc_attr($published) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr($modified) . '">' . "\n";

        $cats = get_the_category();
        foreach ($cats as $cat) {
            echo '<meta property="article:section" content="' . esc_attr(nav_seo_scrub_name($cat->name)) . '">' . "\n";
        }

        $tags = get_the_tags();
        if ($tags) {
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

    // Organization
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
        'sameAs'      => apply_filters('nav_seo_same_as', []),
    ];

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
    } elseif (is_singular('post')) {
        $add('Research', home_url('/research/'));
        $cats = get_the_category();
        if ($cats && !empty($cats[0])) {
            $add($cats[0]->name, (string) get_category_link($cats[0]->term_id));
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
    $sku       = $raw_sku ?: 'NAV-' . $product->get_id();
    $cas       = get_post_meta($product->get_id(), '_nav_cas_number', true);
    $mw        = get_post_meta($product->get_id(), '_nav_molecular_weight', true);
    $sequence  = get_post_meta($product->get_id(), '_nav_sequence', true);
    $purity    = get_post_meta($product->get_id(), '_nav_purity', true);
    $form      = get_post_meta($product->get_id(), '_nav_form', true);

    $image_url = '';
    if (has_post_thumbnail($product->get_id())) {
        $image_url = get_the_post_thumbnail_url($product->get_id(), 'large');
    }
    if (!$image_url) {
        // Branded vial SVG is the fallback — replaces the raster PNG that
        // previously shipped. Schema.org allows SVG for Product.image.
        $image_url = get_template_directory_uri() . '/assets/images/vial-brand.svg';
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
        'sku'         => $sku,
        'url'         => $url,
        'image'       => $image_url,
        'description' => $description,
        'brand'       => [
            '@type' => 'Brand',
            'name'  => NAV_SEO_SITE_NAME,
        ],
        'category'    => 'Research Peptide Compound',
        'offers'      => [
            '@type'           => 'Offer',
            'url'             => $url,
            'priceCurrency'   => get_woocommerce_currency(),
            'price'           => (string) $product->get_price(),
            'priceValidUntil' => gmdate('Y-m-d', strtotime('+1 year')),
            'availability'    => $product->is_in_stock()
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'itemCondition'   => 'https://schema.org/NewCondition',
            'seller'          => ['@id' => home_url('/') . '#organization'],
        ],
    ];

    // Only include MPN when admin supplied a SKU — Google Merchant requires
    // MPN to be manufacturer-assigned, not our synthesized fallback.
    if ($raw_sku) {
        $schema['mpn'] = $raw_sku;
    }

    if ($additional) {
        $schema['additionalProperty'] = $additional;
    }

    // Disambiguating description — compliance reminder embedded in schema
    $schema['disambiguatingDescription'] = 'For research and identification purposes only. Not for human or veterinary use.';

    echo nav_seo_json_ld($schema);
}, 10);

/* ------------------------------------------------------------------
 * Suppress WooCommerce's own Product schema on single product pages —
 * we emit a richer, compliance-scrubbed one at priority 10 above.
 * Leaving Woo's schema in place causes Google to see two Product nodes
 * with different shapes, which trips warnings in rich-result testing.
 * ----------------------------------------------------------------*/
add_filter('woocommerce_structured_data_type_for_page', function ($type) {
    if (is_singular('product')) {
        // Return false-y to skip Woo's default Product emitter.
        return '';
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
    if (!is_singular('post')) return;

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

    $image_url = '';
    if (has_post_thumbnail()) {
        $image_url = get_the_post_thumbnail_url(null, 'large');
    }
    if (!$image_url) {
        $image_url = get_template_directory_uri() . '/assets/images/hero-three-vials.png';
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
        'image'            => $image_url,
        'datePublished'    => $published,
        'dateModified'     => $modified,
        'author'           => $author,
        'publisher'        => ['@id' => home_url('/') . '#organization'],
        'wordCount'        => $word_count,
        'timeRequired'     => 'PT' . $reading_time . 'M',
        'inLanguage'       => get_locale() ?: 'en-US',
    ];

    $cats = get_the_category();
    if ($cats && !empty($cats[0])) {
        $schema['articleSection'] = nav_seo_scrub_name($cats[0]->name);
    }

    $tags = get_the_tags();
    if ($tags) {
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
    ];

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
    // Emit both hints and let crawlers pick — extra sitemap lines are valid.
    $sitemaps = [];
    if (function_exists('wp_sitemaps_get_server') && wp_sitemaps_get_server()) {
        $sitemaps[] = home_url('/wp-sitemap.xml');
    }
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
            update_post_meta($post_id, '_nav_meta_description', $value);
        }
    }
});

/* ------------------------------------------------------------------
 * Preload hero images on single product + homepage for LCP.
 * Prefers WebP — matches the <picture> source order in templates.
 * ----------------------------------------------------------------*/
add_action('wp_head', function () {
    if (is_front_page()) {
        $theme_uri = get_template_directory_uri();
        // Branded vial SVG is the LCP hero — ~8KB, loads inline in one round
        // trip, no WebGL or model-viewer CDN dependency. `type="image/svg+xml"`
        // so browsers apply the correct MIME-based decode path.
        echo '<link rel="preload" as="image" type="image/svg+xml" href="' .
            esc_url($theme_uri . '/assets/images/vial-brand.svg') .
            '" fetchpriority="high">' . "\n";
        return;
    }

    if (is_singular('product') && has_post_thumbnail()) {
        $src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'product-hero');
        if ($src) {
            echo '<link rel="preload" as="image" href="' . esc_url($src[0]) . '" fetchpriority="high">' . "\n";
        }
    }
}, 3);
