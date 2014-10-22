(function($) {

    function dataSets() {
        $('[data-set-id]').on("click", function(ev, el) {

            var data_set = $('#' + $(ev.currentTarget).attr("data-set-id"));

            if ($(ev.currentTarget).is(":checked")) {
                data_set.addClass("open");
            } else {
                data_set.removeClass("open");
            }
        });
    }

    function family() {
        var $fieldset = $("#family_fieldset"),
            $template = $("#family_template").html(),
            $button   = $("#family_button");

        $fieldset.on("click", ".family_remove", function(ev) {
            console.log(ev);
            $(ev.currentTarget).parent().remove();
        });

        $button.on("click", function(ev) {
            ev.preventDefault();
            $fieldset.append($template);
        });
    }

    function ready() {

        dataSets();
        family();
    }

    $(ready);

})(jQuery);
