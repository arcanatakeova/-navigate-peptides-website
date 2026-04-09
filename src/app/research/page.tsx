import type { Metadata } from "next";
import Link from "next/link";
import PageHero from "@/components/PageHero";

export const metadata: Metadata = {
  title: "Research",
  description:
    "Explore peptide research resources including published preclinical studies, pathway analysis, and mechanism-of-action documentation.",
};

const RESEARCH_SECTIONS = [
  {
    title: "Research Intelligence",
    href: "/research/intelligence",
    description:
      "Curated analysis of peptide research developments, regulatory updates, and preclinical findings from peer-reviewed sources.",
    tag: "Analysis",
  },
  {
    title: "Research Library",
    href: "/research/library",
    description:
      "Comprehensive database of compound profiles, mechanism-of-action summaries, and referenced preclinical studies.",
    tag: "Database",
  },
  {
    title: "Research Framework",
    href: "/research/framework",
    description:
      "Methodological guidelines for peptide research including handling protocols, storage requirements, and documentation standards.",
    tag: "Methodology",
  },
  {
    title: "Emerging Research",
    href: "/research/emerging",
    description:
      "Coverage of novel peptide compounds and newly published research exploring uncharacterized signaling pathways.",
    tag: "Frontier",
  },
];

export default function ResearchPage() {
  return (
    <>
      <PageHero
        title="Research Hub"
        subtitle="Scientific resources for peptide research. Pathway analysis, mechanism-of-action documentation, and referenced preclinical studies — organized for research professionals."
      />
      <section className="mx-auto max-w-7xl px-6 py-16">
        <div className="grid gap-6 md:grid-cols-2">
          {RESEARCH_SECTIONS.map((section) => (
            <Link
              key={section.href}
              href={section.href}
              className="group rounded-lg border border-brand-border bg-brand-dark p-8 transition-all hover:border-text-secondary/30"
            >
              <span className="font-mono text-xs uppercase tracking-wider text-text-secondary/60">
                {section.tag}
              </span>
              <h3 className="mt-3 font-serif text-xl font-semibold text-text-primary">
                {section.title}
              </h3>
              <p className="mt-3 text-sm leading-relaxed text-text-secondary">
                {section.description}
              </p>
              <span className="mt-4 inline-block text-xs font-medium text-text-secondary group-hover:text-text-primary">
                Explore &rarr;
              </span>
            </Link>
          ))}
        </div>
      </section>
    </>
  );
}
