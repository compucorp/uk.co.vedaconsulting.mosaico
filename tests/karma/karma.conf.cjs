// Unit tests for the extension's AngularJS code. These run against stubs
// rather than a booted CiviCRM (see tests/karma/bootstrap.js), so `files`
// lists the units under test explicitly - a glob over ang/ would pull in
// files needing core services this harness does not provide.
//
// .cjs, not .js: package.json sets "type": "module", so a .js config would be
// parsed as an ES module and karma's config loader would fail on `module`.
module.exports = function(config) {
  config.set({
    basePath: '../..',
    frameworks: ['jasmine'],
    files: [
      'node_modules/jquery/dist/jquery.js',
      'node_modules/angular/angular.js',
      'node_modules/angular-mocks/angular-mocks.js',
      'tests/karma/bootstrap.js',
      'ang/crmMosaico/Iframe.js',
      'tests/karma/**/*Spec.js'
    ],
    browsers: ['ChromeHeadlessNoSandbox'],
    customLaunchers: {
      // Chrome's sandbox cannot start as root, which is how it runs in CI containers.
      ChromeHeadlessNoSandbox: {base: 'ChromeHeadless', flags: ['--no-sandbox', '--disable-gpu']}
    },
    reporters: ['dots'],
    singleRun: true
  });
};
