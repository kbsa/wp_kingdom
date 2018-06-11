(function ($) {
    'use strict';

    $(document).ready(function () {
        mighty_mc_scan_starting_animation();
        mighty_mc_single_result_page();
        mighty_mc_backup();
        mighty_mc_remove_media();
    });

    function mighty_mc_single_result_page() {

        mighty_mc_single_result_lightbox();


        $(".lazy-load").lazyload({
            effect: "fadeIn",
            container: $(".result-container")
        });

        $(".single-result .image-container").click(function () {
            $(this).parent().toggleClass("selected");
        });

        $(".result-container").niceScroll({
            background: "#e3e3e3",
            cursorcolor: "#d1d1d1",
            autohidemode: false,
            cursorborderradius: "5px",
            horizrailenabled: false
        });

        $(".result-select-all").click(function () {
            $(this).toggleClass("all-selected");


            if ($(this).hasClass('all-selected')) {
                $(".single-result").each(function () {
                    $(this).addClass("selected");

                });
                $(this).html(mighty_data.deSelectAll);
            } else {
                $(".single-result").each(function () {
                    $(this).removeClass("selected");
                });
                $(this).html(mighty_data.selectAll);
            }

        });
    }

    function mighty_mc_single_result_lightbox() {
        return;
        $('.open-popup').magnificPopup({
            type: 'inline',
        });

        $(".single-result .image-container").hoverIntent({
            over: mighty_mc_show_image_popup,
            timeout: 500,
        });

        $(".single-result .image-container").mouseleave(function () {
            window.setTimeout(function () {
                if ($(".mfp-wrap").length < 1 && $(".single-result:hover").length < 1) {
                    $.magnificPopup.close();
                }
            }, 100);
        });

        $(".mighty-popup").mouseleave(function () {
            window.setTimeout(function () {
                if ($(".mfp-wrap").length < 1 || $(".single-result:hover").length < 1) {
                    $.magnificPopup.close();
                }
            }, 100);
        });

    }

    function mighty_mc_show_image_popup() {
        var offset = $(this).parent().offset();
        var scrollTop = $(window).scrollTop();
        var width = $(this).parent().width();
        if ($(this).parent().hasClass('edge')) {
            width = -(340);
        }
        var mtop = (offset.top - scrollTop);
        var mleft = offset.left + width;


        $(this).parent().find(".open-popup").trigger('click');
        $(".mighty-media-cleaner .mfp-wrap").css({'top': mtop});
        $(".mighty-media-cleaner .mfp-wrap").css({'left': mleft});
    }


    function mighty_mc_remove_media() {
        $(".result-delete-media").click(function () {


            if ($(".single-result.selected").size() > 0) {
                var images = {};

                $.MessageBox({
                    buttonDone: mighty_data.yes,
                    buttonFail: mighty_data.no,
                    message: mighty_data.msg1
                }).done(function () {

                    $(".result-delete-media").html(mighty_data.removing);

                    $(".single-result.selected").each(function () {
                        var id = $(this).attr("data-id");
                        var path = $(this).attr("data-physical-path");
                        images[id] = path;
                        $(this).fadeOut('slow');
                        $(this).remove();
                    });

                    $.ajax({
                        url: mighty_data.ajaxurl,
                        type: "POST",
                        dataType: 'html',
                        data: {'action': 'mighty_remove_media', 'media': images},
                        success: function (data) {
                            $(".result-delete-media").html(mighty_data.remove);
                            $(".single-result").each(function (i) {
                                if ((i + 1) > 0 && (i + 1) != 6 && (i + 1) != 7) {
                                    $(this).removeClass('edge').addClass("not-edge");
                                }
                                else {
                                    $(this).removeClass('not-edge').addClass("edge");
                                }
                                $(this).attr('data-counter', (i + 1));

                            });
                            var counter = $(".single-result").size();
                            $('.file-counter').html(counter + " " + mighty_data.fileAvailable);

                        }
                    });
                });

            }
            else {
                $.MessageBox(mighty_data.noMediaSelected);
            }
        });
    }

    function mighty_mc_backup() {
        var $backupBTN = $('.mighty-backup-header-button .mighty-button');
        if (!$backupBTN.length) {
            return;
        }
        // Backup Button functionality
        $backupBTN.click(function () {
            var $this = $(this);


            $this.find(".progress").css({'z-index': '-1'});
            $this.html('<span class="progress"></span>');

            $this.find(".progress").animate({width: "100%"}, 2000, function () {

                $.ajax({
                    url: mighty_data.ajaxurl,
                    type: "POST",
                    data: {
                        'action': 'mighty_mc_do_backup'
                    },
                    success: function (data) {
                        $this.removeClass("transparent").addClass("filled");
                        $this.html('<span class="progress"></span>D O N E');
                        window.location.reload();
                    },
                    error: function (errorThrown) {
                        $this.html(mighty_data.tryAgain);
                    }
                });
                $this.find(".progress").css({'z-index': '1'});

            });


        });


        // Remove Backup Button functionality
        var $removeBTN = $('.mighty-remove-backup');
        $removeBTN.click(function () {
            var $this = $(this);
            $.MessageBox({
                buttonDone: mighty_data.yes,
                buttonFail: mighty_data.no,
                message: mighty_data.msg4
            }).done(function () {
                $.ajax({
                    url: mighty_data.ajaxurl,
                    type: "POST",
                    data: {
                        'action': 'mighty_mc_remove_backup',
                        file: $this.attr('data-file-name')
                    },
                    success: function (data) {
                        $this.html(mighty_data.removed);
                        $this.parents('.file-container').fadeOut(600).remove();
                        if (!$('.backup-list').find('.file-container').length) {
                            $('.backup-list').html('<p class="no-file-found">' + mighty_data.msg5 + '</p>')
                        }
                    },
                    error: function (errorThrown) {
                        $this.html(mighty_data.tryAgain);
                    }
                });
            });
        });

        // Restore Backup Button functionality
        var $restoreBTN = $('.mighty-restore-backup');
        $restoreBTN.click(function () {
            var $this = $(this);
            $.MessageBox({
                buttonDone: mighty_data.yes,
                buttonFail: mighty_data.no,
                message: mighty_data.msg6
            }).done(function () {
                $.ajax({
                    url: mighty_data.ajaxurl,
                    type: "POST",
                    data: {
                        'action': 'mighty_mc_restore_backup',
                        file: $this.attr('data-file-name')
                    },
                    success: function (data) {
                        $.MessageBox({
                            buttonDone: mighty_data.yes,
                            message: mighty_data.msg7
                        }).done(function () {
                            window.reload();
                        });
                    },
                    error: function (errorThrown) {
                        $this.html(mighty_data.tryAgain);
                    }
                });
            });
        })
    }


    function mighty_mc_scan_starting_animation() {
        var count = 0;
        setInterval(function () {
            ++count;
            if (count > 3) {
                count = 1;
            }
            mighty_mc_scan_animation(count);
        }, 5000);
    }


    function mighty_mc_scan_animation(count) {

        var before = 0;

        var elements = {};

        elements['icon3'] = $(".tool-tip-icon .tool-tip-icon3");
        elements['desc3'] = $(".tool-tip-description3");
        elements['image3'] = $(".tool-tip-image .image-3");

        elements['icon2'] = $(".tool-tip-icon .tool-tip-icon2");
        elements['desc2'] = $(".tool-tip-description2");
        elements['image2'] = $(".tool-tip-image .image-2");

        elements['icon1'] = $(".tool-tip-icon .tool-tip-icon1");
        elements['desc1'] = $(".tool-tip-description1");
        elements['image1'] = $(".tool-tip-image .image-1");

        switch (count) {
            case 1:
                before = 3;
                break;

            case 2:
                before = 1;
                break;

            case 3:
                before = 2;
                break;
        }


        elements['icon' + before].animate({
            top: "-150px",
            opacity: 0.1
        }, {
            duration: 1000, easing: 'easeInOutQuad', complete: function () {
                elements['icon' + count].animate({top: "0px", opacity: 1}, {duration: 1000, easing: 'easeInOutQuad'});
            }
        });


        elements['desc' + before].animate({
            left: "-111%",
            opacity: 0.1
        }, {
            duration: 1000, easing: 'easeInOutQuad', complete: function () {
                elements['desc' + count].animate({left: "0", opacity: 1}, {duration: 1000, easing: 'easeInOutQuad'});

            }
        });

        elements['image' + before].animate({
            right: "-100%",
            opacity: 0.1
        }, {
            duration: 1000, easing: 'easeInOutQuad', complete: function () {
                elements['image' + count].animate({right: "0", opacity: 1}, {duration: 1000, easing: 'easeInOutQuad'});
            }
        });
    }


})(jQuery);


