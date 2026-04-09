"use client";

import Link from "next/link";
import { useState } from "react";
import { NAV_ITEMS, SITE_NAME } from "@/lib/constants";

export default function Header() {
  const [mobileOpen, setMobileOpen] = useState(false);
  const [openDropdown, setOpenDropdown] = useState<string | null>(null);

  return (
    <header className="sticky top-0 z-50 border-b border-brand-border bg-brand-dark/95 backdrop-blur-sm">
      <div className="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
        {/* Logo */}
        <Link href="/" className="flex items-center gap-3">
          <div className="flex h-8 w-8 items-center justify-center rounded border border-brand-border">
            <span className="font-mono text-xs font-bold text-text-primary">NP</span>
          </div>
          <span className="font-serif text-lg font-semibold tracking-tight text-text-primary">
            {SITE_NAME}
          </span>
        </Link>

        {/* Desktop Nav */}
        <nav className="hidden items-center gap-8 lg:flex">
          {NAV_ITEMS.map((item) => (
            <div
              key={item.label}
              className="relative"
              onMouseEnter={() => setOpenDropdown(item.label)}
              onMouseLeave={() => setOpenDropdown(null)}
            >
              <Link
                href={item.href}
                className="text-sm font-medium text-text-secondary transition-colors hover:text-text-primary"
              >
                {item.label}
              </Link>
              {item.children && openDropdown === item.label && (
                <div className="absolute left-0 top-full pt-2">
                  <div className="min-w-[220px] rounded-md border border-brand-border bg-brand-dark p-2 shadow-xl">
                    {item.children.map((child) => (
                      <Link
                        key={child.href}
                        href={child.href}
                        className="block rounded px-3 py-2 text-sm text-text-secondary transition-colors hover:bg-brand-light hover:text-text-primary"
                      >
                        {child.label}
                      </Link>
                    ))}
                  </div>
                </div>
              )}
            </div>
          ))}
        </nav>

        {/* CTA + Mobile Toggle */}
        <div className="flex items-center gap-4">
          <Link
            href="/about/contact"
            className="hidden rounded-md bg-text-primary px-4 py-2 text-sm font-semibold text-brand transition-opacity hover:opacity-90 sm:block"
          >
            Request Access
          </Link>
          <button
            onClick={() => setMobileOpen(!mobileOpen)}
            className="flex h-10 w-10 items-center justify-center rounded-md border border-brand-border lg:hidden"
            aria-label="Toggle menu"
          >
            <svg
              className="h-5 w-5 text-text-primary"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              {mobileOpen ? (
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M6 18L18 6M6 6l12 12" />
              ) : (
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M4 6h16M4 12h16M4 18h16" />
              )}
            </svg>
          </button>
        </div>
      </div>

      {/* Mobile Nav */}
      {mobileOpen && (
        <div className="border-t border-brand-border bg-brand-dark px-6 py-4 lg:hidden">
          {NAV_ITEMS.map((item) => (
            <div key={item.label} className="mb-4">
              <Link
                href={item.href}
                className="block text-sm font-semibold text-text-primary"
                onClick={() => setMobileOpen(false)}
              >
                {item.label}
              </Link>
              {item.children && (
                <div className="mt-2 space-y-1 pl-4">
                  {item.children.map((child) => (
                    <Link
                      key={child.href}
                      href={child.href}
                      className="block text-sm text-text-secondary hover:text-text-primary"
                      onClick={() => setMobileOpen(false)}
                    >
                      {child.label}
                    </Link>
                  ))}
                </div>
              )}
            </div>
          ))}
          <Link
            href="/about/contact"
            className="mt-2 block rounded-md bg-text-primary px-4 py-2 text-center text-sm font-semibold text-brand"
            onClick={() => setMobileOpen(false)}
          >
            Request Access
          </Link>
        </div>
      )}
    </header>
  );
}
