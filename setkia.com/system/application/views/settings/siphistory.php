<h3>Saved SIP Settings</h3>

<table class="resultList">
    <thead>
        <tr>
            <th>Name</th>
            <th>App Reference</th>
            <th>Provider-ID</th>
            <th>Public Username</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php
        if(!$siphistory)
        {
        ?>
        <tr>
            <td colspan="5"><p class="notice">No STUN Settings to display</p></td>
        </tr>
        <?php
        }
        else{
            foreach($siphistory as $row)
            {
        ?>

        <tr>

            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['appref']; ?></td>
            <td><?php echo $row['provider-id']; ?></td>
            <td><?php echo $row['puid']; ?></td>
            <td><?php echo anchor('settings/sendsaved/sid/'.$row['id'].'/stype/sip', img($send_icon), array('title' => 'Send SIP Profile'));?>
                &nbsp;&#8226;&nbsp;
                <?php echo anchor('settings/viewsinglesavedxml/sid/'.$row['id'].'/stype/sip?height=500&width=600', img($xml_icon), array('title' => 'XML for '.$row['name'], 'class' => 'thickbox')); ?>
                &nbsp;&#8226;&nbsp;
                <?php echo anchor('settings/voipsettings/sid/'.$row['id'], img($new_voip_icon), array('title' => 'New VoIP Profile'));?>
                &nbsp;&#8226;&nbsp;
                <?php echo anchor('settings/sendwithvoip/sid/'.$row['id'].'/stype/sip?height=200&width=400', img($send_voip_icon), array('title' => 'Send with VoIP Profile', 'class' => 'thickbox')); ?>

            </td>
        </tr>
        <?php
            }

        }
        ?>
    </tbody>

    <tfoot>

        <tr>

            <td colspan="5"><?php echo $this->pagination->create_links(); ?></td>

        </tr>

    </tfoot>

</table>
