<table class="resultList">
    <thead>
        <tr>
            <th colspan="2"><?php echo $costs[0]['country']; ?></th>
        </tr>
    </thead>

    <tbody>
        <?php
        if(!$costs)
        {
        ?>
        <tr>
            <td colspan="4"><p class="notice">Invalid Country</p></td>
        </tr>
        <?php
        }
        else{
            foreach($costs as $row)
            {
        ?>

        <tr>
            <td><?php echo $row['network']; ?></td>
            <td><?php echo $row['cost']; ?></td>
        </tr>
        <?php
            }

        }
        ?>
    </tbody>

    <tfoot>

        <tr>

            <td colspan="2"></td>

        </tr>

    </tfoot>

</table>
