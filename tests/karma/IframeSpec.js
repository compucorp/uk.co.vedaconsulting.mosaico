/* globals describe, beforeEach, afterEach, it, expect, module, inject, CRM */
describe('CrmMosaicoIframe', function() {

  beforeEach(module('crmMosaico', function($provide) {
    $provide.value('crmUiAlert', function() {});
    $provide.factory('CrmMosaicoSyncMonitor', function() {
      return function() {
        this.start = this.stop = function() {};
      };
    });
  }));

  beforeEach(function() {
    // Enough page to scroll, so the editor's position can differ from the
    // navbar's position in the document.
    CRM.$('body').append(CRM.$('<div id="tall">').css('height', '3000px'));
    CRM.crmMosaico = {topNav: '#civicrm-menu'};
  });

  afterEach(function() {
    window.scrollTo(0, 0);
    CRM.$('#civicrm-menu, #tall, #crm-mosaico').remove();
  });

  /**
   * @param string position 'fixed' as CiviCRM's own menubar is positioned under
   *   the default "over-cms-menu" setting, or 'static' as it is under
   *   "above-crm-container".
   */
  function addNavbar(position) {
    CRM.$('<div id="civicrm-menu">')
      .css({position: position, top: 0, left: 0, width: '100%', height: '40px'})
      .prependTo('body');
  }

  function openEditor(CrmMosaicoIframe) {
    var editor = new CrmMosaicoIframe({actions: {}, model: {metadata: '{}', content: '{}'}});
    editor.render();
    return editor;
  }

  it('sits below a fixed navbar', inject(function(CrmMosaicoIframe) {
    addNavbar('fixed');
    var editor = openEditor(CrmMosaicoIframe);
    expect(CRM.$('#crm-mosaico').css('top')).toBe('40px');
    editor.destroy();
  }));

  it('sits below a fixed navbar when the page is scrolled', inject(function(CrmMosaicoIframe) {
    addNavbar('fixed');
    window.scrollTo(0, 400);
    expect(window.scrollY).toBe(400);

    var editor = openEditor(CrmMosaicoIframe);
    // The iframe is position:fixed, so its offset is relative to the viewport,
    // where the navbar still ends 40px down however far the page has scrolled.
    expect(CRM.$('#crm-mosaico').css('top')).toBe('40px');
    expect(CRM.$('#crm-mosaico').height()).toBe(window.innerHeight - 40);
    editor.destroy();
  }));

  it('fills the viewport once a static navbar has scrolled out of view', inject(function(CrmMosaicoIframe) {
    addNavbar('static');
    window.scrollTo(0, 400);

    var editor = openEditor(CrmMosaicoIframe);
    expect(CRM.$('#crm-mosaico').css('top')).toBe('0px');
    editor.destroy();
  }));
});
