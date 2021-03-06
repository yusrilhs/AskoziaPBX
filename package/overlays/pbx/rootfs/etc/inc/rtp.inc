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

/* include all configuration functions */
require_once("functions.inc");

/* add default values to global array */
if (!isset($defaults['rtp'])) {
	$defaults['rtp'] = array();
	$defaults['rtp']['lowport'] = "10000";
	$defaults['rtp']['highport'] = "10200";
} else {
	echo "ERROR: adding default values in rtp.inc failed!\n";
}


/**
 * Generates rtp.conf which contains the RTP port range Asterisk uses
 */
function rtp_conf_generate($return=false) {

	$low_port = rtp_low_port();
	$high_port = rtp_high_port();

	$conf = "[general]\n";
	$conf .= "rtpstart=$low_port\n";
	$conf .= "rtpend=$high_port\n";
	$conf .= "\n";

	return ($return) ? $conf : util_file_write("/etc/asterisk/rtp.conf", $conf);
}

/**
 * gets, verifies and sets values for the rtp low port number
 */
function rtp_low_port($new_port=false) {
	global $defaults, $config;

	// setting a new value
	if ($new_port !== false) {
		// verify port
		if (!verify_is_port($new_port)) {
			return "Invalid RTP low port specified ($new_port).";
		} else {
			// delete existing value
			unset($GLOBALS['config']['services']['rtp']['lowport']);
			// save if new value is not the default value
			if ($new_port != $defaults['rtp']['lowport']) {
				$config['services']['rtp']['lowport'] = $new_port;
			}
			return false;
		}
	
	// get current value
	} else {
		return isset($config['services']['rtp']['lowport']) ?
			$config['services']['rtp']['lowport'] :
			$defaults['rtp']['lowport'];	
	}
}

/**
 * gets, verifies and sets values for the rtp high port number
 */
function rtp_high_port($new_port=false) {
	global $defaults, $config;

	// setting a new value
	if ($new_port !== false) {
		// verify port
		if (!verify_is_port($new_port)) {
			return "Invalid RTP high port specified ($new_port).";
		} else {
			// delete existing value
			unset($GLOBALS['config']['services']['rtp']['highport']);
			// save if new value is not the default value
			if ($new_port != $defaults['rtp']['highport']) {
				$config['services']['rtp']['highport'] = $new_port;
			}
			return false;
		}
	
	// get current value
	} else {
		return isset($config['services']['rtp']['highport']) ?
			$config['services']['rtp']['highport'] :
			$defaults['rtp']['highport'];	
	}
}

?>
