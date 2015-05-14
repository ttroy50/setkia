<h3>Settings Saved</h3>

<?php echo $this->session->flashdata('message'); ?>

<p>
    <br />
    <?php if($xml != '') { ?>
    To view the XML click
   <a href="#TB_inline?height=400&width=700&inlineId=hiddenXML" title="XML generated from settings form" class="thickbox">here</a>
   <div id="hiddenXML" style="display:none">
    <br />
    <pre class="xmlnotice">
<?php echo htmlspecialchars($xml); ?>
    </pre>
    </div>
    <?php }?>

</p>
<p>
    To send this XML file go <?php echo anchor('settings/sendsaved/sid/'.$settings_id.'/stype/'.$settings_type.'/', 'here'); ?>
</p>
