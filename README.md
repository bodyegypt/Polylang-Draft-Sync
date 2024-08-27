# Polylang-Draft-Sync

## Description

Polylang Draft Sync is a WordPress plugin that automatically synchronizes draft status across all language versions of a post when any language version is set to draft. This plugin is designed to work with Polylang, a popular multilingual plugin for WordPress.

## Features

- Automatically sets all translations of a post to draft when one language version is set to draft
- Restores previous status of all translations when a draft post is published
- Provides admin notices for successful synchronization actions
- Logs errors for debugging purposes

## Requirements

- WordPress 4.7 or higher
- Polylang plugin installed and activated

## Installation

1. Download the plugin zip file
2. Go to your WordPress admin panel, navigate to Plugins > Add New
3. Click on "Upload Plugin" and choose the downloaded zip file
4. Click "Install Now" and then "Activate Plugin"

## Usage

Once activated, the plugin works automatically in the background. When you set any language version of a post to draft, all other language versions will also be set to draft. Similarly, when you publish a draft post, all other language versions will be restored to their previous status.

## Frequently Asked Questions

**Q: Does this plugin work with custom post types?**
A: Yes, as long as the custom post type is managed by Polylang, this plugin will synchronize its draft status.

**Q: What happens if I deactivate Polylang?**
A: If Polylang is deactivated, this plugin will display an admin notice informing you that Polylang is required for it to function.

## Changelog

### 1.2
- Added error logging functionality
- Improved admin notices

### 1.1
- Fixed bug with status restoration
- Added more detailed admin notices

### 1.0
- Initial release

## Support

For support, please create an issue on the plugin's GitHub repository or contact the author at https://abdallabayoumi.com.

## License

This plugin is licensed under the GPL v2 or later.

```
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
```

## Author

Abdalla Bayoumi
Website: https://abdallabayoumi.com
