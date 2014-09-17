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
Public/private commands for slappy.
Use /msg <#chan,botnick> <cmd> or /notice <botnick> <cmd>
Currently support the commands: server, join, part, action, do,
bindings, rehash (not done yet), quit, die, mysql (not ready yet).

*/

bind('pubcmd',"PRIVMSG","NOTICE");

function pubcmd($user, $whom, $msg) {
    global $bnick, $version;
    $params = explode(" ",$msg);
    if (ereg("^#",$whom)) { // if they're calling the bot in a channel
	$mecalled = array_shift($params); // getting bots nick out
    } else { // private msg to the bot
	$mecalled = $bnick;
        $whom = get_nick($user);
    }
    $command = strtoupper(array_shift($params));
    if ($mecalled == $bnick) {
    	switch($command) {
	    case "SERVER":
		msg($whom,"I'm using ".ircserver());
		break;
	    case "\001PING":
		dump("NOTICE $whom :\001PING ".implode(" ",$params));
		break;
	    case "\001VERSION\001":
		dump("NOTICE ".get_nick($user)." :\001VERSION $version \001");
		break;
	    default:
		if (is_admin($user)) {
		    switch ($command) {
			case "JOIN":
			    call_user_func_array('jchan',$params);
			    break;
			case "PART":
			    part($params[0]);
			    break;
			case "ACTION":
			    action($whom,join(" ",$params));
			    break;
			case "DO":
			    dump(join(" ",$params));
			    break;
			case "BINDINGS":
			    msg($whom,"BINDINGS for $params[0] -> (".join(",",managebindings('list',$params[0])).")");
			    break;
			case "REHASH":
			    msg($whom,"rehashing...");
			    //rehash();
			    break;
			case "QUIT":
    			    irclog("cmd","ressurect requested by ".get_nick($user));
			    quit("requested by ".get_nick($user));
			    break;
			case "DIE":
			    quit("requested by ".get_nick($user));
			    irclog("cmd","die request by ".get_nick($user));
			    exit();
			    break;
			case "MYSQL":
			    $query = implode(" ",$params);
			    jmysql($query);
			    break;
			default:
			    msg($whom,"Not implemented... yet!");
			    break;
		    }
		}
		break;
        }
    }
}

echo ">> Public commands module loaded\n";
?>
