# Roadmap — mod_vimigallery

A read-only gallery of ViMi Pad maps. Depends on mod_vimipad's public embedding
seam (`mountValue`, read-only) and, later, its source and scoring APIs.

## Version line

From 0.2.x onward the release string counts up in the 0.2.x range
(0.2.1, 0.2.2, …). The version integer follows the 2026xxxxxxx scheme.

## Build order (verifiable increments, each with CI/makefile parity)

1. **Skeleton + uploads (done, 0.1.0):** activity module with `displaymode`
   (page / course), uploaded JSON maps rendered read-only, `showtabs` /
   `showauthors`.
2. **Album/swipe (done, 0.2.0):** multiple maps as a swipeable album with lazy
   mounting.
3. **Map/List toggle (done, 0.2.1):** `showtabs` wired to mod_vimipad's read-only
   `showViewToggle`.
4. **Source adapters (next):** a normalised "source → list of (map + author)"
   with adapters for datafield, qtype and mod_vimipad. Each adapter enforces the
   source's capability / group / visibility rules. Per activity-source freshness
   setting: `live` / `static` / `snapshot`.
5. **Curation / arrange tab:** teachers reorder, hide unwanted maps and refresh
   snapshots; order and visibility persist.
6. **Comments + completion:** per-map learner comments (own table, privacy,
   backup/restore) with a "commented" completion rule.
7. **Left/right comparison:** two read-only mounts side by side, optionally a
   similarity number via `\mod_vimipad\api\score::fraction()`.

## Cross-cutting

- Strict source visibility per adapter (never leak other learners' submissions).
- Accessibility: the list view is the a11y alternative to the canvas.
- No graphic/PDF export in the viewer.
