create table `#__PagePosts` ( `Post_PK` int(11) NOT NULL AUTO_INCREMENT, `User_FK` int(11) NOT NULL, `Owner` char(64) DEFAULT NULL, `Identifier` char(32) DEFAULT NULL, `Content` text, `Current` tinyint(1) DEFAULT NULL, PRIMARY KEY (`Post_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

create table `#__PageReferences` ( `Reference_PK` int(11) NOT NULL AUTO_INCREMENT, `User_FK` int(11) NOT NULL, `Identifier` char(32) DEFAULT NULL, `Type` char(16) DEFAULT NULL, `Stamp` datetime DEFAULT NULL, PRIMARY KEY (`Reference_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

create table `#__PrivacySettings` ( `Setting_PK` int(11) NOT NULL AUTO_INCREMENT, `User_FK` int(11) NOT NULL, `Circle_FK` int(11) DEFAULT NULL, `Type` char(32) DEFAULT NULL, `Identifier` char(32) DEFAULT NULL, `Everybody` tinyint(1) DEFAULT NULL, `Friends` tinyint(1) DEFAULT NULL, PRIMARY KEY (`Setting_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

alter table `#__friendCircles` add Private BOOL;

alter table `#__friendCircles` add Protected BOOL;

alter table `#__friendCircles` add Shared BOOL;

alter table `#__friendInformation` rename `#__FriendInformation`;

alter table `#__FriendInformation` change `tID` `Friend_PK` int(10) unsigned NOT NULL AUTO_INCREMENT;

alter table `#__FriendInformation` change `userAuth_uID` `Owner_FK` INT(10) unsigned not null;

alter table `#__FriendInformation` change `Stamp` `Created` DATETIME;

alter table `#__FriendInformation` drop `Alias`;

alter table `#__FriendInformation` drop `sID`;

create table `#__NotificationsOutgoing` ( `Outgoing_PK` int(11) NOT NULL AUTO_INCREMENT, `Recipient` char(128) DEFAULT NULL, `Owner_FK` int(11) NOT NULL, `ActionOwner` char(128) DEFAULT NULL, `Action` char(32) DEFAULT NULL, `ActionLink` char(255) DEFAULT NULL, `SubjectOwner` char(128) DEFAULT NULL, `ContextOwner` char(128) DEFAULT NULL, `Context` char(32) DEFAULT NULL, `ContextLink` char(255) DEFAULT NULL, `Icon` char(255) DEFAULT NULL, `Comment` char(255) DEFAULT NULL, `Description` text, `Identifier` char(32) DEFAULT NULL, `Created` datetime DEFAULT NULL, `Updated` datetime DEFAULT NULL, PRIMARY KEY (`Outgoing_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

create table `#__NotificationsIncoming` ( `Incoming_PK` int(11) NOT NULL AUTO_INCREMENT, `Owner_FK` int(11) NOT NULL, `ActionOwner` char(128) DEFAULT NULL, `Action` char(32) DEFAULT NULL, `ActionLink` char(255) DEFAULT NULL, `SubjectOwner` char(128) DEFAULT NULL, `ContextOwner` char(128) DEFAULT NULL, `Context` char(32) DEFAULT NULL, `ContextLink` char(255) DEFAULT NULL, `Icon` char(255) DEFAULT NULL, `Comment` char(255) DEFAULT NULL, `Description` text, `Identifier` char(32) DEFAULT NULL, `Created` datetime DEFAULT NULL, `Updated` datetime DEFAULT NULL, PRIMARY KEY (`Incoming_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

create table `#__NotificationsUpdated` ( `Updated_PK` int(11) NOT NULL AUTO_INCREMENT, `Owner_FK` int(11) DEFAULT NULL, `Friend` char(128) DEFAULT NULL, `Updated` datetime DEFAULT NULL, PRIMARY KEY (`Updated_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

create table `#__Janitor` ( `Updated` datetime DEFAULT NULL ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

insert into `#__Janitor` ( `Updated` ) values ( NOW() );