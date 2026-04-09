import type { Metadata } from "next";
import PageHero from "@/components/PageHero";

export const metadata: Metadata = {
  title: "Research Library",
  description:
    "Comprehensive database of compound profiles, mechanism-of-action summaries, and referenced preclinical studies.",
};

export default function ResearchLibraryPage() {
  return (
    <>
      <PageHero
        title="Research Library"
        subtitle="Compound profiles with detailed mechanism-of-action summaries, pathway diagrams, and references to published preclinical studies."
      />
      <section className="mx-auto max-w-7xl px-6 py-16">
        <div className="rounded-lg border border-brand-border bg-brand-dark p-12 text-center">
          <p className="font-mono text-sm text-text-secondary">
            The research library is being compiled. Compound profiles with cited
            preclinical references will be published here.
          </p>
        </div>
      </section>
    </>
  );
}
