=== Simple Events CPT ===
Contributors: jlisojo
Donate link: https://github.com/jlisojo/simple-events-cpt
Tags: events, calendar, custom-post-type, cpt, event-management
Requires at least: 6.0
Requires PHP: 7.4
Tested up to: 7.1
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight WordPress events plugin with custom post types, recurring events via Pro add-on, theme-overridable templates, and Schema.org markup.

== Description ==

Simple Events CPT is a flexible, self-contained events management plugin for WordPress. It registers an `se_event` custom post type with comprehensive event metadata, native archive templates, and a shortcode for event listings.

**Features:**

- **Events Custom Post Type** with categories and tags
- **Event Metadata** including dates, times, venue, location, contact info, pricing
- **Admin List Columns** showing date, location, price, and event status
- **Archive & Single Templates** with theme override support
- **Schema.org Markup** for search engine visibility
- **Gutenberg Event Grid Block** for inserting event grids visually in the Block Editor
- **Event Shortcode** for displaying upcoming events on any page
- **Settings Page** to customize the archive slug, events per page, currency, and date format
- **REST API Support** for programmatic access
- **Optional CPTP Compatibility** for Custom Post Type Permalinks
- **Lightweight** – no page builders, ticketing vendors, or external dependencies required

**Simple Events Pro** (separate add-on) adds:
- Recurring events (daily, weekly, monthly)
- Occurrence exceptions (skip or reschedule individual dates)
- Calendar month view with navigation
- iCalendar export for Google Calendar, Outlook, Apple Calendar integration

== Installation ==

1. Upload the `simple-events-cpt` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the WordPress admin
3. Visit **Settings → Permalinks** to flush rewrite rules (or just activate; rules are flushed automatically)
4. Add events under **Events → Add New**

== Usage ==

**Create an Event**

1. Go to **Events → Add New**
2. Add a title, featured image, and description
3. Fill in start date and venue (required for proper display)
4. Optionally add pricing, contact info, and registration URL
5. Publish

Single events live at `/{slug}/{event-name}/`. The default slug is `events`.

**Shortcode**

```
[simple_events]
[simple_events count="6" title="Upcoming Events" show_button="true" button_text="See all events"]
```

| Parameter | Default | Description |
|---|---|---|
| `count` | `3` | Number of upcoming events to display |
| `title` | empty | Optional heading |
| `show_button` | `true` | Show a link to the archive |
| `button_text` | `View All Events` | Archive button label |
| `button_url` | events archive | Override the archive URL |

**Settings**

Visit **Events → Settings** to configure:
- Archive slug (default: `events`)
- Events per page (default: 12)
- Currency symbol (default: `$`)
- Date format (blank uses WordPress default)

**Template Overrides**

Copy a plugin template into your active theme to customize it:

- `single-se_event.php`
- `archive-se_event.php`

Or use the theme subdirectory:
- `simple-events/single-se_event.php`
- `simple-events/archive-se_event.php`

== Frequently Asked Questions ==

**Does this plugin create a page?**

No. Simple Events CPT uses the native WordPress archive URL (`/events/` by default) and individual post URLs. No page is created.

**Can I change the archive URL?**

Yes. Go to **Events → Settings** and update the "Archive slug" setting. This is preferable to editing the plugin code directly.

**Does this work with third-party ticketing?**

Yes. The "Registration / ticket URL" field lets you link to any ticketing system (Eventbrite, Ticket Tailor, Stripe, etc.). This approach gives you flexibility without plugin dependencies.

**What about recurring events?**

Recurring events are handled by the **Simple Events Pro** add-on, available on GitHub. The free plugin supports individual event dates.

**Can I customize the event display?**

Yes. You can override templates in your theme, or add custom CSS targeting the `.se-card` and `.se-grid` classes.

**Is this REST API enabled?**

Yes. Events are registered with REST support, so you can query them programmatically at `/wp-json/wp/v2/se_event/`.

== Screenshots ==

1. Event editor with meta boxes for dates, venue, pricing, and contact info
2. Events archive showing upcoming events as cards
3. Single event page with Schema.org markup and event details
4. Settings page for configuring slug, per-page, currency, and date format
5. WordPress admin list view with date, location, price, and status columns

== Changelog ==

= 1.0.0 =
* Initial release
* Events custom post type with categories and tags
* Meta boxes for event details (date, time, location, pricing, contact)
* Admin columns showing date, location, price, and status
* Archive and single event templates with theme override support
* Schema.org Event and ItemList markup
* [simple_events] shortcode with configurable display
* Settings page for archive slug, events per page, currency, and date format
* Optional CPTP (Custom Post Type Permalinks) compatibility
* Uninstall handler that preserves event content

== Upgrade Notice ==

= 1.0.0 =
First release. No upgrades from previous versions.

== Support ==

For support, feature requests, or bug reports, visit the GitHub repository:
https://github.com/jlisojo/simple-events-cpt

== License ==

This plugin is licensed under the GPL-2.0-or-later. See LICENSE for details.

== Contributors ==

- Josh (https://github.com/jlisojo)
