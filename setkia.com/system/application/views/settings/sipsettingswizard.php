<h3>SIP Settings Wizard</h3>

<?php echo validation_errors(); ?>
<?php echo $this->session->flashdata('message'); ?>

<?php echo form_open('settings/sipwizard'); ?>

<table>
    <thead>
        <tr>
            <th colspan="2">Required Fields <?php echo anchor('help/sipwizard?height=350&width=600', '?', array('title' => 'SIP Wizard Help', 'class' => 'thickbox')); ?></th>
        </tr>
    </thead>

    <tbody>
        <tr>

            <td>Name</td>

            <td><?php echo form_input('name', set_value('name')); ?></td>

        </tr>
        <tr>

            <td>App Reference</td>

            <td><?php echo form_input('appref', set_value('appref')); ?></td>

        </tr>
        <tr>

            <td>Username</td>

            <td><?php echo form_input('username', set_value('username')); ?></td>

        </tr>

        <tr>

            <td>Password</td>

            <td><?php echo form_password('password'); ?></td>

        </tr>

        <tr>

            <td>Registration</td>

            <td>
            <?php

                    echo form_label("Always On", "registration"); echo form_radio('registration', '1', false, set_radio('registration', '1', TRUE));
                    echo form_label("When Needed", "registration"); echo form_radio('registration', '0', false, set_radio('registration', '0'));
            ?>
            </td>

        </tr>

        <tr>

            <td>Proxy</td>

            <td><?php echo form_input('proxy', set_value('proxy')); ?></td>

        </tr>
        <tr>

            <td>Domain</td>

            <td><?php echo form_input('domain', set_value('domain')); ?></td>

        </tr>
        <tr>

            <td>Realm</td>

            <td><?php echo form_input('realm', set_value('realm')); ?></td>

        </tr>
        <tr>

            <td>Port</td>

            <td><?php echo form_input('port', set_value('port')); ?></td>

        </tr>
        <tr>

            <td>Protocol</td>

            <td>
            <?php
                    
                    echo form_label("UDP", "protocol"); echo form_radio('protocol', 'UDP', false, set_radio('protocol', 'UDP', TRUE));
                    echo form_label("TCP", "protocol"); echo form_radio('protocol', 'TCP', false, set_radio('protocol', 'TCP'));
            ?>
            </td>

        </tr>

    </tbody>

    <tfoot>

        <tr>

            <td colspan="2"><?php echo form_submit('submit', 'submit'); ?></td>

        </tr>

    </tfoot>

</table>



<?php echo form_close(''); ?>