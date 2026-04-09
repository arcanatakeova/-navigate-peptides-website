import type { Metadata } from "next";
import PageHero from "@/components/PageHero";

export const metadata: Metadata = {
  title: "Lab Results / COA",
  description:
    "Access certificates of analysis for all Navigate Peptides compounds. Purity data, molecular identification, and batch-specific results.",
};

export default function COAPage() {
  return (
    <>
      <PageHero
        title="Certificates of Analysis"
        subtitle="Every compound ships with a batch-specific certificate of analysis. Search by product name or batch number to access testing documentation."
      />
      <section className="mx-auto max-w-7xl px-6 py-16">
        {/* COA Search */}
        <div className="mx-auto max-w-xl">
          <div className="rounded-lg border border-brand-border bg-brand-dark p-8">
            <h3 className="mb-4 font-serif text-lg font-semibold text-text-primary">
              COA Lookup
            </h3>
            <div className="space-y-4">
              <div>
                <label
                  htmlFor="batch"
                  className="mb-1 block font-mono text-xs text-text-secondary"
                >
                  Batch Number or Product Name
                </label>
                <input
                  id="batch"
                  type="text"
                  placeholder="e.g., BPC-157 or NP-2024-0142"
                  className="w-full rounded-md border border-brand-border bg-brand px-4 py-3 text-sm text-text-primary placeholder:text-text-secondary/40 focus:border-text-secondary/50 focus:outline-none"
                />
              </div>
              <button className="w-full rounded-md bg-text-primary px-4 py-3 text-sm font-semibold text-brand transition-opacity hover:opacity-90">
                Search Certificates
              </button>
            </div>
            <p className="mt-4 font-mono text-xs text-text-secondary/60">
              COA database is being populated. All certificates will be
              searchable here once published.
            </p>
          </div>
        </div>
      </section>
    </>
  );
}
