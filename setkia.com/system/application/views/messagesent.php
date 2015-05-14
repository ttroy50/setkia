<h3>Message Sent</h3>

<?php echo $this->session->flashdata('message'); ?>

<p>
    Your message has been successfully sent. To view the status of this message go
    <?php echo anchor('users/singlemessage/msgid/'.$this->session->flashdata('cliMsgId'), 'here'); ?>
    <br />
    <?php if($this->session->flashdata('xml') != '') { ?>
    The XML was
    <br />
    <pre class="xmlnotice">
<?php echo htmlspecialchars($this->session->flashdata('xml')); ?>
    </pre>
    <?php }?>

</p>


