<?php
/**
 * ABDetective - Ad Block Detective
 * Copyright (c) 2011 Heiko Irrgang
 *
 * This file is part of Ad Block Detective.
 * 
 * Ad Block Detective is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * FAd Block Detective is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Ad Block Detective. If not, see <http://www.gnu.org/licenses/>.
 */
if ( !is_admin() ) {
	die( 'denied' );
}

if ( isset( $_POST['abdetective_submit'] ) ) {
	update_option( 'abdetective_mode', $_POST['abdetective_mode'] );
	update_option( 'abdetective_redirect', $_POST['abdetective_redirect'] );
	update_option( 'abdetective_info', $_POST['abdetective_info'] );
	update_option( 'abdetective_sharewarefile', $_POST['abdetective_sharewarefile'] );
	update_option( 'abdetective_blockfile', $_POST['abdetective_blockfile'] );
	echo( sprintf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>',
		__('Saved', 'abdetective' )
	));
}

$abdetective_current_mode = get_option( 'abdetective_mode' );
if ( $abdetective_current_mode === false ) {
	$abdetective_current_mode = 'block';
}

?>
<div class="wrap">
<div id="icon-options-general" class="icon32"><br></div><h2>ABDetective Settings</h2>

<div>
	<div style="float: left; padding-right: 1em;">
		<table>
		<tbody>
		<tr>
		<td>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="NE4M2F7Z7HYSC">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
			</form>
		</td>
		<td>
			<a class="FlattrButton" style="display:none;" href="https://sourceforge.net/projects/abdetective/"></a>
			<noscript><a href="http://flattr.com/thing/343475/ABDetective" target="_blank"><img src="http://api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0" /></a></noscript>
		</td>
		</tr>
		</tbody>
		</table>
	</div>
	<div>
	<p>Please keep in mind, that the development of this plugin will be a ongoing effort which has and will require a lot of time. If you want to see new features, improved detection or just want to show your appreciation, consider a donation.</p>
	<p>Thank you for your support.</p>
	</div>
</div>

<form method="post">
	<input type="hidden" name="abdetective_submit" value="1" />
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="abdetective_redirect">Info Page Name</label></th>
			<td><input name="abdetective_info" type="text" id="abdetective_info" value="<?=get_option( 'abdetective_info' );?>" class="regular-text" /><br />
				<span class="description">
					This is the name of the page where additional info is transported trough - like the shareware dialog or the text for blocked users.<br />
					It shoud be a name that does not sound like something to do with ads or adblockers and it <b>may not exist in your site tree</b>. Something like 'info.html' or 'welcome.html' would be good, but you may also leave the default if it is not conflicting with pages within your site tree.
				</span></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="abdetective_mode">Operating Mode</label></th>
			<td>
				<fieldset>
				<input type="radio" <?php if ( $abdetective_current_mode == 'block' ) { echo( 'checked="checked"' ); } ?> name="abdetective_mode" value="block" />
				<span>Block the user and asks him to disable his ad blocker</span>
				<br />
				<input type="radio" <?php if ( $abdetective_current_mode == 'redirect' ) { echo( 'checked="checked"' ); } ?> name="abdetective_mode" value="redirect" />
				<span>Redirect the user to a specific page (specify below)</span>
				<br />
				<input type="radio" <?php if ( $abdetective_current_mode == 'shareware' ) { echo( 'checked="checked"' ); } ?> name="abdetective_mode" value="shareware" />
				<span>Show a dialog similar to that one shareware software shows on startup</span>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="abdetective_redirect">Block Message File</label></th>
			<td><input name="abdetective_blockfile" type="text" id="abdetective_blockfile" value="<?=get_option( 'abdetective_blockfile', 'default.block.html' );?>" class="regular-text" /><br />
				<span class="description">
					This is the file displayed if a user gets blocked. No full html file, it is displayed within a div element. See data/default.block.html as example and create your own if necessary.<br />
					<b>It is recommended NOT to edit the default file but create a new file and add the filename here for custom messages. Otherwise your changes will be overwritten on next plugin update</b>
				</span></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="abdetective_redirect">Redirection URL</label></th>
			<td><input name="abdetective_redirect" type="text" id="abdetective_redirect" value="<?=get_option( 'abdetective_redirect' );?>" class="regular-text" /><br />
				<span class="description">Enter the URL to redirect to, if redirection mode is selected above. Full URL Syntax (e.g. http://mysite/info.html)<br />
					<b>This URL will be checked by URL Blockers. Do not use ad specific names in this URL, choose a unsuspecting title, like 'info', or 'welcome' to prevent ad blockers from blocking this URL.</b><br />
					If in doubt, try a browser with installed ad block. If the redirect does not work, choose another URL.
				</span></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="abdetective_sharewarefile">Shareware Message File</label></th>
			<td><input name="abdetective_sharewarefile" type="text" id="abdetective_sharewarefile" value="<?=get_option( 'abdetective_sharewarefile', 'default.shareware.html' );?>" class="regular-text" /><br />
				<span class="description">
					This is the file displayed in the shareware dialog. No full html file, it is displayed within a div element. See data/default.shareware.html as example and create your own if necessary.<br />
					<b>It is recommended NOT to edit the default file but create a new file and add the filename here for custom messages. Otherwise your changes will be overwritten on next plugin update</b>
				</span></td>
		</tr>
	</table>
	<p class="submit"><input name="submit" id="submit" class="button-primary" value="Save Changes" type="submit"></p>
</form>
</div>
