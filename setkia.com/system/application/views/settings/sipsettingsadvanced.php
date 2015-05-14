<h3>Advanced SIP Settings</h3>

<?php echo validation_errors(); ?>
<?php echo $this->session->flashdata('message'); ?>

<?php echo form_open('settings/sipadvanced'); ?>

<table>
    <thead>
        <tr>
            <th colspan="2">Profile</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>Profile Name</td>
            <td><?php echo form_input('name', set_value('name')); ?></td>
        </tr>
        <tr>
            <td>App Reference</td>
            <td><?php echo form_input('appref', set_value('appref')); ?></td>
        </tr>
        <tr>
            <td>Service Provider</td>
            <td>
            <?php

                    echo form_label("IETF", "ptype"); echo form_radio('ptype', '1', false, set_radio('ptype', '1', TRUE));
            ?>
            </td>
        </tr>
        <tr>
            <td>Public Username</td>
            <td><?php echo form_input('puid', set_value('puid')); ?></td>
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
   </tbody>
    <tfoot>
        <tr>
            <td colspan="2"><?php echo anchor('help/advancedsip?height=350&width=600', '?', array('title' => 'Advanced SIP Help', 'class' => 'thickbox')); ?></td>
        </tr>
    </tfoot>
</table>

<table>
    <thead>
        <tr>
            <th colspan="2">Proxy Server</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>Proxy Server Address</td>
            <td><?php echo form_input('appaddr_addr', set_value('appaddr_addr')); ?></td>
        </tr>
        <tr>
            <td>Realm</td>
            <td><?php echo form_input('appauth_aauthdata', set_value('appauth_aauthdata')); ?></td>
        </tr>
        <tr>
            <td>Username</td>
            <td><?php echo form_input('appauth_aauthname', set_value('appauth_aauthname')); ?></td>
        </tr>
        <tr>
            <td>Password</td>
            <td><?php echo form_password('appauth_aauthsecret'); ?></td>
        </tr>
        <tr>
            <td>Transport Type</td>
            <td>
            <?php

                    echo form_label("UDP", "protocol"); echo form_radio('protocol', 'UDP', false, set_radio('protocol', 'UDP', TRUE));
                    echo form_label("TCP", "protocol"); echo form_radio('protocol', 'TCP', false, set_radio('protocol', 'TCP'));
            ?>
            </td>
        </tr>
        <tr>
            <td>Allow Loose Routing</td>
            <td>
            <?php

                    echo form_label("Yes", "appaddr_lr"); echo form_radio('appaddr_lr', '1', false, set_radio('appaddr_lr', '1', TRUE));
            ?>
            </td>
        </tr>
        <tr>
            <td>Port</td>
            <td><?php echo form_input('appaddr_port_portnbr', set_value('appaddr_port_portnbr')); ?></td>
        </tr>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="2"><?php echo anchor('help/advancedsip?height=350&width=600', '?', array('title' => 'Advanced SIP Help', 'class' => 'thickbox')); ?></td>
        </tr>
    </tfoot>
</table>

<table>
    <thead>
        <tr>
            <th colspan="2">Registrar Server</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>Registrar Server Address</td>
            <td><?php echo form_input('resource_uri', set_value('resource_uri')); ?></td>
        </tr>
        <tr>
            <td>Realm</td>
            <td><?php echo form_input('resource_aauthdata', set_value('resource_aauthdata')); ?></td>
        </tr>
        <tr>
            <td>Username</td>
            <td><?php echo form_input('resource_aauthname', set_value('resource_aauthname')); ?></td>
        </tr>
        <tr>
            <td>Password</td>
            <td><?php echo form_password('resource_aauthsecret'); ?></td>
        </tr>
        <tr>
            <td>Transport Type</td>
            <td>
            <?php

                    echo form_label("UDP", "resource_protocol"); echo form_radio('resource_protocol', 'UDP', false, set_radio('resource_protocol', 'UDP', TRUE));
                    echo form_label("TCP", "resource_protocol"); echo form_radio('resource_protocol', 'TCP', false, set_radio('resource_protocol', 'TCP'));
            ?>
            </td>
        </tr>
        <tr>
            <td>Port</td>
            <td><?php echo form_input('resource_port_portnbr', set_value('resource_port_portnbr')); ?></td>
        </tr>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="2"><?php echo form_submit('submit', 'submit'); ?></td>
        </tr>
    </tfoot>
</table>



<?php echo form_close(''); ?>