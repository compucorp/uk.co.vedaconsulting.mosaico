/**
 * civicrmtoken - TinyMCE plugin which allows users to easily insert CiviCRM mail tokens.
 *
 * Tokens can be inserted multiple ways:
 *
 * 1. The developer adds 'civicrmtoken' to the toolbar.
 *     - If the user clicks the main token button, it opens a dialog.
 *     - If the user clicks the dropdown, it provides a hotlist of common tokens.
 * 2. The user presses the token hotkey, "ctrl-shift-t", and opens a dialog.
 *
 * The token dialog supports:
 *     - Multiple categories of tokens
 *     - Filtering by name
 *     - Keyboard navigation (arrows/tabs)
 *
 * == Configuration ==
 *
 *  When instantiating tinymce, the settings object should define "civicrmtoken", e.g.
 *
 *  tinymce.init({
 *    civicrmtoken: {
 *      hotlist: {'Domain Name': '{domain.name}'},
 *      tokens: [
 *        {
 *          text: 'Domain',
 *          children: [
 *            {id: '{domain.name}', text: 'Domain Name'}
 *          ]
 *        }
 *      ]
 *    }
 *  });
 *
 * Released under MIT License.
 * Copyright (c) 2016 CiviCRM LLC
 */

/*global tinymce:true */

tinymce.PluginManager.add('civicrmtoken', function(editor, pluginUrl) {
  if (!editor.settings.civicrmtoken) {
    throw "Failed to initialize civicrmtoken. TinyMCE settings should define \"civicrmtoken\".";
  }

  function settings() {
    return editor.settings.civicrmtoken;
  }

  /**
   * Create one menu item for each token in the hotlist.
   *
   * @param tokens
   *   Ex: tokens[0].children[0] = {id: '{contact.first_name}', text: 'First Name'}.
   * @param hotlist
   *   Ex: ['{contact.first_name}', '{contact.last_name}']
   * @returns {Array}
   *   List of menu items.
   */
  function createHotListMenu(tokens, hotlist) {
    var hotlistMenu = [];
    tinymce.each(hotlist, function(tokenId, tokenLabel){
      hotlistMenu.push({
        text: tokenLabel,
        onclick: function() {
          editor.insertContent(tokenId);
        }
      });
    });

    hotlistMenu.push({
      'text': '(more...)',
      onclick: function() {
        editor.execCommand('civicrmtoken');
      }
    });

    return hotlistMenu;
  }

  editor.addCommand('civicrmtoken', function(ui, v) {
    var win = null;
    // Controls are created once per dialog, so cache them after it opens.
    var filterCtrls = null;
    var gridCtrl = null;
    var filterTimer = null;
    var pendingFilter = null;
    var currentFilter = '';
    var FILTER_DELAY = 150;

    function setFilter(text) {
      text = (text || '').toLowerCase();
      if (text === currentFilter) {
        return;
      }
      currentFilter = text;

      var changed = false;
      for (var i = 0; i < filterCtrls.length; i++) {
        var ctrl = filterCtrls[i];
        var show = ctrl.data.data.searchText.indexOf(text) >= 0;
        if (ctrl.state.get('visible') !== show) {
          ctrl.state.data.visible = show;
          ctrl.getEl().style.display = show ? '' : 'none';
          changed = true;
        }
      }

      if (changed && gridCtrl) {
        gridCtrl._lastRect = null;
        gridCtrl.reflow();
      }
    }

    function cancelPendingFilter() {
      if (filterTimer) {
        clearTimeout(filterTimer);
        filterTimer = null;
      }
      pendingFilter = null;
    }

    function flushFilter() {
      if (filterTimer) {
        var text = pendingFilter;
        cancelPendingFilter();
        setFilter(text);
      }
    }

    function scheduleFilter(text) {
      pendingFilter = text;
      if (filterTimer) {
        clearTimeout(filterTimer);
      }
      filterTimer = setTimeout(function() {
        filterTimer = null;
        pendingFilter = null;
        setFilter(text);
      }, FILTER_DELAY);
    }

    var items = [];
    tinymce.each(settings().tokens, function(category) {
      var catSearchText = '';
      tinymce.each(category.children, function(token) {
        catSearchText = catSearchText + ' ' + token.text.toLowerCase() + ' ' + token.id.toLowerCase();
      });

      items.push({
        type: 'label',
        text: category.text,
        data: {searchText: catSearchText}
      });
      tinymce.each(category.children, function(token) {
        items.push({
            type: 'button',
            text: token.text,
            data: {searchText: (token.text + " " + token.id).toLowerCase()},
            onclick: function() {
              editor.windowManager.close();
              editor.insertContent(token.id);
            }
          }
        );
      });
    });

    win = editor.windowManager.open({
      title: 'Insert Token',
      autoScroll: true, // Can we move this to the grid?
      width: Math.max(400, Math.round(window.innerWidth/2)),
      height: Math.max(400, Math.round(window.innerHeight*2/3)),
      body: [
        {
          type: 'textbox',
          onkeyup: function(e) {
            switch (e.keyCode) {
              case 40: // Down
                flushFilter();
                win.find('button').each(function(btn) {
                  if (btn.state.data.visible) {
                    btn.focus();
                    return false;
                  }
                });
                break;
              default:
                scheduleFilter(e.target.value);
            }
          }
        },
        {
          layout: 'grid',
          name: 'mybuttons',
          type: 'form',
          columns: 1,
          items: items
        }
      ],
      buttons: []
    });

    gridCtrl = win.find('#mybuttons')[0] || null;
    filterCtrls = win.find('button').toArray().concat(win.find('label').toArray());
    win.on('close', cancelPendingFilter);
  });

  editor.addShortcut('ctrl+shift+t', 'Insert Token', 'civicrmtoken');

  editor.addButton('civicrmtoken', {
    type: 'splitbutton',
    tooltip: 'Token (Ctrl-Shift-T)',
    text: 'Tokens',
    onclick: function() {
      editor.execCommand('civicrmtoken');
    },
    menu: createHotListMenu(settings().tokens, settings().hotlist)
  });

});
