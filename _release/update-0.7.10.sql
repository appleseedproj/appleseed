
alter table `#__Janitor` add `Task` char(64);

alter table `#__Janitor` add primary key ( `Task` );

update `#__Janitor` set Task = 'Janitorial'; 

insert into `#__Janitor` ( `Updated`, `Task` ) values ( NOW(), 'UpdateNodeNetwork' );

insert into `#__Janitor` ( `Updated`, `Task` ) values ( NOW(), 'ProcessNewsfeed' );

drop table `#__photoInformation`;
drop table `#__photoPrivacy`;
drop table `#__photoSets`;

create table `#__PhotoSets` ( `Set_PK` int(11) NOT NULL AUTO_INCREMENT, `Owner_FK` int(11) DEFAULT NULL, `Identifier` char(32) DEFAULT NULL, `Name` char(128) DEFAULT NULL, `Directory` char(128) NOT NULL, `Description` text, `Created` datetime DEFAULT NULL, `Updated` datetime DEFAULT NULL, PRIMARY KEY (`Set_PK`)) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

alter table `#__NetworkNodes` drop `Methods`;

alter table `#__NetworkNodes` change `Version` `Version` char(32);

alter table `#__NetworkNodes` add `Entry` char(128);

alter table `#__userAuthorization` add `Secret` char(32);

### Legacy Database Refactoring

# authSessions

alter table #__authSessions engine="MyIsam";

alter table #__authSessions change tID Session_PK INTEGER NOT NULL AUTO_INCREMENT;

alter table #__authSessions change Identifier Identifier CHAR(32) NOT NULL;
alter table #__authSessions change Username Username char(32) NOT NULL;
alter table #__authSessions change Domain Domain char(128) NOT NULL;

alter table #__authSessions change Address Address char(16) NOT NULL DEFAULT '0.0.0.0';

 alter table #__authSessions change Host Host char(128) NOT NULL;

alter table #__authSessions change Fullname Fullname char(64) NOT NULL; 

alter table #__authSessions rename #___authSessions;

alter table #___authSessions rename #__RemoteSessions;


# friendCircles

alter table #__friendCircles drop key #__Circles_FKIndex1;

alter table #__friendCircles engine="MyIsam";

alter table #__friendCircles change tID Circle_PK INTEGER NOT NULL AUTO_INCREMENT;

alter table #__friendCircles change Name Name char(32) DEFAULT NULL;

alter table #__friendCircles change Description Description char(255) DEFAULT NULL;

alter table #__friendCircles rename #___friendCircles;
alter table #___friendCircles rename #__FriendCircles;


# friendCirclesList

 alter table #__friendCirclesList drop key #__friendCirclesList_FKIndex1;

 alter table #__friendCirclesList drop key #__friendCirclesList_FKIndex2;

alter table #__friendCirclesList engine="MyIsam";

alter table #__friendCirclesList change tID Map_PK INTEGER NOT NULL AUTO_INCREMENT;

alter table #__friendCirclesList change friendCircles_tID Circle_FK INTEGER NOT NULL;

alter table #__friendCirclesList change friendInformation_tID Friend_FK INTEGER NOT NULL;

alter table #__friendCirclesList rename #__FriendCirclesMap;



# userIcons

drop table #__userIcons;

# userAnswers

 drop table #__userAnswers;

# userQuestions

drop table #__userQuestions;

# commentInformation

drop table #__commentInformation;

# systemLogs

drop table #__systemLogs;

# systemConfig

drop table #__systemConfig;

# systemOptions

drop table #__systemOptions;

# systemMaintenance

drop table #__systemMaintenance;

# tagInformation

drop table #__tagInformation;

# tagList

 drop table #__tagList;

# userAccess

drop table #__userAccess;
# userInvites

create table #__UserInvitesNew ( Invite_PK INTEGER PRIMARY KEY AUTO_INCREMENT, Account_FK INTEGER NOT NULL, Value CHAR(32), Active BOOL, Recipient CHAR(128), Stamp DATETIME );

insert into #__UserInvitesNew ( Account_FK, Value, Active, Recipient, Stamp ) SELECT userAuth_uID, Value, Active, Recipient, Stamp FROM #__userInvites; 

drop table #__userInvites;

alter table #__UserInvitesNew rename #__UserInvites;
# userPreferences

drop table #__userPreferences;
# userPrivacy

drop table #__userPrivacy;

# contentPages

drop table #__contentPages;

# contentNodes

drop table #__contentNodes;

# userBlocks

drop table #__userBlocks;
# groupContent

drop table #__groupContent;

# groupInformation

drop table #__groupInformation;
# groupMembers

drop table #__groupMembers;

# cacheNodes

drop table #__cacheNodes;
# userGroups

drop table #__userGroups;
# userSettings

drop table #__userSettings;

# userSessions

drop table #__userSessions;

CREATE TABLE #__UserSessions ( `Session_PK` INTEGER NOT NULL PRIMARY KEY, `Account_FK` INTEGER NOT NULL, `Identifier` CHAR(32), Stamp DATETIME, Address CHAR(16), Host CHAR(128) );

# userInformation

drop table #__userInformation;
# userProfile

CREATE TABLE `#__UserInformation` (
  `Account_FK` int(11) NOT NULL,
  `Fullname` char(64) DEFAULT NULL,
  `Alias` char(64) DEFAULT NULL,
  `Description` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into #__UserInformation select userAuth_uID, Fullname, Alias, Description from #__userProfile;

alter table #__userAuthorization add `Email` char(128) NOT NULL;

update `#__userAuthorization` AS `a` set `a`.`Email` = ( select `p`.`Email` FROM `#__userProfile` AS `p` WHERE `p`.`userAuth_uID` = `a`.`uID` );

drop table #__userProfile;

# messageNotification

alter table #__messageNotification drop key #__messageNotification_FKIndex1;

alter table #__messageNotification engine="MyIsam";

alter table #__messageNotification change tID Notification_PK INTEGER NOT NULL AUTO_INCREMENT;

alter table #__messageNotification change userAuth_uID Account_FK INTEGER NOT NULL;

alter table #__messageNotification change Sender_Username Sender_Username char(32);

alter table #__messageNotification change Sender_Domain Sender_Domain char(128);

alter table #__messageNotification change Identifier Identifier char(128);

alter table #__messageNotification change Subject Subject char(255);

alter table #__messageNotification rename #__MessageNotifications;


# messageLabels

alter table #__messageLabels drop key #__two_messageLabels_FKIndex1;

alter table #__messageLabels engine="MyIsam";

alter table #__messageLabels change userAuth_uID Account_FK INTEGER NOT NULL;

alter table #__messageLabels change tID Label_PK INTEGER AUTO_INCREMENT;

alter table #__messageLabels change Label Label char(32);

alter table #__messageLabels rename #___messageLabels;

alter table #___messageLabels rename #__MessageLabels;


# messageLabelList

alter table #__messageLabelList drop key #__messageLabelList_FKIndex1;

 alter table #__messageLabelList engine="MyIsam";

alter table #__messageLabelList change tID Map_PK INTEGER AUTO_INCREMENT;

alter table #__messageLabelList change messageLabels_tID Label_FK INTEGER NOT NULL;

alter table #__messageLabelList change Identifier Identifier char(128);

alter table #__messageLabelList rename #__MessageLabelsMap;


# messageAttachments

drop table #__messageAttachments;

# messageInformation

alter table #__messageInformation drop key #__messageInformation_FKIndex1;

alter table #__messageInformation engine="MyIsam";

alter table #__messageInformation change userAuth_uID Account_FK INTEGER NOT NULL;

alter table #__messageInformation change tID Entry_PK INTEGER AUTO_INCREMENT;

alter table #__messageInformation change Sender_Username Sender_Username char(32);

alter table #__messageInformation change Identifier Identifier char(128);

alter table #__messageInformation change Subject Subject char(255);

alter table #__messageInformation rename #__MessageEntries;


# authTokens

drop table #__authTokens;

# authVerification

drop table #__authVerification;

# messageStore


alter table #__messageStore drop key #__messageStore_FKIndex1;

alter table #__messageStore engine="MyIsam";

 alter table #__messageStore change tID Draft_PK INTEGER AUTO_INCREMENT;

alter table #__messageStore change userAuth_uID Account_PK INTEGER NOT NULL;

alter table #__messageStore change Subject Subject char(255);

 alter table #__messageStore rename #__MessageDrafts;


# messageRecipient

alter table #__messageRecipient drop key #__two_messageRecipients_FKIndex1;

alter table #__messageRecipient drop key #__two_messageRecipients_FKIndex2;

alter table #__messageRecipient engine="MyIsam";

alter table #__messageRecipient change tID Recipient_PK INTEGER AUTO_INCREMENT;

alter table #__messageRecipient change messageStore_tID Message_FK INTEGER NOT NULL;

alter table #__messageRecipient change Identifier Identifier char(128); 

alter table #__messageRecipient change Username Username char(128);

alter table #__messageRecipient change Domain Domain char(128);

 alter table #__messageRecipient rename #__MessageRecipients;


# userAuthorization

alter table #__userAuthorization drop key #__userAuthorization_index;

alter table #__userAuthorization engine="MyIsam";

alter table #__userAuthorization change uID Account_PK INTEGER AUTO_INCREMENT;

update #__userAuthorization set Secret = md5(rand());

alter table #__userAuthorization rename #__UserAccounts;
