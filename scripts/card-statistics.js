/*=========================================================================================
    File Name: card-statistics.js
    Description: intialize advance card statistics
    ----------------------------------------------------------------------------------------
    Item Name: Stack - Responsive Admin Theme
    Author: Pixinvent
    Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/
(function (window, document, $) {
  'use strict';
  // colors for charts
  var $primary = "#00b5b8",
    $secondary = "#2c3648",
    $success = "#0f8e67",
    $info = "#179bad",
    $warning = "#ffb997",
    $danger = "#ff8f9e"

  var $themeColor = [$info, $success, $warning, $primary, $danger, $secondary]


  /*****************************************************
   *               Grouped Card Statistics              *
   *****************************************************/
  var rtl = false;
  if ($('html').data('textdirection') == 'rtl')
    rtl = true;

  if ($('.knob').length) {
    $(".knob").knob({
      rtl: rtl,
      draw: function () {
        var ele = this.$;
        var style = ele.attr('style');
        style = style.replace("bold", "normal");
        var fontSize = parseInt(ele.css('font-size'), 10);
        var updateFontSize = Math.ceil(fontSize * 1.65);
        style = style.replace("bold", "normal");
        style = style + "font-size: " + updateFontSize + "px;";
        var icon = ele.attr('data-knob-icon');
        ele.hide();
        $('<i class="knob-center-icon ' + icon + '"></i>').insertAfter(ele).attr('style', style);

        // "tron" case
        if (this.$.data('skin') == 'tron') {

          this.cursorExt = 0.3;

          var a = this.arc(this.cv), // Arc
            pa, // Previous arc
            r = 1;

          this.g.lineWidth = this.lineWidth;

          if (this.o.displayPrevious) {
            pa = this.arc(this.v);
            this.g.beginPath();
            this.g.strokeStyle = this.pColor;
            this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, pa.s, pa.e, pa.d);
            this.g.stroke();
          }

          this.g.beginPath();
          this.g.strokeStyle = r ? this.o.fgColor : this.fgColor;
          this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, a.s, a.e, a.d);
          this.g.stroke();

          this.g.lineWidth = 2;
          this.g.beginPath();
          this.g.strokeStyle = this.o.fgColor;
          this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false);
          this.g.stroke();

          return false;
        }
      }
    });
  }





  /************************************************
   *               Sparkline Charts                *
   ************************************************/

  var sparkLineDraw = function () {
    /******************
     *   Line Charts   *
     ******************/
    // Total Cost
    if ($("#sp-line-total-cost").length) {
      $("#sp-line-total-cost").sparkline([14, 12, 4, 9, 3, 6, 11, 10, 13, 9, 14, 11, 16, 20, 15], {
        type: 'line',
        width: '100%',
        height: '100px',
        lineColor: '#FFA87D',
        fillColor: '#FFA87D',
        spotColor: '',
        minSpotColor: '',
        maxSpotColor: '',
        highlightSpotColor: '',
        highlightLineColor: '',
        chartRangeMin: 0,
        chartRangeMax: 20,
      });
    }

    // Total Sales
    if ($("#sp-line-total-sales").length) {
      $("#sp-line-total-sales").sparkline([14, 12, 4, 9, 3, 6, 11, 10, 13, 9, 14, 11, 16, 20, 15], {
        type: 'line',
        width: '100%',
        height: '100px',
        lineColor: '#16D39A',
        fillColor: '#16D39A',
        spotColor: '',
        minSpotColor: '',
        maxSpotColor: '',
        highlightSpotColor: '',
        highlightLineColor: '',
        chartRangeMin: 0,
        chartRangeMax: 20,
      });
    }

    // Total Revenue
    if ($("#sp-line-total-revenue").length) {
      $("#sp-line-total-revenue").sparkline([14, 12, 4, 9, 3, 6, 11, 10, 13, 9, 14, 11, 16, 20, 15], {
        type: 'line',
        width: '100%',
        height: '100px',
        lineColor: '#FF7588',
        fillColor: '#FF7588',
        spotColor: '',
        minSpotColor: '',
        maxSpotColor: '',
        highlightSpotColor: '',
        highlightLineColor: '',
        chartRangeMin: 0,
        chartRangeMax: 20,
      });
    }
    /**********************
     *   Tristate Charts   *
     **********************/
    if ($("#sp-tristate-bar-total-cost").length) {
      $("#sp-tristate-bar-total-cost").sparkline([1, 1, 0, 1, -1, -1, 1, -1, 0, 0, 1, 1, 0, -1, 1, -1], {
        type: 'tristate',
        height: '30',
        posBarColor: '#ffeb3b',
        negBarColor: '#4caf50',
        barWidth: 4,
        barSpacing: 5,
        zeroAxis: false
      });
    }

    if ($("#sp-tristate-bar-total-sales").length) {
      $("#sp-tristate-bar-total-sales").sparkline([1, 1, 0, 1, -1, -1, 1, -1, 0, 0, 1, 1, 0, -1, 1, -1], {
        type: 'tristate',
        height: '30',
        posBarColor: '#009688',
        negBarColor: '#FF5722',
        barWidth: 4,
        barSpacing: 5,
        zeroAxis: false
      });
    }

    if ($("#sp-tristate-bar-total-revenue").length) {
      $("#sp-tristate-bar-total-revenue").sparkline([1, 1, 0, 1, -1, -1, 1, -1, 0, 0, 1, 1, 0, -1, 1, -1], {
        type: 'tristate',
        height: '30',
        posBarColor: '#00BCD4',
        negBarColor: '#E91E63',
        barWidth: 4,
        barSpacing: 5,
        zeroAxis: false
      });
    }


    // Total Revenue
    if ($("#sp-line-total-profit").length) {
      $("#sp-line-total-profit").sparkline([14, 12, 4, 9, 3, 6, 11, 10, 13, 9, 14, 11, 16, 20, 15], {
        type: 'line',
        width: '100%',
        height: '50px',
        lineColor: '#E91E63',
        fillColor: '',
        spotColor: '',
        minSpotColor: '',
        maxSpotColor: '',
        highlightSpotColor: '',
        highlightLineColor: '',
        chartRangeMin: 0,
        chartRangeMax: 20,
      });
    }
  };

  var sparkResize;

  $(window).resize(function (e) {
    clearTimeout(sparkResize);
    sparkResize = setTimeout(sparkLineDraw, 500);
  });
  sparkLineDraw();






  // perfect scrollbar for latest update card

  if ($('.latest-update-tracking').length > 0) {
		new PerfectScrollbar(".latest-update-tracking-list", {
		  wheelPropagation: false
		});
	}
})(window, document, jQuery);
