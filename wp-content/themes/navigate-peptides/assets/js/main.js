/**
 * Navigate Peptides — Main JavaScript
 *
 * @package NavigatePeptides
 */
(function () {
    'use strict';

    /* ------------------------------------------------------------------
     * Mobile Menu Toggle
     * ----------------------------------------------------------------*/
    const header    = document.getElementById('nav-header');
    const toggle    = document.getElementById('nav-mobile-toggle');
    const mobileMenu = document.getElementById('nav-mobile-menu');

    if (toggle && mobileMenu && header) {
        toggle.addEventListener('click', function () {
            const isOpen = header.getAttribute('data-mobile-open') === 'true';
            header.setAttribute('data-mobile-open', isOpen ? 'false' : 'true');
            mobileMenu.setAttribute('aria-hidden', isOpen ? 'true' : 'false');
            toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');

            // Prevent body scroll when menu is open
            document.body.style.overflow = isOpen ? '' : 'hidden';
        });

        // Close on ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && header.getAttribute('data-mobile-open') === 'true') {
                header.setAttribute('data-mobile-open', 'false');
                mobileMenu.setAttribute('aria-hidden', 'true');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        });

        // Close on resize to desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024 && header.getAttribute('data-mobile-open') === 'true') {
                header.setAttribute('data-mobile-open', 'false');
                mobileMenu.setAttribute('aria-hidden', 'true');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        });
    }

    /* ------------------------------------------------------------------
     * Header scroll effect — subtle background opacity change
     * ----------------------------------------------------------------*/
    let lastScroll = 0;
    const headerEl = document.querySelector('.nav-header');

    if (headerEl) {
        window.addEventListener('scroll', function () {
            const scrollY = window.scrollY;
            if (scrollY > 10) {
                headerEl.classList.add('nav-header--scrolled');
            } else {
                headerEl.classList.remove('nav-header--scrolled');
            }
            lastScroll = scrollY;
        }, { passive: true });
    }

    /* ------------------------------------------------------------------
     * WooCommerce: Update cart count via AJAX fragments
     * ----------------------------------------------------------------*/
    if (typeof jQuery !== 'undefined') {
        jQuery(document.body).on('added_to_cart removed_from_cart updated_cart_totals', function () {
            var countEl = document.getElementById('nav-cart-count');
            if (countEl) {
                // WooCommerce updates fragments; grab from .cart-contents-count if available
                var fragment = document.querySelector('.cart-contents-count');
                if (fragment) {
                    countEl.textContent = fragment.textContent;
                }
            }
        });
    }

    /* ------------------------------------------------------------------
     * Smooth scroll for anchor links
     * ----------------------------------------------------------------*/
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

})();
