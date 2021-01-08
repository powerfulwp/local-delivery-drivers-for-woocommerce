(function() {
    "use strict";

    
/* Premium Code Stripped by Freemius */

    jQuery(".lddfw_premium-feature button").click(function() {
        jQuery(this).parent().find(".lddfw_lightbox").show();
    });

    jQuery("#lddfw_out_for_delivery_button").click(
        function() {
            jQuery("#lddfw_out_for_delivery_button").hide();
            jQuery("#lddfw_out_for_delivery_button_loading").show();

            var lddfw_order_list = '';
            jQuery("#lddfw_alert").html();
            jQuery('.lddfw_multi_checkbox .custom-control-input').each(
                function(index, item) {
                    if (jQuery(this).prop("checked") == true) {
                        if (lddfw_order_list != "") {
                            lddfw_order_list = lddfw_order_list + ",";
                        }
                        lddfw_order_list = lddfw_order_list + jQuery(this).val();
                    }
                }
            );
            jQuery.ajax({
                url: lddfw_ajax_url,
                type: 'POST',
                data: {
                    action: 'lddfw_ajax',
                    lddfw_service: 'lddfw_out_for_delivery',
                    lddfw_orders_list: lddfw_order_list,
                    lddfw_driver_id: lddfw_driver_id,
                    lddfw_wpnonce: lddfw_nonce,
                    lddfw_data_type: 'json'
                }
            }).done(
                function(data) {

                    jQuery("#lddfw_out_for_delivery_button").show();
                    jQuery("#lddfw_out_for_delivery_button_loading").hide();

                    jQuery("#lddfw_alert").show();
                    var lddfw_json = JSON.parse(data);
                    if (lddfw_json["result"] == "0") {
                        jQuery("#lddfw_alert").html("<div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\"><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" + lddfw_json["error"] + "</div>");
                    }
                    if (lddfw_json["result"] == "1") {

                        jQuery('.lddfw_multi_checkbox .custom-control-input').each(
                            function(index, item) {
                                if (jQuery(this).prop("checked") == true) {
                                    jQuery(this).parents(".lddfw_multi_checkbox").replaceWith("");
                                }
                            }
                        );
                        jQuery("#lddfw_alert").html(lddfw_json["error"]);
                        if (jQuery('.lddfw_multi_checkbox').length == 0) {
                            jQuery(".lddfw_footer_buttons").hide();
                        }
                    }
                }
            );
            return false;
        }
    );

    jQuery(".lddfw_multi_checkbox .lddfw_wrap").click(
        function() {
            var lddfw_chk = jQuery(this).find(".custom-control-input");
            if (lddfw_chk.prop("checked") == true) {
                jQuery(this).parents(".lddfw_multi_checkbox").removeClass("lddfw_active");
                lddfw_chk.prop("checked", false);
            } else {
                jQuery(this).parents(".lddfw_multi_checkbox").addClass("lddfw_active");
                lddfw_chk.prop("checked", true);
            }
        }
    );

    jQuery("#lddfw_start").click(
        function() {
            jQuery("#lddfw_home").hide();
            jQuery("#lddfw_login").show();
        }
    );

    jQuery("#lddfw_login_button").click(
        function() {
            // hide the sign up button
            jQuery("#lddfw_signup_button").hide();
            // show the login form
            jQuery("#lddfw_login_wrap").toggle();
            return false;
        }
    );

    jQuery("#lddfw_availability").click(
        function() {
            if (jQuery(this).hasClass("lddfw_active")) {
                jQuery(this).removeClass("lddfw_active");
                jQuery(this).html('<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="toggle-off" class="svg-inline--fa fa-toggle-off fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M384 64H192C85.961 64 0 149.961 0 256s85.961 192 192 192h192c106.039 0 192-85.961 192-192S490.039 64 384 64zM64 256c0-70.741 57.249-128 128-128 70.741 0 128 57.249 128 128 0 70.741-57.249 128-128 128-70.741 0-128-57.249-128-128zm320 128h-48.905c65.217-72.858 65.236-183.12 0-256H384c70.741 0 128 57.249 128 128 0 70.74-57.249 128-128 128z"></path></svg>');
                jQuery("#lddfw_availability_status").html(jQuery("#lddfw_availability_status").attr("unavailable"));
                jQuery("#lddfw_menu .lddfw_availability").removeClass("text-success");
                jQuery("#lddfw_menu .lddfw_availability").addClass("text-danger");
                jQuery.post(
                    lddfw_ajax_url, {
                        action: 'lddfw_ajax',
                        lddfw_service: 'lddfw_availability',
                        lddfw_availability: "0",
                        lddfw_driver_id: lddfw_driver_id,
                        lddfw_wpnonce: lddfw_nonce,
                        lddfw_data_type: 'html'
                    }
                );
            } else {
                jQuery(this).addClass("lddfw_active");
                jQuery("#lddfw_availability_status").html(jQuery("#lddfw_availability_status").attr("available"));
                jQuery(this).html('<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="toggle-on" class="svg-inline--fa fa-toggle-on fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M384 64H192C86 64 0 150 0 256s86 192 192 192h192c106 0 192-86 192-192S490 64 384 64zm0 320c-70.8 0-128-57.3-128-128 0-70.8 57.3-128 128-128 70.8 0 128 57.3 128 128 0 70.8-57.3 128-128 128z"></path></svg>');
                jQuery("#lddfw_menu .lddfw_availability").removeClass("text-danger");
                jQuery("#lddfw_menu .lddfw_availability").addClass("text-success");
                jQuery.post(
                    lddfw_ajax_url, {
                        action: 'lddfw_ajax',
                        lddfw_service: 'lddfw_availability',
                        lddfw_availability: "1",
                        lddfw_driver_id: lddfw_driver_id,
                        lddfw_wpnonce: lddfw_nonce,
                        lddfw_data_type: 'html'
                    }
                );
            }
            return false;
        }
    );

    jQuery("#lddfw_dates_range").change(
        function() {
            var lddfw_location = jQuery(this).attr("data") + '&lddfw_dates=' + this.value;
            window.location.replace(lddfw_location);
            return false;
        }
    );

    if (lddfw_dates != "") {
        jQuery("#lddfw_dates_range").val(lddfw_dates);
    }

    function lddfw_delivered_screen_open() {
        jQuery("#lddfw_driver_complete_btn").show();
        jQuery(".lddfw_page_content").hide();
        jQuery("#lddfw_delivery_signature").hide();
        jQuery("#lddfw_delivery_photo").hide();
        jQuery("#lddfw_delivered_form").hide();
        jQuery("#lddfw_failed_delivery_form").hide();
        jQuery(".delivery_proof_bar a").removeClass("active");
        jQuery(".delivery_proof_bar a").eq(0).addClass("active");
    }

    jQuery("#lddfw_delivered_screen_btn").click(
        function() {
            jQuery("#lddfw_driver_complete_btn").attr("delivery", "success");
            jQuery(".delivery_proof_notes").attr("href", "lddfw_delivered_form");
            lddfw_delivered_screen_open();
            jQuery("#lddfw_delivered_form").show();
            jQuery("#lddfw_delivery_screen").show();
            return false;
        }
    );

    jQuery("#lddfw_failed_delivered_screen_btn").click(
        function() {
            jQuery("#lddfw_driver_complete_btn").attr("delivery", "failed");
            jQuery(".delivery_proof_notes").attr("href", "lddfw_failed_delivery_form");
            lddfw_delivered_screen_open();
            jQuery("#lddfw_failed_delivery_form").show();
            jQuery("#lddfw_delivery_screen").show();
            return false;
        }
    );

    jQuery(".lddfw_dashboard .lddfw_box a").click(function() {
        jQuery(this).parent().addClass("lddfw_active");
    });

    jQuery(".lddfw_confirmation .lddfw_cancel").click(
        function() {
            jQuery(".lddfw_page_content").show();
            jQuery(this).parents(".lddfw_lightbox").hide();
            return false;
        }
    );

    jQuery("#lddfw_delivered_confirmation .lddfw_ok").click(
        function() {

            var lddfw_reason = jQuery('input[name=lddfw_delivery_dropoff_location]:checked', '#lddfw_delivered_form');
            if (lddfw_reason.attr("id") != "lddfw_delivery_dropoff_other") {
                jQuery("#lddfw_driver_delivered_note").val(lddfw_reason.val());
            }
            jQuery("#lddfw_delivered").hide();
            jQuery("#lddfw_thankyou").show();

            var lddfw_orderid = jQuery("#lddfw_driver_complete_btn").attr("order_id");
            var lddfw_signature = '';
            var lddfw_delivery_image = '';
            
/* Premium Code Stripped by Freemius */


            jQuery.ajax({
                type: "POST",
                url: lddfw_ajax_url,
                data: {
                    action: 'lddfw_ajax',
                    lddfw_service: 'lddfw_status',
                    lddfw_order_id: lddfw_orderid,
                    lddfw_order_status: jQuery("#lddfw_driver_complete_btn").attr("delivered_status"),
                    lddfw_driver_id: lddfw_driver_id,
                    lddfw_note: jQuery("#lddfw_driver_delivered_note").val(),
                    lddfw_wpnonce: lddfw_nonce,
                    lddfw_data_type: 'html',
                    lddfw_signature: lddfw_signature,
                    lddfw_delivery_image: lddfw_delivery_image


                },
                success: function(data) {
                    
/* Premium Code Stripped by Freemius */

                },
                error: function(request, status, error) {}
            });

            return false;
        }
    );

    if (jQuery("#lddfw_delivered_form .custom-control.custom-radio").length == 1) {
        jQuery("#lddfw_delivered_form .custom-control.custom-radio").hide();
    }
    if (jQuery("#lddfw_failed_delivery_form .custom-control.custom-radio").length == 1) {
        jQuery("#lddfw_failed_delivery_form .custom-control.custom-radio").hide();
    }

    jQuery("#lddfw_driver_complete_btn").click(
        function() {
            jQuery("#lddfw_delivery_screen").hide();
            if (jQuery(this).attr("delivery") == "success") {
                jQuery("#lddfw_delivered_confirmation").show();
            } else {
                jQuery("#lddfw_failed_delivery_confirmation").show();
            }
            return false;
        }
    );
    jQuery("#lddfw_failed_delivery_confirmation .lddfw_ok").click(
        function() {

            var lddfw_reason = jQuery('input[name=lddfw_delivery_failed_reason]:checked', '#lddfw_failed_delivery_form');
            if (lddfw_reason.attr("id") != "lddfw_delivery_failed_6") {
                jQuery("#lddfw_driver_note").val(lddfw_reason.val());
            }

            jQuery("#lddfw_failed_delivery").hide();
            jQuery("#lddfw_thankyou").show();

            var lddfw_orderid = jQuery("#lddfw_driver_complete_btn").attr("order_id");

            var lddfw_signature = '';
            var lddfw_delivery_image = '';
            
/* Premium Code Stripped by Freemius */


            jQuery.ajax({
                type: "POST",
                url: lddfw_ajax_url,
                data: {
                    action: 'lddfw_ajax',
                    lddfw_service: 'lddfw_status',
                    lddfw_order_id: lddfw_orderid,
                    lddfw_order_status: jQuery("#lddfw_driver_complete_btn").attr("failed_status"),
                    lddfw_driver_id: lddfw_driver_id,
                    lddfw_note: jQuery("#lddfw_driver_note").val(),
                    lddfw_wpnonce: lddfw_nonce,
                    lddfw_data_type: 'html',
                    lddfw_signature: lddfw_signature,
                    lddfw_delivery_image: lddfw_delivery_image
                },
                success: function(data) {
                    
/* Premium Code Stripped by Freemius */

                },
                error: function(request, status, error) {}
            });

            return false;
        }
    );

    jQuery("#lddfw_delivered_form input[type=radio]").click(
        function() {
            jQuery("#lddfw_driver_delivered_note").val("");
            if (jQuery(this).attr("id") == "lddfw_delivery_dropoff_other") {
                jQuery("#lddfw_driver_delivered_note_wrap").show();
            } else {
                jQuery("#lddfw_driver_delivered_note_wrap").hide();
            }
        }
    );

    jQuery("#lddfw_failed_delivery_form input[type=radio]").click(
        function() {
            jQuery("#lddfw_driver_note").val("");
            if (jQuery(this).attr("id") == "lddfw_delivery_failed_6") {
                jQuery("#lddfw_driver_note_wrap").show();
            } else {
                jQuery("#lddfw_driver_note_wrap").hide();
            }
        }
    );

    jQuery(".lddfw_lightbox_close,#lddfw_driver_cancel_btn").click(
        function() {
            jQuery(".lddfw_page_content").show();
            jQuery(this).parents(".lddfw_lightbox").hide();
            return false;
        }
    );

    jQuery("#lddfw_login_frm").submit(
        function(e) {
            e.preventDefault();

            var lddfw_form = jQuery(this);
            var lddfw_loading_btn = lddfw_form.find(".lddfw_loading_btn")
            var lddfw_submit_btn = lddfw_form.find(".lddfw_submit_btn")
            var lddfw_alert_wrap = lddfw_form.find(".lddfw_alert_wrap");

            var lddfw_nextpage = lddfw_form.attr('nextpage');

            lddfw_submit_btn.hide();
            lddfw_loading_btn.show();
            lddfw_alert_wrap.html("");

            jQuery.ajax({
                type: "POST",
                url: lddfw_ajax_url,
                data: {
                    action: 'lddfw_ajax',
                    lddfw_service: 'lddfw_login',
                    lddfw_login_email: jQuery("#lddfw_login_email").val(),
                    lddfw_login_password: jQuery("#lddfw_login_password").val(),
                    lddfw_wpnonce: lddfw_nonce,
                    lddfw_data_type: 'json'
                },
                success: function(data) {
                    var lddfw_json = JSON.parse(data);
                    if (lddfw_json["result"] == "0") {
                        lddfw_alert_wrap.html("<div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">" + lddfw_json["error"] + "</div>");
                        lddfw_submit_btn.show();
                        lddfw_loading_btn.hide();
                    }
                    if (lddfw_json["result"] == "1") {
                        window.location.replace(lddfw_nextpage);
                    }
                },
                error: function(request, status, error) {
                    lddfw_alert_wrap.html("<div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">" + status + ' ' + error + "</div>");
                    lddfw_submit_btn.show();
                    lddfw_loading_btn.hide();
                }
            });
            return false;
        }
    );

    jQuery("#lddfw_back_to_forgot_password_link").click(
        function() {
            jQuery(".lddfw_page").hide();
            jQuery("#lddfw_forgot_password").show();
        }
    );
    jQuery("#lddfw_login_button").click(
        function() {
            jQuery(".lddfw_page").hide();
            jQuery("#lddfw_login").show();
        }
    );
    jQuery("#lddfw_new_password_login_link").click(
        function() {
            jQuery(".lddfw_page").hide();
            jQuery("#lddfw_login").show();
        }
    );
    jQuery("#lddfw_new_password_reset_link").click(
        function() {
            jQuery("#lddfw_create_new_password").hide();
            jQuery("#lddfw_forgot_password").show();
        }
    );
    jQuery("#lddfw_forgot_password_link").click(
        function() {
            jQuery("#lddfw_login").hide();
            jQuery("#lddfw_forgot_password").show();
        }
    );
    jQuery(".lddfw_back_to_login_link").click(
        function() {
            jQuery(".lddfw_page").hide();
            jQuery("#lddfw_login").show();

        }
    );
    jQuery("#lddfw_resend_button").click(
        function() {
            jQuery(".lddfw_page").hide();
            jQuery("#lddfw_forgot_password").show();
        }
    );
    jQuery("#lddfw_application_link").click(
        function() {
            jQuery(".lddfw_page").hide();
            jQuery("#lddfw_application").show();
        }
    );

    jQuery("#lddfw_forgot_password_frm").submit(
        function(e) {
            e.preventDefault();

            var lddfw_form = jQuery(this);
            var lddfw_loading_btn = lddfw_form.find(".lddfw_loading_btn");
            var lddfw_submit_btn = lddfw_form.find(".lddfw_submit_btn");
            var lddfw_alert_wrap = lddfw_form.find(".lddfw_alert_wrap");

            lddfw_submit_btn.hide();
            lddfw_loading_btn.show();
            lddfw_alert_wrap.html("");


            var lddfw_nextpage = lddfw_form.attr('nextpage');
            jQuery.ajax({
                type: "POST",
                url: lddfw_ajax_url,
                data: {
                    action: 'lddfw_ajax',
                    lddfw_service: 'lddfw_forgot_password',
                    lddfw_user_email: jQuery("#lddfw_user_email").val(),
                    lddfw_wpnonce: lddfw_nonce,
                    lddfw_data_type: 'json'

                },
                success: function(data) {
                    var lddfw_json = JSON.parse(data);

                    if (lddfw_json["result"] == "0") {
                        lddfw_alert_wrap.html("<div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">" + lddfw_json["error"] + "</div>");
                        lddfw_submit_btn.show();
                        lddfw_loading_btn.hide();
                    }
                    if (lddfw_json["result"] == "1") {
                        jQuery(".lddfw_page").hide();
                        jQuery("#lddfw_forgot_password_email_sent").show();

                        lddfw_submit_btn.show();
                        lddfw_loading_btn.hide();
                    }
                },
                error: function(request, status, error) {
                    lddfw_submit_btn.show();
                    lddfw_loading_btn.hide();
                }
            });
            return false;
        }
    );

    jQuery("#lddfw_new_password_frm").submit(
        function(e) {
            e.preventDefault();

            var lddfw_form = jQuery(this);
            var lddfw_loading_btn = lddfw_form.find(".lddfw_loading_btn");
            var lddfw_submit_btn = lddfw_form.find(".lddfw_submit_btn");
            var lddfw_alert_wrap = lddfw_form.find(".lddfw_alert_wrap");

            lddfw_submit_btn.hide();
            lddfw_loading_btn.show();
            lddfw_alert_wrap.html("");


            var lddfw_nextpage = lddfw_form.attr('nextpage');
            jQuery.ajax({
                type: "POST",
                url: lddfw_ajax_url,
                data: {
                    action: 'lddfw_ajax',
                    lddfw_service: 'lddfw_newpassword',
                    lddfw_new_password: jQuery("#lddfw_new_password").val(),
                    lddfw_confirm_password: jQuery("#lddfw_confirm_password").val(),
                    lddfw_reset_key: jQuery("#lddfw_reset_key").val(),
                    lddfw_reset_login: jQuery("#lddfw_reset_login").val(),
                    lddfw_wpnonce: lddfw_nonce,
                    lddfw_data_type: 'json'
                },

                success: function(data) {
                    var lddfw_json = JSON.parse(data);
                    if (lddfw_json["result"] == "0") {
                        lddfw_alert_wrap.html("<div class=\"alert alert-warning alert-dismissible fade show\" role=\"alert\">" + lddfw_json["error"] + "</div>");
                        lddfw_submit_btn.show();
                        lddfw_loading_btn.hide();
                    }
                    if (lddfw_json["result"] == "1") {
                        jQuery(".lddfw_page").hide();
                        jQuery("#lddfw_new_password_created").show();

                    }
                },
                error: function(request, status, error) {
                    lddfw_submit_btn.show();
                    lddfw_loading_btn.hide();
                }
            });
            return false;
        }
    );

    jQuery("body").on("click", "#lddfw_orders_table .lddfw_box a", function() {
        jQuery(this).closest(".lddfw_box").addClass("lddfw_active");
    });
    
/* Premium Code Stripped by Freemius */


})(jQuery);

function lddfw_openNav() {
    jQuery(".lddfw_page_content").hide();
    document.getElementById("lddfw_mySidenav").style.width = "100%";
}

function lddfw_closeNav() {
    jQuery(".lddfw_page_content").show();
    document.getElementById("lddfw_mySidenav").style.width = "0";
}


/* Premium Code Stripped by Freemius */

jQuery("#lddfw_driver_add_signature_btn").click(function() {

    jQuery(".signature-wrapper").show();
    
/* Premium Code Stripped by Freemius */

});

jQuery(".delivery_proof_bar a").click(function() {

    var $lddfw_this = jQuery(this);
    var $lddfw_screen_class = $lddfw_this.attr("href")
    $lddfw_this.parents(".delivery_proof_bar").find("a").removeClass("active");
    $lddfw_this.addClass("active");
    $lddfw_this.parents(".lddfw_lightbox").find(".screen_wrap").hide();
    $lddfw_this.parents(".lddfw_lightbox").find("." + $lddfw_screen_class).show();

    
/* Premium Code Stripped by Freemius */

    return false;
});

//switch lazyload src
jQuery("img.lazyload").each(function() {
    var $lddfw_src = $(this).attr("data-src");
    $(this).attr("src", $lddfw_src);
});