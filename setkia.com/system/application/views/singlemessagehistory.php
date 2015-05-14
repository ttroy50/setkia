<h1>Single Message History</h1>

<?php
if(!$messagehistory)
{
    ?>
    <p class="error">Unknown Message</p>
    <?php
}
else
{
?>
<table>
    <thead>
        <tr>
            <th colspan="2">Sending Details</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>Message ID : </td><td><?php echo $messagehistory[0]['cliMsgId']; ?></td>
        </tr>
        <tr>
            <td>Time Sent : </td><td><?php echo date("Y-m-d H:i:s", $messagehistory[0]['timeSent']); ?></td>
        </tr>
        <tr>
            <td>Message Status : </td><td><?php echo element($messagehistory[0]['status'], $messageStatusCodes, 'Unknown'); ?></td>
        </tr>
        <tr>
            <td>Time of last status update : </td><td><?php if($messagehistory[0]['callbacktimestamp'] == '') echo date("Y-m-d H:i:s", $messagehistory[0]['timeSent']);
            else echo date("Y-m-d H:i:s", $messagehistory[0]['callbacktimestamp']); ?></td>
        </tr>
        <tr>
            <td>Errors : </td><td><?php echo $messagehistory[0]['errorStr']; ?></td>
        </tr>

    </tbody>
    <tfoot>

        <tr>

            <td colspan="2">&nbsp;</td>

        </tr>

    </tfoot>

</table>
<table>
    <thead>
        <tr>
            <th colspan="2">Message Details</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>Message Type : </td><td><?php echo element($messagehistory[0]['msgType'], $msgTypes, 'Unknown'); ?></td>
        </tr>
        <tr>
            <td>UDH :  </td><td><?php echo $messagehistory[0]['udh']; ?></td>
        </tr>
        <tr>
            <td>WSP :  </td><td><?php echo $messagehistory[0]['wsp']; ?></td>
        </tr>

    </tbody>
    <tfoot>

        <tr>

            <td colspan="2">&nbsp;</td>

        </tr>

    </tfoot>

</table>
<?php
}
?>