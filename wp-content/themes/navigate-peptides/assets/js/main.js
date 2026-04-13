/**
 * Navigate Peptides — Main JavaScript
 * Production-grade interactions
 *
 * @package NavigatePeptides
 */
(function () {
    'use strict';

    /* ------------------------------------------------------------------
     * Mobile Menu Toggle
     * ----------------------------------------------------------------*/
    var header = document.getElementById('nav-header');
    var toggle = document.getElementById('nav-mobile-toggle');
    var mobileMenu = document.getElementById('nav-mobile-menu');

    if (toggle && mobileMenu && header) {
        toggle.addEventListener('click', function () {
            var isOpen = header.getAttribute('data-mobile-open') === 'true';
            header.setAttribute('data-mobile-open', isOpen ? 'false' : 'true');
            mobileMenu.setAttribute('aria-hidden', isOpen ? 'true' : 'false');
            toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            document.body.style.overflow = isOpen ? '' : 'hidden';
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && header.getAttribute('data-mobile-open') === 'true') {
                header.setAttribute('data-mobile-open', 'false');
                mobileMenu.setAttribute('aria-hidden', 'true');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        });

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
     * Header: Scroll effect — solid bg + shadow on scroll
     * ----------------------------------------------------------------*/
    var headerEl = document.querySelector('.nav-header');
    if (headerEl) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 10) {
                headerEl.classList.add('nav-header--scrolled');
            } else {
                headerEl.classList.remove('nav-header--scrolled');
            }
        }, { passive: true });
    }

    /* ------------------------------------------------------------------
     * Intersection Observer: Animate elements on scroll
     * ----------------------------------------------------------------*/
    var animatedEls = document.querySelectorAll(
        '.nav-category-card, .nav-cat-icon-card, .nav-trust-card, .nav-link-card, .nav-info-card, .nav-post-card'
    );

    if (animatedEls.length && 'IntersectionObserver' in window) {
        // Add initial hidden state
        animatedEls.forEach(function (el) {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.5s cubic-bezier(0.4,0,0.2,1), transform 0.5s cubic-bezier(0.4,0,0.2,1)';
        });

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    // Stagger animation based on index
                    var siblings = Array.from(entry.target.parentElement.children);
                    var index = siblings.indexOf(entry.target);
                    entry.target.style.transitionDelay = (index * 0.06) + 's';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        animatedEls.forEach(function (el) { observer.observe(el); });
    }

    /* ------------------------------------------------------------------
     * WooCommerce: Update cart count via AJAX fragments
     * ----------------------------------------------------------------*/
    if (typeof jQuery !== 'undefined') {
        jQuery(document.body).on('added_to_cart removed_from_cart updated_cart_totals', function () {
            var countEl = document.getElementById('nav-cart-count');
            if (countEl) {
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
            var href = this.getAttribute('href');
            if (href === '#') return;
            var target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* ------------------------------------------------------------------
     * Header scroll shadow
     * ----------------------------------------------------------------*/
    var style = document.createElement('style');
    style.textContent = '.nav-header--scrolled { box-shadow: 0 4px 24px rgba(0,0,0,0.3); background-color: rgba(20,31,27,0.98); }';
    document.head.appendChild(style);

})();
