import type { Metadata } from "next";
import { notFound } from "next/navigation";
import PageHero from "@/components/PageHero";
import ProductCard from "@/components/ProductCard";
import { CATEGORIES, getCategoryBySlug } from "@/lib/constants";
import { getProductsByCategory } from "@/lib/sample-products";

interface Props {
  params: Promise<{ category: string }>;
}

export async function generateStaticParams() {
  return CATEGORIES.map((c) => ({ category: c.slug }));
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { category: slug } = await params;
  const category = getCategoryBySlug(slug);
  if (!category) return {};
  return {
    title: category.name,
    description: category.description,
  };
}

export default async function CategoryPage({ params }: Props) {
  const { category: slug } = await params;
  const category = getCategoryBySlug(slug);
  if (!category) notFound();

  const products = getProductsByCategory(slug);

  return (
    <>
      <PageHero
        title={category.name}
        subtitle={category.description}
        accentColor={category.color}
      />
      <section className="mx-auto max-w-7xl px-6 py-16">
        {products.length > 0 ? (
          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {products.map((product) => (
              <ProductCard
                key={product.slug}
                name={product.name}
                slug={product.slug}
                category={slug}
                categoryColor={category.color}
                subtitle={product.subtitle}
                description={product.description}
                price={product.price}
              />
            ))}
          </div>
        ) : (
          <div className="rounded-lg border border-brand-border p-12 text-center">
            <p className="text-text-secondary">
              Compounds for this category are being prepared. Check back soon.
            </p>
          </div>
        )}
      </section>
    </>
  );
}
