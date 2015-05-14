<table class="resultList">
    <thead>
        <tr>
            <th>VoIP Profile</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php

        ?>
        <tr>

            <td>Setkia Default</td>
            <td><?php echo anchor('settings/sendsaved/sid/'.$sid.'/stype/sip/vid/0/votype/addvoip/', 'Send');?></td>
        </tr>
        <?php

        if($settings != false){
            foreach($settings as $row)
            {
        ?>

        <tr>

            <td><?php echo $row['name']; ?></td>


            <td><?php echo anchor('settings/sendsaved/sid/'.$sid.'/stype/sip/vid/'.$row['id'].'/votype/addvoip/', 'Send');?>
                
            </td>
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
