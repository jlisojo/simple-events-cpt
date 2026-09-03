# Changelog

All notable changes to the Simple Events CPT plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] – 2026-09-03

### Added
- Initial release of Simple Events CPT
- Custom post type `se_event` for managing events
- Event categories and tags taxonomies
- REST API support for events
- Meta boxes for event details:
  - Start and end dates
  - Start and end times
  - Venue and location name
  - Street address, city, state, ZIP
  - Contact phone and email
  - Event website and registration link
  - Age range and capacity fields
  - Pricing (general, child, adult) and free event option
  - Short description for cards
- Admin list columns showing:
  - Event date and time
  - Venue location
  - Price
  - Event status (Past, Today, In X days, Upcoming)
- Event archive template with pagination and past events section
- Single event template with full details
- Theme-overridable templates via `simple-events/` subdirectory
- Schema.org Event and ItemList JSON-LD markup
- `[simple_events]` shortcode for displaying upcoming events
  - Configurable count, title, button text
  - Optional button to archive
- Settings page for:
  - Archive slug customization
  - Events per page
  - Currency symbol
  - Date format
- Sortable date column in WordPress admin
- Custom permalink structure with optional CPTP (Custom Post Type Permalinks) support
- Uninstall handler that preserves event content
- Language files and localization support
- GPL-2.0-or-later license

### Technical
- Organized architecture with separate classes for each concern
- Proper escaping and sanitization of all user input
- Nonce verification for meta box saves
- No external dependencies (WordPress core only)
- Compatible with WordPress 6.0+
- Compatible with PHP 7.4+
- Tested on modern WordPress installations
