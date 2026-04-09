import Link from "next/link";
import { DISCLAIMERS } from "@/lib/constants";

interface ProductCardProps {
  name: string;
  slug: string;
  category: string;
  categoryColor: string;
  subtitle: string;
  description: string;
  price: string;
}

export default function ProductCard({
  name,
  slug,
  category,
  categoryColor,
  subtitle,
  description,
  price,
}: ProductCardProps) {
  return (
    <Link
      href={`/compounds/${category}/${slug}`}
      className="group flex flex-col overflow-hidden rounded-lg border border-brand-border bg-brand-dark transition-all hover:border-transparent"
    >
      {/* Color accent */}
      <div className="h-1" style={{ backgroundColor: categoryColor }} />

      {/* Image placeholder */}
      <div
        className="flex h-48 items-center justify-center"
        style={{ backgroundColor: `${categoryColor}15` }}
      >
        <div className="flex h-16 w-16 items-center justify-center rounded-full border border-brand-border">
          <span className="font-mono text-xs text-text-secondary">
            {name.slice(0, 3).toUpperCase()}
          </span>
        </div>
      </div>

      <div className="flex flex-1 flex-col p-5">
        <h3 className="font-serif text-base font-semibold text-text-primary">
          {name}
        </h3>
        <p className="mt-1 font-mono text-xs text-text-secondary">{subtitle}</p>
        <p className="mt-3 flex-1 text-sm leading-relaxed text-text-secondary">
          {description}
        </p>
        <div className="mt-4 flex items-center justify-between border-t border-brand-border pt-4">
          <span className="font-mono text-sm font-medium text-text-primary">
            {price}
          </span>
          <span
            className="text-xs font-medium uppercase tracking-wider"
            style={{ color: categoryColor }}
          >
            Details &rarr;
          </span>
        </div>
        <p className="mt-3 text-[10px] leading-tight text-text-secondary/60">
          {DISCLAIMERS.product}
        </p>
      </div>
    </Link>
  );
}
