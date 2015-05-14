<h3>Update Profile</h3>

<p>
Edit your profile
</p>
<?php echo $this->session->flashdata('message'); ?>
<?php echo validation_errors(); ?>

<?php echo form_open('users/updateprofile'); ?>
<table>
    <thead>
        <tr>
            <th colspan="2">Account Details</th>
        </tr>
    </thead>
    
    <tbody>
        <tr>
            <td>Email Address</td>
            <td><?php echo form_input('email', set_value('email', $email)); ?></td>
        </tr>
        <tr>
            <td>First Name</td>
            <td><?php echo form_input('first_name', set_value('first_name', $first_name)); ?></td>
        </tr>
        <tr>
            <td>Last Name</td>
            <td><?php echo form_input('last_name', set_value('last_name', $last_name)); ?></td>
        </tr>
        

    </tbody>

    <tfoot>
        <tr>
            <td colspan="2"><?php echo form_submit('submit', 'Update'); ?></td>
        </tr>
    </tfoot>
</table>

<?php echo form_close(''); ?>