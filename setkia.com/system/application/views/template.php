<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <meta name="keywords" content="Nokia, Settings, OTA, Provisioning, SIP, VoIP, SMS, wbxml" />
        <meta name="description" content="<?php echo $currentPageTitle; ?>. From SetKia - OTA Provisioning for Nokia Settings" />

        <title><?php echo $currentPageTitle; ?></title>


        <?php echo link_tag('assets/css/reset.css')."\n"; ?>

        <?php echo link_tag('assets/css/screen.css')."\n"; ?>

        <!--[if IE]>

            <?php echo link_tag('assets/css/ie.css')."\n"; ?>

        <![endif]-->

        <?php echo link_tag('assets/css/style.css')."\n"; ?>

        <?php echo link_tag('assets/css/typography.css')."\n"; ?>
        <?php echo link_tag('assets/css/thickbox.css')."\n"; ?>
        <?php echo script_tag('assets/js/jquery-latest.pack.js')."\n"; ?>
        <?php echo script_tag('assets/js/thickbox-compressed.js')."\n"; ?>
        <?php if(isset($useJS)) echo script_tag('assets/js/setkiaJS.js')."\n"; ?>


    </head>
    <body>

        <div id="wrapper">

            <div id="head">
                <div id="logo">
                    <h1><a href="#"><span>SetKia</span> - Settings for Nokia</a></h1>
                </div>
                <?php echo $headContent; ?>
            </div>

            <div id="menu">
            <ul id="main">

                <li><?php echo anchor('', 'Home'); ?></li>

                <?php
                if(!$loggedIn)
                {
                ?>
                <li><?php echo anchor('users/register', 'Register'); ?></li>
                <li><?php echo anchor('users/login', 'Login'); ?></li>

                <?php 
                }
                if ($loggedIn)
                {
                ?>

                <li><?php echo anchor('users/status', 'Account Status'); ?></li>
                
                <li><?php echo anchor('settings/', 'Provisioning'); ?></li>
                <li><?php echo anchor('users/logout', 'Logout'); ?></li>
                <?php
                

                    if(isset($profile))
                    {
                        if(strcmp($profile[0]['group'], 'administrators') == 0)
                        {
                        ?>
                        <li><?php echo anchor('setkiaadmin', 'Admin'); ?></li>
                        <?php
                        }
                    }
                }

                ?>

            </ul>
            </div>

            <div id="contentwrapper">
                
                <div id="content">
                    <?php echo $content."\n" ?>

                </div>
                <?php if(isset($sideMenu)) { echo $sideMenu."\n"; } ?>
                
            </div>
            <div style="clear: both;">&nbsp;</div>
            <div id="foot">
                <p class="copyright">&copy;&nbsp;&nbsp;2009 All Rights Reserved
                <br /><?php echo anchor('about/privacypolicy', 'Privacy Policy');?>
                &nbsp;&#8226;&nbsp;<?php echo anchor('about/terms', 'Terms of Use');?>
                &nbsp;&#8226;&nbsp;<?php echo anchor('about/contact', 'Contact');?>
                &nbsp;&#8226;&nbsp;<?php echo anchor('about/faq', 'FAQ');?>
                </p>
            </div>

        </div>

        <?php
        if(base_url() == "http://setkia.com/")
        {
        ?>
        <script type="text/javascript">
            var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
            document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
        </script>
        <script type="text/javascript">
            try {
                var pageTracker = _gat._getTracker("UA-8920484-1");
                pageTracker._trackPageview();
            } catch(err) {}
        </script>
        <?php
        }
        ?>
    </body>
</html>