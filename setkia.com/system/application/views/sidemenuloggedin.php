		<div id="sideMenuL" class="sideMenu">

            <ul>
				<li>
					<h2>SIP</h2>
					<ul>
						<li><?php echo anchor('settings/sipwizard', 'SIP Settings Wizard'); ?></li>
						<li><?php echo anchor('settings/sipadvanced', 'Advanced SIP Settings'); ?></li>
                        <li><?php echo anchor('settings/siphistory', 'Saved SIP Settings'); ?></li>
					</ul>
				</li>
                <li>
					<h2>VoIP</h2>
					<ul>
                        <li><?php echo anchor('settings/voipsettings', 'New VoIP Settings'); ?></li>
                        <li><?php echo anchor('settings/voiphistory', 'Saved VoIP Settings'); ?></li>
					</ul>
				</li>
                <li>
					<h2>STUN</h2>
					<ul>
                        <li><?php echo anchor('settings/stun', 'New STUN Settings'); ?></li>
                        <li><?php echo anchor('settings/stunhistory', 'Saved STUN Settings'); ?></li>
					</ul>
				</li>
                <li>
					<h2>Other</h2>
					<ul>
						<li><?php echo anchor('settings/provisionxml', 'Provision XML File'); ?></li>
                        <li><?php echo anchor('settings/provisionwbxml', 'Provision WBXML File'); ?></li>
					</ul>
				</li>
				
			</ul>

            <ul>
				<li>
					<h2>Account</h2>
					<ul>
						<li><?php echo anchor('users/status', 'Account Status'); ?></li>
						<li><?php echo anchor('users/messagehistory', 'Message History'); ?></li>
						<li><?php echo anchor('users/settings', 'Account Settings'); ?></li>
					</ul>
				</li>
			</ul>
		</div>

