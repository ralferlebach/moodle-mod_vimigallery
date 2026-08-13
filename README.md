# mod_vimigallery — ViMi Gallery

A gallery of ViMi Pad knowledge maps for Moodle. It shows a set of maps as an
album — one map at a time, with previous/next controls — either embedded on the
course page or behind a link, rendered through mod_vimipad's embeddable read-only
editor.

Requires **mod_vimipad** (>= 0.9.17): the gallery validates every map it
materialises through the parent plugin's public map-value API.

## Where the maps come from

A gallery draws its maps from one of four sources:

| Source | What it shows |
| --- | --- |
| Upload | JSON maps the teacher uploads |
| ViMi Pad activity | the reference map, or learners' submitted snapshots |
| Quiz (qtype_vimipad) | question reference maps, or learners' attempt answers |
| Database (datafield_vimipad) | the maps held in a ViMi Pad field |

Activity sources can be **live** (resolved per viewer on every request, so each
reader sees exactly what they are entitled to) or **materialised** into stored
items when the activity is saved or refreshed. The materialised modes publish a
teacher's view to everyone, which is the point of a curated gallery — and the
reason materialised copies of learners' work carry provenance and are covered by
the privacy provider.

## What it does

- **Album view** with previous/next and a slide counter; only the first map is in
  the page, the rest are fetched as the reader reaches them.
- **Curation**: reorder by drag-and-drop (with keyboard-accessible up/down
  fallbacks), hide and show individual maps, refresh a materialised source.
- **Comments** on each map, optionally required for activity completion.
- **Comparison**: place any two maps side by side, with a similarity score from
  mod_vimipad's scoring facade and optional coupled scrolling.
- **Backup/restore**, including source remapping into the restored course, and a
  **privacy provider** covering comments and materialised learner maps.

Optional peers degrade gracefully: if qtype_vimipad or datafield_vimipad is not
installed, the corresponding source yields an empty gallery rather than failing.
