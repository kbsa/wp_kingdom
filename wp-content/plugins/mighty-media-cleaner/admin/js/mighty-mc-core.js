(function ($) {
    'use strict';
    $(document).ready(function () {
        mighty_mc_db_cloner();
    });

    function mighty_mc_scan() {
        'use strict';
        var items = JSON.parse(mighty_core.items),
            items_arr = {0: {}},
            i = 0,
            j = 0,
            stop = 20,
            key;
        for (key in items) {
            if (!items.hasOwnProperty(key)) continue;
            if (i == stop) {
                stop = stop + 20;
                j++;
                items_arr[j] = {};
            }
            items_arr[j][key] = items[key];
            i++;
        }
        localStorage.setItem('mc_scan', ++j);
        localStorage.setItem('mc_scan_total', j);
        if (undefined != items_arr[0]) {
            mighty_mc_scan_ajax(items_arr, 0);
        }
    }

    function mighty_mc_scan_ajax(items, id) {
        'use strict';
        var totalz = parseInt(localStorage.getItem('mc_scan_total'));
        var steps = (100 / totalz),
            id = id,
            items = items;
        var sacn_ajax = $.ajax({
            url: mighty_core.ajaxurl,
            type: "POST",
            data: {
                'action': 'mighty_mc_scan_ajax',
                items: JSON.stringify(items[id])
            },
            success: function (data) {
                var total = parseInt(localStorage.getItem('mc_scan'));
                localStorage.setItem('mc_scan', total - 1);
                var remain = localStorage.getItem('mc_scan');


                var width = $('.bar-container .back-bor').width();
                var parentWidth = $('.bar-container .back-bor').parent().width();
                var percent = 100 * width / parentWidth;


                var per = parseInt(percent) + steps + "%";
                if (per + steps == steps) {
                    per = 0;
                }
                var per2 = parseInt(percent - 4) + steps + "%";

                $(".bar-container .back-bor").animate({'width': per}, {duration: 1000, easing: 'easeInOutQuad'});
                $(".percentage-container span").animate({'left': per2}, {duration: 1000, easing: 'easeInOutQuad'});
                $(".percentage-container span").html(Math.floor(parseInt(per)) + "%");
                if (remain == 0) {
                    console.log(data);
                    //the scan has benn finished
                    var end = new Date().getTime();
                    var start = parseInt(localStorage.getItem('mc_scan_time'));
                    var time = end - start;
                    $(".bar-container .back-bor").animate({'width': '100%'}, {duration: 1000, easing: 'easeInOutQuad'});
                    $(".percentage-container span").animate({'left': '95%'}, {duration: 1000, easing: 'easeInOutQuad'});
                    $(".percentage-container span").html(Math.floor(parseInt('100%')) + "%");
                    setTimeout(function(){
                        window.location.href = mighty_core.resultURL + '&time=' + time;
                    },100);
                } else {
                    mighty_mc_scan_ajax(items, id + 1);
                }
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });
        $(window).unload(function () {
            sacn_ajax.abort();
        });
    }

    function mighty_mc_db_cloner() {
        'use strict';
        var start = new Date().getTime();
        localStorage.setItem('mc_scan_time', start);
        $.ajax({
            url: mighty_core.ajaxurl,
            type: "POST",
            data: {
                'action': 'mighty_mc_db_cloner'
            },
            success: function (data) {
                console.log('db cloned');
                mighty_mc_scan();
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });
    }

})(jQuery);


