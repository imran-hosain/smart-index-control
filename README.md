# Smart Index Control

A lightweight WordPress plugin that gives you simple, dashboard-based control over how search engines index your site — without editing your theme files or `.htaccess`.

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-21759B)
![License](https://img.shields.io/badge/license-GPLv2-blue)

## Features

- **Noindex tag/category archives** — Add a `noindex` directive to taxonomy archive pages with a single toggle, useful for sites where thin tag/category pages hurt SEO.
- **Disable XML/RSS feeds** — Turn off feed endpoints (`/feed/`, `/comments/feed/`, etc.) and return a proper `410 Gone` response instead of a 404, signaling to search engines that the feed is permanently removed.
- **Redirect attachment pages** — Automatically 301-redirect standalone attachment pages to their parent post, preventing duplicate/thin content from being indexed.
- **Custom redirect fallback URL** — Choose where orphaned attachments (no parent post) should redirect to, instead of always defaulting to the homepage.
- **Export/Import settings** — Download your configuration as JSON and re-apply it on another site in seconds.

All features are optional and controlled from a clean, tabbed admin dashboard — nothing is enabled by default.

## Admin UI

Smart Index Control ships with a custom-built dashboard (no page-builder or framework dependency):

- Top-level admin menu item, positioned alongside WooCommerce
- Tabbed navigation (Archives / Feeds / Attachments / Advanced / About) with **instant, client-side tab switching** — no page reload
- iOS-style toggle switches with live "X of Y features active" status
- Settings persist correctly per-tab (each tab's form preserves the other tabs' saved values on submit)

## Installation

1. Download the latest release or clone this repository.
2. Upload the `smart-index-control` folder to `/wp-content/plugins/`.
3. Activate the plugin from the **Plugins** screen in WordPress.
4. Find **Smart Index Control** in your admin sidebar to configure.

## Requirements

- WordPress 6.0+
- PHP 7.4+

## Why this plugin

Most SEO plugins bundle dozens of unrelated features. Smart Index Control focuses on three specific indexing/crawling issues that commonly come up during technical SEO audits, so you can fix them quickly without installing a heavier all-in-one SEO suite.

## Development notes

- Settings are stored as a single option (`smidx_settings`) and cleaned up on uninstall, not on deactivation, so temporarily disabling the plugin never wipes your configuration.
- The sanitize callback re-validates every known key on every save; hidden fields preserve cross-tab values so saving one tab never resets another.
- Admin CSS/JS is enqueued only on the plugin's own settings page — it never loads elsewhere in wp-admin.
- Follows WordPress Coding Standards throughout.

## License

GPL v2 or later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).

## Author

[Imran Hosain](https://imranhosain.com) — WordPress & Shopify Developer