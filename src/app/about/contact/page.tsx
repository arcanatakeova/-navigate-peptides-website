import type { Metadata } from "next";
import PageHero from "@/components/PageHero";

export const metadata: Metadata = {
  title: "Contact / Request Access",
  description:
    "Contact Navigate Peptides for research inquiries, wholesale requests, or account access.",
};

export default function ContactPage() {
  return (
    <>
      <PageHero
        title="Request Access"
        subtitle="Contact our team for research inquiries, wholesale pricing, or to request an account."
      />
      <section className="mx-auto max-w-7xl px-6 py-16">
        <div className="mx-auto max-w-2xl">
          <div className="rounded-lg border border-brand-border bg-brand-dark p-8">
            <form className="space-y-6">
              <div className="grid gap-6 sm:grid-cols-2">
                <div>
                  <label
                    htmlFor="firstName"
                    className="mb-1 block font-mono text-xs text-text-secondary"
                  >
                    First Name
                  </label>
                  <input
                    id="firstName"
                    type="text"
                    required
                    className="w-full rounded-md border border-brand-border bg-brand px-4 py-3 text-sm text-text-primary placeholder:text-text-secondary/40 focus:border-text-secondary/50 focus:outline-none"
                  />
                </div>
                <div>
                  <label
                    htmlFor="lastName"
                    className="mb-1 block font-mono text-xs text-text-secondary"
                  >
                    Last Name
                  </label>
                  <input
                    id="lastName"
                    type="text"
                    required
                    className="w-full rounded-md border border-brand-border bg-brand px-4 py-3 text-sm text-text-primary placeholder:text-text-secondary/40 focus:border-text-secondary/50 focus:outline-none"
                  />
                </div>
              </div>
              <div>
                <label
                  htmlFor="email"
                  className="mb-1 block font-mono text-xs text-text-secondary"
                >
                  Email Address
                </label>
                <input
                  id="email"
                  type="email"
                  required
                  className="w-full rounded-md border border-brand-border bg-brand px-4 py-3 text-sm text-text-primary placeholder:text-text-secondary/40 focus:border-text-secondary/50 focus:outline-none"
                />
              </div>
              <div>
                <label
                  htmlFor="organization"
                  className="mb-1 block font-mono text-xs text-text-secondary"
                >
                  Organization / Institution
                </label>
                <input
                  id="organization"
                  type="text"
                  className="w-full rounded-md border border-brand-border bg-brand px-4 py-3 text-sm text-text-primary placeholder:text-text-secondary/40 focus:border-text-secondary/50 focus:outline-none"
                />
              </div>
              <div>
                <label
                  htmlFor="inquiry"
                  className="mb-1 block font-mono text-xs text-text-secondary"
                >
                  Inquiry Type
                </label>
                <select
                  id="inquiry"
                  className="w-full rounded-md border border-brand-border bg-brand px-4 py-3 text-sm text-text-primary focus:border-text-secondary/50 focus:outline-none"
                >
                  <option value="general">General Inquiry</option>
                  <option value="wholesale">Wholesale / Bulk Pricing</option>
                  <option value="account">Account Access Request</option>
                  <option value="technical">Technical / Product Question</option>
                  <option value="coa">COA Request</option>
                </select>
              </div>
              <div>
                <label
                  htmlFor="message"
                  className="mb-1 block font-mono text-xs text-text-secondary"
                >
                  Message
                </label>
                <textarea
                  id="message"
                  rows={5}
                  required
                  className="w-full rounded-md border border-brand-border bg-brand px-4 py-3 text-sm text-text-primary placeholder:text-text-secondary/40 focus:border-text-secondary/50 focus:outline-none"
                />
              </div>
              <button
                type="submit"
                className="w-full rounded-md bg-text-primary px-4 py-3 text-sm font-semibold text-brand transition-opacity hover:opacity-90"
              >
                Submit Inquiry
              </button>
            </form>
          </div>
        </div>
      </section>
    </>
  );
}
