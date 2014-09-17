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
This is the API for Slappy. It provides more easy-to-use functions
to the end-user.
*/

function msg($target,$text) {
    dump("PRIVMSG $target :$text");
}

function notice($target,$text) {
    dump("NOTICE $target :$text");
}

function jchan($chan) { // can be used as jchan($chan,$key) too
    $chan_and_key = implode(" ",func_get_args());
    dump("JOIN :$chan_and_key");
}

function part($chan) {
    dump("PART $chan");
}

function kick($chan,$nick) { // can be used as kick($chan,$nick,$reason) too
    $reason = '';
    if (func_num_args()>2) $reason = func_get_arg(2); 
    dump("KICK $chan $nick :$reason");
}

function mode($target,$modes) { // can be with more than two params too
    $args = func_get_args();
    $target = array_shift($args);
    $modes = array_shift($args);
    $params = implode(" ",$args);
    dump("MODE $target $modes :$params");
}

function topic($chan,$topic) { 
    dump("TOPIC $chan :$topic");
}

function quit() { //can be used as quit($reason) too
    $reason = '';
    if (func_num_args()) $reason = func_get_arg(0);
    dump("QUIT :$reason");
}
	
function action($target,$text) {
    dump("PRIVMSG $target :\001ACTION $text\001");
}

function version($target) {
    dump("PRIVMSG $target :\001VERSION\001");
}

function ping($target) {
    dump("PRIVMSG $target :\001PING ".time()."\001");
}

function wall($text) {
    dump("WALLOPS :$text");
}

function operwl($text) {
    dump("OPERWALL :$text");
}

function locops($text) {
    dump("LOCOPS :$text");
}

function get_user_info($user) {
    ereg("(.*)!(.*)@(.*)",$user,$regs);
    return $regs;
}

function get_nick($user) {
    $result = get_user_info($user);
    return $result[1];
}

function get_ident($user) {
    $result = get_user_info($user);
    return $result[2];
}

function get_host($user) {
    $result = get_user_info($user);
    return $result[3];
}

echo ">> Api module loaded\n";

?>
