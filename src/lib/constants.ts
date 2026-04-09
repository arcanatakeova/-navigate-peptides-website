export const SITE_NAME = "Navigate Peptides";

export const COLORS = {
  base: "#2A3B36",
  textPrimary: "#EAEAEA",
  textSecondary: "#A8B0AD",
  border: "#3A4B46",
  surfaceLight: "#344540",
  surfaceDark: "#1E2D28",
} as const;

export const CATEGORIES = [
  {
    name: "Metabolic Research",
    slug: "metabolic-research",
    color: "#2F4666",
    description: "Peptides studied for metabolic pathway modulation and energy metabolism research.",
  },
  {
    name: "Tissue Repair Research",
    slug: "tissue-repair-research",
    color: "#9C843E",
    description: "Compounds investigated for tissue regeneration and structural repair mechanisms.",
  },
  {
    name: "Cognitive Research",
    slug: "cognitive-research",
    color: "#5E507F",
    description: "Peptides explored for neuroprotective pathways and cognitive function research.",
  },
  {
    name: "Inflammation Research",
    slug: "inflammation-research",
    color: "#4A141C",
    description: "Compounds studied for inflammatory response modulation and immune signaling.",
  },
  {
    name: "Cellular Research",
    slug: "cellular-research",
    color: "#8E5660",
    description: "Peptides investigated for cellular signaling pathways and proliferation mechanisms.",
  },
  {
    name: "Dermal Research",
    slug: "dermal-research",
    color: "#4A6B5F",
    description: "Compounds explored for dermal tissue modeling and epidermal pathway analysis.",
  },
  {
    name: "Research Blends",
    slug: "research-blends",
    color: "#474C50",
    description: "Multi-peptide formulations designed for synergistic pathway research applications.",
  },
] as const;

export const NAV_ITEMS = [
  {
    label: "Compounds",
    href: "/compounds",
    children: CATEGORIES.map((c) => ({
      label: c.name,
      href: `/compounds/${c.slug}`,
    })),
  },
  {
    label: "Research",
    href: "/research",
    children: [
      { label: "Research Intelligence", href: "/research/intelligence" },
      { label: "Research Library", href: "/research/library" },
      { label: "Research Framework", href: "/research/framework" },
      { label: "Emerging Research", href: "/research/emerging" },
    ],
  },
  {
    label: "Quality",
    href: "/quality",
    children: [
      { label: "Testing & Verification", href: "/quality/testing" },
      { label: "Lab Results / COA", href: "/quality/coa" },
      { label: "Manufacturing Standards", href: "/quality/manufacturing" },
      { label: "Handling & Storage", href: "/quality/handling" },
    ],
  },
  {
    label: "About",
    href: "/about",
    children: [
      { label: "Standards", href: "/about/standards" },
      { label: "Contact", href: "/about/contact" },
    ],
  },
] as const;

export const DISCLAIMERS = {
  product:
    "All products currently listed on this site are for research purposes ONLY.",
  sitewide:
    "All products sold on this website are intended for research and identification purposes only. These products are not intended for human dosing, injection, or ingestion.",
} as const;

export type Category = (typeof CATEGORIES)[number];

export function getCategoryBySlug(slug: string): Category | undefined {
  return CATEGORIES.find((c) => c.slug === slug);
}
