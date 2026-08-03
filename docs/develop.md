# Development

This extension integrates `voidlabs/mosaico` with CiviCRM.  Like CiviCRM, the main body of this extension is built on
PHP, MySQL, and AngularJS. You can generally develop updates to the extension in a regular CiviCRM environment.

There are a few important exceptions and additions: the stylesheets (`./sass`, `./css`) and the editor (`voidlabs/mosaico`).
If you wish to develop patches for these, then it will require additional tools and processes.

## Requirements

* *Basic Development*
    * CiviCRM
    * [`git`](https://git-scm.com/)
    * [`composer`](https://git-scm.com/)
    * [`cv`](https://github.com/civicrm/cv) (*recommended*)
    * [`phpunit`](https://phpunit.de) (*recommended*)
* *Stylesheet Development* and *Editor Development*
    * [`nodejs`](https://nodejs.org/en) (*Currently the mosaico build script is requires v18 or v22. You can use
        [nvm](https://github.com/nvm-sh/nvm#installing-and-updating) to use multiple versions of nodejs. Eg. `nvm install 22 && nvm use 22`*)
    * [`npm`](https://www.npmjs.com)
    * [`grunt-cli`](http://gruntjs.com/getting-started) (*only for Editor Development, see below*)

## Basic Development

The process is similar to many CiviCRM extensions:

```
## Navigate to your extension directory, e.g.
cd sites/default/files/civicrm/ext

## Download the extensions
git clone https://lab.civicrm.org/extensions/mosaico.git mosaico

## Download additional dependencies
cd mosaico
composer install

## Enable the extension via web UI or CLI, e.g.
cv en mosaico
```

At this point, you can iteratively develop patches.  Submit proposed updates via GitLab merge requests.

## Stylesheet Development

This project depends on `sass` to compile its CSS. Some Bootstrap 3 mixins/variables from the
[Shoreditch theme](https://github.com/civicrm/org.civicrm.shoreditch) are vendored into `sass/vendor/shoreditch/` (see the
README there) rather than requiring a separate clone of that extension.

Before you can compile the CSS you need to do the following from within this extension's directory:

```
npm install
```

You can then compile the Sass from sass/, recreating files in css/ like this:

```
./compile-sass-to-css.sh
```

Commit changes for both SCSS and CSS. Submit proposed updates via GitLab merge requests.

## Editor Development

The CiviCRM extension (`uk.co.vedaconsulting.mosaico`) depends on the editor ([mosaico](https://github.com/voidlabs/mosaico)), which is an
independent project. The `mosaico` component has its own requirements and workflows.

`uk.co.vedaconsulting.mosaico` uses a pre-built copy of `mosaico` (`./packages/mosaico`), so you may work on `uk.co.vedaconsulting.mosaico`
without needing to understand `mosaico` development.  However, if you are doing development for both, then there are a few important details:

Note: `./packages/mosaico` (along with `./vendor`) is committed directly into this repository, so a fresh `git clone` already has a working
copy without running `composer install`. Re-run `composer install` and commit the result whenever you update the `mosaico` dependency below.

* __Using `mosaico.git` in the extension__: You may replace the pre-built folder (`./packages/mosaico`) with a git repo. The extension specifically
  uses a fork (https://github.com/civicrm/mosaico) with a few small patches. Typical setup steps:
    ```bash
    ## Remove pre-built copy of mosaico
    rm -rf packages/mosaico

    ## Download git repo
    git clone https://github.com/civicrm/mosaico.git -b v0.18.10-civicrm-1

    ## Build
    cd packages/civicrm
    npm install
    grunt build
    ```
* __Branching for `mosaico.git`__: The `civicrm/mosaico` fork follows the [Twigflow (Rebase)](https://gist.github.com/totten/39e932e5d10bc9e73e82790b2475eff2) pattern.
  You will notice additional branches such as `v0.15-civicrm-2` (*a branch derived from `v0.15` for use by `civicrm`; it is the second major variant of the branch*).
* __Tagging for `mosaico.git`__: If there has been an update to `mosaico.git`, then you should make a new tag (eg `v0.18.10-civicrm-4.0`). Github will generate
  a pre-built package for the new version.
* __Updating the dependency__: If there is a newer build of `mosaico`, then you may edit `./composer.json` and update the the `extra: downloads` configuration.
    ```bash
    vi composer.json
    composer update --lock
    ```

## Addendum: Publication

Because the composer-managed assets (`./vendor`, `./packages/mosaico`) are committed directly into this repository, any checkout or
source archive of a commit/tag (eg. `git archive`, or a GitLab/GitHub "Download source" link) is already a complete, installable
extension — there is no separate build/packaging step required.

To publish a new official release to the CiviCRM extension directory:

* Bump `<version>` (and `<releaseDate>`) in `info.xml`, and tag the release in git.
* Tagging the release automatically triggers https://civicrm.org/extdir-backend/scan/5243 (via a GitLab webhook on release/tag-push events) - this publishes the release automatically to https://civicrm.org/extensions. No manual trigger is needed.
* Pushing to the repository automatically triggers https://docs.civicrm.org/admin/publish/mosaico (via a GitLab webhook on push events), keeping the mosaico docs up to date. No manual trigger is needed.

