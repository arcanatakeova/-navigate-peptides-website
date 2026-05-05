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
                    <svg viewBox="0 0 288 312.52" preserveAspectRatio="xMidYMid meet" fill="none" xmlns="http://www.w3.org/2000/svg" class="nav-icon" style="width:36px;height:36px;" aria-hidden="true" focusable="false">
                        <g transform="translate(0 313) scale(0.1 -0.1)" fill="currentColor">
                            <path d="M0 3123c0 -3 50 -113 111 -242 61 -130 124 -263 139 -296 15 -33 101 -215 190 -405 89 -190 171 -364 181 -387 10 -24 21 -43 24 -43 16 0 210 143 207 153 -3 10 -110 247 -227 502 -20 44 -43 94 -50 110 -8 17 -43 95 -79 174 -37 79 -66 145 -66 147 0 2 357 4 794 4l794 0 111 -206c61 -113 111 -208 111 -212 0 -3 -20 -45 -45 -93 -25 -48 -45 -90 -45 -93 0 -2 25 -10 56 -16 31 -7 81 -21 112 -31 30 -10 61 -19 68 -19 14 0 144 248 144 273 0 8 -41 90 -91 183 -50 93 -127 239 -171 324 -44 85 -85 161 -91 168 -7 9 -238 12 -1093 12 -596 0 -1084 -3 -1084 -7z"/>
                            <path d="M2460 3121c0 -14 213 -371 221 -371 3 0 37 57 74 128 37 70 81 150 97 179 15 29 28 57 28 62 0 8 -69 11 -210 11 -125 0 -210 -4 -210 -9z"/>
                            <path d="M1885 2124c-254 -30 -455 -90 -680 -204 -456 -230 -817 -593 -941 -947 -20 -56 -11 -83 28 -83 12 0 99 80 231 213 117 118 255 247 307 288 249 197 491 325 761 404 101 29 150 38 322 60l37 5 -14 -32c-21 -47 -102 -229 -180 -403 -90 -199 -132 -292 -206 -460 -129 -296 -144 -326 -151 -313 -12 22 -114 246 -178 393 -86 194 -125 275 -133 275 -12 0 -208 -125 -208 -132 0 -15 517 -1169 529 -1181 7 -7 17 5 32 39 11 27 38 87 59 134 43 97 231 520 345 780 143 323 385 861 391 869 7 8 167 -63 232 -103l69 -43 57 74c31 41 56 78 56 82 0 5 -27 34 -61 65 -173 161 -455 248 -704 220z"/>
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
