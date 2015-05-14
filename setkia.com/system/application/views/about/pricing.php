<h3>Pricing</h3>

<p>
To see the cost of sending to a particular country select below
<br />

<?php
    echo form_dropdown('countries', $countries, null, 'id="countries"');
?>
</p>


<div id="countrycost">
</div>

<table class="resultList">
    <thead>
        <tr>
            <th>Country</th>
            <th>Code</th>
            <th>Network</th>
            <th>Cost (Credits)</th>
        </tr>
    </thead>

    <tbody>
        <?php
        if(!$costs)
        {
        ?>
        <tr>
            <td colspan="4"><p class="notice">No Pricing to display</p></td>
        </tr>
        <?php
        }
        else{
            foreach($costs as $row)
            {
        ?>

        <tr>

            <td><?php echo $row['country']; ?></td>

            <td><?php echo $row['ccode']; ?></td>
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

            <td colspan="4"><?php echo $this->pagination->create_links(); ?></td>

        </tr>

    </tfoot>

</table>
