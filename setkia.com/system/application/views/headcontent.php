
    <?php if(!$loggedIn) { ?>
        <div id="headcontent">
        <?php echo form_open('users/login'); ?>
            <label for="username">Username :</label> <?php echo form_input('username', set_value('username')); ?>
            <br />
            <label for="password">Password : </label><?php echo form_password('password'); ?>
            <br />
            <?php $atts = array("class" => "loginbth"); echo form_submit('submit', 'Login', 'class="loginbtn"'); ?>
        <?php echo form_close(''); ?>
        </div>
    <?php }
    else if($loggedIn)
    { ?>
        <div id="userheadcontent">
        Welcome <?php echo $profile[0]['first_name']; ?>,
        <br />
        You have <?php echo $profile[0]['sms_available']; ?> Credits
        </div>
    <?php } ?>
