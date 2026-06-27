=== Smart Index Control ===
Contributors: imranhosain
Tags: seo, noindex, feed, attachment, indexing
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A focused SEO cleanup plugin: control indexing for tag/category archives, disable XML feeds, and redirect attachment pages.

== Description ==

Smart Index Control gives you simple, settings-based control over how search engines index your site — without editing your theme files or `.htaccess`.

**Features:**

* **Noindex tag/category archives** — Add a `noindex` meta tag to taxonomy archive pages with a single toggle, useful for sites where thin tag/category pages hurt SEO.
* **Disable XML/RSS feeds** — Turn off feed endpoints and return a proper `410 Gone` response instead of a 404, signaling to search engines that the feed is permanently removed.
* **Redirect attachment pages** — Automatically 301-redirect standalone attachment pages to their parent post, preventing duplicate/thin content from being indexed.
* **Custom redirect fallback URL** — Choose where orphaned attachments (with no parent post) should redirect to.
* **Export/Import settings** — Copy your configuration to another site in seconds.

All features are optional and controlled from a clean, tabbed settings dashboard. Nothing is enabled by default.

= Why this plugin =

Most SEO plugins bundle dozens of unrelated features. Smart Index Control focuses on three specific indexing/crawling issues that commonly come up during technical SEO audits, so you can fix them quickly without installing a heavier all-in-one SEO suite.

== Installation ==

1. Upload the `smart-index-control` folder to `/wp-content/plugins/`, or install directly through the WordPress Plugins screen.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **Smart Index Control** in your admin sidebar to configure.

== Frequently Asked Questions ==

= Will this conflict with my SEO plugin (Yoast, Rank Math, etc.)? =

No. Smart Index Control only outputs a noindex tag if your existing SEO plugin doesn't already control that specific page type. If you use another plugin's attachment-redirect or feed-disable feature already, you only need one of the two active to avoid redundant behavior.

= What happens to attachments with no parent post when redirect is enabled? =

They redirect to your homepage by default, or to a custom URL you set on the Advanced tab.

= Does this plugin add any front-end scripts or styles? =

No. It only outputs a meta tag conditionally and handles redirects/feed responses server-side. The plugin's own CSS/JS only loads on its own admin settings page.

== Screenshots ==

1. Archives tab — noindex controls for tag and category archives.
2. Feeds tab — disable RSS/Atom feeds with a single toggle.
3. Attachments tab — redirect standalone attachment pages to their parent post.
4. Advanced tab — custom redirect fallback URL and settings export/import.

== Changelog ==

= 1.0.0 =
* Initial release.
* Noindex controls for tag and category archives.
* Feed disabling with proper 410 Gone response.
* Attachment page redirect to parent post, with custom fallback URL.
* Settings export/import.
* Modern tabbed admin dashboard with instant client-side tab switching.

== Upgrade Notice ==

= 1.0.0 =
Initial release.