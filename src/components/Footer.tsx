import Link from "next/link";
import { DISCLAIMERS, NAV_ITEMS, SITE_NAME } from "@/lib/constants";

export default function Footer() {
  return (
    <footer className="border-t border-brand-border bg-brand-dark">
      <div className="mx-auto max-w-7xl px-6 py-12">
        {/* Navigation Grid */}
        <div className="grid grid-cols-2 gap-8 md:grid-cols-4">
          {NAV_ITEMS.map((item) => (
            <div key={item.label}>
              <h4 className="mb-3 font-sans text-xs font-semibold uppercase tracking-wider text-text-secondary">
                {item.label}
              </h4>
              <ul className="space-y-2">
                {item.children?.map((child) => (
                  <li key={child.href}>
                    <Link
                      href={child.href}
                      className="text-sm text-text-secondary transition-colors hover:text-text-primary"
                    >
                      {child.label}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        {/* Divider */}
        <div className="my-8 border-t border-brand-border" />

        {/* Compliance Disclaimer */}
        <div className="rounded-md border border-brand-border bg-brand/50 p-4">
          <p className="font-mono text-xs leading-relaxed text-text-secondary">
            {DISCLAIMERS.sitewide}
          </p>
        </div>

        {/* Bottom Bar */}
        <div className="mt-8 flex flex-col items-center justify-between gap-4 text-xs text-text-secondary sm:flex-row">
          <p>&copy; {new Date().getFullYear()} {SITE_NAME}. All rights reserved.</p>
          <div className="flex gap-4">
            <Link href="/about/contact" className="hover:text-text-primary">
              Contact
            </Link>
            <span className="text-brand-border">|</span>
            <span>Research Use Only</span>
          </div>
        </div>
      </div>
    </footer>
  );
}
