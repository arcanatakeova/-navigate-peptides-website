<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo esc_url(get_template_directory_uri() . '/assets/images/favicon.svg'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url(get_template_directory_uri() . '/assets/images/favicon-32.png'); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url(get_template_directory_uri() . '/assets/images/apple-touch-icon.png'); ?>">
    <meta name="theme-color" content="#0a100e">
    <?php
    // Google model-viewer loads on:
    //   - Home (hero always ships the rotating vial.glb)
    //   - Quality page (interactive 3-vial trio in template-quality.php)
    //   - About page (editorial 3D hero in template-about.php)
    //   - Single-product pages where admin has set _nav_3d_model_url
    //
    // is_page_template() was returning false on wp.com Atomic even when
    // template-quality.php was being rendered (likely because the page's
    // _wp_page_template meta wasn't persisted through the page-creation
    // flow we used). Using the $template global directly — it's set to
    // the template file actually being rendered by the time header.php
    // runs, which is reliable regardless of how the page was created.
    global $template;
    $nav_tpl_basename = isset($template) ? basename((string) $template) : '';
    $nav_load_model_viewer = is_front_page()
        || in_array($nav_tpl_basename, ['template-quality.php', 'template-about.php'], true);
    if (!$nav_load_model_viewer && function_exists('is_product') && is_product()) {
        // Single-product always needs it (hero + related cards).
        $nav_load_model_viewer = true;
    }
    if (!$nav_load_model_viewer
        && function_exists('is_shop')
        && (is_shop() || is_product_category() || is_product_tag())
    ) {
        // Archive cards render inline <model-viewer> posters so the
        // thumbnail matches the hero rendering pixel-for-pixel.
        $nav_load_model_viewer = true;
    }
    if ($nav_load_model_viewer) :
    ?>
    <!-- Loaded only when this single-product page has a GLB configured.
         Version pinned at 4.0.0. crossorigin + no-referrer reduce CDN
         fingerprinting. -->
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/4.0.0/model-viewer.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <?php endif; ?>

    <!-- JS-required UI elements — hide their entry-point buttons when JS is
         disabled so visitors don't click dead controls. -->
    <noscript>
        <style>
            .nav-announcement__close,
            .nav-header__search-toggle,
            .nav-search-form__close { display: none !important; }
            .nav-header__search { max-height: none; }
            .nav-header__search[aria-hidden="true"] { max-height: none; }
        </style>
    </noscript>
    <?php wp_head(); ?>
</head>
<body <?php body_class('nav-body'); ?>>
<?php wp_body_open(); ?>

<a class="nav-skip-link" href="#main-content"><?php esc_html_e('Skip to content', 'navigate-peptides'); ?></a>

<!-- Announcement Bar — editable via Customizer or filter -->
<?php
$announcement = apply_filters('navigate_announcement_text', 'Free shipping on research orders over $150 — All compounds include Certificate of Analysis');
if ($announcement) :
?>
<div class="nav-announcement" id="nav-announcement">
    <div class="nav-container">
        <p><?php echo esc_html($announcement); ?></p>
    </div>
    <button class="nav-announcement__close" id="nav-announcement-close" aria-label="<?php esc_attr_e('Close announcement', 'navigate-peptides'); ?>" type="button">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
<?php endif; ?>

<header class="nav-header" id="nav-header">
    <div class="nav-container nav-header__inner">

        <!-- Logo -->
        <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-header__logo" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?> — Home">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <span class="nav-header__logo-icon">
                    <svg viewBox="0 0 213.135 232.253" preserveAspectRatio="xMidYMid meet" fill="none" xmlns="http://www.w3.org/2000/svg" class="nav-icon" style="width:34px;height:34px;" aria-hidden="true" focusable="false">
                        <g transform="translate(0.135 232) scale(0.1 -0.1)" fill="currentColor">
                            <path d="M5 2299c4 -12 38 -87 76 -166 38 -80 66 -150 62 -155 -3 -6 -2 -8 4 -5 5 3 18 -13 28 -36 31 -70 75 -166 130 -282 29 -60 65 -137 79 -170 30 -68 63 -130 69 -130 8 0 134 83 150 99 14 15 14 19 1 44 -45 85 -113 246 -107 255 3 6 1 7 -5 3 -11 -7 -16 7 -15 41 1 7 -3 11 -7 8 -4 -3 -15 13 -23 34 -20 48 -75 170 -89 196 -6 11 -24 50 -39 87l-28 68 609 0 608 0 32 -56c17 -31 32 -65 33 -75 1 -10 3 -16 6 -14 8 9 100 -176 94 -190 -3 -8 -10 -12 -16 -8 -6 3 -7 1 -3 -5 4 -7 -5 -35 -20 -62 -15 -28 -25 -52 -23 -53 5 -4 159 -48 177 -50 7 -1 38 45 68 102l55 104 -57 111c-32 61 -65 125 -74 142 -29 50 -54 99 -55 104 -1 3 -11 22 -24 43l-23 37 -840 0 -839 0 6 -21z"/>
                            <path d="M1900 2315c0 -2 26 -47 59 -99 55 -90 59 -95 75 -78 9 10 16 23 16 28 0 5 18 40 40 78 22 37 40 70 40 72 0 2 -52 4 -115 4 -63 0 -115 -2 -115 -5z"/>
                            <path d="M1385 1644c-299 -44 -555 -154 -803 -344 -104 -80 -237 -218 -303 -315 -30 -44 -56 -82 -59 -85 -9 -9 -42 -84 -61 -137 -16 -47 -16 -54 -3 -68 9 -8 20 -15 27 -15 6 0 40 33 76 72 176 193 318 320 489 435 111 74 315 167 442 201 117 32 248 54 255 43 2 -4 -5 -24 -15 -45 -9 -21 -20 -35 -22 -30 -3 5 -3 3 -2 -4 2 -7 -5 -32 -15 -55 -41 -89 -129 -284 -146 -324 -10 -23 -21 -40 -24 -38 -3 1 -7 -7 -7 -18 -1 -12 -41 -111 -89 -220 -87 -195 -89 -197 -101 -168 -7 17 -20 45 -28 63 -101 217 -194 433 -190 440 3 4 -8 0 -24 -9 -16 -10 -55 -33 -86 -52 -32 -19 -60 -39 -62 -46 -3 -7 9 -43 27 -81 17 -38 69 -154 115 -259 47 -104 91 -203 99 -220 7 -16 23 -51 34 -77 30 -69 55 -126 80 -180 11 -26 21 -51 21 -55 0 -5 9 -19 19 -32l18 -24 22 49c13 27 31 67 42 90 10 23 19 44 19 47 0 3 16 39 35 80 19 41 35 80 35 86 0 7 5 9 12 5 7 -4 8 -3 4 4 -4 6 4 33 17 59 13 26 30 65 36 86 8 22 17 34 23 31 6 -4 8 -2 5 3 -4 6 34 101 83 212 50 111 90 207 90 212 0 6 5 7 12 3 7 -4 8 -3 4 4 -4 7 -1 24 7 39 22 41 103 225 111 251 4 12 12 19 18 16 6 -4 8 -2 5 3 -5 8 34 113 49 132 7 8 105 -32 167 -68l67 -39 45 56c25 31 45 60 45 64 0 20 -127 116 -202 153 -49 24 -88 42 -88 40 0 -2 -21 3 -47 10 -49 15 -224 27 -278 19z"/>
                        </g>
                    </svg>
                </span>
                <span class="nav-header__logo-text-group">
                    <span class="nav-header__logo-name">Navigate</span>
                    <span class="nav-header__logo-divider"></span>
                    <span class="nav-header__logo-name nav-header__logo-name--light">Peptides</span>
                </span>
            <?php endif; ?>
        </a>

        <!-- Desktop Navigation -->
        <nav class="nav-header__nav" id="nav-desktop-menu" aria-label="Primary navigation">
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'nav-header__menu',
                    'depth'          => 2,
                    'fallback_cb'    => false,
                ]);
            } else {
                // Default menu when none is assigned
                ?>
                <ul class="nav-header__menu">
                    <li class="menu-item menu-item-has-children">
                        <a href="<?php echo esc_url(home_url('/compounds/')); ?>"><?php esc_html_e('Compounds', 'navigate-peptides'); ?></a>
                        <ul class="sub-menu">
                            <li><a href="<?php echo esc_url(nav_get_product_cat_url('metabolic-research')); ?>"><?php esc_html_e('Metabolic Research', 'navigate-peptides'); ?></a></li>
                            <li><a href="<?php echo esc_url(nav_get_product_cat_url('cellular-research')); ?>"><?php esc_html_e('Cellular Research', 'navigate-peptides'); ?></a></li>
                            <li><a href="<?php echo esc_url(nav_get_product_cat_url('tissue-repair-research')); ?>"><?php esc_html_e('Tissue Repair Research', 'navigate-peptides'); ?></a></li>
                            <li><a href="<?php echo esc_url(nav_get_product_cat_url('hormonal-signaling-research')); ?>"><?php esc_html_e('Hormonal Signaling Research', 'navigate-peptides'); ?></a></li>
                            <li><a href="<?php echo esc_url(nav_get_product_cat_url('cognitive-research')); ?>"><?php esc_html_e('Cognitive Research', 'navigate-peptides'); ?></a></li>
                            <li><a href="<?php echo esc_url(nav_get_product_cat_url('dermal-research')); ?>"><?php esc_html_e('Dermal Research', 'navigate-peptides'); ?></a></li>
                        </ul>
                    </li>
                    <li class="menu-item menu-item-has-children">
                        <a href="<?php echo esc_url(home_url('/research/')); ?>">Research</a>
                        <ul class="sub-menu">
                            <li><a href="<?php echo esc_url(home_url('/research/topic/intelligence/')); ?>">Research Intelligence</a></li>
                            <li><a href="<?php echo esc_url(home_url('/research/topic/library/')); ?>">Research Library</a></li>
                            <li><a href="<?php echo esc_url(home_url('/research/topic/framework/')); ?>">Research Framework</a></li>
                            <li><a href="<?php echo esc_url(home_url('/research/topic/emerging/')); ?>">Emerging Research</a></li>
                        </ul>
                    </li>
                    <li class="menu-item menu-item-has-children">
                        <a href="<?php echo esc_url(home_url('/quality/')); ?>">Quality</a>
                        <ul class="sub-menu">
                            <li><a href="<?php echo esc_url(home_url('/quality/testing/')); ?>">Testing &amp; Verification</a></li>
                            <li><a href="<?php echo esc_url(home_url('/quality/coa/')); ?>">Lab Results / COA</a></li>
                            <li><a href="<?php echo esc_url(home_url('/quality/manufacturing/')); ?>">Manufacturing Standards</a></li>
                            <li><a href="<?php echo esc_url(home_url('/quality/handling/')); ?>">Handling &amp; Storage</a></li>
                        </ul>
                    </li>
                    <li class="menu-item menu-item-has-children">
                        <a href="<?php echo esc_url(home_url('/about/')); ?>">About</a>
                        <ul class="sub-menu">
                            <li><a href="<?php echo esc_url(home_url('/about/standards/')); ?>">Standards</a></li>
                            <li><a href="<?php echo esc_url(nav_get_contact_url()); ?>">Contact</a></li>
                        </ul>
                    </li>
                </ul>
                <?php
            }
            ?>
        </nav>

        <!-- Right: Search + Account + Cart + CTA + Mobile Toggle -->
        <div class="nav-header__actions">
            <!-- Site search — toggleable input for compound / article search -->
            <button
                type="button"
                class="nav-header__search-toggle"
                id="nav-search-toggle"
                aria-label="<?php esc_attr_e('Open site search', 'navigate-peptides'); ?>"
                aria-expanded="false"
                aria-controls="nav-search-form"
            >
                <svg class="nav-icon nav-icon--sm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            </button>

            <?php
            // Account link — shown to all users (admin logins shouldn't break
            // when WC is momentarily deactivated for maintenance).
            $account_url = class_exists('WooCommerce')
                ? (wc_get_page_permalink('myaccount') ?: home_url('/my-account/'))
                : wp_login_url();
            ?>
            <a href="<?php echo esc_url($account_url); ?>" class="nav-header__account" aria-label="<?php esc_attr_e('Account', 'navigate-peptides'); ?>">
                <svg class="nav-icon nav-icon--sm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
            </a>

            <?php if (class_exists('WooCommerce')) :
                $cart_count = WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
            ?>
                <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="nav-header__cart" aria-label="<?php esc_attr_e('Shopping cart', 'navigate-peptides'); ?>">
                    <?php echo nav_icon('cart', 'nav-icon nav-icon--sm'); ?>
                    <!-- Classes .cart-contents-count + data-cart-count are what WC fragment
                         updates target; keep BOTH so JS hooks can find us either way. -->
                    <span class="nav-header__cart-count cart-contents-count"
                          id="nav-cart-count"
                          data-cart-count="<?php echo esc_attr((string) $cart_count); ?>">
                        <?php echo esc_html((string) $cart_count); ?>
                    </span>
                </a>
            <?php endif; ?>

            <button
                type="button"
                class="nav-header__toggle"
                id="nav-mobile-toggle"
                aria-label="<?php esc_attr_e('Toggle mobile menu', 'navigate-peptides'); ?>"
                aria-expanded="false"
                aria-controls="nav-mobile-menu"
            >
                <span class="screen-reader-text"><?php esc_html_e('Menu', 'navigate-peptides'); ?></span>
                <span class="nav-header__toggle-open" aria-hidden="true"><?php echo nav_icon('menu', 'nav-icon'); ?></span>
                <span class="nav-header__toggle-close" aria-hidden="true"><?php echo nav_icon('close', 'nav-icon'); ?></span>
            </button>
        </div>
    </div>

    <!-- Expandable Site Search (toggled by the magnifier button above) -->
    <div class="nav-header__search" id="nav-search-form" aria-hidden="true">
        <div class="nav-container">
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="nav-search-form">
                <label for="nav-search-input" class="screen-reader-text">
                    <?php esc_html_e('Search compounds, research articles, and COAs', 'navigate-peptides'); ?>
                </label>
                <span class="nav-search-form__icon" aria-hidden="true">
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                </span>
                <input
                    id="nav-search-input"
                    type="search"
                    name="s"
                    class="nav-search-form__input"
                    placeholder="<?php esc_attr_e('Search BPC-157, MOTS-c, TB-500…', 'navigate-peptides'); ?>"
                    value="<?php echo esc_attr(get_search_query()); ?>"
                    autocomplete="off"
                >
                <button type="submit" class="nav-search-form__submit">
                    <?php esc_html_e('Search', 'navigate-peptides'); ?>
                </button>
                <button type="button" class="nav-search-form__close" id="nav-search-close" aria-label="<?php esc_attr_e('Close search', 'navigate-peptides'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <div class="nav-mobile-menu" id="nav-mobile-menu" aria-hidden="true">
        <div class="nav-container">
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'nav-mobile-menu__list',
                    'depth'          => 2,
                    'fallback_cb'    => false,
                ]);
            }
            ?>
        </div>
    </div>
</header>

<main id="main-content" class="nav-main">
