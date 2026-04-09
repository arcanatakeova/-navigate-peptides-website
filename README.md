# Navigate Peptides — Website Project

## What Is This

Navigate Peptides is a premium biotech-style research peptide e-commerce website. This repo contains the Next.js frontend that connects to a WooCommerce backend via headless API.

The site should feel minimal, controlled, and high-end — like a biotech company, not a supplement store. Everything is research-positioned. No health claims, no consumer language, no lifestyle marketing.

## Quick Start

```bash
npm install
npm run dev
```

## Repo Structure

```
├── CLAUDE.md                    ← Claude Code instructions (READ FIRST)
├── README.md                    ← You are here
├── docs/
│   ├── BRAND_AND_DESIGN.md      ← Design system, colors, typography, components
│   ├── COMPLIANCE.md            ← ⚠️ FDA + processor content rules (CRITICAL)
│   ├── SITE_ARCHITECTURE.md     ← Routes, nav structure, page requirements
│   ├── PRODUCT_CATALOG.md       ← All SKUs, data model, pricing, categories
│   ├── PAYMENT_PROCESSING.md    ← Processor stack, gateway integration, BRAM
│   ├── CONTENT_STRATEGY.md      ← Research hub, SEO, compliant content approach
│   ├── COMPETITIVE_INTEL.md     ← Competitor analysis, positioning
│   └── MARKET_INTEL.md          ← Market size, FDA timeline, trends
├── src/
│   ├── app/                     ← Next.js App Router pages
│   ├── components/              ← React components
│   ├── lib/                     ← API clients, utilities
│   └── styles/                  ← Global CSS, Tailwind config
├── public/                      ← Static assets, product images
└── package.json
```

## Tech Stack

- **Frontend:** Next.js (App Router) + Tailwind CSS
- **Backend:** WooCommerce (headless, via REST API)
- **Payments:** AllayPay/NMI (primary), Coinbase Commerce (crypto), Paycron (ACH)
- **Fonts:** Playfair Display, Inter, JetBrains Mono
- **Hosting:** Vercel
- **CMS:** WordPress/WooCommerce admin for non-technical content updates

## Key Constraints

1. **Compliance is the #1 priority.** Read `docs/COMPLIANCE.md` before writing any text. The payment processor audits every page before approving the merchant account. FDA has shut down vendors for language violations.

2. **No health claims anywhere.** Not in copy, not in meta tags, not in alt text, not in code comments that render. Describe mechanisms and pathways only.

3. **Payment processor requires WooCommerce.** AllayPay's NMI plugin requires a WooCommerce backend. The checkout either redirects to WooCommerce-hosted checkout or uses NMI's Collect.js tokenization on the Next.js side.

4. **No paid advertising.** LegitScript certification is unavailable for RUO vendors, which blocks Google Ads and Meta Ads. Organic SEO is the only scalable traffic channel. Every page must be optimized.

5. **Site must be fully compliant before first transaction.** Mastercard's MMP program requires pre-transaction website scanning. No grace period.

## Design Direction

Dark, matte, scientific. Seven research categories each with a unique color. See `docs/BRAND_AND_DESIGN.md` for the full design system including exact hex codes, typography, and component patterns from the client-approved mockup.

## Navigation

Four items: **Compounds** | **Research** | **Quality** | **About**

## Owner

Built for Navigate Peptides by [your name/studio].
