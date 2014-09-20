Lista wydarzeń

<table>
    <tbody>

        <?php foreach ($events as $event): ?>
        <tr>
            <td><?php echo $event->name ?></td>
        </tr>
        <?php endforeach; ?>

    </tbody>
</table>

<a href="/wp-admin/admin.php?page=resform_add">Dodaj wydarzenie</a>
