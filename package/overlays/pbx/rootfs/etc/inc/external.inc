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
	
	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
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

/**
 * Returns all phones sorted by extension.
 * If no phones are defined, the global array is initialized.
 */
function external_get_phones() {

	if (!is_array($GLOBALS['config']['external']['phone'])) {
		$GLOBALS['config']['external']['phone'] = array();
	}

	$tmp = $GLOBALS['config']['external']['phone'];
	usort($tmp, "pbx_sort_by_extension");

	return $tmp;
}

/**
 * Returns a specific phone based on its uniqid
 */
function external_get_phone($uniqid) {
	global $uniqid_map;

	return $GLOBALS['config']['external']['phone'][$uniqid_map[$uniqid]];
}

/**
 * Returns an array of all assigned phone extensions
 */
function external_get_extensions() {

	$extensions = array();

	if (is_array($GLOBALS['config']['external']['phone'])) {
		foreach($GLOBALS['config']['external']['phone'] as $phone) {
			$extensions[$phone['uniqid']] = $phone['extension'];
		}
	}

	return $extensions;
}




function external_generate_default_phone() {
	global $defaults;

	$phone = array();

	$phone['extension'] = pbx_get_next_default_phone_extension();
	$phone['callerid'] = $defaults['accounts']['phones']['callerid'];
	$phone['language'] = $defaults['accounts']['language'];
	$phone['ringlength'] = $defaults['accounts']['phones']['ringlength'];
	$phone['uniqid'] = "EXTERNAL-PHONE-" . uniqid(rand());

	$phone['dialprovider'] = $defaults['accounts']['phones']['dialprovider'];
	$phone['vmpin'] = rand(1000,9999);
	
	return $phone;
}

function external_verify_phone($post, $errors) {

	$phone = array();

	v_cleanliness(&$post, &$errors);
	v_extension(&$phone, &$post, &$errors);
	v_callerid(&$phone, &$post, &$errors);
	v_optional('descr', &$phone, &$post);
	v_public_access_editor(&$phone, &$post, &$errors);
	v_notifications_editor(&$phone, &$post, &$errors);
	v_voicemail_editor(&$phone, &$post, &$errors);
	v_dialstring(&$phone, &$post, &$errors);

	$trusted = array(
		'uniqid',
		'language',
		'ringlength',
		'dialprovider'
	);
	foreach ($trusted as $t) {
		$phone[$t] = $post[$t];
	}

	return $phone;
}

function external_save_phone($phone) {
	global $g, $uniqid_map;

	if (external_get_phone($phone['uniqid'])) {
		$GLOBALS['config']['external']['phone'][$uniqid_map[$phone['uniqid']]] = $phone;
	} else {
		$GLOBALS['config']['external']['phone'][] = $phone;
	}

	write_config();
	touch($g['external_dirty_path']);
}

?>
