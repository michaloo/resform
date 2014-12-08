(function($) {

    function init() {
        $.fn.editable.defaults.params = { action: "resform_person_inline_update"};
        $.fn.editable.defaults.mode = 'inline';
        $('[data-editable]').editable();

        $('[data-colorpicker]').colorpicker({
            inline: false,
            modal: true
        });

        $("#select_view [name=view]").change(function() {
            $("#select_view").submit();
        });

        $(".draggable").draggable({
            revert: "invalid", snap: "true", snapMode: "inner",
            connectToSortable: ".droppable"
            });

        $(".droppable").sortable({
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
