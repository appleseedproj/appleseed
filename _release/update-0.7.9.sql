create table `#__PageLinks` ( `Link_PK` int(11) NOT NULL AUTO_INCREMENT, `User_FK` int(11) DEFAULT NULL, `Owner` char(64) DEFAULT NULL, `Identifier` char(32) DEFAULT NULL, `Content` text, `Link` char(255) DEFAULT NULL, `Title` char(64) DEFAULT NULL, `Description` text, `Thumb` char(255) DEFAULT NULL, PRIMARY KEY (`Link_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

alter table `#__NotificationsOutgoing` add Title char(255);
alter table `#__NotificationsIncoming` add Title char(255);
