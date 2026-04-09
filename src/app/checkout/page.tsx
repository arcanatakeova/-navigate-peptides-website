import type { Metadata } from "next";
import Link from "next/link";
import { DISCLAIMERS } from "@/lib/constants";

export const metadata: Metadata = {
  title: "Checkout",
  description: "Complete your research compound order.",
};

export default function CheckoutPage() {
  return (
    <div className="mx-auto max-w-4xl px-6 py-16">
      <h1 className="font-serif text-3xl font-semibold text-text-primary">
        Checkout
      </h1>

      <div className="mt-8 grid gap-8 lg:grid-cols-5">
        {/* Checkout Form */}
        <div className="lg:col-span-3">
          <div className="rounded-lg border border-brand-border bg-brand-dark p-6">
            <h2 className="mb-4 font-serif text-lg font-semibold text-text-primary">
              Shipping Information
            </h2>
            <form className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div>
                  <label htmlFor="checkoutFirstName" className="mb-1 block font-mono text-xs text-text-secondary">
                    First Name
                  </label>
                  <input
                    id="checkoutFirstName"
                    type="text"
                    required
                    className="w-full rounded-md border border-brand-border bg-brand px-4 py-3 text-sm text-text-primary focus:border-text-secondary/50 focus:outline-none"
                  />
                </div>
                <div>
                  <label htmlFor="checkoutLastName" className="mb-1 block font-mono text-xs text-text-secondary">
                    Last Name
                  </label>
                  <input
                    id="checkoutLastName"
                    type="text"
                    required
                    className="w-full rounded-md border border-brand-border bg-brand px-4 py-3 text-sm text-text-primary focus:border-text-secondary/50 focus:outline-none"
                  />
                </div>
              </div>
              <div>
                <label htmlFor="checkoutEmail" className="mb-1 block font-mono text-xs text-text-secondary">
                  Email
                </label>
                <input
                  id="checkoutEmail"
                  type="email"
                  required
                  className="w-full rounded-md border border-brand-border bg-brand px-4 py-3 text-sm text-text-primary focus:border-text-secondary/50 focus:outline-none"
                />
              </div>
              <div>
                <label htmlFor="checkoutAddress" className="mb-1 block font-mono text-xs text-text-secondary">
                  Address
                </label>
                <input
                  id="checkoutAddress"
                  type="text"
                  required
                  className="w-full rounded-md border border-brand-border bg-brand px-4 py-3 text-sm text-text-primary focus:border-text-secondary/50 focus:outline-none"
                />
              </div>
              <div className="grid gap-4 sm:grid-cols-3">
                <div>
                  <label htmlFor="checkoutCity" className="mb-1 block font-mono text-xs text-text-secondary">
                    City
                  </label>
                  <input
                    id="checkoutCity"
                    type="text"
                    required
                    className="w-full rounded-md border border-brand-border bg-brand px-4 py-3 text-sm text-text-primary focus:border-text-secondary/50 focus:outline-none"
                  />
                </div>
                <div>
                  <label htmlFor="checkoutState" className="mb-1 block font-mono text-xs text-text-secondary">
                    State
                  </label>
                  <input
                    id="checkoutState"
                    type="text"
                    required
                    className="w-full rounded-md border border-brand-border bg-brand px-4 py-3 text-sm text-text-primary focus:border-text-secondary/50 focus:outline-none"
                  />
                </div>
                <div>
                  <label htmlFor="checkoutZip" className="mb-1 block font-mono text-xs text-text-secondary">
                    ZIP
                  </label>
                  <input
                    id="checkoutZip"
                    type="text"
                    required
                    className="w-full rounded-md border border-brand-border bg-brand px-4 py-3 text-sm text-text-primary focus:border-text-secondary/50 focus:outline-none"
                  />
                </div>
              </div>
            </form>
          </div>

          {/* Payment section placeholder */}
          <div className="mt-6 rounded-lg border border-brand-border bg-brand-dark p-6">
            <h2 className="mb-4 font-serif text-lg font-semibold text-text-primary">
              Payment
            </h2>
            <p className="font-mono text-xs text-text-secondary">
              Payment processing via AllayPay/NMI gateway will be configured
              when the WooCommerce backend is connected. Collect.js tokenization
              fields will render here.
            </p>
          </div>

          {/* RUO Acknowledgment Checkbox */}
          <div className="mt-6 rounded-lg border border-brand-border bg-brand-dark p-6">
            <label className="flex items-start gap-3">
              <input
                type="checkbox"
                required
                className="mt-1 h-4 w-4 rounded border-brand-border bg-brand accent-text-primary"
              />
              <span className="text-sm leading-relaxed text-text-secondary">
                I acknowledge that all products purchased are intended for
                research and identification purposes only. These products are not
                intended for human dosing, injection, or ingestion.
              </span>
            </label>
          </div>

          <button className="mt-6 w-full rounded-md bg-text-primary px-6 py-3 text-sm font-semibold text-brand transition-opacity hover:opacity-90">
            Place Order
          </button>
        </div>

        {/* Order Summary */}
        <div className="lg:col-span-2">
          <div className="sticky top-24 rounded-lg border border-brand-border bg-brand-dark p-6">
            <h2 className="mb-4 font-serif text-lg font-semibold text-text-primary">
              Order Summary
            </h2>
            <div className="rounded-md border border-brand-border p-8 text-center">
              <p className="text-sm text-text-secondary">No items in cart.</p>
              <Link
                href="/compounds"
                className="mt-3 inline-block text-xs text-text-secondary underline underline-offset-2 hover:text-text-primary"
              >
                Browse compounds
              </Link>
            </div>
            <div className="mt-4">
              <p className="font-mono text-[10px] leading-relaxed text-text-secondary/60">
                {DISCLAIMERS.sitewide}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
