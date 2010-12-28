create table `#__PageLinks` ( `Link_PK` int(11) NOT NULL AUTO_INCREMENT, `User_FK` int(11) DEFAULT NULL, `Owner` char(64) DEFAULT NULL, `Identifier` char(32) DEFAULT NULL, `Content` text, `Link` char(255) DEFAULT NULL, `Title` char(64) DEFAULT NULL, `Description` text, `Thumb` char(255) DEFAULT NULL, PRIMARY KEY (`Link_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

alter table `#__NotificationsOutgoing` add Title char(255);
alter table `#__NotificationsIncoming` add Title char(255);

drop table `#__journalPrivacy`;
drop table `#__journalPost`;

create table `#__JournalEntries` ( `Entry_PK` int(11) NOT NULL AUTO_INCREMENT, `Owner_FK` int(11) DEFAULT NULL, `Title` char(200) DEFAULT NULL, `Body` text, `Identifier` char(32) DEFAULT NULL, `Created` datetime DEFAULT NULL, `Updated` datetime DEFAULT NULL, PRIMARY KEY (`Entry_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

drop table `#__contentArticles`;

create table `#__SearchIndexes` ( `Index_PK` int(11) NOT NULL AUTO_INCREMENT, `Context` char(16) DEFAULT NULL, `Context_FK` int(11) DEFAULT NULL, `Keywords` TEXT DEFAULT NULL, `Created` datetime DEFAULT NULL, `Updated` datetime DEFAULT NULL, PRIMARY KEY (`Index_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

alter table `#__NotificationsOutgoing` add `Feedback` MEDIUMINT default 0;
alter table `#__NotificationsOutgoing` add `Comments` MEDIUMINT default 0;
alter table `#__NotificationsIncoming` add `Feedback` MEDIUMINT default 0;
alter table `#__NotificationsIncoming` add `Comments` MEDIUMINT default 0;

create table `#__FriendPing` ( `Ping_PK` int(11) NOT NULL AUTO_INCREMENT, `Sender` char(200) DEFAULT NULL, `Recipient` char(200) DEFAULT NULL, `Created` datetime DEFAULT NULL, `Status` tinyint(1) DEFAULT '0', PRIMARY KEY (`Ping_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

create table `#__NetworkNodes` ( `Node_PK` int(11) NOT NULL AUTO_INCREMENT, `Description` char(200) DEFAULT NULL, `Domain` char(128) DEFAULT NULL, `Trust` enum('blocked','discovered','trusted') DEFAULT 'discovered', `Source` char(128) DEFAULT NULL, `Access` enum('public','trusted','private') DEFAULT 'private', `Inherit` tinyint(1) DEFAULT NULL, `Created` datetime DEFAULT NULL, `Updated` datetime DEFAULT NULL, `Contacted` datetime DEFAULT NULL, `Expires` datetime DEFAULT '0000-00-00 00:00:00', `Methods` char(100) DEFAULT NULL, `Version` char(8) DEFAULT NULL, `Status` tinyint(1) DEFAULT '0', PRIMARY KEY (`Node_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `#__NetworkNodes` values (1,'Official beta test site for the Appleseed Project: The first open source, fully decentralized social networking software.','appleseedproject.org','trusted','','public',1,NOW(),NOW(),NOW(),'0000-00-00 00:00:00','http','QS/0.1.1',1);

create table `#__SchemaVersions` ( `Schema_PK` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `Version` char(16) DEFAULT NULL, `Notes` text, PRIMARY KEY (`Schema_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

insert into `#__SchemaVersions` ( `Version`, `Notes` ) values ( '0.7.9', '+PageLinks +NotificationsOutgoing.Title +NotificationsIncoming.Title -journalPrivacy -journalPost +JournalEntries -contentArticles +SearchIndexes +NotificationsOutgoing.Feedback +NotificationsOutgoing.Comments +NotificationsIncoming.Feedback +NotificationsIncoming.Comments +FriendPing +NetworkNodes >NetworkNodes.Domain="appleseedproject.org" +SchemaVersions >SchemaVersions.Version="0.7.9" ' );

drop table `#__systemNodes`;
drop table `#__NodeDiscovery`;
