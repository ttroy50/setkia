<h3>Your Profile</h3>
<p>
Your account status is
</p>

<table>
    <thead>
        <tr>
            <th colspan="2">Account Details</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>Username</td>
            <td><?php echo $profile[0]['username']; ?></td>
        </tr>
        <tr>
            <td>First Name</td>
            <td><?php echo $profile[0]['first_name']; ?></td>
        </tr>
        <tr>
            <td>Last Name</td>
            <td><?php echo $profile[0]['last_name']; ?></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><?php echo $profile[0]['email']; ?></td>
        </tr>
        <tr>
            <td>Phone Number</td>
            <td><?php echo $profile[0]['phonenumber']; ?></td>
        </tr>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="2"><?php echo anchor('users/updateprofile', 'Edit Details'); ?></td>
        </tr>
    </tfoot>
</table>

<table>
    <thead>
        <tr>
            <th colspan="2">Account Settings</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>Mailing List</td>
            <td><?php if($settings[0]['mailinglist'] == 0){ echo 'Not signed up'; }else{ echo 'signed up'; }?></td>
        </tr>
        <tr>
            <td>WBXML Parser</td>
            <td><?php  if($settings[0]['xmlparser'] == 0) echo 'libwbxml'; else echo 'phpwbxml'; ?></td>
        </tr>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="2"><?php echo anchor('users/settings', 'Edit Settings'); ?></td>
        </tr>
    </tfoot>
</table>

<table>
    <thead>
        <tr>
            <th colspan="2">Credits</th>
            
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>Available Credits</td>
            <td><?php echo $profile[0]['sms_available']; ?></td>
        </tr>
        <tr>
            <td>Total Number of credits used</td>
            <td><?php echo $profile[0]['total_num_sms_bought'] - $profile[0]['sms_available'] ; ?></td>
        </tr>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="2"><?php echo anchor('users/buycredits', 'Get More Credits'); ?></td>
        </tr>
    </tfoot>
</table>
