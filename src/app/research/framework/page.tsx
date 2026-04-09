import type { Metadata } from "next";
import PageHero from "@/components/PageHero";

export const metadata: Metadata = {
  title: "Research Framework",
  description:
    "Methodological guidelines for peptide research including handling protocols, storage requirements, and documentation standards.",
};

export default function ResearchFrameworkPage() {
  return (
    <>
      <PageHero
        title="Research Framework"
        subtitle="Methodological guidelines and best practices for peptide research. Protocols for handling, reconstitution, storage, and experimental documentation."
      />
      <section className="mx-auto max-w-7xl px-6 py-16">
        <div className="rounded-lg border border-brand-border bg-brand-dark p-12 text-center">
          <p className="font-mono text-sm text-text-secondary">
            Research framework documentation is in development. Protocols and
            methodology guides will be published here.
          </p>
        </div>
      </section>
    </>
  );
}
