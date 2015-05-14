<h1>STUN Settings</h1>

<?php echo validation_errors(); ?>
<?php echo $this->session->flashdata('message'); ?>

<?php echo form_open('settings/stun'); ?>

<table>
    <thead>
        <tr>
            <th colspan="2">Required Fields</th>
        </tr>
    </thead>

    <tbody>
        <tr>

            <td>Name</td>

            <td><?php echo form_input('name', set_value('name')); ?></td>

        </tr>
        <tr>

            <td>APP Reference</td>

            <td><?php echo form_input('appref', set_value('appref')); ?></td>

        </tr>
        <tr>

            <td>Domain</td>

            <td><?php echo form_input('domain', set_value('domain')); ?></td>

        </tr>
        <tr>

            <td>STUN Server Address</td>

            <td><?php echo form_input('stunsrvaddr', set_value('stunsrvaddr')); ?></td>

        </tr>

        <tr>

            <td>STUN Server Port</td>

            <td><?php echo form_input('stunsrvport', set_value('stunsrvport')); ?></td>

        </tr>

        <tr>

            <td>NAT Refresh TCP</td>

            <td><?php echo form_input('natrefreshtcp', set_value('natrefreshtcp')); ?></td>

        </tr>
        <tr>

            <td>NAT Refresh UDP</td>

            <td><?php echo form_input('natrefreshudp', set_value('natrefreshudp')); ?></td>

        </tr>

    </tbody>

    <tfoot>

        <tr>

            <td colspan="2"><?php echo form_submit('submit', 'submit'); ?></td>

        </tr>

    </tfoot>

</table>



<?php echo form_close(''); ?>