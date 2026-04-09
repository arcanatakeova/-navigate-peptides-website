import type { Metadata } from "next";
import Link from "next/link";
import { DISCLAIMERS } from "@/lib/constants";

export const metadata: Metadata = {
  title: "Cart",
  description: "Review your research compound selections.",
};

export default function CartPage() {
  return (
    <div className="mx-auto max-w-4xl px-6 py-16">
      <h1 className="font-serif text-3xl font-semibold text-text-primary">
        Shopping Cart
      </h1>

      {/* Empty state — will be replaced with dynamic cart from WooCommerce */}
      <div className="mt-8 rounded-lg border border-brand-border bg-brand-dark p-12 text-center">
        <p className="text-text-secondary">Your cart is empty.</p>
        <Link
          href="/compounds"
          className="mt-4 inline-block rounded-md bg-text-primary px-6 py-3 text-sm font-semibold text-brand transition-opacity hover:opacity-90"
        >
          Browse Compounds
        </Link>
      </div>

      {/* Disclaimer */}
      <div className="mt-8 rounded-md border border-brand-border p-4">
        <p className="font-mono text-xs leading-relaxed text-text-secondary/70">
          {DISCLAIMERS.sitewide}
        </p>
      </div>
    </div>
  );
}
