interface PageHeroProps {
  title: string;
  subtitle?: string;
  accentColor?: string;
}

export default function PageHero({ title, subtitle, accentColor }: PageHeroProps) {
  return (
    <section className="border-b border-brand-border bg-brand-dark">
      <div className="mx-auto max-w-7xl px-6 py-16 md:py-24">
        {accentColor && (
          <div
            className="mb-4 h-1 w-12 rounded-full"
            style={{ backgroundColor: accentColor }}
          />
        )}
        <h1 className="max-w-3xl font-serif text-3xl font-semibold leading-tight text-text-primary md:text-5xl">
          {title}
        </h1>
        {subtitle && (
          <p className="mt-4 max-w-2xl text-lg leading-relaxed text-text-secondary">
            {subtitle}
          </p>
        )}
      </div>
    </section>
  );
}
