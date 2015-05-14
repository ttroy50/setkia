<h3>Provision WBXML</h3>

<p class="test">
    Use this page to provision a WAP Provisioning WBXML file that you created manually. The message will be sent to <?php echo $profile[0]['phonenumber']; ?>
</p>
<p class="notice">
    Sending with a PIN is currently not supported
</p>

<?php echo validation_errors(); ?>
<?php if(isset($error)) echo $error; ?>
<?php echo $this->session->flashdata('message'); ?>

<?php echo form_open_multipart('settings/provisionwbxml'); ?>

<table>
    <thead>
        <tr>
            <th colspan="2">Required Fields</th>
        </tr>
    </thead>

    <tbody>

        <tr>

            <td>WBXML file to Provision</td>

            <td>
            <?php echo form_upload('wbxml'); ?>
            </td>

        </tr>

        <tr>

            <td>OTA PIN Type</td>

            <td><?php
                    echo form_label("None", "pintype"); echo form_radio('pintype', '1', false, set_radio('pintype', '1', TRUE));
                    echo form_label("User PIN", "pintype"); echo form_radio('pintype', '2', false, set_radio('pintype', '2'));
                    echo form_label("Network PIN", "pintype"); echo form_radio('pintype', '3', false, set_radio('pintype', '3'));
                    ?>
            </td>

        </tr>
        <tr>
            <td>PIN</td>
            <td><?php echo form_input('pin', set_value('pin')); ?></td>
        </tr>

    </tbody>

    <tfoot>

        <tr>

            <td colspan="2"><?php echo form_submit('submit', 'submit'); ?></td>

        </tr>

    </tfoot>

</table>



<?php echo form_close(''); ?>


