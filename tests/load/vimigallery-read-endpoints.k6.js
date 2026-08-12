// mod_vimigallery — load test (k6), the album's read path.
//
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
// The gallery page ships only its first map and fetches the rest through
// mod_vimigallery_get_item as the viewer swipes. Each iteration therefore walks
// several maps of one album, which is what a real reader does — and it is the
// measurement that tells us whether the per-map fetch stays flat as the album
// grows, or whether it re-does work proportional to the album size.

import http from 'k6/http';
import { check } from 'k6';
import { Rate, Trend } from 'k6/metrics';

const BASE = __ENV.BASE_URL || 'http://localhost:8000';
const TOKEN = __ENV.TOKEN || '';
const CMID = __ENV.CMID || '1';
const ITEMIDS = String(__ENV.ITEMIDS || '').split(',').filter((id) => id !== '');
// How many maps one reader walks per iteration.
const SLIDES = Number(__ENV.SLIDES || '5');
const MAXMS = Number(__ENV.MAXMS || '2000');

const t = {
  get_item: new Trend('vimigallery_get_item', true),
};
const bytes = new Trend('vimigallery_item_bytes');
const exceptions = new Rate('vimigallery_exceptions');
const httperrors = new Rate('vimigallery_http_errors');

export const options = {
  vus: Number(__ENV.VUS || '25'),
  duration: __ENV.DURATION || '60s',
  thresholds: {
    'http_req_duration': [`p(95)<${MAXMS}`],
    // Functional failures are not statistical: an exception or a non-200 is a
    // defect, so these carry zero tolerance and are tracked separately from the
    // latency checks.
    'vimigallery_exceptions': ['rate==0'],
    'vimigallery_http_errors': ['rate==0'],
  },
};

function call(fn, params) {
  const url = `${BASE}/webservice/rest/server.php`;
  const body = Object.assign(
    {
      wstoken: TOKEN,
      wsfunction: `mod_vimigallery_${fn}`,
      moodlewsrestformat: 'json',
    },
    params
  );
  const res = http.post(url, body);
  t[fn].add(res.timings.duration);
  bytes.add(res.body ? res.body.length : 0);

  const httpok = res.status === 200;
  const hasexception = !res.body || res.body.indexOf('"exception"') !== -1;
  httperrors.add(!httpok, { endpoint: fn });
  exceptions.add(hasexception, { endpoint: fn });

  if (hasexception && res.body) {
    console.error(`${fn} returned an exception: ${String(res.body).slice(0, 300)}`);
  } else if (!httpok) {
    console.error(`${fn} returned HTTP ${res.status}`);
  }

  check(res, {
    [`${fn} status 200`]: () => httpok,
    [`${fn} no exception`]: () => !hasexception,
    [`${fn} under budget`]: (r) => r.timings.duration < MAXMS,
  });
  return res;
}

export default function () {
  if (ITEMIDS.length === 0) {
    console.error('No ITEMIDS given — run "make load-seed" first.');
    return;
  }
  // Start at a random point in the album so the run does not all hammer the
  // same rows, then walk forward like a reader swiping through slides.
  const start = Math.floor(Math.random() * ITEMIDS.length);
  for (let i = 0; i < SLIDES; i++) {
    const itemid = ITEMIDS[(start + i) % ITEMIDS.length];
    call('get_item', { cmid: CMID, itemid: itemid });
  }
}
