<h3>Saved STUN Settings</h3>

<table class="resultList">
    <thead>
        <tr>
            <th>Name</th>
            <th>Domain</th>
            <th>Stun Server</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php
        if(!$stunhistory)
        {
        ?>
        <tr>
            <td colspan="4"><p class="notice">No STUN Settings to display</p></td>
        </tr>
        <?php
        }
        else{
            foreach($stunhistory as $row)
            {
        ?>

        <tr>

            <td><?php echo $row['name']; ?></td>

            <td><?php echo $row['domain']; ?></td>
            <td><?php echo $row['stunsrvaddr']; ?></td>
            <td><?php echo anchor('settings/sendsaved/sid/'.$row['id'].'/stype/stun', img($send_icon), array('title' => 'Send STUN Profile'));?>
                &nbsp;&#8226;&nbsp;
                <?php echo anchor('settings/viewsinglesavedxml/sid/'.$row['id'].'/stype/stun?height=350&width=600', img($xml_icon), array('title' => 'XML for '.$row['name'], 'class' => 'thickbox')); ?>
            </td>
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
