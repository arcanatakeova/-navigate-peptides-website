# CLAUDE.md — Navigate Peptides

## What You're Building

A premium biotech-style research peptide website. NOT a typical e-commerce store. Minimal, controlled, high-end, scientifically credible. The client's words: "premium biotech-style brand" and "a knowledge authority, not just a supplier."

The site must be simple to update — adding products and editing content without needing technical help.

## Critical: Read These Before Writing Anything

| File | When to Read |
|------|-------------|
| `docs/COMPLIANCE.md` | ⚠️ Before writing ANY text — FDA + processor rules dictate every word |
| `docs/BRAND_AND_DESIGN.md` | Before any styling — exact colors, category system, component patterns |
| `docs/SITE_ARCHITECTURE.md` | Before planning routes or pages |
| `docs/PRODUCT_CATALOG.md` | Before building product pages |
| `docs/PAYMENT_PROCESSING.md` | Before building checkout |
| `docs/CONTENT_STRATEGY.md` | Before building research/blog section |
| `docs/COMPETITIVE_INTEL.md` | For design/UX context |
| `docs/MARKET_INTEL.md` | For business context |

## Architecture

**Hybrid headless:** WooCommerce backend (products, orders, payments) + Next.js frontend (rendering, UX) on Vercel.

AllayPay (the payment processor) requires WooCommerce. Their NMI gateway plugin runs on the WooCommerce backend. The Next.js frontend connects via WooCommerce REST API. Checkout can either redirect to WooCommerce-hosted checkout or use NMI's Collect.js tokenization on the Next.js side.

WordPress admin is the CMS interface for non-technical content updates.

## Navigation (Client-Specified)

```
Compounds | Research | Quality | About
```

Four items. Plus "Request Access" CTA button.

## Design Direction

**Base:** Dark brand green `#2A3B36`
**Text:** Primary `#EAEAEA`, Secondary `#A8B0AD`
**Feel:** Dark, matte, low-saturation. Serif headlines + sans-serif body.

**Seven categories, each with one color:**
- Metabolic Research → `#2F4666`
- Tissue Repair Research → `#9C843E`
- Cognitive Research → `#5E507F`
- Inflammation Research → `#4A141C`
- Cellular Research → `#8E5660`
- Dermal Research → `#4A6B5F`
- Research Blends → `#474C50`

**Rules:** One color per category. No gradients. Matte only.

## Product Page Structure (Client-Specified)

Every product must have:
1. Product name
2. Technical subtitle
3. Short scientific description (1 line)
4. Research focus (bullet points)
5. Studies available
6. Purity / COA information
7. Research-use disclaimer

## Absolute Rules

### 1. COMPLIANCE FIRST
The payment processor audits the site before approving the merchant account. Every word is a compliance surface — product descriptions, meta tags, alt text, blog posts. Read `docs/COMPLIANCE.md`.

**Processor-mandated disclaimers (exact text required):**
- Per-product: "All products currently listed on this site are for research purposes ONLY."
- Sitewide: "All products sold on this website are intended for research and identification purposes only. These products are not intended for human dosing, injection, or ingestion."

### 2. CONTENT VOICE
Describe mechanisms, pathways, and scientific context. Clear and easy to understand. NO benefit claims. NO consumer language. NO lifestyle framing.

### 3. NO BLOGS/FAQS IMPLYING HUMAN USE
The processor explicitly prohibits blogs, FAQs, or SEO terms that imply personal or human use. Research content must be purely scientific — molecular mechanisms, pathway analysis, published preclinical citations.

### 4. NO INFLUENCER/SOCIAL/LIFESTYLE MARKETING
Processor requirement. No testimonials. No outcomes. No before/after. No influencer content.

## Routes

```
/                                    → Homepage
/compounds                           → Category grid
/compounds/[category]                → Category listing
/compounds/[category]/[slug]         → Product detail
/research                            → Research hub
/research/intelligence               → Research Intelligence
/research/library                    → Research Library
/research/framework                  → Research Framework
/research/emerging                   → Emerging Research
/research/[slug]                     → Individual article
/quality                             → Quality overview
/quality/testing                     → Testing & Verification
/quality/coa                         → Lab Results / COA lookup
/quality/manufacturing               → Manufacturing Standards
/quality/handling                    → Handling & Storage
/about                               → Company
/about/standards                     → Standards
/about/contact                       → Contact / Request Access
/cart                                → Shopping cart
/checkout                            → Checkout
```

## Pre-Ship Checklist

- [ ] Homepage matches approved mockup
- [ ] Dark theme, no white flashes on load
- [ ] Category colors match exact hex values
- [ ] Product pages follow client's 7-point structure
- [ ] Processor-mandated disclaimers present (exact text)
- [ ] RUO disclaimer in footer on every page
- [ ] Checkout has RUO acknowledgment checkbox
- [ ] No prohibited language anywhere (scan full site against COMPLIANCE.md)
- [ ] No blogs/FAQs implying human use
- [ ] Mobile responsive (375px, 768px, 1024px, 1440px)
- [ ] Fonts loading (Playfair Display, Inter, JetBrains Mono)
- [ ] Schema markup on product and article pages
- [ ] Page load <3s
- [ ] Products manageable through WordPress admin without code
