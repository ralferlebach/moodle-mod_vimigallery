# Backlog — mod_vimigallery

## Done (session 006)

- Module skeleton with `displaymode` (page / course), uploaded JSON maps rendered
  read-only (mountValue, lazy, dynamic height), `showtabs` / `showauthors`,
  backup/restore, null privacy provider.
- Full CI (dev + release, mod_vimipad via `--extra-plugins`, lint-js AMD gate) and
  a local makefile + tools.
- Album/swipe with prev/next, counter, keyboard and touch; per-profile form config
  via a JSON script element.
- `showtabs` made functional via mod_vimipad 0.9.10 `showViewToggle`.

## Open

1. **Source adapters** (datafield → qtype → mod_vimipad) with a normalised
   `source → [ (mapjson, author) ]` contract. Each adapter must enforce the
   source's capability, group and visibility rules. Freshness setting
   `live` / `static` / `snapshot` per activity source; qtype/mod_vimipad source
   selectable between reference solution and learner submissions.
2. **Curation / arrange tab** for teachers: reorder, hide, refresh snapshots.
3. **Comments + completion** ("commented" rule) with privacy and backup/restore.
4. **Left/right comparison** with optional `api\score` similarity.
5. **Behat scenarios** (the behat CI job runs empty until then).
6. **Browser verification** of swipe / keyboard / zoom / full screen / tabs.

## Notes

- The Map/List toggle relies on mod_vimipad `showViewToggle` (>= 0.9.10).
- Album lazy-mounts the active slide and its immediate neighbours.
