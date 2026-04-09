import type { Metadata } from "next";
import PageHero from "@/components/PageHero";

export const metadata: Metadata = {
  title: "Research Intelligence",
  description:
    "Curated analysis of peptide research developments and preclinical findings from peer-reviewed sources.",
};

export default function ResearchIntelligencePage() {
  return (
    <>
      <PageHero
        title="Research Intelligence"
        subtitle="Curated analysis of peptide research developments, regulatory updates, and preclinical findings sourced from peer-reviewed publications."
      />
      <section className="mx-auto max-w-7xl px-6 py-16">
        <div className="rounded-lg border border-brand-border bg-brand-dark p-12 text-center">
          <p className="font-mono text-sm text-text-secondary">
            Research intelligence articles are being prepared by our scientific
            review team. Published content will appear here.
          </p>
        </div>
      </section>
    </>
  );
}
