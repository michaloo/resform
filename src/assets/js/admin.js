(function($) {

    function init() {

        $("[data-get-family]").each(function() {

            var $list     = $(this),
                url       = $list.attr("data-url"),
                $template = $list.find("[data-tmpl]"),
                $content  = $(this).find("[data-content]");

            $.getJSON(url, null, function(data) {
                $.each(data, function() {
                    var $new_item = $template.clone();

                    var parsed = $new_item.html()
                        .replace("{{ person_id }}", this.person_id)
                        .replace("{{ first_name }}", this.first_name)
                        .replace("{{ last_name }}", this.last_name);

                    $new_item.html(parsed);

                    $new_item.appendTo($content).removeAttr("data-tmpl");
                });
            });
        });

        $("[data-search-person]").each(function() {

            var $el     = $(this),
                $select = $el.find('[data-search-select]'),
                $link   = $el.find('[data-search-link]'),
                url     = $select.attr("data-url-search");

            $select.select2({
                minimumInputLength: 2,
                allowClear: true,
                initSelection: function(element, callback) {
                    return $.getJSON(element.attr('data-url-get') + '&person_id=' + element.val(), null, function(data) {
                        return callback({
                            id: data.person_id,
                            text: [data.first_name, data.last_name].join(" ")
                        });
                    });
                },
                ajax: {
                    url: url,
                    data: function(term, page) {
                        return {
                            search: term
                        };
                    },
                    results: function(data, page, query) {
                        var results = data.map(function(d) {
                            return {
                                id: d.person_id,
                                text: [d.first_name, d.last_name].join(" ")
                            };
                        });
                        return {
                            results: results
                        };
                    }
                }
            });

            $select.on("change", function(select) {
                var url_template = $link.attr("data-url");
                if (select.val) {
                    $link.attr("href", url_template + select.val);
                    $link.removeAttr("data-disabled");
                } else {
                    $link.attr("href", "#");
                    $link.attr("data-disabled", "");
                }
            });
        });

        $.fn.editable.defaults.mode = 'inline';
        $('[data-editable]').editable();
        // $('[data-colorpicker]').colorpicker({
        //     inline: false,
        //     modal: true
        // });

        $('[data-tooltip]').tooltipsy();


        $("#select_view [name=view]").change(function() {
            $("#select_view").submit();
        });

        $("[data-draggable]").draggable({
            revert: "invalid",
            snap: "true",
            snapMode: "inner",
            connectToSortable: "[data-droppable]",
            handle: "[data-handle]",
            cursorAt: { left: 5 }
        });

        $("[data-droppable]").sortable({
            handle: ".non-existing-handle",
            stop: function( event, ui ) {
            //     ui.helper.css('top','');
            //     ui.helper.css('left','');
            //     $(this).append(ui.helper);
                var room_id = $(this).parent().attr("data-room_id");
                ui.item.find("input").val(room_id);
            },
            over: function( event, ui ) {
                $(this).addClass("over");
            },
            out: function( event, ui ) {
                $(this).removeClass("over");
            }
        });
    }

    $(init);

})(jQuery);
