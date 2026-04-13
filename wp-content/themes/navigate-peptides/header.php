<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Google model-viewer for 3D vials -->
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/4.0.0/model-viewer.min.js"></script>
    <?php wp_head(); ?>
</head>
<body <?php body_class('nav-body'); ?>>
<?php wp_body_open(); ?>

<header class="nav-header" id="nav-header">
    <div class="nav-container nav-header__inner">

        <!-- Logo -->
        <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-header__logo" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?> — Home">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <span class="nav-header__logo-icon">
                    <svg viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" class="nav-icon" style="width:24px;height:24px;">
                        <path d="M14 2L2 26h24L14 2z" stroke="currentColor" stroke-width="1.5" fill="none"/>
                        <path d="M8 20c4-6 8-6 12 0" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/>
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
                        <a href="<?php echo esc_url(home_url('/compounds/')); ?>">Compounds</a>
                        <ul class="sub-menu">
                            <li><a href="<?php echo esc_url(get_term_link('metabolic-research', 'product_cat')); ?>">Metabolic Research</a></li>
                            <li><a href="<?php echo esc_url(get_term_link('tissue-repair-research', 'product_cat')); ?>">Tissue Repair Research</a></li>
                            <li><a href="<?php echo esc_url(get_term_link('cognitive-research', 'product_cat')); ?>">Cognitive Research</a></li>
                            <li><a href="<?php echo esc_url(get_term_link('inflammation-research', 'product_cat')); ?>">Inflammation Research</a></li>
                            <li><a href="<?php echo esc_url(get_term_link('cellular-research', 'product_cat')); ?>">Cellular Research</a></li>
                            <li><a href="<?php echo esc_url(get_term_link('dermal-research', 'product_cat')); ?>">Dermal Research</a></li>
                            <li><a href="<?php echo esc_url(get_term_link('research-blends', 'product_cat')); ?>">Research Blends</a></li>
                        </ul>
                    </li>
                    <li class="menu-item menu-item-has-children">
                        <a href="<?php echo esc_url(home_url('/research/')); ?>">Research</a>
                        <ul class="sub-menu">
                            <li><a href="<?php echo esc_url(home_url('/research/category/intelligence/')); ?>">Research Intelligence</a></li>
                            <li><a href="<?php echo esc_url(home_url('/research/category/library/')); ?>">Research Library</a></li>
                            <li><a href="<?php echo esc_url(home_url('/research/category/framework/')); ?>">Research Framework</a></li>
                            <li><a href="<?php echo esc_url(home_url('/research/category/emerging/')); ?>">Emerging Research</a></li>
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
                            <li><a href="<?php echo esc_url(home_url('/about/contact/')); ?>">Contact</a></li>
                        </ul>
                    </li>
                </ul>
                <?php
            }
            ?>
        </nav>

        <!-- Right: Cart + CTA + Mobile Toggle -->
        <div class="nav-header__actions">
            <?php if (class_exists('WooCommerce')) : ?>
                <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="nav-header__cart" aria-label="Shopping cart">
                    <?php echo nav_icon('cart', 'nav-icon nav-icon--sm'); ?>
                    <span class="nav-header__cart-count" id="nav-cart-count">
                        <?php echo esc_html(WC()->cart ? WC()->cart->get_cart_contents_count() : '0'); ?>
                    </span>
                </a>
            <?php endif; ?>

            <a href="<?php echo esc_url(home_url('/about/contact/')); ?>" class="nav-btn nav-btn--primary nav-header__cta">
                Request Access
            </a>

            <button
                class="nav-header__toggle"
                id="nav-mobile-toggle"
                aria-label="Toggle mobile menu"
                aria-expanded="false"
                aria-controls="nav-mobile-menu"
            >
                <span class="nav-header__toggle-open"><?php echo nav_icon('menu', 'nav-icon'); ?></span>
                <span class="nav-header__toggle-close"><?php echo nav_icon('close', 'nav-icon'); ?></span>
            </button>
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
            <a href="<?php echo esc_url(home_url('/about/contact/')); ?>" class="nav-btn nav-btn--primary nav-btn--full">
                Request Access
            </a>
        </div>
    </div>
</header>

<main id="main-content" class="nav-main">
