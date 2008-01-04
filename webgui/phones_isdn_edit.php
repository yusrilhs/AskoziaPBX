#!/usr/local/bin/php
<?php 
/*
	$Id$
	part of AskoziaPBX (http://askozia.com/pbx)
	
	Copyright (C) 2007-2008 IKT <http://itison-ikt.de>.
	All rights reserved.
	
	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:
	
	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.
	
	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.
	
	THIS SOFTWARE IS PROVIDED "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/

require_once("functions.inc");

$pgtitle = array("Phones", "ISDN", "Edit Line");
require("guiconfig.inc");

/* grab and sort the isdn phones in our config */
if (!is_array($config['isdn']['phone']))
	$config['isdn']['phone'] = array();

isdn_sort_phones();
$a_isdnphones = &$config['isdn']['phone'];

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

/* pull current config into pconfig */
if (isset($id) && $a_isdnphones[$id]) {
	$pconfig['extension'] = $a_isdnphones[$id]['extension'];
	$pconfig['callerid'] = $a_isdnphones[$id]['callerid'];
	$pconfig['provider'] = $a_isdnphones[$id]['provider'];
	$pconfig['outbounduridial'] = isset($a_isdnphones[$id]['outbounduridial']);
	$pconfig['voicemailbox'] = $a_isdnphones[$id]['voicemailbox'];
	$pconfig['sendcallnotifications'] = isset($a_isdnphones[$id]['sendcallnotifications']);
	$pconfig['allowdirectdial'] = isset($a_isdnphones[$id]['allowdirectdial']);
	$pconfig['publicname'] = $a_isdnphones[$id]['publicname'];
	$pconfig['interface'] = $a_isdnphones[$id]['interface'];
	$pconfig['language'] = $a_isdnphones[$id]['language'];
	$pconfig['descr'] = $a_isdnphones[$id]['descr'];
}

if ($_POST) {

	unset($input_errors);
	$pconfig = $_POST;
	
	/* input validation */
	$reqdfields = explode(" ", "extension callerid");
	$reqdfieldsn = explode(",", "Extension,Caller ID");
	
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (($_POST['extension'] && !pbx_is_valid_extension($_POST['extension']))) {
		$input_errors[] = "A valid extension must be entered.";
	}
	if (($_POST['callerid'] && !pbx_is_valid_callerid($_POST['callerid']))) {
		$input_errors[] = "A valid Caller ID must be specified.";
	}
	if (!isset($id) && in_array($_POST['extension'], pbx_get_extensions())) {
		$input_errors[] = "A phone with this extension already exists.";
	}
	if (($_POST['voicemailbox'] && !verify_is_email_address($_POST['voicemailbox']))) {
		$input_errors[] = "A valid e-mail address must be specified.";
	}
	if ($_POST['publicname'] && ($msg = verify_is_public_name($_POST['publicname']))) {
		$input_errors[] = $msg;
	}

	if (!$input_errors) {
		$ip = array();
		$ip['extension'] = $_POST['extension'];
		$ip['callerid'] = $_POST['callerid'];
		$ip['voicemailbox'] = verify_non_default($_POST['voicemailbox']);
		$ip['sendcallnotifications'] = $_POST['sendcallnotifications'] ? true : false;
		$ip['allowdirectdial'] = $_POST['allowdirectdial'] ? true : false;
		$ip['publicname'] = verify_non_default($_POST['publicname']);
		$ip['interface'] = $_POST['interface'];
		$ip['language'] = $_POST['language'];
		$ip['descr'] = verify_non_default($_POST['descr']);

		$a_providers = pbx_get_providers();
		$ip['provider'] = array();
		foreach ($a_providers as $provider) {
			if($_POST[$provider['uniqid']] == true) {
				$ip['provider'][] = $provider['uniqid'];
			}
		}
		$ip['outbounduridial'] = $_POST['outbounduridial'] ? true : false;
		
		if (isset($id) && $a_isdnphones[$id]) {
			$ip['uniqid'] = $a_isdnphones[$id]['uniqid'];
			$a_isdnphones[$id] = $ip;
		 } else {
			$ip['uniqid'] = "ISDN-PHONE-" . uniqid(rand());
			$a_isdnphones[] = $ip;
		}
		
		touch($d_isdnconfdirty_path);
		
		write_config();
		
		header("Location: phones_isdn.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<script type="text/JavaScript">
<!--
	<?=javascript_public_direct_dial_editor("functions");?>

	jQuery(document).ready(function(){

		<?=javascript_public_direct_dial_editor("ready");?>

	});

//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
	<form action="phones_isdn_edit.php" method="post" name="iform" id="iform">
		<table width="100%" border="0" cellpadding="6" cellspacing="0">
			<tr> 
				<td width="20%" valign="top" class="vncellreq">Extension</td>
				<td width="80%" class="vtable">
					<input name="extension" type="text" class="formfld" id="extension" size="20" value="<?=htmlspecialchars($pconfig['extension']);?>">
					<br><span class="vexpl">This phone's number (MSN).</span>
				</td>
			</tr>
			<tr> 
				<td valign="top" class="vncellreq">Caller ID</td>
				<td class="vtable">
					<input name="callerid" type="text" class="formfld" id="callerid" size="40" value="<?=htmlspecialchars($pconfig['callerid']);?>"> 
					<br><span class="vexpl">Text to be displayed for Caller ID.</span>
				</td>
			</tr>
			<? display_call_notifications_editor($pconfig['voicemailbox'], $pconfig['sendcallnotifications'], 1); ?>
			<? display_public_direct_dial_editor($pconfig['allowdirectdial'], $pconfig['publicname'], 1); ?>
			<tr> 
				<td valign="top" class="vncell">ISDN Interface</td>
				<td class="vtable">
					<select name="interface" class="formfld" id="interface"><?
					
					$isdn_interfaces = isdn_get_nt_interfaces();
					foreach ($isdn_interfaces as $interface) {
						?><option value="<?=$interface['unit'];?>" <?
						if ($interface['unit'] == $pconfig['interface']) {
							echo "selected";
						}
						?>><?=$interface['name'];?></option><?
					}
					
					?></select>
				</td>
			</tr>
			<? display_channel_language_selector($pconfig['language'], 1); ?>
			<? display_provider_access_selector($pconfig['provider'], $pconfig['outbounduridial'], 1); ?>
			<tr> 
				<td valign="top" class="vncell">Description</td>
				<td class="vtable">
					<input name="descr" type="text" class="formfld" id="descr" size="40" value="<?=htmlspecialchars($pconfig['descr']);?>"> 
					<br><span class="vexpl">You may enter a description here 
					for your reference (not parsed).</span>
				</td>
			</tr>
			<tr> 
				<td valign="top">&nbsp;</td>
				<td>
					<input name="Submit" type="submit" class="formbtn" value="Save">
					<?php if (isset($id) && $a_isdnphones[$id]): ?>
					<input name="id" type="hidden" value="<?=$id;?>"> 
					<?php endif; ?>
				</td>
			</tr>
		</table>
	</form>
<?php include("fend.inc"); ?>
