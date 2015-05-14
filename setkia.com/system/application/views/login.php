<h3>Login</h3>

<?php echo validation_errors(); ?>
<?php echo $this->session->flashdata('message'); ?>

<?php echo form_open('users/login'); ?>

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

            <td>Password</td>

            <td><?php echo form_password('password'); ?></td>

        </tr>

    </tbody>

    <tfoot>

        <tr>

            <td colspan="2"><?php echo form_submit('submit', 'Login'); ?></td>

        </tr>

    </tfoot>

</table>



<?php echo form_close(''); ?>
