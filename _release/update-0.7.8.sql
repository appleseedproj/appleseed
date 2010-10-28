create table `#__PagePosts` ( `Post_PK` int(11) NOT NULL AUTO_INCREMENT, `User_FK` int(11) NOT NULL, `Owner` char(64) DEFAULT NULL, `Identifier` char(32) DEFAULT NULL, `Content` text, `Current` tinyint(1) DEFAULT NULL, PRIMARY KEY (`Post_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

create table `#__PageReferences` ( `Reference_PK` int(11) NOT NULL AUTO_INCREMENT, `User_FK` int(11) NOT NULL, `Identifier` char(32) DEFAULT NULL, `Type` char(16) DEFAULT NULL, `Stamp` datetime DEFAULT NULL, PRIMARY KEY (`Reference_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

create table `#__PrivacySettings` ( `Setting_PK` int(11) NOT NULL AUTO_INCREMENT, `User_FK` int(11) NOT NULL, `Circle_FK` int(11) DEFAULT NULL, `Type` char(32) DEFAULT NULL, `Identifier` char(32) DEFAULT NULL, `Everybody` tinyint(1) DEFAULT NULL, `Friends` tinyint(1) DEFAULT NULL, PRIMARY KEY (`Setting_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

alter table `#__friendCircles` add Private BOOL;

alter table `#__friendCircles` add Protected BOOL;

alter table `#__friendCircles` add Shared BOOL;

alter table `#__friendInformation` drop Alias;

alter table `#__friendInformation` drop sID;
