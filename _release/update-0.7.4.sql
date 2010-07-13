update #__userSettings set Value='default' where Identifier='DefaultTheme';
update #__systemConfig set Value='default' where Concern='Theme';
alter table #__userAuthorization modify Pass VARCHAR(144);
drop table #__systemStrings;

create table `#__ExampleCustomers` ( `Customer_PK` int(11) NOT NULL, `CustomerName` varchar(50) NOT NULL, `ContactLastName` varchar(50) NOT NULL, `ContactFirstName` varchar(50) NOT NULL, `Phone` varchar(50) NOT NULL, `AddressLine1` varchar(50) NOT NULL, `AddressLine2` varchar(50) DEFAULT NULL, `City` varchar(50) NOT NULL, `State` varchar(50) DEFAULT NULL, `PostalCode` varchar(15) DEFAULT NULL, `Country` varchar(50) NOT NULL, `SalesRep_Employee_FK` int(11) DEFAULT NULL, `CreditLimit` double DEFAULT NULL, PRIMARY KEY (`Customer_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
create table `#__ExampleEmployees` ( `Employee_PK` int(11) NOT NULL, `LastName` varchar(50) NOT NULL, `FirstName` varchar(50) NOT NULL, `Extension` varchar(10) NOT NULL, `Email` varchar(100) NOT NULL, `OfficeCode` varchar(20) NOT NULL, `ReportsTo_Employee_ID` int(11) DEFAULT NULL, `JobTitle` varchar(50) NOT NULL, PRIMARY KEY (`Employee_PK`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;