import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { DISCLAIMERS, getCategoryBySlug } from "@/lib/constants";
import { SAMPLE_PRODUCTS, getProductBySlug } from "@/lib/sample-products";

interface Props {
  params: Promise<{ category: string; slug: string }>;
}

export async function generateStaticParams() {
  return SAMPLE_PRODUCTS.map((p) => ({
    category: p.categorySlug,
    slug: p.slug,
  }));
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const product = getProductBySlug(slug);
  if (!product) return {};
  return {
    title: product.name,
    description: product.description,
  };
}

export default async function ProductDetailPage({ params }: Props) {
  const { category: categorySlug, slug } = await params;
  const product = getProductBySlug(slug);
  const category = getCategoryBySlug(categorySlug);
  if (!product || !category) notFound();

  return (
    <>
      {/* Breadcrumb */}
      <div className="border-b border-brand-border bg-brand-dark">
        <div className="mx-auto max-w-7xl px-6 py-3">
          <nav className="flex items-center gap-2 text-xs text-text-secondary">
            <Link href="/compounds" className="hover:text-text-primary">
              Compounds
            </Link>
            <span>/</span>
            <Link
              href={`/compounds/${category.slug}`}
              className="hover:text-text-primary"
            >
              {category.name}
            </Link>
            <span>/</span>
            <span className="text-text-primary">{product.name}</span>
          </nav>
        </div>
      </div>

      <div className="mx-auto max-w-7xl px-6 py-12">
        <div className="grid gap-12 lg:grid-cols-5">
          {/* Product Image / Placeholder */}
          <div className="lg:col-span-2">
            <div
              className="flex aspect-square items-center justify-center rounded-lg border border-brand-border"
              style={{ backgroundColor: `${category.color}10` }}
            >
              <div className="text-center">
                <div
                  className="mx-auto mb-3 flex h-20 w-20 items-center justify-center rounded-full border-2"
                  style={{ borderColor: category.color }}
                >
                  <span
                    className="font-mono text-lg font-bold"
                    style={{ color: category.color }}
                  >
                    {product.name.slice(0, 3).toUpperCase()}
                  </span>
                </div>
                <p className="font-mono text-xs text-text-secondary">
                  Product image
                </p>
              </div>
            </div>
          </div>

          {/* Product Details — 7-point structure */}
          <div className="lg:col-span-3">
            {/* 1. Product Name */}
            <div
              className="mb-1 inline-block rounded-sm px-2 py-0.5 text-xs font-medium"
              style={{ backgroundColor: `${category.color}30`, color: category.color }}
            >
              {category.name}
            </div>
            <h1 className="mt-2 font-serif text-3xl font-semibold text-text-primary md:text-4xl">
              {product.name}
            </h1>

            {/* 2. Technical Subtitle */}
            <p className="mt-2 font-mono text-sm text-text-secondary">
              {product.subtitle}
            </p>

            {/* 3. Short Scientific Description */}
            <p className="mt-4 text-base leading-relaxed text-text-secondary">
              {product.description}
            </p>

            {/* Technical specs */}
            {(product.sequence || product.molecularWeight) && (
              <div className="mt-6 grid grid-cols-2 gap-4 rounded-lg border border-brand-border bg-brand-dark p-4">
                {product.sequence && (
                  <div>
                    <p className="font-mono text-xs text-text-secondary/70">
                      Sequence
                    </p>
                    <p className="mt-1 font-mono text-sm text-text-primary">
                      {product.sequence}
                    </p>
                  </div>
                )}
                {product.molecularWeight && (
                  <div>
                    <p className="font-mono text-xs text-text-secondary/70">
                      Molecular Weight
                    </p>
                    <p className="mt-1 font-mono text-sm text-text-primary">
                      {product.molecularWeight}
                    </p>
                  </div>
                )}
              </div>
            )}

            {/* 4. Research Focus */}
            <div className="mt-8">
              <h2 className="mb-3 font-serif text-lg font-semibold text-text-primary">
                Research Focus
              </h2>
              <ul className="space-y-2">
                {product.researchFocus.map((focus) => (
                  <li key={focus} className="flex items-start gap-3 text-sm text-text-secondary">
                    <span
                      className="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full"
                      style={{ backgroundColor: category.color }}
                    />
                    {focus}
                  </li>
                ))}
              </ul>
            </div>

            {/* 5. Studies Available */}
            <div className="mt-8">
              <h2 className="mb-3 font-serif text-lg font-semibold text-text-primary">
                Referenced Studies
              </h2>
              <ul className="space-y-2">
                {product.studies.map((study) => (
                  <li
                    key={study}
                    className="flex items-center gap-2 font-mono text-xs text-text-secondary"
                  >
                    <span className="text-text-secondary/50">&mdash;</span>
                    {study}
                  </li>
                ))}
              </ul>
            </div>

            {/* 6. Purity / COA */}
            <div className="mt-8 rounded-lg border border-brand-border bg-brand-dark p-4">
              <h2 className="mb-2 font-serif text-lg font-semibold text-text-primary">
                Purity &amp; Verification
              </h2>
              <div className="flex items-center gap-4">
                <span className="font-mono text-sm text-text-primary">
                  {product.purity}
                </span>
                <Link
                  href="/quality/coa"
                  className="text-xs font-medium underline underline-offset-2"
                  style={{ color: category.color }}
                >
                  View Certificate of Analysis &rarr;
                </Link>
              </div>
            </div>

            {/* Price + Add to Cart */}
            <div className="mt-8 flex items-center gap-6 border-t border-brand-border pt-8">
              <span className="font-mono text-2xl font-bold text-text-primary">
                {product.price}
              </span>
              <button className="rounded-md bg-text-primary px-8 py-3 text-sm font-semibold text-brand transition-opacity hover:opacity-90">
                Add to Cart
              </button>
            </div>

            {/* 7. Research-Use Disclaimer */}
            <div className="mt-6 rounded-md border border-brand-border bg-brand-dark p-4">
              <p className="font-mono text-xs leading-relaxed text-text-secondary/80">
                {DISCLAIMERS.product}
              </p>
              <p className="mt-2 font-mono text-[10px] leading-relaxed text-text-secondary/60">
                {DISCLAIMERS.sitewide}
              </p>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
