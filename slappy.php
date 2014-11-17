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
if (!@include("config.php")) {
    die("The configuration file was not found!\n");
}

$version = "Slappy , a php irc bot";

$buser = "$bident";
$buser .= " ".rand_name();
$buser .= " ".rand_name();
$buser .= " :$brealname";

set_time_limit(0); // setting max exec time to zero

connect($ircserver);

function connect($ircserver) {
    global $socket,$ircserver,$bnick,$buser,$bchans,$autojoin;
    $socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP); // create the socket
    $connection = socket_connect($socket,$ircserver,6667); // connect

    //var_dump($connection); // THIS IS FOR DEBUG

    socket_write($socket,"USER $buser\r\n"); // send the Username
    socket_write($socket,"NICK $bnick\r\n"); // change our nickname
    
    if ($autojoin == "on") {
	foreach ($bchans as $bchan) {
	    socket_write($socket,"JOIN $bchan\r\n"); // join the channel
	
	}
    }
    ircloop();
}

function ircloop() {
    global $socket,$ircserver;
// read whatever IRC is telling us
    $datum="initial";
    $lastpiece="";
//sometimesh socket_read return "" when the connection is closed
    while ((!socket_last_error()) && ($datum != "")) {
//safe for Windows (PHP_NORMAL_READ doesn't work)
	$datum = $lastpiece.socket_read($socket,1024,PHP_BINARY_READ); // 255 is arbitrary
// preventing too much data from causing buffer overflow by spliting it
// and then agglutinating it to the next piece of data to make ot one
        if (ereg(".*\n(.+)",$datum,$regdata) && (!ereg("\n$",$regdata[1]))) {
    	    $lastpiece = $regdata[1];                                           
    	    $datum = substr($datum,0,strlen($datum)-strlen($lastpiece));
	} else {
    	    $lastpiece = "";
	}

// separating every line from the server
	foreach (explode("\r\n",chop($datum)) as $data) {
// debug
	    echo "data - $data\n";//<br>";
// debug unix time
	    echo time()."\n";//<br>";
// separating the data into an array
    	    $split = explode(" ",$data);
// cutting : and getting the nickname as $user
    	    $user = str_replace(":","",array_shift($split)); 
// getting the command
    	    $cmd = array_shift($split);
// taking the rest as parameters of the command
    	    $params = $split;
// the PING syntax is a little bit strange, so we're taking care of it
    	    if ($user == "PING") {
    		$user = ereg_replace("^:","",$cmd);
        	$cmd = "PING";
    	    }
	    respond($user, $cmd, $params);
	}
    }
    
    global $reconnect; // this should be at the top of the script
    if ($reconnect == "on") {
	connect($ircserver);
    } else {
	exit();
    }
}

function respond($user, $cmd, $params) {
    global $socket,$bnick,$nspass;
// uppering the cmd due RFC standarts
    if (strlen($cmd) == 3) { // if $cmd is raw numeric
    	mfunc("raw".$cmd,$user,$params); //send to our master function for event handling
    	// .inc scriptrs should use bind('function_name',"raw301") (for example)
    	// and then function function_name($user,$msg) 
    	// usually $user is the server sending messages
    	// raw001 will save the zeroes (otherwise 001 will become 1, later in bindings) 
    }
    $cmd = strtoupper($cmd);
    switch ($cmd) {
// event handling - very important
	case "PRIVMSG":
    	    $whom = array_shift($params); // where to (chan or bot)
            $params[0] = ereg_replace("^:","",$params[0]); // taking off the : sign from params
            $msg = implode(" ",$params);
            mfunc("PRIVMSG",$user,$whom,$msg);
	    if ($whom[0] = "#") {
		$chan = $whom;
		mfunc("chan",$user,$chan,$msg);
	    } else {
		mfunc("query",$user,$msg);
	    }
	    break;            
// we're done with the parsing of PRIVMSG
// the include() scripts now can do their job
// $whom - called in chan or bot
// $mecalled - channel or nickname - where is invoked
// $user - nick!ident@host
// $command - the command that was sent to us
        case "PING":
// playing PING-PONG with the server
	    $pingserver = $user;
	    mfunc("PING",$pingserver);
	    ircwrite("raw","PONG $user");
            break;
        case "JOIN":
	    $params[0] = ereg_replace("^:","",$params[0]);
	    $chan = $params[0];
	    mfunc("JOIN",$user,$chan);
            break;
        case "NOTICE":
	    $whom = array_shift($params);
            $params[0] = ereg_replace("^:","",$params[0]);
	    $msg = implode(" ",$params);
	    mfunc("NOTICE",$user,$whom,$msg);
            break;
        case "PART":
	    $params[0] = ereg_replace("^:","",$params[0]);
	    $chan = $params[0];
	    mfunc("PART",$user,$chan);
            break;
        case "NICK":
	    $params[0] = ereg_replace("^:","",$params[0]);
	    $newnick = $params[0];
	    mfunc("NICK",$user,$newnick);
            break;
        case "MODE":
	    $target = array_shift($params);
	    $modes = array_shift($params);
	    mfunc("MODE",$user,$target,$modes,$params);
            break;
        case "TOPIC":
	    $chan = array_shift($params);
	    $params[0] = ereg_replace("^:","",$params[0]);
	    $topic = implode(" ",$params);
	    mfunc("TOPIC",$user,$chan,$topic);
            break;
        case "KICK":
	    $chan = array_shift($params);
	    $knick = array_shift($params);
	    $params[0] = ereg_replace("^:","",$params[0]);
	    $reason = implode(" ",$params);
	    mfunc("KICK",$user,$chan,$knick,$reason);
            break;
        case "QUIT":
	    $params[0] = ereg_replace("^:","",$params[0]);
	    $reason = implode(" ",$params);
	    mfunc("QUIT",$user,$reason);
            break;
	case "WALLOPS":
	    $params[0] = ereg_replace("^:","",$params[0]);
	    $wallmsg = implode(" ",$params);
	    mfunc("WALLOPS",$user,$wallmsg);
	    break;
    }
}

function mfunc() {
    $args = func_get_args();
    $event = array_shift($args);
    foreach (listbindings($event) as $func) {
	    call_user_func_array($func,$args);
    }
}

function bind($function_name, $events) { // it can be called with more events, but at least one is compulsary
    $arr = func_get_args();
    $function = array_shift($arr);
    foreach ($arr as $event) {
	managebindings('set',$event,$function);
    }
}

function unbind($function_name, $events) { // it can be called with more events, but at least one is compulsary
    $arr = func_get_args();
    $function = array_shift($arr);
    foreach ($arr as $event) {
	managebindings('unset',$event,$function);
    }
}
						
function listbindings($event) {
    return managebindings('list',$event);
}

function managebindings() {
    static $bind = array();
    $arr = func_get_args();
    $proto = $arr[0];
    $event = $arr[1];
    if (func_num_args()>2) {
	$func = $arr[2];
    }
    switch ($proto) {
	case 'set':
	    if (!array_key_exists($event,$bind)) {
		$bind[$event][] = $func;
	    } else {
		if (!in_array($func,$bind[$event])) {
		    $bind[$event][] = $func;
		}
	    }
	    break;
	case 'unset':
	    if (array_key_exists($event,$bind)) {
		foreach($bind[$event] as $key => $value) {
		    if ($value == $func) {
			array_splice($bind[$event],$key,1);
		    }
		}
	    }
	    break;
	case 'list':
	    if  (array_key_exists($event,$bind)) {
		return $bind[$event];
	    } else {
		return array();
	    }
	    break;
    }
}

bind("changenick","NICK");
function changenick($user,$newnick) {
    if (get_nick($user) == bnick()) {
	bnick($newnick);
    }
}

function bnick() {
    global $bnick;
    if (func_num_args()) {
	$bnick = func_get_arg(0);
	ircwrite("raw", "NICK $bnick");
    }
    return $bnick;
}
						
function is_admin($user) {
    global $badmins;
    return in_array(get_nick($user),$badmins);
}

function randomname($namelength) {
    $namechars = 'abcdefghijklmnopqrstuvwxyz';
    $vouel = 'aeiou';
    $name = "";
    for ($index=1;$index<=$namelength;$index++) {
        if ($index % 3 == 0) {
            $randomnumber = rand(1,strlen($vouel));
            $name .= substr($vouel,$randomnumber-1,1);
         } else {
          $randomnumber = rand(1,strlen($namechars));
          $name .= substr($namechars,$randomnumber-1,1);
         }
    }
    return $name;
}

function rand_name() {
    return ucfirst(randomname(rand(5,8)));
}

function irclog($logproto,$logdata) {
    $irclog = fopen("phpbot.log","a+"); // opening log file
    fwrite($irclog,"$logproto - $logdata\n");
    fclose($irclog);
}

function ircwrite($proto,$ircdata) {
    global $socket;
    $ircdata .= "\r\n"; // auto putting end-signs at every line
    switch ($proto) {
        case "ctcp": // client-to-client protocol
            socket_write($socket,$ircdata);
            break;
        case "msg": // privmsg
            socket_write($socket,"PRIVMSG $ircdata");
            break;
        case "raw": // raw message
            socket_write($socket," $ircdata");
            break;
        default: // everything else
            irclog($proto,$ircdata);
            break;
    }
}

function dump($ircdata) {
    global $socket;
    socket_write($socket,$ircdata."\r\n");
}


echo "exiting\n";

?>
