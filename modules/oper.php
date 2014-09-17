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
/*
This script provides oper support for slappy.
*/

$nick = "slappy"; // nick in the o:line
$pass = "mypass"; // password for /oper
$omodes = "+cks"; // modes to set after oper identification
$debug = "on"; // debug on/off
$debugchan = "#debug";

bind('operid',"PRIVMSG");

function operid($user,$whom,$msg) {
    global $nick, $pass, $debugchan;
    if (is_admin($user) == true) {
	if ($msg == bnick()." oper") {
	    if ($debug == "on") {
		msg($debugchan,"trying to oper...");
	    }
	    dump("oper $nick $pass");
	}
    }
}

bind('operok','raw381');

function operok($user,$params) {
    global  $debugchan, $omodes;
    if ($user == ircserver()) {
	$smsg = array();
        $smsg = explode(" ",$params);
	if ($debug == "on") {
	    msg($debugchan,"operation successful!");
	}
        dump("MODE ".bnick()." $omodes");
    }
}

bind('opernotok','raw491');

function opernotok($user,$params) {
    global $debugchan;
    if ($debug == "on") {
	msg($debugchan,"operation failed!;(");
    }
}

echo ">> Oper module loaded\n";

?>
