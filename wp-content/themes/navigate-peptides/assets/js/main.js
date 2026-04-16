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
     * Listens for multiple possible events and extracts the count from
     * either the fragment data or common selectors, failing silently.
     * ----------------------------------------------------------------*/
    function updateCartCount(event, fragments) {
        var countEl = document.getElementById('nav-cart-count');
        if (!countEl) return;

        // Try fragment data first (passed by Woo's added_to_cart event)
        if (fragments) {
            for (var key in fragments) {
                if (Object.prototype.hasOwnProperty.call(fragments, key) && key.indexOf('cart-contents-count') !== -1) {
                    var temp = document.createElement('div');
                    temp.innerHTML = fragments[key];
                    var countNode = temp.querySelector('.cart-contents-count, [data-cart-count]');
                    if (countNode) {
                        countEl.textContent = countNode.textContent.trim();
                        return;
                    }
                }
            }
        }

        // Fall back to DOM query
        var fallback = document.querySelector('.cart-contents-count, [data-cart-count]');
        if (fallback) {
            countEl.textContent = fallback.textContent.trim();
        }
    }

    if (typeof jQuery !== 'undefined') {
        jQuery(document.body).on('added_to_cart', function (e, fragments) {
            updateCartCount(e, fragments);
        });
        jQuery(document.body).on('removed_from_cart updated_cart_totals wc_fragments_refreshed', function () {
            updateCartCount();
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

    /* ------------------------------------------------------------------
     * Back to Top Button
     * ----------------------------------------------------------------*/
    var btt = document.createElement('button');
    btt.className = 'nav-back-to-top';
    btt.setAttribute('aria-label', 'Back to top');
    btt.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>';
    document.body.appendChild(btt);

    var bttStyle = document.createElement('style');
    bttStyle.textContent = [
        '.nav-back-to-top {',
        '  position: fixed; bottom: 32px; right: 32px; z-index: 90;',
        '  width: 44px; height: 44px;',
        '  display: flex; align-items: center; justify-content: center;',
        '  background: rgba(26,42,36,0.85); backdrop-filter: blur(8px);',
        '  border: 1px solid rgba(234,234,234,0.1); border-radius: 50%;',
        '  color: #A8B0AD; cursor: pointer;',
        '  opacity: 0; visibility: hidden;',
        '  transform: translateY(12px);',
        '  transition: all 300ms cubic-bezier(0.4,0,0.2,1);',
        '  box-shadow: 0 4px 16px rgba(0,0,0,0.3);',
        '}',
        '.nav-back-to-top:hover { color: #EAEAEA; border-color: rgba(234,234,234,0.2); }',
        '.nav-back-to-top--visible { opacity: 1; visibility: visible; transform: translateY(0); }',
        '.nav-back-to-top:focus-visible { outline: 2px solid #9C843E; outline-offset: 2px; }',
        '@media (max-width: 767px) { .nav-back-to-top { bottom: 20px; right: 20px; width: 40px; height: 40px; } }',
    ].join('\n');
    document.head.appendChild(bttStyle);

    btt.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    window.addEventListener('scroll', function () {
        if (window.scrollY > 500) {
            btt.classList.add('nav-back-to-top--visible');
        } else {
            btt.classList.remove('nav-back-to-top--visible');
        }
    }, { passive: true });

})();
