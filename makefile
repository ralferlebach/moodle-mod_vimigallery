##############################################################################
# Developer makefile for mod_vimigallery (ViMi Pad satellite).
#
# Local pre-flight mirroring the GitHub CI, adapted for a satellite plugin:
# no React/esbuild bundle (the editor is provided by mod_vimipad); the AMD
# modules are built with Moodle's Grunt. Run from inside a Moodle checkout that
# has this plugin AND mod_vimipad installed.
#
#   make check   # lint + build + phpunit (what CI runs)
#   make fix     # auto-fix phpdoc + phpcbf, then rebuild AMD
#   make amd     # rebuild the AMD module(s) only
#
# Override paths if your layout differs, e.g.:
#   make check MOODLE_ROOT=/path/to/moodle
##############################################################################

THIS_DIR      := $(patsubst %/,%,$(dir $(abspath $(lastword $(MAKEFILE_LIST)))))
PLUGIN_DIR    ?= $(THIS_DIR)
MOODLE_ROOT   ?= $(abspath $(PLUGIN_DIR)/../..)
PLUGIN_NAME   ?= mod_vimigallery
PLUGIN_REL    ?= mod/vimigallery
PHP           ?= $(shell which php 2>/dev/null || echo /usr/bin/php)
NPM           ?= npm
# Refresh frontend dependencies before running Jest: `npm update` plus
# `npm audit fix --force`. Off by default because --force accepts breaking major
# versions, which changes the bundled output and therefore the committed
# amd/build artefacts; run it deliberately, then rebuild and re-commit them.
#   make test-js NPM_REFRESH=1
NPM_REFRESH   ?= 0

# --- Load-test tooling ------------------------------------------------------
LOAD_DIR       ?= $(PLUGIN_DIR)/tests/load
JMETER_VERSION ?= 5.6.3
JMETER_HOME    ?= $(LOAD_DIR)/apache-jmeter-$(JMETER_VERSION)
JMETER         ?= $(JMETER_HOME)/bin/jmeter
K6             ?= k6
K6_VERSION     ?= 0.54.0

# Base URL read from the site's own config.php ($CFG->wwwroot), lazily: only the
# load targets expand it. Empty in a bare checkout, then the :8000 fallback wins.
MOODLE_WWWROOT = $(shell $(PHP) -r "define('CLI_SCRIPT',1); define('ABORT_AFTER_CONFIG',1); @include '$(MOODLE_ROOT)/config.php'; echo isset(\$$CFG->wwwroot) ? \$$CFG->wwwroot : '';" 2>/dev/null)

BASE_URL       ?= $(or $(MOODLE_WWWROOT),http://localhost:8000)
TOKEN          ?=
CMID           ?=
ITEMIDS        ?=
ITEMS          ?= 100
NODESPERMAP    ?= 150
SLIDES         ?= 5
THREADS        ?= 25
RAMPUP         ?= 10
LOOPS          ?= 20
MAXDURATION    ?= 2000

# Written by `make load-seed`, so jmeter/load-k6 need no manual eval. The leading
# '-' ignores the file until it exists; a command-line override still wins.
-include $(LOAD_DIR)/.load-env
PHPCS         ?= phpcs
PHPCBF        ?= phpcbf
NPX           ?= npx

.PHONY: all fix check clear test-js \
        load-seed jmeter jmeter-setup load-k6 k6-setup \
        lint-php fix-lint-php lint-phpdoc fix-phpdoc lint-mustache \
        lint-cpd lint-md lint-js amd build phpunit

all: clear fix check
	@echo ""
	@echo "=== All done. ==="

fix: clear fix-phpdoc fix-lint-php amd
	@echo ""
	@echo "=== All fixes complete. ==="

check: clear lint-php lint-phpdoc lint-mustache lint-cpd lint-md lint-js amd test-js phpunit
	@echo ""
	@echo "=== All checks complete. Review output above for errors. ==="

clear:
	@clear || true

lint-php:
	@echo "=== phpcs (Moodle standard, excludes tools/) ==="
	-cd $(PLUGIN_DIR) && $(PHPCS) \
		--standard=moodle --extensions=php --severity=1 \
		--no-cache --ignore=tools/,amd/build/ .

fix-lint-php:
	@echo ""
	@echo "=== phpcbf (auto-fix) ==="
	-cd $(PLUGIN_DIR) && $(PHPCBF) \
		--standard=moodle --extensions=php --ignore=tools/,amd/build/ .

lint-phpdoc:
	@echo ""
	@echo "=== PHPDoc (local_moodlecheck) ==="
	-cd $(MOODLE_ROOT) && $(PHP) local/moodlecheck/cli/moodlecheck.php \
		--path=$(PLUGIN_REL) --format=text 2>&1 \
		| grep -B1 '    Line' | grep -v '^--$$' || true

fix-phpdoc:
	@echo ""
	@echo "=== fix_phpdoc (tools/fix_phpdoc.php) ==="
	-$(PHP) $(PLUGIN_DIR)/tools/fix_phpdoc.php $(PLUGIN_DIR)

lint-mustache:
	@echo ""
	@echo "=== Mustache syntax check ==="
	@if [ -d $(PLUGIN_DIR)/templates ]; then \
		$(PHP) $(PLUGIN_DIR)/tools/mustache_check.php \
			$(PLUGIN_DIR)/templates 2>&1 | grep -v '^OK:' || true; \
	else \
		echo "No templates/ directory - Mustache check skipped."; \
	fi

lint-cpd:
	@echo ""
	@echo "=== PHP Copy/Paste Detector ==="
	-cd $(PLUGIN_DIR) && phpcpd --min-lines 5 --min-tokens 70 \
		--exclude tools --exclude amd/build . || true

lint-md:
	@echo ""
	@echo "=== PHP Mess Detector ==="
	-cd $(PLUGIN_DIR) && phpmd . text \
		cleancode,codesize,controversial,design,naming,unusedcode \
		--exclude tests,tools || true

lint-js:
	@echo ""
	@echo "=== ESLint (skipped when amd/src/ is empty) ==="
	@if ls $(PLUGIN_DIR)/amd/src/*.js 2>/dev/null | grep -q .; then \
		cd $(MOODLE_ROOT) && $(NPX) grunt eslint --root=. \
			--files=$(PLUGIN_REL)/amd/src/ --show-lint-warnings; \
	else \
		echo "No AMD source files - ESLint skipped."; \
	fi

amd:
	@echo ""
	@echo "=== AMD rebuild (Moodle Grunt; skipped when amd/src/ is empty) ==="
	@if ls $(PLUGIN_DIR)/amd/src/*.js 2>/dev/null | grep -q .; then \
		cd $(PLUGIN_DIR) && $(NPX) grunt amd --force; \
	else \
		echo "No AMD source files - skipped."; \
	fi

build: amd
	@echo ""
	@echo "=== Front-end build complete (AMD). ==="

test-js:
	@echo ""
	@echo "=== Jest (gallery JS logic) ==="
	@if [ -d $(PLUGIN_DIR)/tests/js ]; then \
		if [ ! -x $(PLUGIN_DIR)/node_modules/.bin/jest ]; then \
			echo "Installing frontend dev dependencies..."; \
			cd $(PLUGIN_DIR) && $(NPM) install --no-audit --no-fund; \
		fi; \
		if [ "$(NPM_REFRESH)" = "1" ]; then \
			echo "Refreshing frontend dependencies (NPM_REFRESH=1)..."; \
			cd $(PLUGIN_DIR) && $(NPM) update --no-fund || true; \
			cd $(PLUGIN_DIR) && $(NPM) audit fix --force --no-fund || true; \
		fi; \
		cd $(PLUGIN_DIR) && ./node_modules/.bin/jest; \
	else \
		echo "No tests/js — Jest skipped."; \
	fi

phpunit:
	@echo ""
	@echo "=== PHPUnit ==="
	@if ! $(PHP) -r \
		"define('CLI_SCRIPT',1); require '$(MOODLE_ROOT)/config.php'; \
		exit(empty(\$$CFG->phpunit_dataroot) ? 1 : 0);" 2>/dev/null; then \
		echo "SKIP: phpunit_dataroot not configured in config.php."; \
	else \
		reinit_check=$$(cd $(MOODLE_ROOT) && $(PHP) vendor/bin/phpunit \
			--testsuite $(PLUGIN_NAME)_testsuite --testdox 2>&1 | head -5); \
		if printf '%s\n' "$$reinit_check" | grep -q "initialised for different version"; then \
			echo "PHPUnit environment outdated - reinitialising..."; \
			cd $(MOODLE_ROOT) && $(PHP) admin/tool/phpunit/cli/init.php; \
		fi; \
		tmpout=$$(mktemp); \
		cd $(MOODLE_ROOT) && $(PHP) vendor/bin/phpunit \
			--testsuite $(PLUGIN_NAME)_testsuite --testdox > "$$tmpout" 2>&1; \
		phpunit_exit=$$?; \
		grep -v "^ ✔\|^ ✓\|^ ↩" "$$tmpout" || true; \
		rm -f "$$tmpout"; \
		exit $$phpunit_exit; \
	fi

# --- Load tests -------------------------------------------------------------
# These need a live, seeded site. `make load-seed` builds a large album and mints
# a REST token, then stores the values for the two runners.

load-seed: clear
	@echo ""
	@echo "=== Seed large album + web-service token ($(ITEMS) maps x $(NODESPERMAP) nodes) ==="
	@$(PHP) $(PLUGIN_DIR)/tests/load/seed_large.php $(ITEMS) $(NODESPERMAP) > $(LOAD_DIR)/.load-seed.out || { cat $(LOAD_DIR)/.load-seed.out; echo "Seeding failed - see the error above."; rm -f $(LOAD_DIR)/.load-seed.out; exit 1; }
	@cat $(LOAD_DIR)/.load-seed.out
	@sed -n "s/^export \([A-Z_][A-Z_]*\)=.\(.*\)./\1=\2/p" $(LOAD_DIR)/.load-seed.out > $(LOAD_DIR)/.load-env
	@rm -f $(LOAD_DIR)/.load-seed.out
	@echo ""
	@echo "Saved BASE_URL/TOKEN/CMID/ITEMIDS to $(LOAD_DIR)/.load-env"
	@echo "Now just run:  make jmeter   (or: make load-k6) — no eval needed."

jmeter-setup:
	@echo ""
	@echo "=== JMeter setup ==="
	@if [ -x $(JMETER) ]; then \
		echo "JMeter $(JMETER_VERSION) already present at $(JMETER_HOME)."; \
	else \
		echo "Downloading Apache JMeter $(JMETER_VERSION)..."; \
		cd $(LOAD_DIR) && \
		curl -fsSL https://archive.apache.org/dist/jmeter/binaries/apache-jmeter-$(JMETER_VERSION).tgz -o jmeter.tgz && \
		tar xzf jmeter.tgz && rm -f jmeter.tgz && \
		echo "Installed to $(JMETER_HOME)."; \
	fi

jmeter: clear jmeter-setup
	@echo ""
	@echo "=== JMeter load test — gallery item fetch ==="
	@command -v java >/dev/null 2>&1 || { echo "Java (JRE 8+) is required to run JMeter — please install a JRE."; exit 1; }
	@if [ -z "$(TOKEN)" ] || [ -z "$(CMID)" ]; then \
		echo "Missing required parameters. Usage:"; \
		echo "  make jmeter BASE_URL=<wwwroot> TOKEN=<token> CMID=<id> \\"; \
		echo "             [THREADS=25 RAMPUP=10 LOOPS=20 MAXDURATION=2000]"; \
		echo ""; \
		echo "  Run 'make load-seed' first — it seeds the album, mints a token and"; \
		echo "  writes tests/load/itemids.csv, which the plan reads."; \
		exit 1; \
	fi
	cd $(LOAD_DIR) && $(JMETER) -n -t vimigallery-read-endpoints.jmx \
		-Jbase_url='$(BASE_URL)' -Jtoken='$(TOKEN)' -Jcmid='$(CMID)' \
		-Jthreads='$(THREADS)' -Jrampup='$(RAMPUP)' -Jloops='$(LOOPS)' \
		-Jmaxduration='$(MAXDURATION)' \
		-l vimigallery-load-results.jtl
	@echo ""
	@echo "Results written to $(LOAD_DIR)/vimigallery-load-results.jtl"

k6-setup:
	@echo ""
	@echo "=== k6 setup ==="
	@if command -v $(K6) >/dev/null 2>&1; then \
		echo "k6 already on PATH."; \
	elif [ -x $(LOAD_DIR)/k6 ]; then \
		echo "k6 already present at $(LOAD_DIR)/k6."; \
	else \
		arch=$$(uname -m); case "$$arch" in x86_64) a=amd64;; aarch64|arm64) a=arm64;; *) a=amd64;; esac; \
		echo "Downloading k6 $(K6_VERSION) (linux-$$a)..."; \
		cd $(LOAD_DIR) && \
		curl -fsSL "https://github.com/grafana/k6/releases/download/v$(K6_VERSION)/k6-v$(K6_VERSION)-linux-$$a.tar.gz" -o k6.tgz && \
		tar xzf k6.tgz && cp "k6-v$(K6_VERSION)-linux-$$a/k6" ./k6 && chmod +x ./k6 && \
		rm -rf k6.tgz "k6-v$(K6_VERSION)-linux-$$a" && \
		echo "Installed to $(LOAD_DIR)/k6"; \
	fi

load-k6: clear k6-setup
	@echo ""
	@echo "=== k6 load test — gallery item fetch ==="
	@if [ -z "$(TOKEN)" ] || [ -z "$(CMID)" ]; then \
		echo "Missing required parameters. Usage:"; \
		echo "  make load-k6 BASE_URL=<wwwroot> TOKEN=<token> CMID=<id> ITEMIDS=<id,id,...> [SLIDES=5]"; \
		exit 1; \
	fi
	@K6BIN=$$(command -v $(K6) 2>/dev/null || echo "$(LOAD_DIR)/k6"); \
	cd $(LOAD_DIR) && "$$K6BIN" run vimigallery-read-endpoints.k6.js \
		-e BASE_URL='$(BASE_URL)' -e TOKEN='$(TOKEN)' \
		-e CMID='$(CMID)' -e ITEMIDS='$(ITEMIDS)' -e SLIDES='$(SLIDES)'
