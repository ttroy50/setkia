<h3>Update Settings</h3>

<p>
Edit your settings
</p>
<?php echo $this->session->flashdata('message'); ?>
<?php echo validation_errors(); ?>

<?php echo form_open('users/settings'); ?>
<table>
    <thead>
        <tr>
            <th colspan="2">Settings</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>Join Mailing List</td>
            <td>
            <?php
                    if($mailinglist == 1)
                    {
                        $ml1 = true;
                        $ml0 = false;
                    }
                    else
                    {
                        $ml0 = true;
                        $ml1 = false;
                    }
                    echo form_label("Yes", "mailinglist"); echo form_radio('mailinglist', '1', false, set_radio('mailinglist', '1', $ml1)) ;
                    echo form_label("No", "mailinglist"); echo form_radio('mailinglist', '0', false, set_radio('mailinglist', '0', $ml0));
                                       
                    ?>
            </td>
        </tr>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="2"><?php echo form_submit('submit', 'Update'); ?></td>
        </tr>
    </tfoot>
</table>

<?php echo form_close(''); ?>