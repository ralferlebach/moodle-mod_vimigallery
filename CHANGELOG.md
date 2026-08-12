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

## 0.3.4 - 2026-08-12

### Changed
- PHPMD findings reduced from 368 to zero, by refactoring rather than by
  silencing: datafield_source::get_items() (104 lines, NPath 16416) split into
  resolve/fetch/build steps; qtype_source and vimipad_source likewise; the
  renderer's render_gallery() (138 lines) and render_compare() (104 lines) split
  into per-slide, per-pane and control builders; mod_form::definition(),
  data_preprocessing(), prepare_source_fields(), validate_source_selection() and
  rebuild_items() each split into named parts.
- Added phpmd.xml, a curated ruleset that excludes only the rules contradicting
  the Moodle coding standard, each with its reason.

### Fixed
- qtype_source::get_items() carried an unused $DB global.

## 0.3.3 - 2026-08-12

### Added
- Load-test results for the album fetch are documented in
  tests/load/RESULTS-2026-08-12.md: 46 436 requests across both runners with zero
  errors, and a response size flat to within 152 bytes across 24 805 fetches,
  which is the direct evidence that the lazy path does no per-album work.
- A test-js target running Jest, now part of `make check`.

### Changed
- Frontend dependencies can be refreshed before Jest with NPM_REFRESH=1
  (npm update plus npm audit fix --force). Off by default: --force accepts
  breaking major versions, which changes the bundled output and therefore the
  committed amd/build artefacts.
- `make load-seed` now fails when the seed script fails, instead of reporting
  success and writing an empty .load-env.

## 0.3.2 - 2026-08-12

### Fixed
- Five language keys were defined twice in both en and de (novimipads,
  source_vimipad, sourcemode_submissions, vimipadsource, vimipadsource_help),
  left over from the same incomplete merge that duplicated the form element.
  phpcs rejects duplicate keys, so this broke the lint job.
- The qtype reference loop set an item is provenance from a variable that does not
  exist in that scope. It happened to yield the right value (null), since a
  reference map is teacher content, but the code was misleading; it now says so
  explicitly.

### Added
- Load-test harness (tests/load): seed, JMeter plan and k6 script for the album is
  lazy item fetch, plus a README. Not distributed (export-ignore), downloads and
  results gitignored.

## 0.3.1 - 2026-08-12

### Changed
- Real lazy loading of maps. The page now carries only its first map; every
  further map is fetched through the new mod_vimigallery_get_item web service as
  the viewer reaches it. Previously each map was read from the database, held in
  PHP, escaped and written into the HTML on every page view, even though only one
  is ever on screen. The service resolves items through the same renderable the
  page uses, so item visibility and the per-viewer security of live sources apply
  unchanged; a failed fetch leaves the slide retryable instead of blank.

## 0.3.0 - 2026-08-12

First beta. Maturity raised from ALPHA to BETA.

### Added
- Provenance on materialised items (sourceuserid), so the privacy API can find,
  export and remove copies of a learners work that live in a gallery. A deletion
  request removes the copy rather than anonymising it: the copy is derived data
  and the source activity remains the authoritative record. Teacher uploads are
  untouched. The column travels through backup and restore with user mapping.

### Changed
- Comments for a gallery page load in two queries instead of two per map.
- All three activity sources batch author and group names into one query and are
  bounded by source_interface::MAX_ITEMS (newest first, then chronological). This
  matters most for the quiz source, where each attempt needs its own question
  usage load that cannot be batched.
- Optional peer plugins degrade gracefully: if qtype_vimipad or datafield_vimipad
  is not installed, the corresponding source yields an empty gallery instead of
  failing on a missing table.

### Tests
- Privacy coverage for materialised learner maps (discovery, userlist, export,
  deletion, and that teacher uploads are not affected) and a query-budget test
  for the comment loader.

## 0.2.15 - 2026-08-12

### Security
- compare.php now requires mod/vimigallery:view. Previously only login was
  checked, so a user whose view access had been removed could still reach the
  comparison via the direct URL.
- Curation actions (move, hide, show, refresh) are POST-only. They were reachable
  as plain links, which makes a state change something a prefetch or an embedded
  URL could trigger; the sesskey check alone did not prevent that.

### Fixed
- Deleting a gallery now removes its comments as well, instead of leaving
  orphaned rows (or failing where the foreign keys are enforced).
- Rebuilding the item set runs in a transaction: a failure while reading or
  writing the source no longer leaves the gallery emptied.
- The chosen source activity is validated server-side (exists, right module type,
  same course, and for a database source the field belongs to that activity and is
  a ViMi Pad field). The form only offered same-course activities, but the posted
  ids were taken on trust.
- Source ids are remapped on restore. sourcecmid/sourcefieldid used to be written
  back verbatim, so a restored gallery could point at a missing module or at an
  unrelated one with the same numeric id. A source that did not travel with the
  backup now resets the gallery to an empty upload gallery.
- Items materialised from another activity are no longer included in a backup
  taken without user information: they are copies of learners work and carry
  their names.
- Uploaded maps are validated against the public ViMi Pad map policy and bounded
  in size; the privacy notice no longer claims the plugin stores no personal data,
  and materialised items are declared in the privacy metadata.
- Removed three duplicated blocks left by an incomplete merge (the vimipadsource
  form element, its preprocessing, and the vimipad branch in the source fields).

## 0.2.14 - 2026-08-12

### Fixed
- Install failed under moodle-plugin-ci because the new item content-hash column
  was a CHAR NOT NULL with an empty-string default, which XMLDB rejects (the
  emitted debugging message fails every install-dependent CI cell). The column is
  now nullable with no default in both install.xml and the upgrade step; existing
  rows are back-filled with their hash on upgrade, and comment re-linking treats an
  empty/absent hash as no match.

## 0.2.13 - 2026-08-11

### Added
- Jest coverage for the dependency-free gallery JS logic: the compare views
  scroll coupling (including its re-entrancy guard) and the comment DOM builder
  (which renders author and body as text, preventing markup injection). The logic
  now lives in amd/src/logic.js, imported by both compare.js and viewer.js, so the
  tested code is the shipped code.
- CI: a dedicated Jest job, and the AMD build-reproducibility gate now covers all
  modules (viewer, compare, logic, arrange) rather than only viewer.

## 0.2.12 - 2026-08-11

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


