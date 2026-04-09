import type { Metadata } from "next";
import Link from "next/link";
import PageHero from "@/components/PageHero";

export const metadata: Metadata = {
  title: "About",
  description:
    "Navigate Peptides is a research peptide supplier committed to purity, transparency, and scientific rigor. All products are for research purposes only.",
};

export default function AboutPage() {
  return (
    <>
      <PageHero
        title="About Navigate Peptides"
        subtitle="A research-focused peptide supplier built on transparency, scientific rigor, and uncompromising quality standards."
      />
      <section className="mx-auto max-w-7xl px-6 py-16">
        <div className="grid gap-12 lg:grid-cols-5">
          <div className="lg:col-span-3">
            <div className="space-y-6 text-sm leading-relaxed text-text-secondary">
              <p>
                Navigate Peptides was founded to address a gap in the research
                peptide market: the need for a supplier that prioritizes
                scientific credibility, transparent quality documentation, and
                rigorous compliance standards.
              </p>
              <p>
                Every compound in our catalog undergoes independent third-party
                testing via HPLC and mass spectrometry. Certificates of analysis
                are published for every batch, providing researchers with the
                verification data they need to trust their materials.
              </p>
              <p>
                Our compound profiles include detailed mechanism-of-action
                documentation, cited preclinical studies, and pathway analysis —
                because researchers need more than a product listing. They need
                scientific context.
              </p>
              <p>
                All products sold by Navigate Peptides are intended for research
                and identification purposes only.
              </p>
            </div>
          </div>
          <div className="space-y-6 lg:col-span-2">
            <Link
              href="/about/standards"
              className="block rounded-lg border border-brand-border bg-brand-dark p-6 transition-all hover:border-text-secondary/30"
            >
              <h3 className="font-serif text-lg font-semibold text-text-primary">
                Our Standards
              </h3>
              <p className="mt-2 text-sm text-text-secondary">
                The principles and commitments that guide our operations.
              </p>
              <span className="mt-3 inline-block text-xs text-text-secondary">
                Read more &rarr;
              </span>
            </Link>
            <Link
              href="/about/contact"
              className="block rounded-lg border border-brand-border bg-brand-dark p-6 transition-all hover:border-text-secondary/30"
            >
              <h3 className="font-serif text-lg font-semibold text-text-primary">
                Contact / Request Access
              </h3>
              <p className="mt-2 text-sm text-text-secondary">
                Reach our team for inquiries, wholesale requests, or research
                collaboration.
              </p>
              <span className="mt-3 inline-block text-xs text-text-secondary">
                Get in touch &rarr;
              </span>
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}
