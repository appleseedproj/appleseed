create table `#__AccessControl` ( `Access_PK` int(10) unsigned NOT NULL AUTO_INCREMENT, `Account` varchar(255) NOT NULL, `Location` varchar(128) NOT NULL DEFAULT '', `r` tinyint(1) DEFAULT '0', `w` tinyint(1) DEFAULT '0', `a` tinyint(1) DEFAULT '0', `Inheritance` tinyint(1) DEFAULT '0', PRIMARY KEY (`tID`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

insert into #__AccessControl ( Account, Location, r, w, a, Inheritance ) values ( 'admin', '/', 1, 1, 1, 1 );

create table `#__NodeDiscovery` ( `Node_PK` int(11) NOT NULL AUTO_INCREMENT, `Domain` varchar(255) NOT NULL, `Methods` varchar(255) DEFAULT NULL, `Tasks` varchar(255) DEFAULT NULL, `Version` varchar(16) DEFAULT NULL, `Raw` text, `Stamp` datetime DEFAULT NULL, PRIMARY KEY (`node_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1

create table `#__LocalTokens` ( `Token_PK` int(11) NOT NULL AUTO_INCREMENT, `Token` varchar(64) DEFAULT NULL, `Username` varchar(255) DEFAULT NULL, `Target` varchar(255) NOT NULL, `Stamp` datetime DEFAULT NULL, PRIMARY KEY (`Token_PK`) ) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=latin1

create table `#__RemoteTokens` ( `Token_PK` int(11) NOT NULL AUTO_INCREMENT, `Token` varchar(64) DEFAULT NULL, `Username` varchar(255) DEFAULT NULL, `Source` varchar(255) NOT NULL, `Address` varchar(255) DEFAULT NULL, `Host` varchar(255) DEFAULT NULL, `Stamp` datetime DEFAULT NULL, PRIMARY KEY (`Token_PK`) ) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1

alter table `#__systemUpdate` rename `#__SystemUpdate`;
alter table `#__systemUpdate` change tID Server_PK int(11);
insert into `#__systemUpdate` (Server) values ('update.appleseedproject.org');

