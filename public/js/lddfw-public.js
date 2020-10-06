(function() {
    "use strict";

    
/* Premium Code Stripped by Freemius */


    jQuery("#lddfw_out_for_delivery_button").click(
        function() {
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
                    lddfw_wpnonce: lddfw_nonce
                }
            }).done(
                function(data) {
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
                        jQuery("#lddfw_alert").html("<div class=\"alert alert-success alert-dismissible fade show\" role=\"alert\">" + lddfw_json["error"] + "</div>");
                        if (jQuery('.lddfw_multi_checkbox').length == 0) {
                            jQuery(".lddfw_footer_buttons").hide();
                        }
                    }
                }
            );
            return false;
        }
    );



    jQuery(".lddfw_multi_checkbox").click(
        function() {
            var lddfw_chk = jQuery(this).find(".custom-control-input");
            console.log(lddfw_chk.prop("checked"))
            if (lddfw_chk.prop("checked") == true) {
                jQuery(this).removeClass("lddfw_active");
                lddfw_chk.prop("checked", false);
            } else {
                jQuery(this).addClass("lddfw_active");
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
                jQuery(this).html("<i class='fas fa-toggle-off'></i>");
                jQuery("#lddfw_availability_status").html(jQuery("#lddfw_availability_status").attr("unavailable"));
                jQuery("#lddfw_menu .lddfw_availability").removeClass("text-success");
                jQuery("#lddfw_menu .lddfw_availability").addClass("text-danger");
                jQuery.post(
                    lddfw_ajax_url, {
                        action: 'lddfw_ajax',
                        lddfw_service: 'lddfw_availability',
                        lddfw_availability: "0",
                        lddfw_driver_id: lddfw_driver_id,
                        lddfw_wpnonce: lddfw_nonce
                    }
                );

            } else {
                jQuery(this).addClass("lddfw_active");
                jQuery("#lddfw_availability_status").html(jQuery("#lddfw_availability_status").attr("available"));
                jQuery(this).html("<i class='fas fa-toggle-on'></i>");
                jQuery("#lddfw_menu .lddfw_availability").removeClass("text-danger");
                jQuery("#lddfw_menu .lddfw_availability").addClass("text-success");
                jQuery.post(
                    lddfw_ajax_url, {
                        action: 'lddfw_ajax',
                        lddfw_service: 'lddfw_availability',
                        lddfw_availability: "1",
                        lddfw_driver_id: lddfw_driver_id,
                        lddfw_wpnonce: lddfw_nonce
                    }
                );
            }
            return false;
        }
    );



    jQuery("#lddfw_delivered_screen_btn").click(
        function() {
            jQuery(".lddfw_page_content").hide();
            jQuery("#lddfw_delivered").show();
            return false;
        }
    );

    jQuery("#lddfw_failed_delivered_screen_btn").click(
        function() {
            jQuery(".lddfw_page_content").hide();
            jQuery("#lddfw_failed_delivery").show();
            return false;
        }
    );

    jQuery("#lddfw_delivered_btn").click(
        function() {
            jQuery(".lddfw_page_content").hide();
            jQuery("#lddfw_thankyou").show();

            jQuery.ajax({
                type: "POST",
                url: lddfw_ajax_url,
                data: {
                    action: 'lddfw_ajax',
                    lddfw_service: 'lddfw_status',
                    lddfw_order_id: jQuery(this).attr("order_id"),
                    lddfw_order_status: jQuery(this).attr("order_status"),
                    lddfw_driver_id: lddfw_driver_id,
                    lddfw_wpnonce: lddfw_nonce
                },
                success: function(data) {
                    
/* Premium Code Stripped by Freemius */

                },
                error: function(request, status, error) {}

            });
            return false;
        }
    );

    jQuery("#lddfw_failed_delivered_btn").click(
        function() {
            jQuery(".lddfw_page_content").hide();
            jQuery("#lddfw_failed_delivery").show();
            jQuery.post(
                lddfw_ajax_url, {
                    action: 'lddfw_ajax',
                    lddfw_service: 'lddfw_status',
                    lddfw_order_id: jQuery(this).attr("order_id"),
                    lddfw_order_status: jQuery(this).attr("order_status"),
                    lddfw_driver_id: lddfw_driver_id,
                    lddfw_wpnonce: lddfw_nonce
                }
            );
            return false;
        }
    );

    jQuery(".lddfw_confirmation .lddfw_cancel").click(
        function() {
            jQuery(".lddfw_page_content").show();
            jQuery(this).parents(".lddfw_lightbox").hide();
            return false;
        }
    );

    jQuery("#lddfw_driver_delivered_note_btn").click(
        function() {
            jQuery("#lddfw_delivered").hide();
            jQuery("#lddfw_delivered_confirmation").show();
            return false;
        }
    );

    jQuery("#lddfw_delivered_confirmation .lddfw_ok").click(
        function() {

            var lddfw_reason = jQuery('input[name=delivery_dropoff_location]:checked', '#lddfw_delivered_form');
            if (lddfw_reason.attr("id") != "lddfw_delivery_dropoff_other") {
                jQuery("#lddfw_driver_delivered_note").val(lddfw_reason.val());
            }
            jQuery("#lddfw_delivered").hide();
            jQuery("#lddfw_thankyou").show();
            jQuery.ajax({
                type: "POST",
                url: lddfw_ajax_url,
                data: {
                    action: 'lddfw_ajax',
                    lddfw_service: 'lddfw_status',
                    lddfw_order_id: jQuery("#lddfw_driver_delivered_note_btn").attr("order_id"),
                    lddfw_order_status: jQuery("#lddfw_driver_delivered_note_btn").attr("order_status"),
                    lddfw_driver_id: lddfw_driver_id,
                    lddfw_note: jQuery("#lddfw_driver_delivered_note").val(),
                    lddfw_wpnonce: lddfw_nonce
                },
                success: function(data) {
                    
/* Premium Code Stripped by Freemius */

                },
                error: function(request, status, error) {}
            });

            return false;
        }
    );

    jQuery("#lddfw_driver_note_btn").click(
        function() {
            jQuery("#lddfw_failed_delivery").hide();
            jQuery("#lddfw_failed_delivery_confirmation").show();
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

            jQuery.ajax({
                type: "POST",
                url: lddfw_ajax_url,
                data: {
                    action: 'lddfw_ajax',
                    lddfw_service: 'lddfw_status',
                    lddfw_order_id: jQuery("#lddfw_driver_note_btn").attr("order_id"),
                    lddfw_order_status: jQuery("#lddfw_driver_note_btn").attr("order_status"),
                    lddfw_driver_id: lddfw_driver_id,
                    lddfw_note: jQuery("#lddfw_driver_note").val(),
                    lddfw_wpnonce: lddfw_nonce
                },
                success: function(data) {
                    
/* Premium Code Stripped by Freemius */

                },
                error: function(request, status, error) {}
            });

            return false;
        }
    );

    jQuery("#lddfw_failed_delivery input[type=radio]").click(
        function() {
            jQuery("#lddfw_driver_note").val("");
            if (jQuery(this).attr("id") == "lddfw_delivery_failed_6") {
                jQuery("#lddfw_driver_note_wrap").show();
            } else {
                jQuery("#lddfw_driver_note_wrap").hide();
            }
        }
    );

    jQuery(".lddfw_lightbox_close").click(
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
                    lddfw_wpnonce: lddfw_nonce
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

            var lddfw_ajax_url = lddfw_form.attr('action');
            var lddfw_nextpage = lddfw_form.attr('nextpage');
            jQuery.ajax({
                type: "POST",
                url: lddfw_ajax_url,
                data: {
                    lddfw_action: 'lddfw_ajax',
                    lddfw_service: 'lddfw_forgot_password',
                    lddfw_user_email: jQuery("#lddfw_user_email").val(),
                    lddfw_wpnonce: lddfw_nonce

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

            var lddfw_ajax_url = lddfw_form.attr('action');
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
                    lddfw_wpnonce: lddfw_nonce
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
    jQuery("#lddfw_orders_table .lddfw_box a").on(
        "click",
        function() {
            jQuery(this).closest(".lddfw_box").addClass("lddfw_active");
        }
    );



})(jQuery);



function lddfw_openNav() {
    jQuery(".lddfw_page_content").hide();
    document.getElementById("lddfw_mySidenav").style.width = "100%";
}

function lddfw_closeNav() {
    jQuery(".page_content").show();
    document.getElementById("lddfw_mySidenav").style.width = "0";
}

/* <fs_premium_only> */
function lddfw_initMap() {
    var lddfw_directionsService = new google.maps.DirectionsService();
    var lddfw_directionsRenderer = new google.maps.DirectionsRenderer();
    var lddfw_map = new google.maps.Map(
        document.getElementById('lddfw_map123'), {
            zoom: 6,
            center: { lat: 41.85, lng: -87.65 }
        }
    );
    lddfw_directionsRenderer.setMap(lddfw_map);
    lddfw_calculateAndDisplayRoute(lddfw_directionsService, lddfw_directionsRenderer);
}

function lddfw_numtoletter(lddfw_num) {
    var lddfw_s = '',
        lddfw_t;

    while (lddfw_num > 0) {
        lddfw_t = (lddfw_num - 1) % 26;
        lddfw_s = String.fromCharCode(65 + lddfw_t) + lddfw_s;
        lddfw_num = (lddfw_num - lddfw_t) / 26 | 0;
    }
    return lddfw_s || undefined;
}

function lddfw_timeConvert(n) {
    var lddfw_num = n;
    var lddfw_hours = (lddfw_num / 60);
    var lddfw_rhours = Math.floor(lddfw_hours);
    var lddfw_minutes = (lddfw_hours - lddfw_rhours) * 60;
    var lddfw_rminutes = Math.round(lddfw_minutes);
    var lddfw_result = '';
    if (lddfw_rhours > 1) {
        lddfw_result = lddfw_rhours + " " + lddfw_hours_text + " ";
    }
    if (lddfw_rhours == 1) {
        lddfw_result = lddfw_rhours + " " + lddfw_hour_text + " ";
    }
    if (lddfw_rminutes > 0) {
        lddfw_result += lddfw_rminutes + " " + lddfw_mins_text;
    }
    return lddfw_result;
}

function lddfw_computeTotalDistance(result) {
    var lddfw_totalDist = 0;
    var lddfw_totalTime = 0;
    var lddfw_distance_text = '';
    var lddfw_distance_array = '';
    var lddfw_distance_type = '';

    var lddfw_myroute = result.routes[0];
    for (i = 0; i < lddfw_myroute.legs.length; i++) {
        lddfw_totalTime += lddfw_myroute.legs[i].duration.value;
        lddfw_distance_text = lddfw_myroute.legs[i].distance.text;
        lddfw_distance_array = lddfw_distance_text.split(" ");
        lddfw_totalDist += parseFloat(lddfw_distance_array[0]);
        lddfw_distance_type = lddfw_distance_array[1];
    }
    lddfw_totalTime = (lddfw_totalTime / 60).toFixed(0);
    lddfw_TotalTimeText = timeConvert(lddfw_totalTime);
    document.getElementById("total_route").innerHTML = "<b>" + lddfw_TotalTimeText + "</b> <span>(" + (lddfw_totalDist).toFixed(1) + " " + lddfw_distance_type + ")</span> ";
}

function lddfw_calculateAndDisplayRoute(directionsService, directionsRenderer) {
    var lddfw_waypts = [];
    var lddfw_orders_count = jQuery('.lddfw_address_chk').length;
    var lddfw_last_waypoint = 0;
    lddfw_destination_address = jQuery('.lddfw_address_chk').eq(jQuery('.lddfw_address_chk').length - 1).val();
    jQuery('.lddfw_address_chk').each(
        function(index, item) {
            if (jQuery(this).val() != lddfw_destination_address) {
                lddfw_waypts.push({
                    location: jQuery(this).val(),
                    stopover: true
                });
            }
        }
    );
    directionsService.route({
            origin: lddfw_google_api_origin,
            destination: lddfw_destination_address,
            waypoints: lddfw_waypts,
            optimizeWaypoints: lddfw_optimizeWaypoints_flag,
            travelMode: 'DRIVING'
        },
        function(response, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
                var lddfw_route = response.routes[0];
                var lddfw_summaryPanel = document.getElementById('lddfw_directions-panel-listing');
                lddfw_summaryPanel.innerHTML = '<div id="lddfw_total_route"></div>';
                var lddfw_last_address = '';
                // For each route, display summary information.
                for (var i = 0; i < lddfw_route.legs.length; i++) {
                    var lddfw_routeSegment = i + 1;
                    if (lddfw_last_address != lddfw_route.legs[i].start_address) {
                        lddfw_summaryPanel.innerHTML += '<div class="row lddfw_address"><div class="col-2 text-center" > <i class="fas fa-map-marker"></i><span class="lddfw_point">' + numtoletter(routeSegment) + '</span></div><div class="col-10">' + route.legs[i].start_address + '</div></div>';
                    }
                    lddfw_summaryPanel.innerHTML += '<div class="row lddfw_drive"><div class="col-2 text-center"><i class="fas fa-ellipsis-v up"></i><br><i class="fas fa-car"></i><br><i class="fas fa-ellipsis-v down"></i> </div><div class="col-10"  ><b>' + route.legs[i].duration.text + "</b><br>" + route.legs[i].distance.text + '</div></div></div>';
                    lddfw_summaryPanel.innerHTML += '<div class="row lddfw_address"><div class="col-2 text-center"> <i class="fas fa-map-marker"></i><span class="point">' + numtoletter((routeSegment + 1) * 1) + '</span></div><div class="col-10">' + route.legs[i].end_address + '</div></div>';
                    lddfw_last_address = lddfw_route.legs[i].end_address;
                }
                lddfw_computeTotalDistance(response);
            } else {
                window.alert('Directions request failed due to ' + status);
            }
        }
    );
}
/* <fs_premium_only> */