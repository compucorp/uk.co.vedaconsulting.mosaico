# Vendored Shoreditch SCSS

These files are a static copy of a small subset of the [Shoreditch theme](https://github.com/civicrm/org.civicrm.shoreditch)'s
SCSS (Bootstrap 3 mixins and variable overrides), which this extension's own SCSS depends on.

Previously, building `css/` from `sass/` required cloning `org.civicrm.shoreditch` as a sibling directory. These files have been
stable for years upstream (most since 2016-2017), so they're vendored here instead, removing that external build-time
dependency.

* Source: https://github.com/civicrm/org.civicrm.shoreditch
* Vendored from commit: `2aabf8a100a9e6c869b2fcdfd8d37989572a1678` (2026-03-12)
* License: AGPL-3.0 (same as this extension)

If you need to refresh these from upstream, re-copy `scss/bootstrap/mixins/*.scss` and `scss/bootstrap/overrides/_variables.scss`
from that repo and update the commit reference above.
