import type { Metadata } from "next";
import PageHero from "@/components/PageHero";

export const metadata: Metadata = {
  title: "Testing & Verification",
  description:
    "Independent third-party HPLC and mass spectrometry analysis for every batch of research peptide compounds.",
};

export default function TestingPage() {
  return (
    <>
      <PageHero
        title="Testing & Verification"
        subtitle="Every batch of every compound undergoes independent analytical testing before release. No exceptions."
      />
      <section className="mx-auto max-w-7xl px-6 py-16">
        <div className="grid gap-8 lg:grid-cols-2">
          {[
            {
              title: "HPLC Analysis",
              description:
                "High-performance liquid chromatography confirms compound purity and identifies any impurities or degradation products. Each batch must meet our minimum purity threshold.",
            },
            {
              title: "Mass Spectrometry",
              description:
                "Molecular weight verification via mass spectrometry confirms compound identity and structural integrity. Results are documented on every certificate of analysis.",
            },
            {
              title: "Endotoxin Screening",
              description:
                "LAL (Limulus Amebocyte Lysate) testing screens for bacterial endotoxin contamination, ensuring compounds meet research-grade cleanliness standards.",
            },
            {
              title: "Sterility Verification",
              description:
                "Microbial testing protocols verify absence of bacterial and fungal contamination in lyophilized products prior to release.",
            },
          ].map((test) => (
            <div
              key={test.title}
              className="rounded-lg border border-brand-border bg-brand-dark p-6"
            >
              <h3 className="font-serif text-lg font-semibold text-text-primary">
                {test.title}
              </h3>
              <p className="mt-3 text-sm leading-relaxed text-text-secondary">
                {test.description}
              </p>
            </div>
          ))}
        </div>
      </section>
    </>
  );
}
