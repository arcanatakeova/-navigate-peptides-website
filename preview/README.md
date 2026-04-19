# Static Previews

Open `index.html` in a browser to see the homepage rendered with the current theme CSS, no WordPress required. Useful for quick design iteration without spinning up a local WP stack.

## Usage

Double-click the file, or:

```bash
open preview/index.html       # macOS
xdg-open preview/index.html   # Linux
```

## Known limitations

- These files are **static snapshots of the homepage markup**. They won't reflect dynamic content (products from the DB, menu items, custom post types).
- No 3D vial model-viewer — uses the PNG fallback. The real site uses `model-viewer` with a `.glb` file.
- Search, cart, and WooCommerce interactions aren't functional.

## When to rebuild

Update the preview HTML when you change:

- Markup in `front-page.php`, `header.php`, or `footer.php` that affects the homepage
- CSS class names referenced in the preview
- Category icon SVGs (they're inlined into this file too — duplicate source, so keep in sync)

The CSS files are `<link>`ed directly from `wp-content/themes/navigate-peptides/assets/css/`, so style changes show up on reload with no preview-file edits needed.
