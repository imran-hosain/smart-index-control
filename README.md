# Smart Index Control

A lightweight WordPress plugin that gives you simple, settings-based control over how search engines index your site — without editing your theme files or `.htaccess`.

## Features

- **Noindex tag/category archives** — Add a `noindex` directive to taxonomy archive pages with a single toggle, useful for sites where thin tag/category pages hurt SEO.
- **Disable XML/RSS feeds** — Turn off feed endpoints (`/feed/`, `/comments/feed/`, etc.) and return a proper `410 Gone` response instead of a 404, signaling to search engines that the feed is permanently removed.
- **Redirect attachment pages** — Automatically 301-redirect standalone attachment pages to their parent post, preventing duplicate/thin content from being indexed.

All features are optional and controlled from one settings screen — nothing is enabled by default.

## Installation

1. Download the latest release or clone this repository.
2. Upload the `smart-index-control` folder to `/wp-content/plugins/`.
3. Activate the plugin from the **Plugins** screen in WordPress.
4. Go to **Settings → Smart Index Control** to configure.

## Requirements

- WordPress 6.0+
- PHP 7.4+

## Why this plugin

Most SEO plugins bundle dozens of unrelated features. Smart Index Control focuses on three specific indexing/crawling issues that commonly come up during technical SEO audits, so you can fix them quickly without installing a heavier all-in-one SEO suite.

## Development

This plugin follows WordPress Coding Standards. Settings are stored as a single option (`sic_settings`) and cleaned up on uninstall, not on deactivation, so temporarily disabling the plugin never wipes your configuration.

## License

GPL v2 or later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).

## Author

[Imran Hosain](https://imranhosain.com) — WordPress & Shopify Developer
