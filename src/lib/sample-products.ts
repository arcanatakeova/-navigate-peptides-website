/**
 * Sample product data for development.
 * In production, these are fetched from WooCommerce REST API.
 */

export interface Product {
  name: string;
  slug: string;
  categorySlug: string;
  subtitle: string;
  description: string;
  researchFocus: string[];
  studies: string[];
  purity: string;
  price: string;
  sequence?: string;
  molecularWeight?: string;
}

export const SAMPLE_PRODUCTS: Product[] = [
  // Metabolic Research
  {
    name: "Tesamorelin",
    slug: "tesamorelin",
    categorySlug: "metabolic-research",
    subtitle: "Growth Hormone-Releasing Factor Analog",
    description: "A synthetic analog of growth hormone-releasing hormone (GHRH) studied for its interaction with the GHRH receptor and downstream metabolic signaling pathways.",
    researchFocus: [
      "GHRH receptor binding affinity and signaling cascade activation",
      "Lipid metabolism pathway modulation in preclinical models",
      "GH/IGF-1 axis regulation mechanisms",
      "Visceral adipose tissue molecular signaling",
    ],
    studies: ["GHRH receptor binding kinetics (in vitro)", "Lipid pathway modulation (preclinical)"],
    purity: "≥98% (HPLC verified)",
    price: "$89.99",
    sequence: "Modified GHRH(1-44)",
    molecularWeight: "5135.9 Da",
  },
  {
    name: "AOD-9604",
    slug: "aod-9604",
    categorySlug: "metabolic-research",
    subtitle: "Anti-Obesity Drug Fragment 177-191",
    description: "A modified fragment of human growth hormone spanning amino acids 177-191, investigated for its role in lipolytic pathway signaling without affecting IGF-1 levels.",
    researchFocus: [
      "Lipolytic signaling pathways independent of GH receptor",
      "Beta-3 adrenergic receptor interaction studies",
      "Adipocyte differentiation mechanisms",
    ],
    studies: ["Lipolysis pathway analysis (in vitro)", "Adipocyte signaling models"],
    purity: "≥98% (HPLC verified)",
    price: "$64.99",
    sequence: "hGH fragment 177-191 (modified)",
    molecularWeight: "1815.1 Da",
  },
  // Tissue Repair Research
  {
    name: "BPC-157",
    slug: "bpc-157",
    categorySlug: "tissue-repair-research",
    subtitle: "Body Protection Compound-15",
    description: "A pentadecapeptide derived from human gastric juice, studied for its involvement in angiogenic and tissue-remodeling signaling pathways.",
    researchFocus: [
      "VEGF pathway upregulation mechanisms",
      "Nitric oxide system modulation",
      "Growth factor receptor signaling",
      "Extracellular matrix remodeling pathways",
    ],
    studies: ["VEGF expression analysis (in vitro)", "Nitric oxide signaling models (preclinical)"],
    purity: "≥99% (HPLC verified)",
    price: "$54.99",
    sequence: "Gly-Glu-Pro-Pro-Pro-Gly-Lys-Pro-Ala-Asp-Asp-Ala-Gly-Leu-Val",
    molecularWeight: "1419.5 Da",
  },
  {
    name: "TB-500",
    slug: "tb-500",
    categorySlug: "tissue-repair-research",
    subtitle: "Thymosin Beta-4 Fragment",
    description: "A synthetic peptide fragment of thymosin beta-4, investigated for its role in actin regulation and cellular migration signaling.",
    researchFocus: [
      "Actin polymerization and cytoskeletal dynamics",
      "Cell migration and motility mechanisms",
      "Inflammatory mediator signaling",
    ],
    studies: ["Actin binding assays (in vitro)", "Cell migration models"],
    purity: "≥98% (HPLC verified)",
    price: "$69.99",
    molecularWeight: "4963.5 Da",
  },
  // Cognitive Research
  {
    name: "Selank",
    slug: "selank",
    categorySlug: "cognitive-research",
    subtitle: "Synthetic Tuftsin Analog",
    description: "A synthetic heptapeptide analog of the immunomodulatory peptide tuftsin, studied for its interaction with GABAergic and monoamine neurotransmitter systems.",
    researchFocus: [
      "GABA receptor modulation mechanisms",
      "BDNF expression pathway analysis",
      "Monoamine neurotransmitter system interactions",
      "Enkephalin degradation enzyme inhibition",
    ],
    studies: ["GABA binding studies (in vitro)", "BDNF expression analysis (preclinical)"],
    purity: "≥98% (HPLC verified)",
    price: "$59.99",
    molecularWeight: "751.9 Da",
  },
  {
    name: "Semax",
    slug: "semax",
    categorySlug: "cognitive-research",
    subtitle: "Synthetic ACTH(4-10) Analog",
    description: "A synthetic peptide derived from adrenocorticotropic hormone fragment 4-10, investigated for neurotrophic factor signaling pathways.",
    researchFocus: [
      "BDNF and NGF expression modulation",
      "Dopaminergic and serotonergic system interactions",
      "Neurotrophic signaling cascade analysis",
    ],
    studies: ["Neurotrophic factor expression (in vitro)", "Monoamine system studies"],
    purity: "≥98% (HPLC verified)",
    price: "$59.99",
    molecularWeight: "813.9 Da",
  },
  // Inflammation Research
  {
    name: "KPV",
    slug: "kpv",
    categorySlug: "inflammation-research",
    subtitle: "Alpha-MSH C-Terminal Tripeptide",
    description: "The C-terminal tripeptide of alpha-melanocyte stimulating hormone, studied for its interaction with NF-kB and inflammatory cytokine signaling.",
    researchFocus: [
      "NF-kB pathway modulation mechanisms",
      "Pro-inflammatory cytokine signaling regulation",
      "MC1R-independent anti-inflammatory signaling",
    ],
    studies: ["NF-kB inhibition assays (in vitro)", "Cytokine panel analysis (preclinical)"],
    purity: "≥98% (HPLC verified)",
    price: "$49.99",
    molecularWeight: "357.4 Da",
  },
  // Cellular Research
  {
    name: "Epithalon",
    slug: "epithalon",
    categorySlug: "cellular-research",
    subtitle: "Synthetic Epithalamin Tetrapeptide",
    description: "A synthetic tetrapeptide studied for its interaction with telomerase reverse transcriptase and cellular senescence pathways.",
    researchFocus: [
      "Telomerase activation mechanisms",
      "Telomere length maintenance pathways",
      "Cellular senescence signaling",
      "Melatonin synthesis regulation",
    ],
    studies: ["Telomerase activity assays (in vitro)", "Senescence marker analysis"],
    purity: "≥98% (HPLC verified)",
    price: "$44.99",
    sequence: "Ala-Glu-Asp-Gly",
    molecularWeight: "390.3 Da",
  },
  // Dermal Research
  {
    name: "GHK-Cu",
    slug: "ghk-cu",
    categorySlug: "dermal-research",
    subtitle: "Copper Tripeptide-1 Complex",
    description: "A naturally occurring copper-binding tripeptide investigated for its role in extracellular matrix remodeling and metalloproteinase regulation.",
    researchFocus: [
      "Collagen and elastin synthesis pathway activation",
      "Matrix metalloproteinase regulation",
      "Fibroblast proliferation signaling",
      "Antioxidant enzyme expression modulation",
    ],
    studies: ["Collagen synthesis assays (in vitro)", "MMP regulation studies"],
    purity: "≥98% (HPLC verified)",
    price: "$54.99",
    sequence: "Gly-His-Lys:Cu²⁺",
    molecularWeight: "403.9 Da",
  },
  // Research Blends
  {
    name: "BPC-157 + TB-500 Blend",
    slug: "bpc-157-tb-500-blend",
    categorySlug: "research-blends",
    subtitle: "Dual-Peptide Research Formulation",
    description: "A combination formulation containing BPC-157 and TB-500 designed for investigating synergistic tissue-remodeling pathway interactions.",
    researchFocus: [
      "Synergistic VEGF and actin signaling interactions",
      "Complementary growth factor receptor activation",
      "Combined extracellular matrix remodeling mechanisms",
    ],
    studies: ["Synergy analysis models (in vitro)", "Combined pathway activation studies"],
    purity: "≥98% each compound (HPLC verified)",
    price: "$94.99",
  },
];

export function getProductsByCategory(categorySlug: string): Product[] {
  return SAMPLE_PRODUCTS.filter((p) => p.categorySlug === categorySlug);
}

export function getProductBySlug(slug: string): Product | undefined {
  return SAMPLE_PRODUCTS.find((p) => p.slug === slug);
}
