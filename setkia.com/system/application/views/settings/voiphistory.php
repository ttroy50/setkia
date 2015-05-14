<h3>Saved VoIP Settings</h3>

<table class="resultList">
    <thead>
        <tr>
            <th>Name</th>
            <th>Provider-ID</th>
            <th>SIP App Reference</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php
        if(!$voiphistory)
        {
        ?>
        <tr>
            <td colspan="4"><p class="notice">No VoIP Settings to display</p></td>
        </tr>
        <?php
        }
        else{
            foreach($voiphistory as $row)
            {
        ?>

        <tr>

            <td><?php echo $row['name']; ?></td>

            <td><?php echo $row['provider-id']; ?></td>
            <td><?php if($row['sipname']) echo anchor('settings/viewsinglesavedxml/sid/'.$row['sipid'].'/stype/sip?height=500&width=600', $row['sipname'], array('title' => 'XML for '.$row['sipname'], 'class' => 'thickbox')); ?></td>
            <td><?php echo anchor('settings/sendsaved/sid/'.$row['id'].'/stype/voip', img($send_icon), array('title' => 'Send VoIP Profile'));?>
                &nbsp;&#8226;&nbsp;
                <?php echo anchor('settings/viewsinglesavedxml/sid/'.$row['id'].'/stype/voip?height=500&width=600', img($xml_icon), array('title' => 'XML for '.$row['name'], 'class' => 'thickbox')); ?>

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
