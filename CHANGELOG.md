# Changelog — mod_vimigallery

## 0.2.1 - 2026-08-11

### Changed
- The showtabs setting is now functional: it drives mod_vimipad's new read-only
  Map/List toggle (showViewToggle). Dependency raised to mod_vimipad 2026080810
  (0.9.10).

## 0.2.0 - 2026-08-11

### Added
- Album/swipe: multiple maps are shown one at a time with previous/next controls,
  a counter, keyboard (arrow keys) and touch-swipe navigation. The active map and
  its immediate neighbours are mounted lazily. A single map shows no controls.
- Per-profile form configs are delivered via a JSON script element (deduplicated),
  keeping js_call_amd arguments small. Added `styles.css` for the album layout.

## 0.1.0 - 2026-08-11

### Added
- Initial skeleton of the ViMi Gallery activity module (depends on mod_vimipad).
- `displaymode` setting: `page` (link and description, like mod_page) or `course`
  (embedded on the course page with no link, like mod_label).
- Uploaded JSON maps are stored and shown read-only through mod_vimipad's
  embeddable editor (mountValue, read-only, lazy-mounted, dynamic height with a
  minimum). Settings `showtabs` and `showauthors` (the tab toggle is wired through
  and takes effect once mod_vimipad exposes a read-only view toggle).
- Backup/restore and a null privacy provider (no personal data yet).
