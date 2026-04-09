import type { Metadata } from "next";
import PageHero from "@/components/PageHero";

export const metadata: Metadata = {
  title: "Emerging Research",
  description:
    "Coverage of novel peptide compounds and newly published research exploring uncharacterized signaling pathways.",
};

export default function EmergingResearchPage() {
  return (
    <>
      <PageHero
        title="Emerging Research"
        subtitle="Novel peptide compounds and recently published preclinical findings. Covering newly identified signaling pathways and research-stage molecules."
      />
      <section className="mx-auto max-w-7xl px-6 py-16">
        <div className="rounded-lg border border-brand-border bg-brand-dark p-12 text-center">
          <p className="font-mono text-sm text-text-secondary">
            Emerging research coverage is being prepared. New findings and novel
            compound analyses will appear here.
          </p>
        </div>
      </section>
    </>
  );
}
