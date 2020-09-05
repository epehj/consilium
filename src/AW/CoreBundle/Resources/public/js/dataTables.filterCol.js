(function (factory) {
  "use strict";
  if (typeof define === "function" && define.amd ) {
    // AMD
    define (["jquery", "datatables.net"], function ($) {
      return factory ($, window, document);
    });
  } else if (typeof exports === "object") {
    // CommonJS
    module.exports = function (root, $) {
      if (!root) {
        root = window;
      }
      if (!$ || !$.fn.dataTable) {
        $ = require("datatables.net")(root, $).$;
      }
      return factory($, root, root.document);
    };
  } else {
    // Browser
    factory(jQuery, window, document);
  }
}(function ($, window, document, undefined) {
  "use strict";
  var DataTable = $.fn.dataTable;
  var FILTER_SELECTOR = ".filter-col";
  var FILTER_INPUT_SELECTOR = "input" + FILTER_SELECTOR;
  var FILTER_SELECTPICKER_SELECTOR = ".selectpicker" + FILTER_SELECTOR;
  var FILTER_COMBOBOX_SELECTOR = "select.combobox" + FILTER_SELECTOR;
  var FILTER_APPLY_SELECTOR = ".filter-col-apply";
  var FILTER_RESET_SELECTOR = ".filter-col-reset";

  DataTable.filterCol = {};

  /**
   * Initialize
   *
   * @param {DataTable.Api} dt DataTable
   * @private
   */
  DataTable.filterCol.init = function (dt) {
    var ctx = dt.settings()[0];

    ctx._filterCol = {};
    ctx._filterCol.filter = $(FILTER_SELECTOR);
    ctx._filterCol.filterInput = $(FILTER_INPUT_SELECTOR);
    ctx._filterCol.filterSelectPicker = $(FILTER_SELECTPICKER_SELECTOR);
    ctx._filterCol.filterCombobox = $(FILTER_COMBOBOX_SELECTOR);
    ctx._filterCol.filterApply = $(FILTER_APPLY_SELECTOR);
    ctx._filterCol.filterReset = $(FILTER_RESET_SELECTOR);

    if (ctx.oInit.filterCol !== true) {
      return;
    }

    // Handle input apply
    ctx._filterCol.filterInput.keypress(function(e){
      var keycode = (e.keyCode ? e.keyCode : e.which);
      if(keycode != 13){
        return true;
      }

      ctx._filterCol.filterApply.trigger('click');
      return false;
    });

    // Handle selectPicker change
    ctx._filterCol.filterSelectPicker.change(function(e){
      ctx._filterCol.filterApply.trigger('click');
    });

    // Handle combobox change
    ctx._filterCol.filterCombobox.change(function(e){
      ctx._filterCol.filterApply.trigger('click');
    });

    // Apply all filters
    ctx._filterCol.filterApply.click(function(e){
      $.each(ctx._filterCol.filter, function(index, item){
        if($(item).data('filter-col') == undefined){
          return;
        }

        var columnName = $(item).data('filter-col')+':name';
        var value = $(item).val();

        dt.column(columnName).search(value);
      });

      dt.draw();
    });

    // Reset all filters
    ctx._filterCol.filterReset.click(function(e){
      dt.search('').columns().search('');

      $.each(ctx._filterCol.filter, function(index, item){
        if($(item).data('filter-col-type') == 'text'){
          $(item).val('');
        }else if($(item).data('filter-col-type') == 'selectpicker'){
          $(item).selectpicker('val', '');
        }else if($(item).data('filter-col-type') == 'combobox'){
          $(item).val('');
          $(item).data('combobox').clearElement();
        }
      });

      dt.draw();
    });
  };

  // DataTables creation
  $(document).on("init.dt", function (e, ctx, json) {
    if (e.namespace !== "dt") {
      return;
    }

    DataTable.filterCol.init(new DataTable.Api(ctx));
  });
  return DataTable.filterCol;
}));
