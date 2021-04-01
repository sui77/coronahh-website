const inzidenzLabel = function (tooltipItem, data) {
    let index = tooltipItem.index;
    let datasetIndex = tooltipItem.datasetIndex;

    let percent = '';
    if (index > 7) {
        percent = Math.round(100 / data.datasets[datasetIndex].data[index - 7] * data.datasets[datasetIndex].data[index]) - 100;
        if (percent > 0) {
            percent = '+' + percent;
        }
    }
    var label = 'Inzidenz:  ' + data.datasets[datasetIndex].data[index] + '  (LW ' + percent + '%)';

    return label;
};

const inzidenzLabelW = function (tooltipItem, data) {
    let index = tooltipItem.index;
    let datasetIndex = tooltipItem.datasetIndex;

    let percent = '';
    if (index > 1) {
        percent = Math.round(100 / data.datasets[datasetIndex].data[index - 1] * data.datasets[datasetIndex].data[index]) - 100;
        if (percent > 0) {
            percent = '+' + percent;
        }
    }
    var label = '' + data.datasets[datasetIndex].label + '  |  ' + data.datasets[datasetIndex].data[index] + '  (LW ' + percent + '%)';

    return label;
};


$( () => {
    for (n in window.charts) {
        var ctx = document.getElementById(n).getContext('2d');
        window.myLine = new Chart(ctx,  window.charts[n]);
    }
})

var plugin = {
     afterInit: function(chart) {
         this.img = new Image();
         this.img.src = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFAAAAAPCAMAAABEF7i9AAAAaVBMVEUAAAD///+3vbYWFhZWVlaIiIgvLy/x8fHU1NSYmJgaGhrS1tLEycSjp6JBQkHOz87DxcN4eXhnZ2dTU1PR1NG2tranp6een56Xl5eQkJCNko2Dh4JtcG1dXl1LTUvj4+M2NzYzNDElJSU3B71zAAAArklEQVQ4y82T2Q4CIQxF27KKzu6M4778/0cKpE6MbwM8eAMhgebktqVQXlhUAUhBHWz4SoqKaOsgSQuwEoHHxAfR7pgJvGlcZOFCtL/mAb1Bq0EaCdqirH3OB64vB60EnqBF3aNSzkwjKuFzbuI7L1zr8AzWhK70NSJGYNuwQw5Lcyg7Z9T069DvpBqODjojob7j8F3DePxBlysxL8Dhmf0PY1vmD++VOylFBcX1BrmeBxyRm7wxAAAAAElFTkSuQmCC";
         chart.config.options.layout.padding.bottom += chart.config.footer.length * 18;
    },
    beforeDraw: function (chart) {
        var width = chart.chart.width,
            height = chart.chart.height,
            ctx = chart.chart.ctx;
        ctx.restore();
        ctx.font = "0.8em sans-serif";
        ctx.textAlign = "right";
        ctx.textBaseline = "middle";
        ctx.rect(0, 0, width, height);
        ctx.fillStyle = "white";
        ctx.fill();
        ctx.fillStyle = "black";
        ctx.fillText("www.coronahh.de", width - 10, height - 30);
        ctx.textAlign = "left";
        for (n in chart.config.footer) {
            let index = (chart.config.footer.length-1 - n) ;
            ctx.fillText(chart.config.footer[n], 10, height-10-index*15);
        }

        ctx.drawImage(this.img, width-80-14, height-22, 80, 15);
/*
        ctx.beginPath();
        ctx.moveTo(0, height-chart.config.options.layout.padding.bottom);
        ctx.lineTo(width, height-chart.config.options.layout.padding.bottom);
        ctx.stroke();
*/
        ctx.save();
    }
};
Chart.plugins.register(plugin);


Chart.Tooltip.positioners.custom = function(elements, eventPosition) {
    /** @type {Chart.Tooltip} */
    var tooltip = this;

    /* ... */
console.log(elements, eventPosition);
    return {
        x: 0,
        y: 0
    };
};

function saveCanvasAsFile(id, filename) {
    var c = document.getElementById(id);
    c.toBlob(
        blob => {
            const anchor = document.createElement('a');
            anchor.download = filename;
            anchor.href = URL.createObjectURL(blob);
            anchor.click(); // ✨ magic!
            URL.revokeObjectURL(anchor.href); // remove it from memory and save on memory! 😎
        },
        'image/png'
    );
}

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
$(document).on('click', '.navbar-toggler', function () {
    $toggle = $(this);

    if (mobile_menu_visible == 1) {
        $('html').removeClass('nav-open');

        $('.close-layer').remove();
        setTimeout(function () {
            $toggle.removeClass('toggled');
        }, 400);

        mobile_menu_visible = 0;
    } else {
        setTimeout(function () {
            $toggle.addClass('toggled');
        }, 430);

        var $layer = $('<div class="close-layer"></div>');

        if ($('body').find('.main-panel').length != 0) {
            $layer.appendTo(".main-panel");

        } else if (($('body').hasClass('off-canvas-sidebar'))) {
            $layer.appendTo(".wrapper-full-page");
        }

        setTimeout(function () {
            $layer.addClass('visible');
        }, 100);

        $layer.click(function () {
            $('html').removeClass('nav-open');
            mobile_menu_visible = 0;

            $layer.removeClass('visible');

            setTimeout(function () {
                $layer.remove();
                $toggle.removeClass('toggled');

            }, 400);
        });

        $('html').addClass('nav-open');
        mobile_menu_visible = 1;

    }

});

