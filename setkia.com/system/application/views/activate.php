<h3>Account Activation.</h3>



<p>Please enter your username and the activation code from the registration SMS.</p>



<?php echo $this->session->flashdata('message'); ?>

<?php echo validation_errors(); ?>



<?php echo form_open('users/activate'); ?>



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

            <td>Verification Code</td>

            <td><?php echo form_input('code', set_value('code')); ?></td>

        </tr>


    </tbody>

    <tfoot>

        <tr>

            <td colspan="2"><?php echo form_submit('submit', 'Activate'); ?></td>

        </tr>

    </tfoot>

</table>



<?php echo form_close(''); ?>