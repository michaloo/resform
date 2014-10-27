(function($) {

    function dataSets() {
        $('[data-set-id]').on("click", function(ev) {

            var data_set = $('#' + $(ev.currentTarget).attr("data-set-id"));

            if ($(ev.currentTarget).is(":checked")) {
                data_set.addClass("open");
                data_set.attr("disabled", false);
            } else {
                data_set.removeClass("open");
                data_set.attr("disabled", true);
            }
        });

        $('[data-set-id]').each(function(index, el) {
            var data_set = $('#' + $(el).attr("data-set-id"));

            if ($(el).is(":checked")) {
                data_set.addClass("open");
                data_set.attr("disabled", false);
            } else {
                data_set.removeClass("open");
                data_set.attr("disabled", true);
            }
        });
    }

    function family() {
        var $fieldset = $("#family_fieldset"),
            $template = $("#family_template").html().trim(),
            $button   = $("#family_button");

        $fieldset.on("click", ".family_remove", function(ev) {
            console.log(ev);
            $(ev.currentTarget).parent().remove();
        });

        $button.on("click", function(ev) {
            ev.preventDefault();
            var new_element = $($.parseHTML($template).pop()).attr("disabled", false);
            $fieldset.append(new_element);
        });
    }

    function populate() {
        var data = window.resform_family_data,
            first_names = data.first_names || [],
            last_names  = data.last_names || [],
            birth_dates = data.birth_dates || [],

            first_name_errors = data.first_name_errors || [],
            last_name_errors  = data.last_name_errors || [],
            birth_date_errors = data.birth_date_errors || [],

            count  = Math.max(first_names.length, last_names.length, birth_dates.length);

        var $fieldset = $("#family_fieldset"),
            $template = $("#family_template").html().trim()

        for (var i = 0; i < count; i ++) {
            var $new_element = $($.parseHTML($template).pop()).attr("disabled", false);
            $fieldset.append($new_element);

            var $first_name = $new_element.find('[data-name="first_name"]'),
                $last_name  = $new_element.find('[data-name="last_name"]'),
                $birth_date = $new_element.find('[data-name="birth_date"]');

            $first_name.find("input").val(first_names[i]);
            $last_name.find("input").val(last_names[i]);
            $birth_date.find("input").val(birth_dates[i]);

            if (first_name_errors[i]) {
                $first_name.addClass("error");
                $first_name.append("<span>" + first_name_errors[i] + "</span>");
            }

            if (last_name_errors[i]) {
                $last_name.addClass("error");
                $last_name.prepend("<span>" + last_name_errors[i] + "</span>");
            }

            if (birth_date_errors[i]) {
                $birth_date.addClass("error");
                $birth_date.prepend("<span>" + birth_date_errors[i] + "</span>");
            }
        }

    }

    function ready() {

        dataSets();
        family();
        populate();
    }

    $(ready);

})(jQuery);
