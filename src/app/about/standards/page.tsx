import type { Metadata } from "next";
import PageHero from "@/components/PageHero";

export const metadata: Metadata = {
  title: "Standards",
  description:
    "Navigate Peptides operational standards: quality assurance, compliance, transparency, and scientific integrity.",
};

export default function StandardsPage() {
  return (
    <>
      <PageHero
        title="Our Standards"
        subtitle="The principles that define our approach to research peptide supply."
      />
      <section className="mx-auto max-w-7xl px-6 py-16">
        <div className="space-y-8">
          {[
            {
              title: "Quality Without Compromise",
              description:
                "Every batch is independently tested. Every certificate of analysis is published. We do not ship compounds that fail to meet our purity specifications, regardless of cost implications.",
            },
            {
              title: "Regulatory Compliance",
              description:
                "Our operations, content, and marketing practices comply with FDA regulations for research-use-only compounds, payment processor requirements, and applicable state and federal guidelines.",
            },
            {
              title: "Scientific Integrity",
              description:
                "Product descriptions reference published preclinical research and describe molecular mechanisms. We do not make health claims, therapeutic suggestions, or consumer-oriented marketing statements.",
            },
            {
              title: "Transparency",
              description:
                "Public certificates of analysis, documented testing methodologies, and clear research-use disclaimers on every product. No hidden information, no misleading language.",
            },
            {
              title: "Researcher-First Approach",
              description:
                "Our catalog, documentation, and quality processes are designed for research professionals. Scientific context, pathway documentation, and verified purity data — not marketing copy.",
            },
          ].map((standard) => (
            <div
              key={standard.title}
              className="rounded-lg border border-brand-border bg-brand-dark p-6"
            >
              <h3 className="font-serif text-lg font-semibold text-text-primary">
                {standard.title}
              </h3>
              <p className="mt-3 text-sm leading-relaxed text-text-secondary">
                {standard.description}
              </p>
            </div>
          ))}
        </div>
      </section>
    </>
  );
}
