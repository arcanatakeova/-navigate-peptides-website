import type { Metadata } from "next";
import PageHero from "@/components/PageHero";

export const metadata: Metadata = {
  title: "Manufacturing Standards",
  description:
    "GMP-compliant synthesis facilities and validated production processes for research-grade peptide manufacturing.",
};

export default function ManufacturingPage() {
  return (
    <>
      <PageHero
        title="Manufacturing Standards"
        subtitle="Research-grade peptide synthesis requires controlled environments, validated processes, and rigorous documentation at every stage."
      />
      <section className="mx-auto max-w-7xl px-6 py-16">
        <div className="space-y-6">
          {[
            {
              title: "GMP-Compliant Facilities",
              description:
                "All compounds are synthesized in facilities following Good Manufacturing Practice guidelines. Controlled environments with validated HVAC, cleanroom classification, and environmental monitoring.",
            },
            {
              title: "Solid-Phase Peptide Synthesis",
              description:
                "Automated SPPS with Fmoc chemistry protocols ensures consistent, high-purity peptide production. Each synthesis run follows validated standard operating procedures.",
            },
            {
              title: "Purification Protocols",
              description:
                "Reverse-phase HPLC purification removes truncated sequences, deletion products, and synthesis byproducts. Multiple purification cycles are used when required to meet purity specifications.",
            },
            {
              title: "Lyophilization & Packaging",
              description:
                "Controlled freeze-drying preserves compound stability and integrity. Nitrogen-flushed vials prevent oxidative degradation during storage and shipping.",
            },
          ].map((item) => (
            <div
              key={item.title}
              className="rounded-lg border border-brand-border bg-brand-dark p-6"
            >
              <h3 className="font-serif text-lg font-semibold text-text-primary">
                {item.title}
              </h3>
              <p className="mt-3 text-sm leading-relaxed text-text-secondary">
                {item.description}
              </p>
            </div>
          ))}
        </div>
      </section>
    </>
  );
}
