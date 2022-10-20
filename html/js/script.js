window.phonePortrait = $( window ).width()<576;
window.initialWidth =  $( window ).width();
/*$( window ).resize(function() {
    window.phonePortrait = $( window ).width()<576;
    for (n in window.charts) {
        window.charts[n].options.responsive = window.phonePortrait?false:true;
        var ctx = document.getElementById(n).getContext('2d');
        window.myLine = new Chart(ctx,  window.charts[n]);
    }
});*/

$(window).resize(function() {
    clearTimeout(window.resizedFinished);
    window.resizedFinished = setTimeout(function(){
        if (
            (initialWidth > 576 &&  $( window ).width() < 576)
            || (initialWidth < 576 &&  $( window ).width() > 576)
        )
        {
            top.location.reload();
        }
        console.log('Resized finished.');
    }, 250);
});


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
    var label = data.datasets[datasetIndex].label + ':  ' + data.datasets[datasetIndex].data[index] + '  (LW ' + percent + '%)';
    return label;
};

const inzidenzBar = function (tooltipItem, data) {
    let index = tooltipItem.index;
    let datasetIndex = tooltipItem.datasetIndex;

    var label;
    if (datasetIndex == 1) {
        label = data.datasets[datasetIndex].label + ':  ' + (data.datasets[0].data[index]*1+data.datasets[1].data[index]*1).toFixed(2);
    } else {
        label = data.datasets[datasetIndex].label + ':  ' + (data.datasets[datasetIndex].data[index]*1).toFixed(2);
    }
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

    var label = [ data.datasets[datasetIndex].datasetTooltipPrefix + data.datasets[datasetIndex].label];
    label.push('Inzidenz: ' + data.datasets[datasetIndex].data[index] + ' (' + percent + '% z. Vorw.)');
    if (typeof data.datasets[datasetIndex].absValues != 'undefined') {
        label.push('Neuinfektionen absolut: ' + data.datasets[datasetIndex].absValues[index]);
    }
    return label;
};

const pcrtestsLabel = function(tooltipItem, data) {
    let index = tooltipItem.index;
    let datasetIndex = tooltipItem.datasetIndex;
    let value = data.datasets[datasetIndex].data[index];
    var label = data.datasets[datasetIndex].label;
    if (datasetIndex == 0) {
        label += ' ' + (value*1).toFixed(1) + '%';
    } else {
        let e = (value % 1000)+"";

        for (i=e.length; i<3; i++) {
            e = '0' + e;
        }
        return Math.floor(value/1000) + "." + e;
    }
    return label;
}

$( () => {
    for (n in window.charts) {
        var ctx = document.getElementById(n).getContext('2d');
        window.myLine = new Chart(ctx,  window.charts[n]);
    }

    if (window.phonePortrait) {
        $('.card-body').animate({scrollLeft: 800}, 2000, 'swing');
    }

    if (!navigator.canShare) { //} || !navigator.canShare({files})) {
        $('.webshare').hide();
    }

})

var plugin = {
     afterInit: function(chart) {
         if (typeof chart.options.footer != undefined && chart.options.footer == false) { return; }
         this.img = new Image();
         this.img.src = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFAAAAAPCAMAAABEF7i9AAAAaVBMVEUAAAD///+3vbYWFhZWVlaIiIgvLy/x8fHU1NSYmJgaGhrS1tLEycSjp6JBQkHOz87DxcN4eXhnZ2dTU1PR1NG2tranp6een56Xl5eQkJCNko2Dh4JtcG1dXl1LTUvj4+M2NzYzNDElJSU3B71zAAAArklEQVQ4y82T2Q4CIQxF27KKzu6M4778/0cKpE6MbwM8eAMhgebktqVQXlhUAUhBHWz4SoqKaOsgSQuwEoHHxAfR7pgJvGlcZOFCtL/mAb1Bq0EaCdqirH3OB64vB60EnqBF3aNSzkwjKuFzbuI7L1zr8AzWhK70NSJGYNuwQw5Lcyg7Z9T069DvpBqODjojob7j8F3DePxBlysxL8Dhmf0PY1vmD++VOylFBcX1BrmeBxyRm7wxAAAAAElFTkSuQmCC";
         chart.config.options.layout.padding.bottom += chart.config.footer.length * 18;
    },
    beforeDraw: function (chart) {
        if (typeof chart.options.footer != undefined && chart.options.footer == false) { return; }
        var width = chart.chart.width,
            height = chart.chart.height,
            ctx = chart.chart.ctx;
        ctx.restore();
        ctx.font = "0.8em sans-serif";

        ctx.textBaseline = "middle";
        ctx.rect(0, 0, width, height);
        ctx.fillStyle = "white";
        ctx.fill();
        ctx.fillStyle = "black";
        ctx.textAlign = "left";
        for (n in chart.config.footer) {
            let index = (chart.config.footer.length-1 - n) ;
            ctx.fillText(chart.config.footer[n], 10, height-10-index*15);
        }

        ctx.textAlign = "right";
        ctx.fillText("www.coronahh.de", width-100, height - 10);
        ctx.drawImage(this.img, width-90, height-21, 80, 15);
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

    return {
        x: 0,
        y: 0
    };
};


function downloadCanvas(id, filename) {
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

function downloadCanvas2(id, filename) {
    var link = document.createElement('a');
    link.download = filename;
    link.href = document.getElementById(id).toDataURL()
    link.click();
}

function saveCanvasAsFile(id, title) {

    var c = document.getElementById(id);
    c.toBlob(
        async (blob) => {

            const text = title;
            const url = top.location.href;
            const files = [
                new File(
                    [blob],
                    'chart.png',
                    {
                        type: "image/png",
                        lastModified: new Date().getTime()
                    }
                )
            ];

            if (files && files.length > 0) {
                if (!navigator.canShare || !navigator.canShare({files})) {
                    alert('Error: Unsupported feature: navigator.canShare()');
                    return;
                }
            }

            try {
                await navigator.share({files, url});
                console.log('Successfully sent share');
            } catch (error) {
                console.log('Error sharing: ' + error);
            }
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

