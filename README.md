# Simple Events CPT

A lightweight WordPress plugin for publishing events. It registers an `se_event` custom post type with date, location, and pricing fields, theme-overridable templates, Schema.org markup, and a shortcode.

Built as a generic, self-contained plugin suitable for any WordPress site. No page builder or third-party ticketing vendor required.

**📦 Available on WordPress.org:** [WordPress Plugin Directory](https://wordpress.org/plugins/simple-events-cpt/)

**🚀 Premium features:** [Simple Events Pro](https://github.com/jlisojo/simple-events-pro) (separate add-on with recurring events, calendar view, iCal export)

## Features

- **Events CPT** with categories and tags, REST-enabled
- **Meta boxes** for dates, times, venue, contact, pricing, and a registration URL
- **Admin list columns** for date, location, price, and upcoming / today / past status
- **Archive + single templates** with theme overrides
- **Schema.org** Event and ItemList JSON-LD
- **Gutenberg Event Grid Block** for inserting events directly in the Block Editor with live preview and custom inspector controls
- **`[simple_events]` shortcode** for upcoming event cards
- **Settings page** for archive slug, events per page, currency, and date format
- **Optional CPTP compatibility** if Custom Post Type Permalinks is active

## Requirements

- WordPress 6.0+
- PHP 7.4+

## Installation

1. Copy this folder to `wp-content/plugins/simple-events-cpt/`
2. Activate **Simple Events CPT**
3. Visit **Settings → Permalinks** (or just activate; rewrite rules are flushed on activation)
4. Add events under **Events** in wp-admin

## Usage

### Create an event

1. Go to **Events → Add New**
2. Add a title, featured image, and description
3. Fill in start date and venue (required for a complete listing)
4. Optionally add pricing, contact info, and a registration URL
5. Publish

Single events live at `/{slug}/{event-name}/`. The default slug is `events`.

### Gutenberg Block

Add the **Event Grid** block in the WordPress Block Editor (`simple-events-cpt/event-grid`). You can configure grid title, number of events, toggle the "View All" button, and set custom button labels directly in the block sidebar inspector with a real-time preview.

### Shortcode

```
[simple_events]
[simple_events count="6" title="Upcoming Events" show_button="true" button_text="See all events"]
```

| Parameter | Default | Description |
|---|---|---|
| `count` | `3` | Number of upcoming events |
| `title` | empty | Optional heading |
| `show_button` | `true` | Show a link to the archive |
| `button_text` | `View All Events` | Archive button label |
| `button_url` | events archive | Override the archive URL |

### Settings

**Events → Settings**

- Archive slug
- Events per page
- Currency symbol
- Date format (blank uses the WordPress date format)

## Template overrides

Copy a plugin template into the active theme:

- `single-se_event.php` or `simple-events/single-se_event.php`
- `archive-se_event.php` or `simple-events/archive-se_event.php`

The plugin template is used only when the theme does not provide one.

## Architecture

```
simple-events-cpt.php          Bootstrap, constants, activation
includes/
  class-helpers.php            Meta helpers, query args, formatting
  class-settings.php           Settings API
  class-post-type.php          CPT + taxonomies + archive query
  class-meta-boxes.php         Admin fields and sanitization
  class-admin-columns.php      List table columns
  class-assets.php             Enqueued CSS/JS
  class-routing.php            Optional CPTP permalink fix
  class-seo.php                Canonical/title + JSON-LD
  class-template-loader.php    Theme-overridable templates
  class-shortcodes.php         [simple_events]
templates/
  single-se_event.php
  archive-se_event.php
  parts/event-card.php
assets/
  css/frontend.css
  css/admin.css
  js/frontend.js
  js/admin.js
uninstall.php                  Deletes plugin options only
```

## Decisions

- **Prefixed CPT (`se_event`)** avoids colliding with other event plugins that register `event`.
- **Native archive** (`has_archive`) instead of a hardcoded page ID, so the plugin works on a fresh install.
- **Generic registration URL** instead of vendor-specific checkout modals. Sites can still link to Eventbrite, Ticket Tailor, or any other ticket page.
- **`posts_pre_query` CPTP fix is opt-in.** Custom Post Type Permalinks can set a post-type query var instead of `name`, which breaks `is_single()` and can look like a 404 to SEO plugins. The compatibility layer only loads when CPTP is present.
- **Plugin templates with theme override** keep the plugin usable without a custom theme, while still allowing design control.
- **Uninstall leaves event posts in the database** so deactivating the plugin does not destroy content.

## What I would add next

- Gutenberg block equivalent of the shortcode
- Recurring events
- REST collection filters (`after`, `before`, `upcoming`)
- iCal / `.ics` export
- Optional map embed from the stored address

## License

GPL-2.0-or-later
