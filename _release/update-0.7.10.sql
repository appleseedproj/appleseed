
alter table `#__Janitor` add `Task` char(64);

alter table `#__Janitor` add primary key ( `Task` );

update `#__Janitor` set Task = 'Janitorial'; 

insert into `#__Janitor` ( `Updated`, `Task` ) values ( NOW(), 'UpdateNodeNetwork' );

insert into `#__Janitor` ( `Updated`, `Task` ) values ( NOW(), 'ProcessNewsfeed' );

alter table `#__PhotoSets` change `Directory` `Directory` char(128) not null;

alter table `#__NetworkNodes` drop `Methods`;

alter table `#__NetworkNodes` change `Version` `Version` char(32);

alter table `#__NetworkNodes` add `Entry` varchar(128);
