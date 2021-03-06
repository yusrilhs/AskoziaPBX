#!/usr/bin/php
<?php 
/*
	$Id$
	part of AskoziaPBX (http://askozia.com/pbx)
	
	Copyright (C) 2007-2008 tecema (a.k.a IKT) <http://www.tecema.de>.
	All rights reserved.
	
	Askozia®PBX is a registered trademark of tecema. Any unauthorized use of
	this trademark is prohibited by law and international treaties.
	
	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:
	
	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.
	
	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.
	
	3. Redistribution in any form at a charge, that in whole or in part
	   contains or is derived from the software, including but not limited to
	   value added products, is prohibited without prior written consent of
	   tecema.
	
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


require("guiconfig.inc");
$pgtitle = array(gettext("Accounts"), gettext("Edit ISDN Phone"));


if ($_POST) {
	unset($input_errors);

	$phone = isdn_verify_phone(&$_POST, &$input_errors);
	if (!$input_errors) {
		isdn_save_phone($phone);
		header("Location: accounts_phones.php");
		exit;
	}
}


$colspan = 1;
$carryovers[] = "uniqid";

$uniqid = $_GET['uniqid'];
if (isset($_POST['uniqid'])) {
	$uniqid = $_POST['uniqid'];
}

if ($_POST) {
	$form = $_POST;
} else if ($uniqid) {
	$form = isdn_get_phone($uniqid);
} else {
	$form = isdn_generate_default_phone();
}


include("fbegin.inc");
d_start("phones_isdn_edit.php");


	// General
	d_header(gettext("General Settings"));

	d_field(gettext("Number"), "extension", 20,
		gettext("The number used to dial this phone."), "required");

	d_field(gettext("Caller ID"), "callerid", 40,
		gettext("Text to be displayed for Caller ID."), "required");

	display_channel_language_selector($form['language']);

	display_phone_ringlength_selector($form['ringlength']);

	d_hwport_selector($form['port'], "isdn", "nt");

	d_field(gettext("Description"), "descr", 40,
		gettext("You may enter a description here for your reference (not parsed)."));
	d_spacer();


	// Security
	d_header(gettext("Security"));

	display_public_access_editor($form['publicaccess'], $form['publicname']);

	d_provider_access_selector($form['provider']);
	d_spacer();


	// Call Notifications & Voicemail
	d_header(gettext("Call Notifications & Voicemail"));

	d_notifications_editor($form['emailcallnotify'], $form['emailcallnotifyaddress']);

	d_voicemail_editor($form['vmtoemail'], $form['vmtoemailaddress'], $form['vmpin']);
	d_spacer();


	// Outgoing Caller ID
	if ($form['outgoingcalleridmap']) {
		d_header(gettext("Outgoing Caller ID"));
		d_outgoing_callerid_map();
	} else {
		d_collapsible(gettext("Outgoing Caller ID"));
		d_outgoing_callerid_map();
		d_collapsible_end();
	}
	d_spacer();


	// Advanced Options
	d_collapsible(gettext("Advanced Options"));
	d_manualattributes_editor($form['manualattributes']);
	d_collapsible_end();
	d_spacer();


d_submit();
include("fend.inc");
