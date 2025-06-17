=== FD Maintenance Report ===
Contributors: wordpress-fourthd
Donate link: https://fourthd.io
Tags: maintenance, report, performance, seo, security, plugins, system
Requires at least: 5.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Displays a complete WordPress maintenance report on a dedicated page, including core info, plugin status, SEO, performance, and security checks.

== Description ==

FD Maintenance Report is a lightweight plugin that generates a comprehensive report of your WordPress site. Ideal for developers, agencies, or site owners who want a quick overview of their site’s technical health.

**Features:**

- View WordPress core version and update status
- Display active theme and plugin information
- Analyze caching, SEO, backup, and analytics tools
- Check PHP settings and disk usage
- Review user roles and general security

A custom frontend page is generated at `/fd-maintenance-report` displaying the report.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Visit yoursite.com/fd-maintenance-report to view the report.

== Frequently Asked Questions ==

= Where is the report page located? =
The report is available at `https://yoursite.com/fd-maintenance-report`.

= Is the page protected? =
No, currently the page is public. We recommend securing it via a plugin like Password Protected or adding custom auth logic.

= Can I customize the layout? =
Yes, modify the file `templates/report-template.php` as needed.

== Screenshots ==

1. Report view showing core, plugin, and performance data.

== Changelog ==

= 1.0.0 =
* Initial release with core system checks and frontend reporting.

== Upgrade Notice ==

= 1.0.0 =
First stable release. Future updates may include admin settings, access control, and export features.

== License ==

This plugin is licensed under the GPLv2 or later.
