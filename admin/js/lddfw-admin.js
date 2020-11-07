jQuery(document).ready(
    function($) {

        function lddfw_dates_range() {
            var $lddfw_this = $("#lddfw_dates_range");
            if ($lddfw_this.val() == "custom") {
                $("#lddfw_dates_custom_range").show();
            } else {
                var lddfw_fromdate = $('option:selected', $lddfw_this).attr('fromdate');
                var lddfw_todate = $('option:selected', $lddfw_this).attr('todate');
                $("#lddfw_dates_custom_range").hide();
                $("#lddfw_dates_range_from").val(lddfw_fromdate);
                $("#lddfw_dates_range_to").val(lddfw_todate);
            }
        }

        $("#lddfw_dates_range").change(function() {
            lddfw_dates_range()
        });

        if ($("#lddfw_dates_range").length) {
            lddfw_dates_range();
        }


        
/* Premium Code Stripped by Freemius */


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



        $(".lddfw_copy_template_to_textarea").click(
            function() {
                var textarea_id = $(this).parent().parent().find("textarea").attr("id");

                var text = $(this).attr("data");
                $("#" + textarea_id).val(text);

                return false;
            }
        );

        $(".lddfw_copy_tags_to_textarea a").click(
            function() {
                var textarea_id = $(this).parent().attr("data-textarea");
                var text = $("#" + textarea_id).val() + $(this).attr("data");
                $("#" + textarea_id).val(text);

                return false;
            }
        );

        
/* Premium Code Stripped by Freemius */


    }
);


/* Premium Code Stripped by Freemius */
