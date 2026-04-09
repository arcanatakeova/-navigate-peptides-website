import Link from "next/link";
import type { Category } from "@/lib/constants";

export default function CategoryCard({ category }: { category: Category }) {
  return (
    <Link
      href={`/compounds/${category.slug}`}
      className="group block overflow-hidden rounded-lg border border-brand-border bg-brand-dark transition-all hover:border-transparent"
    >
      {/* Color Bar */}
      <div
        className="h-1.5 w-full transition-all group-hover:h-2"
        style={{ backgroundColor: category.color }}
      />
      <div className="p-6">
        <h3 className="mb-2 font-serif text-lg font-semibold text-text-primary">
          {category.name}
        </h3>
        <p className="text-sm leading-relaxed text-text-secondary">
          {category.description}
        </p>
        <span
          className="mt-4 inline-block text-xs font-medium uppercase tracking-wider"
          style={{ color: category.color }}
        >
          View Compounds &rarr;
        </span>
      </div>
    </Link>
  );
}
