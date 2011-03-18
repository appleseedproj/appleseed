
alter table `#__Janitor` add `Task` char(64);

alter table `#__Janitor` add primary key ( `Task` );

update `#__Janitor` set Task = 'Janitorial'; 

insert into `#__Janitor` ( `Updated`, `Task` ) values ( NOW(), 'UpdateNodeNetwork' );

insert into `#__Janitor` ( `Updated`, `Task` ) values ( NOW(), 'ProcessNewsfeed' );

drop table `asd_photoInformation`;
drop table `asd_photoPrivacy`;
drop table `asd_photoSets`;

create table `asd_PhotoSets` ( `Set_PK` int(11) NOT NULL AUTO_INCREMENT, `Owner_FK` int(11) DEFAULT NULL, `Identifier` char(32) DEFAULT NULL, `Name` char(128) DEFAULT NULL, `Directory` char(128) NOT NULL, `Description` text, `Created` datetime DEFAULT NULL, `Updated` datetime DEFAULT NULL, PRIMARY KEY (`Set_PK`)) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

alter table `#__NetworkNodes` drop `Methods`;

alter table `#__NetworkNodes` change `Version` `Version` char(32);

alter table `#__NetworkNodes` add `Entry` char(128);

alter table `#__userAuthorization` add `Secret` char(32);

