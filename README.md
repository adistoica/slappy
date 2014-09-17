slappy
======

An intelligent IRC bot that is capable of doing basic functions. Written in PHP.

>> Basic commands.

This is the list that it currently can handle:
- server : shows the server slappy currently is connected;
- join : joins a channel;
- part : parts a channel;
- action : does a action like " *slappy is happy " ;
- do 
- bindings : shows the current bindings for the bot;
- quit : disconnects the bot from the irc;
- die : kills the bot ;


>> Oper module (as of version 1.1);

This module can oper the bot on your irc server.
For the config parameters see oper.php in modules/ .

>> NickServ moule

Yes, Slappy comes with a nickserv module that can help you to
identify the bot to the service. For more details see nickserv.php

>> Installing

To install Slappy you need php installed. Version 4.3.1+ is recomended.
First of all you need to get Slappy from http://sourceforge.net/projects/slappy .Unzip the archive.
Then you need to create the config file or just edit slappy-config.sample and rename it to config.php.
After that you just need to run "php slappy.php" . If there are no errors then you've done everything right.

>> Licence

See licence details in the LICENCE file.

>> What can Slappy do ?

Public/private commands for slappy.
Use /msg <#chan,botnick> <cmd> or /notice <botnick> <cmd>
Currently support the commands: server, join, part, action, do,
bindings, rehash (not done yet), quit, die, mysql (not ready yet).
