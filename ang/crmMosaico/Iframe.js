(function(angular, $, _) {

  /**
   * The class CrmMosaicoIframe allows you to instantiate and manage a
   * full-screen IFRAME with embedded Mosaico runtime.
   */
  angular.module('crmMosaico').factory('CrmMosaicoIframe', function(crmUiAlert, $q, $timeout, $rootScope, CrmMosaicoSyncMonitor) {

    /**
     * @param Object newOptions
     *  - dimensions: function()
     *  - url: string (optional)
     *  - actions: Object
     *    - save: function(ko, viewModel)
     *    - test: function(ko, viewModel)
     *    - close: function(ko, viewModel) (an alias for "save")
     *  - model: Object. Must have "metadata+content" or "template"
     *    - metadata: string, JSON-encoded
     *    - content: string, JSON-encoded
     *    - template: string, relative file path
     */
    return function CrmMosaicoIframe(options){
      var cfg = {
        url: CRM.url('civicrm/mosaico/iframe', 'snippet=1'),
        dimensions: function resize() {
          var c = CRM.crmMosaico || {};
          var top = 0, left = 0;
          // Account for top navigation bars (e.g. CiviCRM menu, Joomla header, Drupal toolbar)
          var topNavSelectors = [c.topNav, c.topNav2];
          for (var i = 0; i < topNavSelectors.length; i++) {
            var sel = topNavSelectors[i];
            if (sel && $(sel).length > 0 && $(sel).is(':visible')) {
              // The iframe is position:fixed, so what matters is the navbar's
              // viewport-relative bottom; a fixed navbar's document offset grows
              // with the scroll position.
              var navBottom = $(sel)[0].getBoundingClientRect().bottom;
              if (navBottom > top) top = navBottom;
            }
          }
          // Account for left navigation sidebar (e.g. WordPress adminmenu, Joomla sidebar)
          if (c.leftNav && $(c.leftNav).length > 0 && $(c.leftNav).is(':visible')) {
            left = $(c.leftNav).outerWidth();
          }
          // z-index layers the editor above Joomla 4/5 Atum's fixed sidebar
          // (z-index 1010). Harmless on other CMSes, which don't overlap it. See #611.
          return {position: 'fixed', left: left + 'px', top: top + 'px', width: 'calc(100% - ' + left + 'px)', height: 'calc(100% - ' + top + 'px)', 'z-index': 1020};
        }
      };
      angular.extend(cfg, options);

      var model = cfg.model, actions = cfg.actions;
      var isVisible = false, $iframe = null, iframe = null;
      var syncMonitor = new CrmMosaicoSyncMonitor();

      if (actions.save && actions.close) {
        throw "Error: Save and Close actions are mutually exclusive";
      }

      var oldOverflow = null;
      function scrollHide() {
        if (oldOverflow === null) {
          oldOverflow = $('body').css('overflow');
          $(document).on('dialogclose', scrollRefresh); // jQuery dialog bug
        }
        $('body').css('overflow', 'hidden');
      }
      function scrollRestore() {
        if (oldOverflow !== null) {
          $(document).off('dialogclose', scrollRefresh); // jQuery dialog bug
          $('body').css('overflow', oldOverflow);
        }
        oldOverflow = null;
      }
      function scrollRefresh() { $('body').css('overflow', 'hidden'); }

      function onResize() {
        if ($iframe) $iframe.css(cfg.dimensions());
      }

      // Debounced resize for smooth sidebar transition handling
      var resizeTimer = null;
      function onResizeDelayed() {
        onResize();
        // Also recalculate after CSS transition completes (Joomla Atum: 300ms)
        if (resizeTimer) clearTimeout(resizeTimer);
        resizeTimer = setTimeout(onResize, 350);
      }

      this.render = function render() {
        var c = CRM.crmMosaico || {};
        $iframe = $('<iframe id="crm-mosaico" frameborder="0" class="ui-front">');
        $('body').append($iframe);
        onResize();
        $(window).on('resize', onResize);

        // Watch for sidebar width changes (e.g. Joomla Atum collapse/expand).
        // Observe class changes on the sidebar's parent, which typically
        // controls the collapsed state via a CSS class.
        if (c.leftNav && window.MutationObserver) {
          var leftNavEl = $(c.leftNav)[0];
          var observeTarget = leftNavEl ? leftNavEl.parentElement : null;
          if (observeTarget) {
            this._sidebarObserver = new MutationObserver(onResizeDelayed);
            this._sidebarObserver.observe(observeTarget, {attributes: true, attributeFilter: ['class']});
          }
        }

        iframe = $iframe[0];
        iframe.setAttribute('src', cfg.url);

        return this;
      };

      // @return Promise<null>
      this.open = function open() {
        var dfr = $q.defer();

        if ($iframe) {
          dfr.resolve();
          return dfr.promise;
        }

        window.top.crmMosaicoIframe = function(newWindow, Mosaico, config, plugins) {
          plugins.push(function(viewModel) {
            mosaicoPlugin(newWindow.ko, viewModel);
          });

          var ok = true;
          if (model.content) {
            Mosaico.start(config, undefined, JSON.parse(model.metadata), JSON.parse(model.content), plugins);
          } else if (model.template) {
            Mosaico.start(config, model.template, undefined, undefined, plugins);
          } else {
            crmUiAlert({text: 'Cannot edit mailing'});
            ok = false;
          }

          $timeout(function(){
            if (ok) dfr.resolve();
            else dfr.reject();
          }, 150);
        };

        this.render();
        this.show();
        return dfr.promise;
      };

      this.hide = function hide() {
        isVisible = false;
        if ($iframe) {
          syncMonitor.stop($iframe);
          scrollRestore();
          $iframe.hide();
        }
        return this;
      };

      this.show = function show() {
        isVisible = true;
        if ($iframe) {
          scrollHide();
          onResize();
          $iframe.show();
          syncMonitor.start($iframe);
        }
        return this;
      };

      this.destroy = function destroy() {
        this.hide();
        $(window).off('resize', onResize);
        if (this._sidebarObserver) {
          this._sidebarObserver.disconnect();
          this._sidebarObserver = null;
        }
        if (resizeTimer) clearTimeout(resizeTimer);
        if ($iframe) $iframe.remove();
        iframe = $iframe = null;
        return this;
      };

      // See https://github.com/voidlabs/mosaico/wiki/Mosaico-Plugins
      // Generally: Implement the in-dialog "Save" and "Test" buttons.
      function mosaicoPlugin(ko, viewModel) {
        // Clicking the default link isn't very useful in IFRAME context.
        viewModel.logoUrl = null;

        function mkCmd(name, callback) {
          var cmd = {
            name: name, // l10n happens in the template
            enabled: ko.observable(true)
          };
          cmd.execute = function() {
            cmd.enabled(false);
            $rootScope.$apply(function(){
              callback(ko, viewModel);
            });
            cmd.enabled(true);
          };
          return cmd;
        }

        if (actions.save) {
          viewModel.save = mkCmd("Save", actions.save);
        }
        if (actions.close) { // pretend like Mosaico has a "Close" action.
          viewModel.save = mkCmd("Close", actions.close);
        }
        if (actions.test) {
          viewModel.test = mkCmd("Test", actions.test);
        }

        syncMonitor.onSync = function () {
          if (actions.sync) actions.sync(ko, viewModel);
        };
      }

    };
  });


})(angular, CRM.$, CRM._);
