import type { Metadata } from "next";
import CategoryCard from "@/components/CategoryCard";
import PageHero from "@/components/PageHero";
import { CATEGORIES } from "@/lib/constants";

export const metadata: Metadata = {
  title: "Research Compounds",
  description:
    "Browse our catalog of research-grade peptide compounds organized by research category. All products are for research purposes only.",
};

export default function CompoundsPage() {
  return (
    <>
      <PageHero
        title="Research Compound Library"
        subtitle="Browse peptide compounds organized by research application. Each category contains compounds with verified certificates of analysis and detailed pathway documentation."
      />
      <section className="mx-auto max-w-7xl px-6 py-16">
        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {CATEGORIES.map((category) => (
            <CategoryCard key={category.slug} category={category} />
          ))}
        </div>
      </section>
    </>
  );
}
