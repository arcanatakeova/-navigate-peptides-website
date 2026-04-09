import type { Metadata } from "next";
import PageHero from "@/components/PageHero";

export const metadata: Metadata = {
  title: "Handling & Storage",
  description:
    "Proper reconstitution protocols, storage temperature requirements, and stability documentation for research peptide compounds.",
};

export default function HandlingPage() {
  return (
    <>
      <PageHero
        title="Handling & Storage"
        subtitle="Proper handling and storage are critical for maintaining compound integrity. Follow these guidelines for optimal stability."
      />
      <section className="mx-auto max-w-7xl px-6 py-16">
        <div className="grid gap-8 lg:grid-cols-2">
          <div className="rounded-lg border border-brand-border bg-brand-dark p-6">
            <h3 className="font-serif text-lg font-semibold text-text-primary">
              Storage Requirements
            </h3>
            <ul className="mt-4 space-y-3">
              {[
                "Lyophilized peptides: Store at -20°C for long-term stability",
                "Reconstituted peptides: Store at 2-8°C, use within 30 days",
                "Avoid repeated freeze-thaw cycles",
                "Keep away from direct light exposure",
                "Store in original nitrogen-flushed vials until reconstitution",
              ].map((item) => (
                <li key={item} className="flex items-start gap-3 text-sm text-text-secondary">
                  <span className="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-text-secondary/40" />
                  {item}
                </li>
              ))}
            </ul>
          </div>
          <div className="rounded-lg border border-brand-border bg-brand-dark p-6">
            <h3 className="font-serif text-lg font-semibold text-text-primary">
              Reconstitution Protocol
            </h3>
            <ul className="mt-4 space-y-3">
              {[
                "Use bacteriostatic water or sterile water for reconstitution",
                "Add solvent slowly along the vial wall — do not agitate",
                "Allow lyophilized powder to dissolve naturally (2-5 minutes)",
                "Gently swirl if needed — never shake or vortex",
                "Calculate concentration based on total solvent volume added",
                "Document reconstitution date and concentration",
              ].map((item) => (
                <li key={item} className="flex items-start gap-3 text-sm text-text-secondary">
                  <span className="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-text-secondary/40" />
                  {item}
                </li>
              ))}
            </ul>
          </div>
        </div>
      </section>
    </>
  );
}
