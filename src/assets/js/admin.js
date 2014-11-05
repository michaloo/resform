(function($) {

    function init() {

        $("#select_view [name=view]").change(function() {
            $("#select_view").submit();
        });

        $(".draggable").draggable({
            revert: "invalid", snap: "true", snapMode: "inner",
            connectToSortable: ".droppable"
            });
        $(".droppable").sortable({
            drop: function( event, ui ) {
                ui.helper.css('top','');
                ui.helper.css('left','');
                $(this).append(ui.helper);
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
