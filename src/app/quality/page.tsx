import type { Metadata } from "next";
import Link from "next/link";
import PageHero from "@/components/PageHero";

export const metadata: Metadata = {
  title: "Quality",
  description:
    "Our quality assurance program includes third-party HPLC testing, mass spectrometry verification, and publicly accessible certificates of analysis.",
};

const QUALITY_SECTIONS = [
  {
    title: "Testing & Verification",
    href: "/quality/testing",
    description:
      "Independent third-party HPLC and mass spectrometry analysis for every batch. Endotoxin screening and sterility verification protocols.",
    icon: "01",
  },
  {
    title: "Lab Results / COA",
    href: "/quality/coa",
    description:
      "Publicly accessible certificates of analysis for every compound. Purity data, molecular identification, and batch-specific documentation.",
    icon: "02",
  },
  {
    title: "Manufacturing Standards",
    href: "/quality/manufacturing",
    description:
      "GMP-compliant synthesis facilities, validated production processes, and controlled environment standards for peptide manufacturing.",
    icon: "03",
  },
  {
    title: "Handling & Storage",
    href: "/quality/handling",
    description:
      "Proper reconstitution, storage temperature requirements, and stability documentation for maintaining compound integrity.",
    icon: "04",
  },
];

export default function QualityPage() {
  return (
    <>
      <PageHero
        title="Quality Assurance"
        subtitle="Every compound undergoes rigorous independent testing and verification. We publish certificates of analysis for complete transparency."
      />
      <section className="mx-auto max-w-7xl px-6 py-16">
        <div className="grid gap-6 md:grid-cols-2">
          {QUALITY_SECTIONS.map((section) => (
            <Link
              key={section.href}
              href={section.href}
              className="group rounded-lg border border-brand-border bg-brand-dark p-8 transition-all hover:border-text-secondary/30"
            >
              <span className="font-mono text-xs text-text-secondary/60">
                {section.icon}
              </span>
              <h3 className="mt-3 font-serif text-xl font-semibold text-text-primary">
                {section.title}
              </h3>
              <p className="mt-3 text-sm leading-relaxed text-text-secondary">
                {section.description}
              </p>
              <span className="mt-4 inline-block text-xs font-medium text-text-secondary group-hover:text-text-primary">
                Learn more &rarr;
              </span>
            </Link>
          ))}
        </div>
      </section>
    </>
  );
}
