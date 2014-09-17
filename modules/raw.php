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
// raw messages support.

bind('NickInUse','raw433');
function NickInUse() {
	bnick(rand_name());
}

bind('raw_002_handle','raw002');
function raw_002_handle($user, $params) {
	 ircserver('set',$user);
	 return;
}

function ircserver() {
    global $ircserver;
    if ((func_num_args() == 2) && (func_get_arg(0) == 'set')) {
    	$ircserver = func_get_arg(1);
    }
    return $ircserver;
}

bind('WhoisHandle','raw311');
function WhoisHandle($user,$params) {
	array_shift($params); // this is bot's nick, not useful 
	$arr['nick'] = array_shift($params);
	$arr['ident'] = array_shift($params);
	$arr['host'] = array_shift($params);
	array_shift($params); // useless *
	$arr['ircname'] = strip_colon_and_join($params);
	return $arr;
}

echo ">> Raw module loaded \n";

?>
