/**
 * @file
 * Provides base JS for gridstack.
 */

(function ($, Drupal) {

  "use strict";

  function prepareContent(src) {
    return '<img src="' + src + '" alt="item" class="--grid-img">';
  }

  function checkColumns(val){
    let nc = parseFloat(val);
    nc = nc < 2 ? 2 : nc;
    nc = nc > 55 ? 55 : nc;

    return nc;
  }

  function writeGrid(grid, textarea) {
    const data = [];
    grid.engine.nodes.forEach(function (node) {
      data.push({
        x: node.x,
        y: node.y,
        w: node.w,
        h: node.h,
        id: node.id,
      });
    });

    data.sort(function (a, b) {
      return a.id - b.id;
    });

    textarea.val(JSON.stringify(data, null, '  '));
  }

  function makeGridstack(textarea_id, wrapper, col_selector_id, wrapper_classes) {
    let textarea = $(textarea_id, wrapper);
    textarea.after('<div><div></div></div>');
    let div_wrapper = textarea.next();
    div_wrapper.addClass('gridstack_wrapper');

    if (wrapper_classes) {
      div_wrapper.addClass(wrapper_classes);
    }

    let div = $('div', div_wrapper);
    div.addClass('grid-stack');
    let m = textarea.data('drupal-selector');
    div.addClass('gridstack-' + m);

    let items = [];

    try {
      items = JSON.parse(textarea.val());
    }
    catch (e) {
      console.log('JSON parsing error were found in gridstack data.', e);
    }

    let grid = GridStack.init({
      float: true,
    },  `.gridstack-${m}`);

    let col_select = $(col_selector_id, wrapper);

    function columnsChanged() {
      let nc = checkColumns(col_select.val());
      grid.column(nc, 'moveScale');
      writeGrid(grid, textarea);

      if (div_wrapper.width() > 0) {
        grid.cellHeight(parseInt(div_wrapper.width() / nc) + 'px');
      }
      else {
        grid.cellHeight(parseInt(wrapper.width() / nc) + 'px');
      }

      grid.load(JSON.parse(textarea.val()));
    }

    grid.column(checkColumns(col_select.val()), 'moveScale');
    col_select.change(() => {
      columnsChanged();
    });

    grid.batchUpdate();
    $('.field--type-image img', wrapper).each(function (index) {

      let n = items[index] ? items[index] : {};
      n.id = index;
      n.content = prepareContent($(this).attr('src'));
      grid.addWidget(n);
    });

    grid.commit();
    columnsChanged();

    grid.on('added removed change', function (e, items) {
      writeGrid(grid, textarea);
    });

  }

  Drupal.behaviors.paragraphsGridstackInitEditable = {
    attach: function (context, settings) {
      $('.paragraphs-behavior', context).once().each(function () {
        // Attach additional options for a behavior.
        $(this).prepend('<div class="additional-functionality-wrapper"></div>');
        var $container = $('.additional-functionality-wrapper', context);
        $container.prepend('<div><input type="button" class="change-grid-settings" value="Desktop / Mobile"></div>');
        $container.prepend('<div><input type="button" class="restore-default-settings" value="Restore"></div>');
        $container.prepend('<div><input type="button" class="set-by-template" value="Set by template"></div>');

        let wrapper = $(this).parent();
        // Control switching of display modes.
        $('.change-grid-settings', wrapper).once().click(function () {
          let c = '--hide';
          $('select.grid_columns', wrapper).toggleClass(c);
          $('select.grid_columns_mobile', wrapper).toggleClass(c);
          $('.gridstack_wrapper', wrapper).toggleClass(c);

          if ($('textarea.grid_json_mobile', wrapper).hasClass('--grid-not-ready')) {
            $('textarea.grid_json_mobile', wrapper).removeClass('--grid-not-ready');
            makeGridstack('textarea.grid_json_mobile', wrapper, 'select.grid_columns_mobile', ['gridstack_wrapper', 'gridstack-wrapper-mobile']);
          }
        });

        makeGridstack('textarea.grid_json', wrapper, 'select.grid_columns',['gridstack_wrapper', 'gridstack-wrapper-desktop']);
        $('textarea.grid_json_mobile', wrapper).addClass('--grid-not-ready');
        $('textarea.grid_json_default').val($('textarea.grid_json').val());
        $('textarea.grid_json_mobile_default').val($('textarea.grid_json_mobile').val());

        // Restore settings of previous revision.
        $('.restore-default-settings', wrapper).once().click(function () {
          if (!$('.gridstack_wrapper.gridstack-wrapper-desktop', wrapper).hasClass('--hide')
            && !$('.gridstack_wrapper.gridstack-wrapper-desktop', wrapper).hasClass('--grid-not-ready')) {
            $('.gridstack_wrapper.gridstack-wrapper-desktop').remove();
            $('.gridstack_wrapper.gridstack-wrapper-mobile').addClass('--hide');
            $('textarea.grid_json').val($('textarea.grid_json_default').val());
            makeGridstack('textarea.grid_json', wrapper, 'select.grid_columns', ['gridstack_wrapper', 'gridstack-wrapper-desktop']);
          }
          else if (!$('.gridstack_wrapper.gridstack-wrapper-mobile', wrapper).hasClass('--hide')
            && !$('.gridstack_wrapper.gridstack-wrapper-mobile', wrapper).hasClass('--grid-not-ready')) {
            $('.gridstack_wrapper.gridstack-wrapper-mobile').remove();
            $('.gridstack_wrapper.gridstack-wrapper-desktop').addClass('--hide');
            $('textarea.grid_json_mobile').val($('textarea.grid_json_mobile_default').val());
            makeGridstack('textarea.grid_json_mobile', wrapper, 'select.grid_columns_mobile', ['gridstack_wrapper', 'gridstack-wrapper-mobile']);
          }
        });

        // Set elementree position by template.
        $('.set-by-template', wrapper).once().click(function () {
          if (!$('.gridstack_wrapper.gridstack-wrapper-desktop', wrapper).hasClass('--hide')
            && !$('.gridstack_wrapper.gridstack-wrapper-desktop', wrapper).hasClass('--grid-not-ready')) {
            $('.gridstack_wrapper.gridstack-wrapper-desktop').remove();
            $('.gridstack_wrapper.gridstack-wrapper-mobile').addClass('--hide');
            $('textarea.grid_json').val($('textarea.grid_json_template').val());
            makeGridstack('textarea.grid_json', wrapper, 'select.grid_columns', ['gridstack_wrapper', 'gridstack-wrapper-desktop']);
          }
          else if (!$('.gridstack_wrapper.gridstack-wrapper-mobile', wrapper).hasClass('--hide')
            && !$('.gridstack_wrapper.gridstack-wrapper-mobile', wrapper).hasClass('--grid-not-ready')) {
            $('.gridstack_wrapper.gridstack-wrapper-mobile').remove();
            $('.gridstack_wrapper.gridstack-wrapper-desktop').addClass('--hide');
            $('textarea.grid_json_mobile').val($('textarea.grid_json_mobile_template').val());
            makeGridstack('textarea.grid_json_mobile', wrapper, 'select.grid_columns_mobile', ['gridstack_wrapper', 'gridstack-wrapper-mobile']);
          }
        });
      });
    }
  };

  var paragraphsGridstackInitOnce = true;
  Drupal.behaviors.paragraphsGridstackInit = {
    attach: function (context, settings) {
      if (paragraphsGridstackInitOnce) {
        gridStackInit();
        paragraphsGridstackInitOnce = false;
      }

      let gridstackResizeEvent;
      $(window).once('paragraphsGridstackInit').on('resize', function (event) {
        clearTimeout(gridstackResizeEvent);
        gridstackResizeEvent = setTimeout(gridStackInit, 500);
      });

      function gridStackInit() {
        var $source, columns;
        var div_wrapper = $('.gridstack_wrapper');

        if ($(window).width() > 750) {
          $source = $('.gridstack_view > textarea')
          columns = $(div_wrapper).attr('data-grid-columns');
        }
        else {
          $source = $('.gridstack_view_mobile > textarea');
          columns = $(div_wrapper).attr('data-grid-columns-mobile');
        }

        var serializedData = $source.val();
        serializedData = JSON.parse(serializedData);

        var grid = GridStack.init({
          float: true,
        });

        let nc = checkColumns(columns);
        grid.column(nc, 'moveScale');
        grid.cellHeight(parseInt(div_wrapper.width() / nc) + 'px');
        grid.load(serializedData);
        grid.column(checkColumns(columns), 'moveScale');
        grid.batchUpdate();

        var elementsData = $('.grid-stack > .paragraph');

        for (var index = 0; index < serializedData.length; index++) {
          var id = serializedData[index]['id'];
          var $element;

          $element = id === 0 ?
            $(".grid-stack > .grid-stack-item:not(.grid-stack-item[gs-id]) > .grid-stack-item-content") :
            $(".grid-stack > .grid-stack-item[gs-id=" + id + "] > .grid-stack-item-content");

          $(elementsData[id]).appendTo($element);
        }

        grid.commit();
        grid.setStatic(true);
      }
    }
  };

}(jQuery, Drupal));
