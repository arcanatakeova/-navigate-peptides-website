import Link from "next/link";
import CategoryCard from "@/components/CategoryCard";
import { CATEGORIES, DISCLAIMERS, SITE_NAME } from "@/lib/constants";

export default function HomePage() {
  return (
    <>
      {/* Hero */}
      <section className="relative overflow-hidden border-b border-brand-border bg-brand-dark">
        <div className="absolute inset-0 opacity-5">
          <div className="absolute inset-0 bg-[radial-gradient(circle_at_30%_50%,#4A6B5F_0%,transparent_50%)]" />
          <div className="absolute inset-0 bg-[radial-gradient(circle_at_70%_80%,#2F4666_0%,transparent_50%)]" />
        </div>
        <div className="relative mx-auto max-w-7xl px-6 py-24 md:py-36">
          <p className="mb-4 font-mono text-xs uppercase tracking-[0.2em] text-text-secondary">
            Research Peptide Compounds
          </p>
          <h1 className="max-w-4xl font-serif text-4xl font-semibold leading-tight text-text-primary md:text-6xl md:leading-tight">
            Advancing Peptide Research Through Precision and Purity
          </h1>
          <p className="mt-6 max-w-2xl text-lg leading-relaxed text-text-secondary">
            High-purity research compounds with verified certificates of analysis.
            Supporting scientific investigation through rigorous quality standards
            and transparent documentation.
          </p>
          <div className="mt-10 flex flex-col gap-4 sm:flex-row">
            <Link
              href="/compounds"
              className="rounded-md bg-text-primary px-6 py-3 text-center text-sm font-semibold text-brand transition-opacity hover:opacity-90"
            >
              Browse Compounds
            </Link>
            <Link
              href="/quality"
              className="rounded-md border border-brand-border px-6 py-3 text-center text-sm font-medium text-text-primary transition-colors hover:bg-brand-light"
            >
              View Quality Standards
            </Link>
          </div>
        </div>
      </section>

      {/* Research Categories */}
      <section className="mx-auto max-w-7xl px-6 py-20">
        <div className="mb-12">
          <p className="mb-2 font-mono text-xs uppercase tracking-[0.2em] text-text-secondary">
            Research Categories
          </p>
          <h2 className="font-serif text-3xl font-semibold text-text-primary">
            Compound Library
          </h2>
        </div>
        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          {CATEGORIES.map((category) => (
            <CategoryCard key={category.slug} category={category} />
          ))}
        </div>
      </section>

      {/* Quality Pillars */}
      <section className="border-t border-brand-border bg-brand-dark">
        <div className="mx-auto max-w-7xl px-6 py-20">
          <div className="mb-12">
            <p className="mb-2 font-mono text-xs uppercase tracking-[0.2em] text-text-secondary">
              Our Approach
            </p>
            <h2 className="font-serif text-3xl font-semibold text-text-primary">
              Research-Grade Standards
            </h2>
          </div>
          <div className="grid gap-8 md:grid-cols-3">
            {[
              {
                title: "Third-Party Testing",
                description:
                  "Every batch undergoes independent HPLC and mass spectrometry analysis by accredited laboratories.",
                icon: "01",
              },
              {
                title: "Certificate of Analysis",
                description:
                  "Publicly accessible COAs for every compound, including purity data, molecular identification, and endotoxin screening.",
                icon: "02",
              },
              {
                title: "Research Documentation",
                description:
                  "Comprehensive compound profiles with cited preclinical research, pathway analysis, and mechanism-of-action context.",
                icon: "03",
              },
            ].map((pillar) => (
              <div
                key={pillar.icon}
                className="rounded-lg border border-brand-border p-6"
              >
                <span className="font-mono text-xs text-text-secondary">
                  {pillar.icon}
                </span>
                <h3 className="mt-3 font-serif text-lg font-semibold text-text-primary">
                  {pillar.title}
                </h3>
                <p className="mt-2 text-sm leading-relaxed text-text-secondary">
                  {pillar.description}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="border-t border-brand-border">
        <div className="mx-auto max-w-7xl px-6 py-20 text-center">
          <h2 className="font-serif text-3xl font-semibold text-text-primary">
            Begin Your Research
          </h2>
          <p className="mx-auto mt-4 max-w-xl text-text-secondary">
            Access our full catalog of research-grade peptide compounds. All
            products include certificates of analysis and are intended for
            research purposes only.
          </p>
          <div className="mt-8 flex justify-center gap-4">
            <Link
              href="/compounds"
              className="rounded-md bg-text-primary px-6 py-3 text-sm font-semibold text-brand transition-opacity hover:opacity-90"
            >
              View Compounds
            </Link>
            <Link
              href="/about/contact"
              className="rounded-md border border-brand-border px-6 py-3 text-sm font-medium text-text-primary transition-colors hover:bg-brand-light"
            >
              Request Access
            </Link>
          </div>
        </div>
      </section>

      {/* Sitewide Disclaimer Banner */}
      <section className="border-t border-brand-border bg-brand-dark">
        <div className="mx-auto max-w-7xl px-6 py-6">
          <p className="text-center font-mono text-xs leading-relaxed text-text-secondary/70">
            {DISCLAIMERS.sitewide}
          </p>
        </div>
      </section>
    </>
  );
}
