update #__userSettings set Value='default' where Identifier='DefaultTheme';
update #__systemConfig set Value='default' where Concern='Theme';
alter table #__userAuthorization modify Pass VARCHAR(144);
drop table #__systemStrings;

CREATE TABLE `#__Example` ( `id` int(11) NOT NULL AUTO_INCREMENT, `Name` varchar(128) DEFAULT NULL, `Email` varchar(128) DEFAULT NULL, `Link` varchar(255) DEFAULT NULL, `Phone` varchar(24) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1