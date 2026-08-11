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
- Backup/restore and a null privacy provider (no personal data yet).## 0.2.4 - 2026-08-11

### Added
- Qtype source: a gallery can draw the model solutions of the ViMi Pad questions
  of a Quiz activity (reference mode). Model solutions are shown live only to
  users who may grade the quiz; a teacher can publish them to everyone via a
  static/snapshot freshness.
- New source mode setting and a shared source factory covering datafield and
  qtype. Schema: sourcemode column with an upgrade step.

### Note
- Qtype submissions mode (learners attempt responses) is planned as a separate
  step (it involves the quiz attempt data model).

## 0.2.3 - 2026-08-11

### Added
- Source adapters: a gallery can now draw its maps from a Database (mod_data)
  activity field (datafield_vimipad) instead of uploads. New source configuration
  on the activity (source type, database field, freshness).
- The datafield source strictly follows the source database access rules: the
  viewer must be able to view its entries, unapproved entries are hidden unless
  the viewer may approve or owns them, and separate groups are honoured.
- Freshness: 'live' reads the current entries per viewer at view time; 'static'
  and 'snapshot' materialise a copy of the maps the teacher can see when the
  activity is saved. Default is live.
- Schema: source columns added to the vimigallery table with an upgrade step.


