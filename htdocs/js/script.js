const inzidenzLabel = function(tooltipItem, data) {
    let index = tooltipItem.index;
    let datasetIndex = tooltipItem.datasetIndex;

    let percent = '';
    if (index > 7) {
        percent = Math.round(100/data.datasets[datasetIndex].data[index-7] * data.datasets[datasetIndex].data[index])-100 ;
        if (percent > 0) { percent = '+' + percent; }
    }
    var label = 'Inzidenz:  ' + data.datasets[datasetIndex].data[index] + '  (LW ' + percent + '%)';

    return label;
};

const inzidenzLabelW = function(tooltipItem, data) {
    let index = tooltipItem.index;
    let datasetIndex = tooltipItem.datasetIndex;

    let percent = '';
    if (index > 1) {
        percent = Math.round(100/data.datasets[datasetIndex].data[index-1] * data.datasets[datasetIndex].data[index])-100 ;
        if (percent > 0) { percent = '+' + percent; }
    }
    var label = '' + data.datasets[datasetIndex].label + '  |  ' +  data.datasets[datasetIndex].data[index] + '  (LW ' + percent + '%)';

    return label;
};



/*!

 =========================================================
 * Material Dashboard - v2.1.2
 =========================================================

 * Product Page: https://www.creative-tim.com/product/material-dashboard
 * Copyright 2020 Creative Tim (http://www.creative-tim.com)

 * Designed by www.invisionapp.com Coded by www.creative-tim.com

 =========================================================

 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

 */



var mobile_menu_visible = 0;
$(document).on('click', '.navbar-toggler', function() {
    $toggle = $(this);

    if (mobile_menu_visible == 1) {
        $('html').removeClass('nav-open');

        $('.close-layer').remove();
        setTimeout(function() {
            $toggle.removeClass('toggled');
        }, 400);

        mobile_menu_visible = 0;
    } else {
        setTimeout(function() {
            $toggle.addClass('toggled');
        }, 430);

        var $layer = $('<div class="close-layer"></div>');

        if ($('body').find('.main-panel').length != 0) {
            $layer.appendTo(".main-panel");

        } else if (($('body').hasClass('off-canvas-sidebar'))) {
            $layer.appendTo(".wrapper-full-page");
        }

        setTimeout(function() {
            $layer.addClass('visible');
        }, 100);

        $layer.click(function() {
            $('html').removeClass('nav-open');
            mobile_menu_visible = 0;

            $layer.removeClass('visible');

            setTimeout(function() {
                $layer.remove();
                $toggle.removeClass('toggled');

            }, 400);
        });

        $('html').addClass('nav-open');
        mobile_menu_visible = 1;

    }

});

