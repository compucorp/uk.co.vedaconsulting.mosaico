## Release 3.14.0 (2026-09-06)

* [!733](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/733) Fix `createResizedImage()` to preserve the aspect ratio when both dimensions are given.
* [!734](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/734) Fix [#712](https://lab.civicrm.org/extensions/mosaico/-/issues/712) editor opening part-way down the page when the page is scrolled (regression in 3.13).
* [!735](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/735) Run civilint, PHPCompatibility, jshint and the new JavaScript unit tests on GitLab CI.

## Release 3.13 (2026-08-24)

* [!718](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/718) Upgrade Intervention library to v3.
* [!717](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/717) Fix [#704](https://lab.civicrm.org/extensions/mosaico/-/issues/704) placeholder images not displaying due to missing freetype dependency; prefer imagick over gd for the 'auto' graphic driver.
* [!719](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/719) Update composer dependencies.
* [!720](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/720) Fix typo in permission name.
* [!721](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/721) Prevent session error after streaming image in sendImage().
* [!722](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/722) Fix test failures on PHP 8.5, convert Graphics Intervention test to headless rather than e2e.
* [!716](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/716) Fix Mosaico iframe positioning on Joomla 4/5 (Atum template).
* [!726](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/726) Bundle composer-managed assets (`vendor/`, `packages/mosaico`) directly into the repo, so a checkout/tag archive is a complete, installable extension with no separate build step.
* [!727](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/727) Clean up outdated build tooling and dead CSS/SCSS.
* [!725](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/725) Remove CSS override of core's mailing-preview dialog.
* [!728](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/728) Update docs to reflect that release/publish are automatic GitLab webhooks; move `composer-downloads-plugin` to require-dev.
* [!724](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/724) Fix: restore FlexMailer's default composer after composing a Mosaico mailing (follow-up to !675).
* [!723](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/723) Support custom mailing data.
* [!731](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/731) Replace deprecated `CRM_Core_BAO_Setting::getItem` with `Civi::settings()`.
* Update link for issue tracker to GitLab.

## Release 3.12 (2026-03-19)

* Revert !708 because it is causing syntax errors per https://lab.civicrm.org/extensions/mosaico/-/merge_requests/712 and others.

## Release 3.11 (2026-03-18)

* [!709](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/709) Fix: prevent login loop when accessing Mosaico templates in Drupal.
* [!704](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/704) Remove api4 wrapper.
* [!708](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/708) Fixing positions of iFrame.
* [!711](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/711) Fix permissions for API4 MosaicoTemplate.

## Release 3.10 (2026-03-11)

* [!702](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/702) Avoid fatal error about missing API_Wrapper, fix #699.
* [!703](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/703) Add message template permission to edit user driven message templates.
* [!705](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/705) Fix group fields on mailing screens.

## Release 3.9 (2026-02-17)

* Update mosaico package - see changes: [https://github.com/civicrm/mosaico/compare/v0.18.10-civicrm-4.0...v0.18.11-civicrm-1.0](https://github.com/civicrm/mosaico/compare/v0.18.10-civicrm-4.0...v0.18.11-civicrm-1.0)
* [!699](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/699) - Support filtering by ID in the URL for mosaico templates.
* [#666](https://lab.civicrm.org/extensions/mosaico/-/issues/666) Refresh template list after delete.
* [#698](https://lab.civicrm.org/extensions/mosaico/-/issues/698) Fix PHP warning by rounding divided placeholder values.
* [!696](https://lab.civicrm.org/extensions/mosaico/-/merge_requests/696) Add setting to override color picker theme.
* [#675](https://lab.civicrm.org/extensions/mosaico/-/issues/675) Ensure that Flexmailer does not re-trigger the processing in onCompose.
* [#686](https://lab.civicrm.org/extensions/mosaico/-/issues/686) For A/B content mailing, copy template_options so usable copies can be made.
* [#688](https://lab.civicrm.org/extensions/mosaico/-/issues/688) Add support for uploading webp files.
* [#690](https://lab.civicrm.org/extensions/mosaico/-/issues/690) - dev/core#5041 - Add role img and ariahidden true to all Font Awesome Icons.
* [#695](https://lab.civicrm.org/extensions/mosaico/-/issues/695) Replace deprecated CRM_Utils_Number::formatUnitSize.
* Fix [#692](https://lab.civicrm.org/extensions/mosaico/-/issues/692) Avoid consecutive slashes in metadata template URL.

## Release 3.8 (2025-07-09)

* [#683](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/683) - Use upstream tidy process to fix the send to a group functionality whilst preserving fix for #664 (Fix send test email to group).
* [#677](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/677) - Fix deprecation notices on PHP8.1.
* [#685](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/685) Cannot edit saved templates after #674.

## Release 3.7 (2025-07-03)

* Update to latest upstream library.
* [#660](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/660) Update composer.json, add type:civicrm-ext to allow the Mosaico extension to be installed via Composer.
* [#664](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/664) Fix duplicate contact creation when sending a test to multiple emails when there's a space after the comma.
* [#673](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/673) Add label and description to templateType.
* [#672](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/672) Convert to EntityFrameworkV2.
* Add compatibility declarations, remove requires for compulsory extensions.
* [#674](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/674) Civi cannot handle Drupal multilanguage path, cannot find template url.
* Remove legacy MosaicoMsgTemplate entity (not used since Mosaico 1.x).

## Release 3.6 (2024-09-06)

* Replace htmlspecialchars with escape (Smarty5 compatibility).
* Refactor out call to deprecated CRM_Utils_Array::value
* Permit install of composer-dowloads-plugin v4 to support php8.3.
* "From" and "Reply-To" lists - (Re)Enable select2 support
* Add A/B test for "Reply-To"
* Add A/B testing for "From" address
* AbDemuxTest - Add demonstration of "From" variants
* Subject List - On the add/remove button, give clearer hint
* Fix civicrmtoken error when `mosaicoConfig` hook is used
* Fix settings path

## Release 3.5 (2024-03-01)

* [#638](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/638) Fix issues with labels on template selection.
* [#633](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/633) Add A/B testing for email design

## Release 3.4 (2024-02-26)

* Support guzzle 7.0
* Update mosaico library
* [#632](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/632), 
[#631](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/631),
[#630](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/630) Css, html fixes for selecting a template.
  * Replace gulpfile with node sass
  * Fix fixed width on chosen tpl display on mailing design screen

* [#627](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/627) Move navigation menu items declarations to managed entities.
* [#620](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/620) Update info.xml to link to correct page to download from and whereto report issues.

## Release 3.3 (2023-10-15)

* [#617](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/617) Angular - Switch to settingsFactory.
* [#612](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/612) Fix notice error about no buffer to flush.
* [#609](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/609) Add pseudoconstant for base template and regenerate DAOs.

## Release 3.2 (2023-08-03)

* Update to Mosaico library v0.18.10 (was v0.18.9).
* [#510](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/510) Add support for TinyMCE translations.
* [#572](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/572) Resolve #572 by ensuring that the value of the base template option is the name of the base template not the title.
* [#597](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/597) Specify 'match' in mgd.php files.

## Release 3.1 (2023-06-21)

* Fix [#596](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/596) Fix image placeholders and translation.

## Release 3.0 (2023-06-08)

**Major change: Mosaico library and versafix template updated to latest versions (0.18.9, 1.3.4)**

* **Update mosaico library to latest version (0.18).**
* [#593](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/593) Enhance api call replaceurls.

## Release 2.12 (2023-04-22)

* [#577](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/577) 5.60 compatibility.

## Release 2.11 (2023-02-14)

* [#569](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/569) Use more standard param for pragma header.
* [#568](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/568) On WordPress mosaico/img may have a / after - teach the regex to replace this with static images. Fixes performance issues with bulk mailings on WordPress sites.
* Test fixes / PHP8.2 compatibility fixes.
* [#549](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/549) Improve performance (response time) of image processing backend.
* [#554](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/554) Template path fixes.
* Library updates.
* [#541](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/541) Fix 2.10 upgrade from command line.

## Release 2.10

* Fix [#539](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/539) Fix issue with urls that are double url encoded.
* Fix issue where by some large placeholders aren't rendering behind application load balancers.
* Fix broken images when not logged in - accidently changed with multisite patches.
* [#489](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/489) Add validation rule to Mosaico uploader.
* [#536](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/536) Pass in String null so that db field is set to NULL.
* [#533](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/533) Add type hint to return value.
* [#530](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/530) Multisite support for search kit templates display.
* [#532](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/532) Fix tests following #529.
* [#410](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/410) Multisite support for mosaico.
* [#528](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/528) Various updates for `develop.md`.
* [#525](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/525) Download Mosaico JS files via composer-downloads-plugin.
* [#512](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/512) Define customTemplatesUrl absolute as templatesUrl.
* [#523](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/523) Fix crash when installing via CLI.
* [#520](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/520) Update civix templates.
* [#508](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/508) Bump postcss from 5.2.18 to 7.0.36.
* [#501](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/501) Switch newsletter category to one-time insert instead of managed.
* [#507](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/507) Use CiviCRM core Guzzle dependencies.
* [#500](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/500) Default orderBy Title on template listing search display.
* [#502](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/502) Add APIv4 permissions for MosaicoTemplate.
* Switch newsletter category to one-time insert instead of a managed entity.
* Default orderBy Title on template listing search display.
* [#496](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/496) Don't add the "schedule/send via CiviMail" search task, if the user has insufficient permissions.
* [#488](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/488) Validate multiple email when testing.
* [#490](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/490) Loosen restrictions on required fields.
* [#493](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/493) Ensure that when tests are run search kit is installed.
* [#485](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/485) Remove crmStar styling, require Bootstrap3.

## Release 2.9

* [#478](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/478) Add Mosaico Template Listing page based on Searchkit.
* Add template "Categories" and allow them to be filtered in the UI.
* [#470](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/470) Add hooks to alter Mosaico plugins

## Release 2.8

* [#456](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/456) Add hooks to alter relevant assets in the Mosaico interface.
* [#462](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/462) PHP7.4 compatibility - Fix usage of Curly brackets to do array access.
* [#463](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/463) Improve error handling on image params.
* [#468](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/468) Allow users to choose hotlist tokens.
* [#472](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/472) / [#444](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/444) Auto-save after idle-timeout (~4s) and/or active-timeout (~90s)

## Release 2.7

* [#440](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/440) Add support for personalized tokens for "View in your browser" mode.
* [#451](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/451) Update unit tests to work with phpunit8.
* [#452](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/452) Use Generic Settings Form rather than our custom form.    Fix for issue #427
* [#427](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/427) Update AutoGenerated DAO, Civix, auto_install files to be latest versions from civix commands.
* [#437](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/437) phpunit8 testing compatibility.
* [#433](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/433) Throw error message on resizing image and prevent hard fail.
* [#414](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/414) Add setting to hide base templates.

## Release 2.6

* [#321](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/321) Introduce A/B testing for subject lines.
* [#406](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/406) Fix for mosaico placeholder images not accessible in joomla.
* [#409](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/409) Fix Leave site? warning when not required.
* [#412](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/412) Use standard methods and add validation to URL params for placeholder image endpoint.

## Release 2.5

* Fix [#319](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/319) Warn user before clicking away from mosaico editor.
* [#388](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/388) Adds bullets (unorderd list) to editor
* [#389](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/389)
[#390](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/390)
Fix placeholder images showing on Drupal8, Wordpress.

## Release 2.4

#### Features
* [#345](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/345) Use mosaico graphics service to resize thumbnail.
* [#352](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/352) Make spell check available directly in mosaico.
* [#374](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/374) Add API method to update template URLs.
* [#371](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/371) Provide better support for high resolution images in mosaico.
* Improvements to documentation.

#### Fixes
* Fix path to translation files.
* [#376](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/376) Fix issue with image rendering (sometimes output buffer started by another module or plugin causes problem with image rendering).
* Update composer libraries for PHP7.3 support.
* Update Mosaico Services to be compatible with Symfony 4.
* Update npm dependencies.
* PHPUnit configuration compatible with PHPUnit6 and PHPUnit7.
* Various tweaks for compatibility if using Shoreditch theme.

## Release 2.3

* Change image placeholder permissions (*Fixes issue with image placeholders for some sites*).

## Release 2.2

* Allow sending traditional emails as a search task.
* Improve validation in image processing.
* Move maximum image size into a setting.
* Drop system check for shoreditch theme as it is not required for Mosaico to function.
* Improve browser incompatibility message.
* Sort templates alphabetically when displaying 'New Mailing'.
* Add translation support - the Mosaico editor will now appear in the same language as CiviCRM if language files are available.

## Release 2.1

* Always place iframe below KAM menu instead of behind.
* Replace '<title>TITLE<title>' with subject (render-time).
* Document hook_civicrm_mosaicoConfig.

## Release 2.0

All users of Mosaico should evaluate and upgrade to this release.

* Simplify requirements:
  * The shoreditch theme is not required - the extension will work with the current CiviCRM theme.
  * ImageMagick is not required - the extension will auto-detect between imagemagick and gd depending on what the server supports.
* Reduce memory usage which previously caused issues sending mail on some servers.
* Add a hook for mosaico editor configuration.

**Download link: https://civicrm.org/extensions/mosaico-civicrm-integration/version-20**
