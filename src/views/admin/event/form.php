<div class="wrap">

    <form name="post" action="/wp-admin/admin.php?page=resform_add" method="post">
        <h2>Nowe wydarzenie</h2>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">

                    <input type="text" name="name" placeholder="nazwa" /><br />
                    <input type="text" name="start_time" placeholder="data rozpoczęcia" /><br />
                    <input type="text" name="end_time" placeholder="data zakończenia" /><br />
                    <input type="text" name="reservation_start_time" placeholder="data rozpoczęcia zapisów" /><br />
                    <input type="text" name="reservation_end_time" placeholder="data zakończenia zapisów" /><br />

                    <textarea name="front_info" placeholder="informacja początkowa"></textarea><br />

                    <textarea name="room_type_info" placeholder="informacja o pokojach"></textarea><br />

                    <textarea name="transport_info" placeholder="informacja o rodzajach dojazdu"></textarea><br />

                    <textarea name="regulations" placeholder="regulamin"></textarea><br />

                    <button>Dodaj</button>
                </div>
            </div>
        </div>


    </form>
</div>
