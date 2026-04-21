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
     * Announcement Bar: dismiss + persist for 7 days via localStorage
     * ----------------------------------------------------------------*/
    var announcementClose = document.getElementById('nav-announcement-close');
    var announcementBar = document.getElementById('nav-announcement');
    var ANN_KEY = 'nav_announcement_dismissed_until';

    function hideAnnouncement() {
        if (!announcementBar) return;
        announcementBar.remove();
        document.documentElement.style.setProperty('--nav-announcement-h', '0px');
    }

    // On load, honor a prior dismissal within the last 7 days.
    try {
        var dismissedUntil = parseInt(localStorage.getItem(ANN_KEY) || '0', 10);
        if (dismissedUntil > Date.now()) {
            hideAnnouncement();
        }
    } catch (e) { /* localStorage disabled — fall through, no persist */ }

    if (announcementClose && announcementBar) {
        announcementClose.addEventListener('click', function () {
            try {
                var sevenDays = 7 * 24 * 60 * 60 * 1000;
                localStorage.setItem(ANN_KEY, String(Date.now() + sevenDays));
            } catch (e) { /* noop */ }
            hideAnnouncement();
        });
    }

    /* ------------------------------------------------------------------
     * Header: Search toggle — expand/collapse the search form
     * ----------------------------------------------------------------*/
    var searchToggle = document.getElementById('nav-search-toggle');
    var searchForm = document.getElementById('nav-search-form');
    var searchClose = document.getElementById('nav-search-close');
    var searchInput = document.getElementById('nav-search-input');
    if (searchToggle && searchForm) {
        var setSearchOpen = function (open) {
            searchForm.setAttribute('aria-hidden', open ? 'false' : 'true');
            searchToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (open && searchInput) {
                setTimeout(function () { searchInput.focus(); }, 50);
            }
        };
        searchToggle.addEventListener('click', function () {
            var isOpen = searchForm.getAttribute('aria-hidden') === 'false';
            setSearchOpen(!isOpen);
        });
        if (searchClose) {
            searchClose.addEventListener('click', function () { setSearchOpen(false); });
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && searchForm.getAttribute('aria-hidden') === 'false') {
                setSearchOpen(false);
                searchToggle.focus();
            }
        });
        // If the user lands on a URL that already has ?s=, open the search
        // form by default so the existing query is visible + editable.
        try {
            if (new URLSearchParams(window.location.search).has('s')) {
                setSearchOpen(true);
            }
        } catch (e) { /* URLSearchParams unsupported — ignore */ }
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
     *
     * The PHP side registers the fragment 'span.nav-header__cart-count' —
     * Woo writes it straight into the DOM via WC core's updateFragment()
     * without any code from us. This handler only covers cases where the
     * selector isn't an exact match (variant carts, added_to_cart events
     * that pass the fragment object directly) and the parent-side pulse
     * animation.
     * ----------------------------------------------------------------*/
    // Pulse the cart badge after WC has finished swapping fragments into
    // the DOM. Pulsing on `added_to_cart` loses to WC's updateFragment()
    // which replaces the span wholesale and clobbers the animation class;
    // pulsing on `wc_fragments_refreshed` runs AFTER the swap.
    function pulseCartBadge() {
        var el = document.getElementById('nav-cart-count');
        if (!el) return;
        el.classList.remove('is-pulse');
        void el.offsetWidth; // force reflow so the class re-trigger restarts the keyframes
        el.classList.add('is-pulse');
    }

    if (typeof jQuery !== 'undefined') {
        jQuery(document.body).on('added_to_cart', function () {
            // Wait one animation frame so WC's updateFragment() has a chance
            // to land before we pulse.
            requestAnimationFrame(pulseCartBadge);
        });
        jQuery(document.body).on('wc_fragments_refreshed', pulseCartBadge);
    }

    /* ------------------------------------------------------------------
     * <model-viewer> error handler — when the GLB 404s or CORS blocks,
     * fall back to the poster image instead of leaving the user staring
     * at a frozen viewport.
     *
     * When the poster is a <picture>, we MUST move the <picture> element
     * (not just the <img>), because <source> siblings carry the WebP
     * srcset; detaching the <img> alone collapses selection to the PNG.
     * ----------------------------------------------------------------*/
    document.querySelectorAll('model-viewer').forEach(function (mv) {
        var fired = false;
        var handler = function (ev) {
            if (fired) return;
            fired = true;
            mv.removeEventListener('error', handler);

            // Prefer <picture> so <source type=image/webp> stays attached.
            var picture = mv.querySelector('picture');
            var poster = picture || mv.querySelector('img');
            if (!poster || !mv.parentNode) return;

            var wrap = document.createElement('div');
            wrap.className = 'nav-vial-fallback';
            wrap.appendChild(poster);
            mv.parentNode.replaceChild(wrap, mv);
        };
        mv.addEventListener('error', handler);
    });

    /* ------------------------------------------------------------------
     * Smooth scroll for anchor links
     * ----------------------------------------------------------------*/
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var href = this.getAttribute('href');
            if (href === '#' || href.length < 2) return;
            // querySelector throws SyntaxError on CSS-invalid hashes like
            // #2col or #foo:bar. Swallow so navigation isn't cancelled.
            var target = null;
            try {
                target = document.querySelector(href);
            } catch (_) {
                return;
            }
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

    /* ------------------------------------------------------------------
     * Minicart drawer — open/close + trap
     * ----------------------------------------------------------------*/
    var minicart = document.getElementById('nav-minicart');
    var minicartClose = document.getElementById('nav-minicart-close');
    var minicartScrim = document.getElementById('nav-minicart-scrim');
    var lastFocusBeforeCart = null;

    window.navMinicartOpen = function () {
        if (!minicart) return;
        lastFocusBeforeCart = document.activeElement;
        minicart.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        if (minicartClose) setTimeout(function () { minicartClose.focus(); }, 80);
    };

    window.navMinicartClose = function () {
        if (!minicart) return;
        minicart.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (lastFocusBeforeCart && typeof lastFocusBeforeCart.focus === 'function') {
            lastFocusBeforeCart.focus();
        }
    };

    if (minicart) {
        if (minicartClose) minicartClose.addEventListener('click', window.navMinicartClose);
        if (minicartScrim) minicartScrim.addEventListener('click', window.navMinicartClose);

        // Focus trap: when the drawer is open, Tab cycles focusable
        // children of .nav-minicart__panel. Without this, Tab escapes the
        // drawer into the page behind the scrim — a WCAG 2.4.3 focus-order
        // failure for modal dialogs.
        var minicartPanel = minicart.querySelector('.nav-minicart__panel');
        var FOCUSABLE_SEL = 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

        document.addEventListener('keydown', function (e) {
            if (minicart.getAttribute('aria-hidden') !== 'false') return;
            if (e.key === 'Escape') {
                window.navMinicartClose();
                return;
            }
            if (e.key !== 'Tab' || !minicartPanel) return;
            var nodes = minicartPanel.querySelectorAll(FOCUSABLE_SEL);
            if (!nodes.length) return;
            var first = nodes[0];
            var last  = nodes[nodes.length - 1];
            var active = document.activeElement;
            if (e.shiftKey && active === first) {
                last.focus();
                e.preventDefault();
            } else if (!e.shiftKey && active === last) {
                first.focus();
                e.preventDefault();
            } else if (!minicartPanel.contains(active)) {
                // Focus started outside the panel (e.g. user clicked scrim,
                // then hit Tab) — pull it back in.
                first.focus();
                e.preventDefault();
            }
        });
    }

    /* ------------------------------------------------------------------
     * Newsletter form — POST /wp-json/nav/v1/subscribe
     * Submits over fetch so the user stays on the page; shows inline
     * success/error feedback without a round-trip.
     * ----------------------------------------------------------------*/
    (function () {
        var forms = document.querySelectorAll('[data-nav-subscribe]');
        if (!forms.length) return;

        var endpoint = (window.navConfig && window.navConfig.subscribeUrl)
            || '/wp-json/nav/v1/subscribe';

        Array.prototype.forEach.call(forms, function (form) {
            var feedback = form.querySelector('.nav-newsletter__feedback');
            var submit   = form.querySelector('[type="submit"]');
            var input    = form.querySelector('input[name="email"]');

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!input || !input.value) return;

                if (feedback) {
                    feedback.textContent = '';
                    feedback.removeAttribute('data-state');
                }
                if (submit) submit.disabled = true;

                var data = new FormData(form);
                var body = {
                    email:  data.get('email'),
                    source: data.get('source') || 'footer',
                    nav_hp: data.get('nav_hp') || ''
                };

                fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify(body)
                })
                    .then(function (res) {
                        return res.json().then(function (json) { return { ok: res.ok, json: json }; });
                    })
                    .then(function (r) {
                        if (submit) submit.disabled = false;
                        if (!feedback) return;
                        if (r.ok && r.json && r.json.success) {
                            feedback.textContent = r.json.message || 'Thanks — you\'re on the list.';
                            feedback.setAttribute('data-state', 'success');
                            form.reset();
                        } else {
                            var msg = (r.json && (r.json.message || r.json.code)) || 'Something went wrong. Try again.';
                            feedback.textContent = msg;
                            feedback.setAttribute('data-state', 'error');
                        }
                    })
                    .catch(function () {
                        if (submit) submit.disabled = false;
                        if (!feedback) return;
                        feedback.textContent = 'Network error. Please try again.';
                        feedback.setAttribute('data-state', 'error');
                    });
            });
        });
    })();

})();
