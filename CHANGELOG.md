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
- Backup/restore and a null privacy provider (no personal data yet).## 0.2.12 - 2026-08-11

### Added
- Backup/restore roundtrip test: proves a gallerys settings, items (with content
  hash) and comments (re-linked, with user mapping) survive a course backup and
  restore into a new course.
- Behat scenarios: creating a gallery with the comment and compare options, and
  reaching the compare view. All steps resolve against existing step definitions.

## 0.2.11 - 2026-08-11

### Added
- Side-by-side comparison: when the teacher enables it, a "Compare" view lets
  users place any two maps of the gallery next to each other, both read-only.
- Optional similarity score between the two maps, computed with mod_vimipads
  public scoring facade (token matching), shown as a percentage.
- Optional coupled scrolling so both panes move together.

## 0.2.10 - 2026-08-11

### Added
- Per-map comments: learners with the new mod/vimigallery:comment capability can
  comment on each map in the album when the teacher enables comments. Comments are
  posted via a web service and appended in place.
- Completion rule "require comments": mark the activity complete once a learner has
  posted at least a configurable number of comments.
- Comments survive a rebuild/refresh: each item now carries a content hash and
  comments are re-linked to the same map by content; comments on maps that have
  disappeared are removed.
- Full privacy provider covering comments (export and deletion) and backup/restore
  of comments (with the userinfo setting).

## 0.2.9 - 2026-08-11

### Fixed
- CI: the datafield and qtype source tests now skip gracefully when their optional
  peer plugin (datafield_vimipad / qtype_vimipad) is not installed, instead of
  failing. This plugins own CI only installs mod_vimipad as a dependency, so the
  qtype integration tests could not create a vimipad question there. The tests run
  fully in an environment where the peer plugins are present.
## 0.2.8 - 2026-08-11

### Added
- Qtype submissions mode: a gallery can show the learners submitted maps from the
  vimipad questions of a Quiz. Visibility follows the quiz: a learner sees only
  their own finished attempts; a viewer with report/grade access sees everyone, and
  under separate groups only their own groups. Completes both qtype modes.
## 0.2.7 - 2026-08-11

### Added
- Drag-and-drop reordering on the arrange page (core/sortable_list) persisted via a
  new mod_vimigallery_reorder web service. The up/down links remain as an accessible,
  no-JavaScript fallback. New curation::set_order operation.
## 0.2.6 - 2026-08-11

### Added
- Arrange (curation) page for teachers (mod/vimigallery:manageitems): reorder maps,
  hide or show individual maps, and refresh a materialised static/snapshot source
  from its origin. Order and visibility persist. Live sources have no stored items
  and are not curated. A link to the page appears on the gallery for managers.
- New curation service (`\mod_vimigallery\local\curation`) with move, visibility
  and normalise operations.
## 0.2.5 - 2026-08-11

### Added
- ViMi Pad source: a gallery can draw from a ViMi Pad activity. Submissions mode
  shows the submitted maps (a learner sees their own; a grader sees all; separate
  groups honoured). Reference mode shows the model solution to graders (a teacher
  may publish it via static/snapshot). The qtype source now also offers the
  submissions option in the form (qtype submissions extraction remains a later
  step; qtype currently returns reference only).
- All three source adapters (datafield, qtype, vimipad) are covered by the shared
  factory and normalised source interface.

## 0.2.5 - 2026-08-11

### Added
- mod_vimipad source: a gallery can draw from a ViMi Pad activity, showing either
  its model solution (reference mode, graders only live) or the submitted maps
  (submissions mode). Submissions follow the activity access rules: a learner sees
  only their own and their groups; a grader sees all, subject to separate groups.
- Completes the three activity source adapters (datafield, qtype, vimipad) behind
  the shared source factory. The source mode setting now also offers submissions.
## 0.2.4 - 2026-08-11

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


