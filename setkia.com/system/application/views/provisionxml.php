<h3>Provision XML</h3>

<p class="test">
    Use this page to provision a WAP Provisioning XML file that you created manually. The message will be sent to <?php echo $profile[0]['phonenumber']; ?>
    <br />
    Please Note : The doctype MUST be added to the start of the XML, and each tag or end tag should be on a new line.
</p>
<p class="notice">
    Sending with a PIN is currently not supported
</p>

<?php echo validation_errors(); ?>
<?php echo $this->session->flashdata('message'); ?>

<?php echo form_open('settings/provisionxml'); ?>

<table>
    <thead>
        <tr>
            <th colspan="2">Required Fields</th>
        </tr>
    </thead>

    <tbody>

        <tr>

            <td>XML to Provision</td>

            <td><?php
            $data = array(
              'name'        => 'xml',
              'id'          => 'xml',
              'value'       => set_value('xml'),
              'rows'   => '50',
              'cols'        => '60'
            );

            echo form_textarea($data); ?></td>

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

