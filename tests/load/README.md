# mod_vimigallery — load tests (JMeter / k6)

The gallery's load profile is the **album**. Since 0.3.1 the page ships only its
first map and fetches the rest through `mod_vimigallery_get_item` as the reader
swipes. The question these tests answer is therefore not "how big is the page"
but **"what does one map fetch cost, and does it stay flat as the album grows?"**

Both runners drive the same endpoint so their numbers are comparable.

## Quick start

```
make load-seed        # 100 maps x 150 nodes, one comment each, + REST token
make jmeter           # or: make load-k6
```

`make load-seed` writes `BASE_URL`, `TOKEN`, `CMID` and `ITEMIDS` to
`tests/load/.load-env`, which both runners read automatically — no `eval`, no
copying values. Command-line overrides still win
(`make load-k6 VUS=50 SLIDES=10`).

It also writes `tests/load/itemids.csv`; the JMeter plan reads it as a CSV data
set so threads spread across the album instead of all hammering one row. k6 takes
the same ids through `ITEMIDS` and starts each virtual user at a random slide.

## Sizing the fixture

```
make load-seed ITEMS=500 NODESPERMAP=300
```

The interesting comparison is between fixture sizes: if `vimigallery_get_item`
stays flat from 100 to 500 maps, the lazy path is doing its job. If it climbs
with `ITEMS`, something is resolving the whole album per fetch.

Note that `live` sources re-resolve through the source adapter on every fetch,
bounded by `source_interface::MAX_ITEMS`. The seed uses upload items (the
materialised case) so the measurement isolates the fetch itself; point a gallery
at a quiz or database source to measure the adapter instead.

## What is measured

| Metric | Meaning |
| --- | --- |
| `vimigallery_get_item` | per-fetch latency |
| `vimigallery_item_bytes` | response size, i.e. the map actually shipped |
| `vimigallery_exceptions` | zero tolerance: a web-service exception is a defect |
| `vimigallery_http_errors` | zero tolerance: a non-200 is a defect |

Latency is statistical, so it is judged at p95 against `MAXMS` (default 2000 ms,
a server-side budget, looser than the ~200 ms client-side target). Functional
failures are not statistical and carry `rate==0` thresholds, so a handful of real
exceptions cannot disappear behind thousands of passing latency checks.

## Not part of CI

These runs need a live, seeded site and they change site configuration: the seed
enables web services and REST, grants `webservice/rest:use` to authenticated
users and mints a token. Excellent as a developer tool, unacceptable on a
production server — which is why `/tests/load` is `export-ignore` in
`.gitattributes` and never ships in a release package. The downloaded JMeter
distribution, the k6 binary, `.jtl` results and `itemids.csv` are gitignored.
