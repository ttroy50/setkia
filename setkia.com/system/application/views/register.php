<h3>Register</h3>



<?php echo $this->session->flashdata('message'); ?>



<?php echo validation_errors(); ?>



<?php echo form_open('users/register'); ?>


<table>
    <thead>
        <tr>
            <th colspan="2">Required Fields</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>Username</td>
            <td><?php echo form_input('username', set_value('username')); ?></td>
        </tr>
        <tr>
            <td>Email Address</td>
            <td><?php echo form_input('email', set_value('email')); ?></td>
        </tr>
        <tr>
            <td>Phone Number</td>
            <td><?php echo form_input('phonenumber', set_value('phonenumber')); ?></td>
        </tr>
        <tr>
            <td>Password</td>
            <td><?php echo form_password('password'); ?></td>
        </tr>
        <tr>
            <td>Password Confirmation</td>
            <td><?php echo form_password('passconf'); ?></td>
        </tr>
        <tr>
            <td>Captcha</td>
            <td><?php echo $captchaImage; ?></td>
        </tr>
        <tr>
            <td>Enter Text from picture above</td>
            <td><?php echo form_input('captcha'); ?></td>
        </tr><?php echo form_hidden('userhid'); ?>
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th colspan="2">Optional Fields</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>First Name</td>
            <td><?php echo form_input('first_name', set_value('first_name')); ?></td>
        </tr>
        <tr>
            <td>Last Name</td>
            <td><?php echo form_input('last_name', set_value('last_name')); ?></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"><?php echo form_submit('submit', 'Register'); ?></td>
        </tr>
    </tfoot>
</table>


<?php echo form_close(''); ?>
