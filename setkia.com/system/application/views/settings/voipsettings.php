<h3>VoIP Settings</h3>
<?php
if(!$sipHistory){
?>
    <p class="notice">You have no SIP settings created with us.
    We recommend that you link a VoIP account to a SIP profile you created with Setkia.
    <br />
    However it is possible to link a VoIP Profile to an external SIP profile</p>
<?php
}
 echo validation_errors(); ?>
<?php echo $this->session->flashdata('message'); ?>

<?php echo form_open('settings/voipsettings'); ?>

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

            <td>Provider ID</td>

            <td><?php echo form_input('providerid', set_value('providerid')); ?></td>

        </tr>
        <tr>
            <td>SIP Settings APP Reference</td>
            <td>
            <?php //echo form_input('to-appref', set_value('to-appref'));
                $options['0'] = 'Other';
                if($sipHistory != false){
                    foreach($sipHistory as $history)
                    {
                        $options[$history['id']] = $history['name'];
                    }
                }
                echo form_dropdown('to-appref', $options, set_value('to-appref', $defaultsipprofile));
            ?>
            </td>
        </tr>
        <tr>
            <td>Other SIP APP Reference</td>
            <td><?php echo form_input('otherto-appref', set_value('otherto-appref')); ?></td>
        </tr>
        <tr>

            <td>Start Media Port</td>

            <td><?php echo form_input('smport', set_value('smport', '41532')); ?></td>

        </tr>
        
        <tr>

            <td>End Media Port</td>

            <td><?php echo form_input('emport', set_value('emport', '65534')); ?></td>

        </tr>

        <tr>

            <td>Media QOS</td>

            <td><?php echo form_input('mediaqos', set_value('mediaqos', '46')); ?></td>

        </tr>
        <tr>
            <td>DTMF Inband</td>
            <td>
            <?php
                    echo form_label("On", "dtmfib"); echo form_radio('dtmfib', '1', false, set_radio('dtmfib', '1', TRUE));
                    echo form_label("Off", "dtmfib"); echo form_radio('dtmfib', '0', false, set_radio('dtmfib', '0'));
            ?>
            </td>
        </tr>
        <tr>
            <td>DTMF Outband</td>
            <td>
            <?php
                    echo form_label("On", "dtmfob"); echo form_radio('dtmfob', '1', false, set_radio('dtmfob', '1', TRUE));
                    echo form_label("Off", "dtmfob"); echo form_radio('dtmfob', '0', false, set_radio('dtmfob', '0'));
            ?>
            </td>
        </tr>
        <tr>
            <td>Allow VoIP over WCDMA</td>
            <td>
            <?php
                    echo form_label("On", "voipoverwcdma"); echo form_radio('voipoverwcdma', '1', false, set_radio('voipoverwcdma', '1', TRUE));
                    echo form_label("Off", "voipoverwcdma"); echo form_radio('voipoverwcdma', '0', false, set_radio('voipoverwcdma', '0'));
            ?>
            </td>
        </tr>
        <tr>
            <td>RTCP Reporting</td>
            <td>
            <?php
                    echo form_label("On", "rtcp"); echo form_radio('rtcp', '1', false, set_radio('rtcp', '1'));
                    echo form_label("Off", "rtcp"); echo form_radio('rtcp', '0', false, set_radio('rtcp', '0', TRUE));
            ?>
            </td>
        </tr>
        <tr>
            <td>User-Agent Header : term type</td>
            <td>
            <?php
                    echo form_label("On", "uahtermtype"); echo form_radio('uahtermtype', '1', false, set_radio('uahtermtype', '1'));
                    echo form_label("Off", "uahtermtype"); echo form_radio('uahtermtype', '0', false, set_radio('uahtermtype', '0', TRUE));
            ?>
            </td>
        </tr>
        <tr>
            <td>User-Agent Header : MAC Address</td>
            <td>
            <?php
                    echo form_label("On", "uahmac"); echo form_radio('uahmac', '1', false, set_radio('uahmac', '1'));
                    echo form_label("Off", "uahmac"); echo form_radio('uahmac', '0', false, set_radio('uahmac', '0', TRUE));
            ?>
            </td>
        </tr>
        <tr>
            <td>User-Agent Header : Free String</td>
            <td><?php echo form_input('uahfree', set_value('uahfree')); ?></td>
        </tr>
        <tr>
            <td>Secure Call Preference</td>
            <td>
            <?php
                    echo form_label("Prefer non-secure", "securecall"); echo form_radio('securecall', '0', false, set_radio('securecall', '0', TRUE));
                    echo form_label("Prefer Secure", "securecall"); echo form_radio('securecall', '1', false, set_radio('securecall', '1'));
                    echo form_label("Secure Only", "securecall"); echo form_radio('securecall', '2', false, set_radio('securecall', '2'));
            ?>
            </td>
        </tr>
        <tr>
            <td>Count of VoIP Digits</td>
            <td><?php echo form_input('voipdigits', set_value('voipdigits', '0')); ?></td>
        </tr>
        <tr>
            <td>Ignoring Domain Part of URI</td>
            <td>
            <?php
                    echo form_label("Off", "igndom"); echo form_radio('igndom', '0', false, set_radio('igndom', '0', TRUE));
                    echo form_label("Numbers only", "igndom"); echo form_radio('igndom', '1', false, set_radio('igndom', '1'));
                    echo form_label("On", "igndom"); echo form_radio('igndom', '2', false, set_radio('igndom', '2'));
            ?>
            </td>
        </tr>
        <tr>
            <td>First Codec</td>
            <td>
            <?php //echo form_input('to-appref', set_value('to-appref'));

                $coptions['3'] = 'G711 A-Law';
                $coptions['4'] = 'G711 U-Law';
                $coptions['1'] = 'iLBC';
                $coptions['10'] = 'G729';
                $coptions['0'] = 'AMR';
                

                echo form_dropdown('codec1', $coptions, set_value('codec1'));
            ?>
            </td>
        </tr>
        <tr>
            <td>Second Codec</td>
            <td>
            <?php //echo form_input('to-appref', set_value('to-appref'));
                $c2options['4'] = 'G711 U-Law';
                $c2options['3'] = 'G711 A-Law';

                $c2options['1'] = 'iLBC';
                $c2options['10'] = 'G729';
                $c2options['0'] = 'AMR';
                $c2options['110'] = 'None';

                echo form_dropdown('codec2', $c2options, set_value('codec2'));
            ?>
            </td>
        </tr>
        <tr>
            <td>Third Codec</td>
            <td>
            <?php //echo form_input('to-appref', set_value('to-appref'));
                $c3options['10'] = 'G729';
                $c3options['3'] = 'G711 A-Law';
                $c3options['4'] = 'G711 U-Law';
                $c3options['1'] = 'iLBC';

                $c3options['0'] = 'AMR';
                $c3options['110'] = 'None';

                echo form_dropdown('codec3', $c3options, set_value('codec3'));
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