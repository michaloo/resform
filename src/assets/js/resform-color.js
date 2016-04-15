(function($) {

    $(init);


    function init() {
        var tagName = 'resform-color';
        var options = [
            'none',
            '#c9abe2',
            '#b9eddb',
            '#f1f3b5',
            '#efbebe',
            '#ff8787'
        ];
        var template = $('#resform-color').html();

        $(tagName).each(handleNode.bind(this, options, template))
    }

    function handleNode(options, template, index, element) {
        var $this = $(element);
        var value = $this.html();
        var data = {
            name: 'test',
            options: options,
            value: value
        };

        var output = swig.render(template, { locals: data });

        $this.html(output);

        var select = $('select', $this)
            .iconselectmenu({
                value: value,
                appendTo: $this,
                select: selectHandler.bind($this),
                change: (function( event, ui ) {

                    if (!window.confirm("Czy chcesz zmienić kolor tej osoby?")) {
                        return;
                    }

                    var url  = this.attr('data-url');
                    var data = {
                        name: this.attr('data-name'),
                        value: ui.item.value,
                        pk: this.attr('data-pk'),
                    };
                    var params = JSON.parse(this.attr('data-params'));

                    var toSend = $.extend(data, params);

                    $.post(url, toSend, function(color) {

                        var $person = $(this)
                            .parents('[data-person]');

                        if (color == 'none') {
                            color = '';
                        }

                        $person.css('background-color', color);

                    }.bind(this, data.value));


                }).bind($this)
            });

            selectHandler.call($this);
    }

    function selectHandler($this) {
        var $label = this.find('.ui-selectmenu-text');
        var color = $label.text();

        $label.html('');
        $( "<span>", {
            'data-color': '',
            style: "background-color: " + color,
        }).appendTo($label);

    }

    $.widget( "custom.iconselectmenu", $.ui.selectmenu, {
      _renderItem: function( ul, item ) {
        var li = $( "<li>"); //, { text: item.label } );

        if ( item.disabled ) {
          li.addClass( "ui-state-disabled" );
        }

        $( "<span>", {
            'data-color': '',
            style: "background-color: " + item.element.val(),
        })
          .appendTo( li );

        return li.appendTo( ul );
      }
    });

})(jQuery);
