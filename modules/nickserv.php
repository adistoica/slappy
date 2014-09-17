<?
/*

   Slappy - a php ircbot
    Copyright (C) 2004-2005

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

 */

$nspass = "mypass";
$debug = "on"; // debug on/off
$debugchan = "#debug";

bind('nsident',"NOTICE");

function nsident($user,$whom,$msg) {
    global $nspass, $debugchan;
    if (get_nick($user) == "NS") {
	if ("This nickname is owned by someone else" == $msg) {
	    msg("NS","identify $nspass");
	    irclog("NS","Trying to identify myself");
	    msg($debugchan,"trying to id");
	} elseif ("Password accepted - you are now recognized" == $msg) {
	    irclog("NS","Identification successful, yuppie!");
	    if ($debug == "on") {
    		msg($debugchan,"Identification successful: yuppie!");
	    }
	    msg("CS","op all");
	} elseif ("Password Incorrect" == $msg) {
	    irclog("NS","Identification failed: wrong password");
	    if ($debug == "on") {
		msg($debugchan,"Identification failed: wrong password");
	    }
	}
    }
}

echo ">> Nickserv module loaded\n";

?>
