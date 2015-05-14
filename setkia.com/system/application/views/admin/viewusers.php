<h3>Users</h3>

<table class="resultList">
    <thead>
        <tr>
            <th>Username</th>
            <th>E-Mail</th>
            <th>Phone Number</th>
            <th>Activated</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php
        if(!$userlist)
        {
        ?>
        <tr>
            <td colspan="5"><p class="notice">No users to display</p></td>
        </tr>
        <?php
        }
        else{
            foreach($userlist as $row)
            {
        ?>

        <tr>

            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['phonenumber']; ?></td>
            <td><?php echo $row['activation_code']; ?></td>
            <td><?php echo anchor('setkiaadmin/addcredit/uid/'.$row['id'], 'Add Credits');?>
                &nbsp;&#8226;&nbsp;
                <?php echo anchor('setkiaadmin/deactivate/uid/'.$row['id'], 'Deactivate'); ?>

            </td>
        </tr>
        <?php
            }

        }
        ?>
    </tbody>

    <tfoot>

        <tr>

            <td colspan="5"><?php echo $this->pagination->create_links(); ?></td>

        </tr>

    </tfoot>

</table>
