jQuery(document).ready(
    function($) {

        function checkbox_toggle(element) {
            if (!element.is(':checked')) {
                element.parent().next().hide();
            } else {
                element.parent().next().show();
            }

        }

        $(".checkbox_toggle input").click(
            function() {
                checkbox_toggle($(this))

            }
        );
        $(".checkbox_toggle input").each(
            function() {
                checkbox_toggle($(this))
            }
        );

        $(".copy_tags_to_textarea a").click(
            function() {
                var textarea_id = $(this).parent().attr("data-textarea");
                var text = $("#" + textarea_id).val() + $(this).attr("data");
                $("#" + textarea_id).val(text);

                return false;
            }
        );

        $(".post-type-shop_order #bulk-action-selector-top").change(
            function() {

                if ($(this).val() == "assign_a_driver") {
                    var $this = $(this);
                    if ($("#lddfw_driverid_lddfw_action").length) {
                        $("#lddfw_driverid_lddfw_action").show();
                    } else {
                        $.post(
                            WPaAjax.ajaxurl, {
                                action: 'lddfw_ajax',
                                lddfw_service: 'lddfw_get_drivers_list',
                                lddfw_obj_id: 'lddfw_action',
                            },
                            function(data) {
                                $(data).insertAfter($this);
                            }
                        );
                    }
                } else {
                    $("#lddfw_driverid_lddfw_action").hide();
                }
            }
        );

        $(".post-type-shop_order #bulk-action-selector-bottom").change(
            function() {
                if ($(this).val() == "assign_a_driver") {
                    var $this = $(this);
                    if ($("#lddfw_driverid_lddfw_action2").length) {
                        $("#lddfw_driverid_lddfw_action2").show();
                    } else {
                        $.post(
                            WPaAjax.ajaxurl, {
                                action: 'lddfw_ajax',
                                lddfw_service: 'lddfw_get_drivers_list',
                                lddfw_obj_id: 'lddfw_action2',
                            },
                            function(data) {
                                $(data).insertAfter($this);
                            }
                        );
                    }
                } else {
                    $("#lddfw_driverid_lddfw_action2").hide();
                }
            }
        );

    }
);