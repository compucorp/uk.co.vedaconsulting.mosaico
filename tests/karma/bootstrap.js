/* globals angular */
// Stand-ins for the globals CiviCRM defines on a real page. The files under
// test close over CRM.$ and CRM._ when they load, so this must load first.
window.CRM = window.CRM || {};
CRM.$ = window.jQuery;
CRM._ = window._ || {};
CRM.url = function() {
  return 'about:blank';
};
CRM.crmMosaico = {};

angular.module('crmMosaico', []);
