# Local Last Update

**Contributors:** topher1kenobe
**Tags:** plugins, admin, maintenance, updates, column
**Donate link:** https://heropress.com/donate
**Requires at least:** 5.0
**Tested up to:** 6.9
**Requires PHP:** 7.2
**Stable tag:** 2.0.1
**License:** GPLv2 or later
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin that adds a sortable "Last Updated" column to the Plugins admin screen, showing when each plugin's files were last modified on your server.

## Description

Ever wonder which plugins on your site haven't been touched in years? **Local Plugin Last Update** adds a "Last Updated" column to your WordPress plugins list that shows the last time each plugin's files were modified on your server.

Unlike the "last updated" date shown in the plugin repository (which reflects when the author released a new version), this plugin shows you the actual file modification date on *your* installation — making it a practical tool for auditing stale plugins, identifying what changed after a bulk update, or just keeping tabs on your site.

### Features

- Adds a "Last Updated" column to the Plugins admin screen
- Dates reflect real file modification times on your server
- Column header is clickable — sort ascending or descending by date
- Update-notice rows stay correctly paired with their plugin when sorting
- Lightweight — one PHP file, no database queries, no external requests
- No settings page needed — works immediately on activation

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher

## Installation

1. Clone or download this repository into your `/wp-content/plugins/` directory:
   ```bash
   cd wp-content/plugins
   git clone https://github.com/topher/local-plugin-last-update.git
   ```
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Visit **Plugins** — the "Last Updated" column appears immediately.

Alternatively, download the zip and install via **Plugins → Add New → Upload Plugin** in your WordPress admin.

## FAQ

**What date is being shown?**
The date your server's filesystem last recorded a modification to the plugin's main PHP file. This changes when the plugin is installed, updated, or when the file is otherwise written to on disk. It is not the release date from the WordPress.org plugin repository.

**Why is the date different from what I see on wordpress.org?**
The wordpress.org repository shows when the plugin author published a new version. This plugin shows when the file on *your server* was last changed. If you installed a plugin a long time ago and it hasn't been updated since, those two dates can be very different.

**Why doesn't sorting happen on the server side?**
The WordPress plugins list isn't backed by a database query the way post lists are, so the standard `WP_List_Table` orderby mechanism doesn't apply. Sorting is handled in the browser using the file modification timestamps embedded in each row — it's fast and requires no additional requests.

**Does this work with must-use plugins or drop-ins?**
No — it only covers plugins shown in the standard Plugins admin screen.

## Changelog

### 1.1.0
- Added sortable column header — click to sort ascending or descending by date
- Added machine-readable timestamp attribute to each cell to ensure accurate sorting
- Update-notice rows now stay correctly paired with their parent plugin row after sorting

### 1.0.0
- Initial release

## License

[GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)
