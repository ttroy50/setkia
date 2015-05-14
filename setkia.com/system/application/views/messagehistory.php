<h3>Message History</h3>

<table class="resultList">
    <thead>
        <tr>
            <th>Message ID</th>
            <th>Time Sent</th>
            <th>Status</th>
            <th>Error</th>
            <th>Message Type</th>
        </tr>
    </thead>

    <tbody>
        <?php
        if(!$messagehistory)
        {
        ?>
        <tr>
            <td colspan="5"><p class="notice">No Messages to display</p></td>
        </tr>
        <?php
        }
        else{
            foreach($messagehistory as $row)
            {
        ?>

        <tr>

            <td><?php echo anchor('users/singlemessage/msgid/'.$row['cliMsgId'], $row['cliMsgId']); ?></td>

            <td><?php echo date("Y-m-d H:i:s", $row['timeSent']); ?></td>
            <td><?php echo element($row['status'], $messageStatusCodes, 'Unknown'); ?></td>
            <td> <?php echo $row['errorStr']; ?> </td>
            <td> <?php echo element($row['msgType'], $msgTypes, 'Unknown');?> </td>
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