jQuery(document).ready(function($) {
    $("#local_product").change(function() {

        if ($(this).val() == "no") {
            $(".tkdn").attr("disabled", true);
        } else {
            $(".tkdn").attr("disabled", false);
        }

    }).change();
});