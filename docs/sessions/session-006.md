# Session 006 — mod_vimigallery: read-only Galerie (0.1.0 → 0.2.1)

> Ein Chat = eine Sitzung. Dieses Dokument hält den Galerie-Anteil der Sitzung
> 006 fest; der mod_vimipad-Anteil derselben Sitzung liegt in dessen
> `docs/sessions/session-006.md`.

## Ergebnis in einem Satz

Ein neues Aktivitätsmodul `mod_vimigallery` wurde als read-only Galerie von
ViMi-Pad-Maps angelegt: installierbar, getestet, mit voller CI und makefile,
und bis zum swipebaren Album mit funktionierendem Map/Liste-Reiter ausgebaut.

## Bestandsaufnahme / festgezurrtes Konzept

- **Ein Aktivitätsmodul mit `displaymode`** (nicht Block, nicht zwei Plugins):
  `page` = wie mod_page (Link + Beschreibung), `course` = wie mod_label
  (eingebettet, kein Link).
- **Minimal interaktiv:** Scrollen/Zoom/Vollbild + Reiter Map/Liste (per Setting
  `showtabs`). Kein Grafik-/PDF-Export im Viewer.
- **Album/Swipe:** eine oder mehrere Maps, wie ein Album zu swipen; Lazy-Mount.
- **Herkunft:** Uploads (JSON/gezippte JSON, multiple) und/oder Aktivitäts-
  Quellen (mod_vimipad, qtype mit Fragewahl, datafield mit Feldwahl). Frische je
  Quelle als Setting: `live` / `static` / `snapshot`. Uploads immer eingefroren.
- **qtype/mod_vimipad-Quelle:** Musterlösung/Referenz ODER Lernenden-Abgaben,
  je nach Einstellung.
- **Reihenfolgen-Editor = Kuratierung** (Lehrenden-Reiter): Verschieben,
  Ausblenden, Snapshots aktualisieren; Reihenfolge/Sichtbarkeit persistiert.
- **Autorenliste** (`showauthors`), **Kommentare** (Setting, Completion-Kriterium),
  optionaler **Links/Rechts-Vergleich** (+ optionale `api\score`-Ähnlichkeit).
- **Sicherheit:** aktivitätsbasierte Quellen strikt nach Capability/Gruppen/
  Sichtbarkeit der Quelle prüfen (kein Leak fremder Abgaben) — pro Adapter.

## Umgesetzt in dieser Sitzung

| Version | Inhalt |
| --- | --- |
| 0.1.0 | Modul-Skelett: `displaymode` (page/course), ein/mehrere hochgeladene JSON readonly gerendert (mountValue, lazy, dynamische Höhe), Settings `showtabs`/`showauthors`, Backup/Restore, Null-Privacy |
| 0.1.0 (Infra) | Volle CI (Dev + Release, `--extra-plugins` für mod_vimipad, lint-js-AMD-Gate) + makefile + tools, wie die Satelliten |
| 0.2.0 | Album/Swipe: Vor/Zurück, Zähler, Tastatur, Touch; aktive Folie + Nachbarn lazy montiert; formconfig pro Profil via JSON-Script (kleine js_call_amd-Argumente); styles.css |
| 0.2.1 | `showtabs` funktional dank mod_vimipad 0.9.10 (`showViewToggle`); Dependency auf 2026080810 angehoben |

## Verifikationsstand

Schema installiert sauber; phpcs 0/0 (tools/ exkludiert); validate/savepoints/
moodlecheck sauber; PHPUnit 4/4 (Features, Instanz-CRUD, Upload->Item inkl.
Überspringen von Nicht-Maps, Album-Renderer-Markup); AMD `viewer.js` eslint-
sauber und byte-reproduzierbar. `make check`/`amd`/`lint-js`/`phpunit` lokal grün.

## Offen / nächste Schritte (Reihenfolge)

1. Quellen-Adapter: datafield -> qtype -> mod_vimipad, je mit strikter Rechte-/
   Gruppen-/Sichtbarkeitsprüfung und Frische-Setting.
2. Autorenliste-Anzeige-Feinschliff + Anordnen-/Kuratierungs-Reiter (Reihenfolge,
   Ausblenden, Snapshot-Aktualisierung).
3. Kommentare + Completion-Kriterium (Tabelle, Privacy, Backup/Restore).
4. Optionaler Links/Rechts-Vergleich (+ optionale `api\score`-Ähnlichkeit).
5. Behat-Szenarien (der behat-CI-Job läuft bis dahin leer).
6. Browser-Verifikation (Swipe/Tastatur/Zoom/Vollbild, Reiter).
