--
-- Table structure for table `userAuthorization`
--

DROP TABLE IF EXISTS `%PREFIX%userAuthorization`;
CREATE TABLE `%PREFIX%userAuthorization` (
  `uID` int(10) unsigned zerofill NOT NULL auto_increment,
  `Username` varchar(32) NOT NULL default '',
  `Pass` varchar(64) NOT NULL default '',
  `Verification` int(1) default NULL,
  `Standing` int(1) default NULL,
  PRIMARY KEY  (`uID`),
  UNIQUE KEY `userAuthorization_index` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `journalPost`
--

DROP TABLE IF EXISTS `%PREFIX%journalPost`;
CREATE TABLE `%PREFIX%journalPost` (
  `tID` int(10) unsigned zerofill NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `Stamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `Content` text NOT NULL,
  `Title` varchar(128) NOT NULL default '',
  `Notification` int(10) unsigned default NULL,
  `Tags` varchar(128) default NULL,
  `userIcons_Filename` varchar(64) default NULL,
  PRIMARY KEY  (`tID`,`userAuth_uID`),
  KEY `JournalPost_FKIndex1` (`userAuth_uID`),
  KEY `JournalPost_FKIndex2` (`userAuth_uID`),
  CONSTRAINT `journalPost_ibfk_1` FOREIGN KEY (`userAuth_uID`) REFERENCES `%PREFIX%userAuthorization` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userAuthorization`
--

LOCK TABLES `%PREFIX%userAuthorization` WRITE;
/*!40000 ALTER TABLE `%PREFIX%userAuthorization` DISABLE KEYS */;
INSERT INTO `%PREFIX%userAuthorization` VALUES (0000000001,'Admin','*D815428C18AF686BBFCA029CBFBABD06DAD049DC',0,0);
/*!40000 ALTER TABLE `%PREFIX%userAuthorization` ENABLE KEYS */;
UNLOCK TABLES;

DROP TABLE IF EXISTS `%PREFIX%authSessions`;
CREATE TABLE `%PREFIX%authSessions` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `Username` varchar(32) NOT NULL default '',
  `Domain` varchar(128) NOT NULL default '',
  `Identifier` varchar(32) default '00000000000000000000000000000000',
  `Stamp` datetime default NULL,
  `Address` varchar(16) default '0.0.0.0',
  `Host` varchar(128) default NULL,
  `Fullname` varchar(64) default NULL,
  PRIMARY KEY  (`tID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `authTokens`
--

DROP TABLE IF EXISTS `%PREFIX%authTokens`;
CREATE TABLE `%PREFIX%authTokens` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `Username` varchar(32) NOT NULL default '',
  `Domain` varchar(128) NOT NULL default '',
  `Token` varchar(32) default NULL,
  `Stamp` datetime default NULL,
  PRIMARY KEY  (`tID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `authVerification`
--

DROP TABLE IF EXISTS `%PREFIX%authVerification`;
CREATE TABLE `%PREFIX%authVerification` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `Username` varchar(32) NOT NULL default '',
  `Domain` varchar(128) NOT NULL default '',
  `Verified` int(1) default NULL,
  `Address` varchar(16) default '0.0.0.0',
  `Host` varchar(128) default NULL,
  `Active` int(1) default NULL,
  `Stamp` datetime default NULL,
  PRIMARY KEY  (`tID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `commentInformation`
--

DROP TABLE IF EXISTS `%PREFIX%commentInformation`;
CREATE TABLE `%PREFIX%commentInformation` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `rID` int(10) unsigned zerofill default NULL,
  `Context` varchar(32) default NULL,
  `parent_tID` int(10) unsigned default NULL,
  `Subject` varchar(128) default NULL,
  `Body` text,
  `Stamp` datetime default NULL,
  `Owner_Username` varchar(32) NOT NULL default '',
  `Owner_Domain` varchar(128) NOT NULL default '',
  `Owner_Icon` varchar(32) NOT NULL default '',
  `Owner_Address` varchar(16) default '0.0.0.0',
  PRIMARY KEY  (`tID`),
  KEY `commentInformation_referenceIndex` (`rID`,`Context`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `contentArticles`
--

DROP TABLE IF EXISTS `%PREFIX%contentArticles`;
CREATE TABLE `%PREFIX%contentArticles` (
  `tID` int(10) unsigned zerofill NOT NULL auto_increment,
  `Title` varchar(128) default NULL,
  `Full` text,
  `Formatting` int(1) default NULL,
  `Language` char(2) default NULL,
  `Submitted_Username` varchar(64) default NULL,
  `Submitted_Domain` varchar(64) default NULL,
  `Verification` int(1) default '0',
  `Stamp` datetime default NULL,
  `Summary` text,
  PRIMARY KEY  (`tID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `contentPages`
--

DROP TABLE IF EXISTS `%PREFIX%contentPages`;
CREATE TABLE `%PREFIX%contentPages` (
  `tID` int(10) unsigned zerofill NOT NULL auto_increment,
  `Title` varchar(128) default NULL,
  `Output` text,
  `Formatting` int(1) default NULL,
  `Location` varchar(128) default NULL,
  `Template` varchar(32) default NULL,
  `Language` char(2) default NULL,
  `Context` varchar(32) default NULL,
  `Style` text,
  PRIMARY KEY  (`tID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `contentPages`
--

LOCK TABLES `%PREFIX%contentPages` WRITE;
/*!40000 ALTER TABLE `%PREFIX%contentPages` DISABLE KEYS */;
INSERT INTO `%PREFIX%contentPages` VALUES (0000000001,'Frequently Asked Questions','<p><b>FAQ</b> is an <a href=\"/wiki/Abbreviation\" title=\"Abbreviation\">abbreviation</a> for \"Frequently Asked Question(s)\". The term refers to listed questions and answers, all supposed to be frequently asked in some context, and pertaining to a particular topic. \r\n\r\nSince the <a href=\"/wiki/Acronym\" title=\"Acronym\">acronym</a> originated in textual media, its <a href=\"/wiki/Pronunciation\" title=\"Pronunciation\">pronunciation</a> varies; both \"fak\" and \"F.A.Q.\" are commonly heard (and therefore, when used with an indefinite article, it is either \"a FAQ\" or \"an FAQ\"). \r\n\r\nDepending on usage, the term may refer specifically to a single frequently-asked question, or to an assembled list of many questions and their answers. An alternative suggestion is that FAQ is actually a clumsily-constructed <a href=\"/wiki/Three_letter_acronym\" title=\"Three letter acronym\">three letter acronym</a> purported to have come from computer IT specialists, frustrated with answering over and over again the same, perceived stupid questions from computer users, and which secretly stands for the pronunciation \"fah-queue\". \r\n\r\nFor example: \"Please read the FAQ list to ensure your question has not already been answered before bothering the overworked IT department\".</p>',2,'faq','Default_Template.atpl','en','content.faq','#content #faq #container {\r\n  width:562px;\r\n  float:left;\r\n  position:relative;\r\n  left:-75px;\r\n  clear:both;\r\n}\r\n\r\n#content #faq #caption {\r\n  position:relative;\r\n  width:562px;\r\n  float:left;\r\n  left:-75px;\r\n  clear:both;\r\n  font-size:14px;\r\n  font-weight:bold;\r\n}'),(0000000003,'About Us','(More information available at <a href=\'http://appleseed.sourceforge.net\'>appleseed.sourceforge.net</a>)\r\n\r\n  <h1>PROJECT OVERVIEW</h1>\r\n  <br />\r\n\r\n  The Appleseed Project is an effort to create open source \r\n  <a href=\'http://en.wikipedia.org/wiki/Social_networking\'>Social Networking</a> \r\n  software that is based on a distributed model.  For instance, a profile on one \r\n  Appleseed website could \"friend\" a profile on another Appleseed website, and \r\n  the two profiles could interact with each other.\r\n  \r\n  <br /><br />\r\n\r\n  Apart from being distributed, Appleseed will also have a strong focus on\r\n  privacy and security, as well as a commitment to seeing the user as an\r\n  online citizen, as opposed to a consumer to be targetted.  This is in\r\n  stark contrast to current social networking websites, who rely heavily on\r\n  ad placement and data mining of their users.\r\n  \r\n  <br /><br /> \r\n\r\n  The first goal is to create a codebase for basic interaction, such as \r\n  creating profiles, creating and participating in message groups, journals and\r\n  comments, etc.\r\n\r\n  <br /><br />\r\n\r\n  Eventually, Appleseed will encompass many different aspects, from mail to\r\n  messaging to journals/blogs to photo uploads and management.  A module\r\n  architecture is also in the works for even greater extensibility.\r\n\r\n  <br /><br />\r\n\r\n  Development currently uses Object Oriented PHP4, MySQL (InnoDB), XHTML, \r\n  Javascript, and CSS2.  Mozilla/Firefox will be the target platform.\r\n\r\n  <br /><br />\r\n  <h1>PROGRESS - 40% \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      + + + + . . . . . . . . . . . . . . . </h1>\r\n\r\n  April 09, 2006\r\n      &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp;\r\n\r\n  (unreleased)\r\n  <br /><br />\r\n\r\n  I\'ve completed the bulk of the Journaling and Messaging subsystems.  A new\r\n  release is coming very soon.  The test site (<a href=\'http://www.appleseedproject.org\'>www.appleseedproject.org</a>)\r\n  has been updated with the new features.\r\n  <br /><br />\r\n  Next comes a huge code clean, and then the fun part:  Adding friends to your\r\n  friends list.\r\n  \r\n  <br /><br />\r\n  <h1>PROGRESS - 21% \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      + + + + . . . . . . . . . . . . . . . </h1>\r\n\r\n  January 13, 2006\r\n      &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp;\r\n\r\n  (version 0.2.1 beta)\r\n  <br /><br />\r\n\r\n  Appleseed 0.2.1 Beta has been released.  You can download the source\r\n  from the <a href=\'http://sourceforge.net/projects/appleseed/\'>Appleseed\r\n  Sourceforge Project Page</a>.<br /><br />\r\n\r\n  You can also download the file directly here:<br /><br />\r\n  <center><a href=\'http://prdownloads.sourceforge.net/appleseed/appleseed-0.2.1-beta.tar.gz?download\'>appleseed-0.2.1-beta.tar.gz</a></center>\r\n  <br /><br />\r\n\r\n  This version includes a lot of cleanups, and invites and registering an account.\r\n  Version 0.3 is soon to come.\r\n\r\n  <br /><br />\r\n  A test site has also been set up at <a href=\'http://www.appleseedproject.org\'>www.appleseedproject.org</a>.  If you would like to help test,\r\n  please send an email to <a href=\'mailto:michael.chisari@gmail.com\'>michael.chisari@gmail.com</a>\r\n  \r\n  <br /><br />\r\n  <h1>PROGRESS - 20% \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      + + + + . . . . . . . . . . . . . . . </h1>\r\n\r\n  October 03, 2005\r\n      &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp;\r\n\r\n  (initial source release)\r\n  <br /><br />\r\n\r\n  Appleseed 0.2.0 Beta has been released.  You can download the source\r\n  from the <a href=\'http://sourceforge.net/projects/appleseed/\'>Appleseed\r\n  Sourceforge Project Page</a>.<br /><br />\r\n\r\n  You can also download the file directly here:<br /><br />\r\n  <center><a href=\'http://prdownloads.sourceforge.net/appleseed/appleseed-0.2.0.beta.tar.gz?download\'>appleseed-0.2.0.beta.tar.gz</a></center>\r\n  \r\n  <br /><br />\r\n\r\n  <h1>PROGRESS - 18% \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      + + + + . . . . . . . . . . . . . . . </h1>\r\n\r\n  September 21, 2005\r\n      &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp;\r\n\r\n  (source unreleased)\r\n  <br /><br />\r\n  \r\n  Photo management has been added.  You can now create and manage photo albums,\r\n  and upload and manage photos.  A privacy system (for restricting access to\r\n  photo albums, and later, journals and other areas) has been implemented as \r\n  well, and, while still in it\'s infancy, is looking pretty good.  The \r\n  commenting subsystem is also working.\r\n  <br /><br />\r\n\r\n  We\'re looking at a source release very soon now.  I\'ll be finishing up the\r\n  commenting subsystem, cleaning up the code, and giving it a good once-over\r\n  so as not to completely embarrass myself.  And then the fun begins.  I\'m\r\n  also going to be setting up an invite-only site for beta testers.\r\n\r\n  <br /><br />\r\n\r\n  Stay tuned...\r\n  \r\n  <br /><br />\r\n  <h1>PROGRESS - 15% \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      + + + . . . . . . . . . . . . . . . . </h1>\r\n\r\n  July 20, 2005\r\n      &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n  (source unreleased)\r\n  <br /><br />\r\n\r\n  \r\n  Work has resumed.  I\'ve added the beginnings of an invite system, and you can\r\n  upload a new profile photo and icons.  The foundation for photo management is\r\n  slowly being built as well.\r\n  <br /><br />\r\n\r\n  Due to the recent <a href=\'http://slashdot.org/article.pl?sid=05/07/19/179201\'>acquisition</a> of <a href=\'http://www.myspace.com\'>MySpace</a> by\r\n  <a href=\'http://en.wikipedia.org/wiki/Rupert_Murdoch\'>Rupert Murdoch</a>, a\r\n  lot of interest in Appleseed has been sparked.  Given that, I\'m doing my best\r\n  to stop myself from adding features and do a code review so that I can release\r\n  the source much sooner than anticipated.  Stay tuned.\r\n  \r\n  <br /><br />\r\n  <h1>PROGRESS - 10% \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;\r\n\r\n      + + . . . . . . . . . . . . . . . . . </h1>\r\n  May 15, 2005\r\n      &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \r\n  (source unreleased)\r\n  <br /><br />\r\n\r\n  \r\n  This is just an estimate, definitely nothing scientific.  A good amount\r\n  of the foundation has been built and tested, so development is going a lot\r\n  faster now.  The security system has been completed, \"themes\" are in place,\r\n  user logins are working, and profiles can be configured. \r\n\r\n  <br /><br />\r\n\r\n  Work right now is focused on going through and commenting and cleaning up\r\n  code before moving on to the next step, which will be adding and managing\r\n  friends.  At first, only local friends will be able to be added, but the\r\n  hooks are there to add friends from a remote Appleseed site.',5,'about','Default_Template.atpl','en','content.about','#content #about #container {\r\n  width:562px;\r\n  float:left;\r\n  position:relative;\r\n  left:-75px;\r\n  clear:both;\r\n}\r\n\r\n#content #about #caption {\r\n  position:relative;\r\n  width:562px;\r\n  float:left;\r\n  left:-75px;\r\n  clear:both;\r\n  font-size:14px;\r\n  font-weight:bold;\r\n}');
/*!40000 ALTER TABLE `%PREFIX%contentPages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `friendCircles`
--

DROP TABLE IF EXISTS `%PREFIX%friendCircles`;
CREATE TABLE `%PREFIX%friendCircles` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `sID` int(10) unsigned default NULL,
  `Name` varchar(32) default NULL,
  `Description` varchar(255) default NULL,
  PRIMARY KEY  (`tID`),
  KEY `Circles_FKIndex1` (`userAuth_uID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `friendCirclesList`
--

DROP TABLE IF EXISTS `%PREFIX%friendCirclesList`;
CREATE TABLE `%PREFIX%friendCirclesList` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `friendCircles_tID` int(10) unsigned NOT NULL default '0',
  `friendInformation_tID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tID`),
  KEY `friendCirclesList_FKIndex1` (`friendInformation_tID`),
  KEY `friendCirclesList_FKIndex2` (`friendCircles_tID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `friendInformation`
--

DROP TABLE IF EXISTS `%PREFIX%friendInformation`;
CREATE TABLE `%PREFIX%friendInformation` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `sID` int(10) unsigned default NULL,
  `Username` varchar(128) default NULL,
  `Domain` varchar(128) default NULL,
  `Verification` int(1) default NULL,
  `Alias` varchar(128) default NULL,
  `Stamp` datetime default NULL,
  PRIMARY KEY  (`tID`),
  KEY `friendInformation_FKIndex1` (`userAuth_uID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `groupContent`
--

DROP TABLE IF EXISTS `%PREFIX%groupContent`;
CREATE TABLE `%PREFIX%groupContent` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `groupInformation_tID` int(10) unsigned NOT NULL default '0',
  `parent_tID` int(10) unsigned default NULL,
  `Subject` varchar(128) default NULL,
  `Body` text,
  `Stamp` datetime default NULL,
  `Owner_Username` varchar(32) default NULL,
  `Owner_Domain` varchar(128) default NULL,
  `Owner_Icon` varchar(32) default NULL,
  `Owner_Address` varchar(16) default NULL,
  `Views` int(10) unsigned default NULL,
  `Tags` varchar(128) default NULL,
  PRIMARY KEY  (`tID`),
  KEY `groupPost_FKIndex1` (`groupInformation_tID`),
  KEY `groupPost_FKIndex2` (`userAuth_uID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `groupInformation`
--

DROP TABLE IF EXISTS `%PREFIX%groupInformation`;
CREATE TABLE `%PREFIX%groupInformation` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Name` varchar(32) default NULL,
  `Fullname` varchar(128) default NULL,
  `Description` varchar(255) default NULL,
  `Stamp` datetime default NULL,
  `Access` int(10) unsigned default NULL,
  `Tags` varchar(128) default NULL,
  PRIMARY KEY  (`tID`),
  KEY `groupInformation_FKIndex1` (`userAuth_uID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `groupMembers`
--

DROP TABLE IF EXISTS `%PREFIX%groupMembers`;
CREATE TABLE `%PREFIX%groupMembers` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `groupInformation_tID` int(10) unsigned NOT NULL default '0',
  `Username` varchar(64) default NULL,
  `Domain` varchar(128) default NULL,
  `Verification` int(10) unsigned default NULL,
  `Stamp` datetime default NULL,
  PRIMARY KEY  (`tID`),
  KEY `groupMembers_FKIndex1` (`groupInformation_tID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `journalPrivacy`
--

DROP TABLE IF EXISTS `%PREFIX%journalPrivacy`;
CREATE TABLE `%PREFIX%journalPrivacy` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `journalPost_tID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `friendCircles_sID` int(11) default NULL,
  `Access` int(10) unsigned default NULL,
  PRIMARY KEY  (`tID`,`userAuth_uID`,`journalPost_tID`),
  KEY `journalPrivacy_FKIndex1` (`journalPost_tID`,`userAuth_uID`),
  CONSTRAINT `journalPrivacy_ibfk_1` FOREIGN KEY (`journalPost_tID`, `userAuth_uID`) REFERENCES `%PREFIX%journalPost` (`tID`, `userAuth_uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `messageInformation`
--

DROP TABLE IF EXISTS `%PREFIX%messageInformation`;
CREATE TABLE `%PREFIX%messageInformation` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Sender_Username` varchar(32) default NULL,
  `Sender_Domain` varchar(128) default NULL,
  `Identifier` varchar(128) default NULL,
  `Subject` varchar(128) default NULL,
  `Body` text,
  `Sent_Stamp` datetime default NULL,
  `Received_Stamp` datetime default NULL,
  `Standing` int(10) unsigned default NULL,
  `Location` int(11) default NULL,
  PRIMARY KEY  (`tID`),
  KEY `messageInformation_FKIndex1` (`userAuth_uID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `messageLabelList`
--

DROP TABLE IF EXISTS `%PREFIX%messageLabelList`;
CREATE TABLE `%PREFIX%messageLabelList` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `messageLabels_tID` int(10) unsigned NOT NULL default '0',
  `Identifier` varchar(128) default NULL,
  PRIMARY KEY  (`tID`),
  KEY `messageLabelList_FKIndex1` (`messageLabels_tID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `messageLabels`
--

DROP TABLE IF EXISTS `%PREFIX%messageLabels`;
CREATE TABLE `%PREFIX%messageLabels` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Label` varchar(32) default NULL,
  PRIMARY KEY  (`tID`),
  KEY `two_messageLabels_FKIndex1` (`userAuth_uID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `messageNotification`
--

DROP TABLE IF EXISTS `%PREFIX%messageNotification`;
CREATE TABLE `%PREFIX%messageNotification` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Sender_Username` varchar(32) default NULL,
  `Sender_Domain` varchar(128) default NULL,
  `Identifier` varchar(128) default NULL,
  `Subject` varchar(128) default NULL,
  `Stamp` datetime default NULL,
  `Standing` int(10) unsigned default NULL,
  `Location` int(11) default NULL,
  PRIMARY KEY  (`tID`),
  KEY `messageNotification_FKIndex1` (`userAuth_uID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `messageRecipient`
--

DROP TABLE IF EXISTS `%PREFIX%messageRecipient`;
CREATE TABLE `%PREFIX%messageRecipient` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `messageStore_tID` int(10) unsigned NOT NULL default '0',
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  PRIMARY KEY  (`tID`),
  KEY `two_messageRecipient_FKIndex1` (`userAuth_uID`),
  KEY `two_messageRecipient_FKIndex2` (`messageStore_tID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `messageStore`
--

DROP TABLE IF EXISTS `%PREFIX%messageStore`;
CREATE TABLE `%PREFIX%messageStore` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Sender_Username` varchar(32) default NULL,
  `Sender_Domain` varchar(128) default NULL,
  `Identifier` varchar(128) default NULL,
  `Subject` varchar(128) default NULL,
  `Body` text,
  `Stamp` datetime default NULL,
  `Standing` int(10) unsigned default NULL,
  `Location` int(10) unsigned default NULL,
  PRIMARY KEY  (`tID`),
  KEY `messageStore_FKIndex1` (`userAuth_uID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `photoSets`
--

DROP TABLE IF EXISTS `%PREFIX%photoSets`;
CREATE TABLE `%PREFIX%photoSets` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `sID` int(10) unsigned default NULL,
  `Name` varchar(128) default NULL,
  `Directory` varchar(128) default NULL,
  `Description` text,
  `Tags` varchar(128) default NULL,
  `Created` datetime default NULL,
  `Updated` datetime default NULL,
  PRIMARY KEY  (`tID`,`userAuth_uID`),
  KEY `photoSets_FKIndex1` (`userAuth_uID`),
  CONSTRAINT `photoSets_ibfk_1` FOREIGN KEY (`userAuth_uID`) REFERENCES `%PREFIX%userAuthorization` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `photoInformation`
--

DROP TABLE IF EXISTS `%PREFIX%photoInformation`;
CREATE TABLE `%PREFIX%photoInformation` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `photoSets_tID` int(10) unsigned NOT NULL default '0',
  `sID` int(10) unsigned NOT NULL default '0',
  `Filename` varchar(64) default NULL,
  `Height` int(10) unsigned default NULL,
  `Width` int(10) unsigned default NULL,
  `ThumbWidth` int(10) unsigned default NULL,
  `ThumbHeight` int(10) unsigned default NULL,
  `Description` text,
  `Tags` varchar(128) default NULL,
  `Stamp` datetime default NULL,
  `Hint` varchar(6) default NULL,
  PRIMARY KEY  (`tID`,`userAuth_uID`,`photoSets_tID`),
  KEY `photoList_FKIndex1` (`photoSets_tID`,`userAuth_uID`),
  CONSTRAINT `photoInformation_ibfk_1` FOREIGN KEY (`photoSets_tID`, `userAuth_uID`) REFERENCES `%PREFIX%photoSets` (`tID`, `userAuth_uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `photoPrivacy`
--

DROP TABLE IF EXISTS `%PREFIX%photoPrivacy`;
CREATE TABLE `%PREFIX%photoPrivacy` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `photoSets_tID` int(10) unsigned NOT NULL default '0',
  `friendCircles_sID` int(11) default NULL,
  `Access` int(10) unsigned default NULL,
  PRIMARY KEY  (`tID`,`userAuth_uID`,`photoSets_tID`),
  KEY `photoPrivacy_FKIndex1` (`photoSets_tID`,`userAuth_uID`),
  CONSTRAINT `photoPrivacy_ibfk_1` FOREIGN KEY (`photoSets_tID`, `userAuth_uID`) REFERENCES `%PREFIX%photoSets` (`tID`, `userAuth_uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `systemConfig`
--

DROP TABLE IF EXISTS `%PREFIX%systemConfig`;
CREATE TABLE `%PREFIX%systemConfig` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `RequireAge` int(1) default NULL,
  `ExpireMessages` int(10) unsigned default '0',
  `UseInvites` int(1) default NULL,
  `InviteAmount` int(10) unsigned default NULL,
  PRIMARY KEY  (`tID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `systemDefaults`
--

DROP TABLE IF EXISTS `%PREFIX%systemDefaults`;
CREATE TABLE `%PREFIX%systemDefaults` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `Theme` int(10) unsigned default '0',
  `Framework` int(10) unsigned default NULL,
  PRIMARY KEY  (`tID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `systemLogs`
--

DROP TABLE IF EXISTS `%PREFIX%systemLogs`;
CREATE TABLE `%PREFIX%systemLogs` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Entry` varchar(255) default NULL,
  `Stamp` datetime default NULL,
  `Severity` int(1) default '0',
  `Location` varchar(128) default NULL,
  PRIMARY KEY  (`tID`,`userAuth_uID`),
  KEY `systemActivityLog_FKIndex1` (`userAuth_uID`),
  CONSTRAINT `systemLogs_ibfk_1` FOREIGN KEY (`userAuth_uID`) REFERENCES `%PREFIX%userAuthorization` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `systemOptions`
--

DROP TABLE IF EXISTS `%PREFIX%systemOptions`;
CREATE TABLE `%PREFIX%systemOptions` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `Concern` varchar(255) default NULL,
  `Label` varchar(255) default NULL,
  `Value` varchar(16) default NULL,
  `Chosen` int(1) default '0',
  `Language` char(2) default NULL,
  PRIMARY KEY  (`tID`),
  KEY `systemOptions_index` (`Concern`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `systemOptions`
--

LOCK TABLES `%PREFIX%systemOptions` WRITE;
/*!40000 ALTER TABLE `%PREFIX%systemOptions` DISABLE KEYS */;
INSERT INTO `%PREFIX%systemOptions` VALUES (1,'FORMATTING','No Formatting','0',0,NULL),(2,'FORMATTING','ASD Tags Only','1',1,NULL),(3,'FORMATTING','Basic HTML','2',0,NULL),(4,'FORMATTING','Extended HTML','3',0,NULL),(5,'FORMATTING','Secure HTML','4',0,NULL),(6,'FORMATTING','Unprocessed','5',0,NULL),(7,'FORMATTING','Viewable','6',0,NULL),(8,'BOOLEAN','True','1',0,NULL),(9,'BOOLEAN','False','0',1,NULL),(10,'LANGUAGE','English','en',0,NULL),(11,'LANGUAGE','Espanol','es',0,NULL),(12,'LANGUAGE','Francais','fr',0,NULL),(24,'QUESTIONTYPE','Menu','0',1,NULL),(25,'QUESTIONTYPE','Checklist','1',0,NULL),(26,'QUESTIONTYPE','String','2',0,NULL),(31,'QUESTIONVISIBILITY','Yes','1',1,NULL),(32,'QUESTIONVISIBILITY','No','0',0,NULL),(33,'QUESTIONVISIBILITY','Logged In','2',0,NULL),(34,'QUESTIONTYPE','Web Link','3',0,NULL),(35,'QUESTIONTYPE','Linked String','4',0,NULL),(36,'GENDER','No Answer','',1,NULL),(37,'GENDER','Female','0',0,NULL),(38,'GENDER','Male','1',0,NULL),(39,'GENDER','Transgender','2',0,NULL),(40,'MONTH','January','1',1,NULL),(41,'MONTH','February','2',0,NULL),(42,'MONTH','March','3',0,NULL),(43,'MONTH','April','4',0,NULL),(44,'MONTH','May','5',0,NULL),(45,'MONTH','June','6',0,NULL),(46,'MONTH','July','7',0,NULL),(47,'MONTH','August','8',0,NULL),(48,'MONTH','September','9',0,NULL),(49,'MONTH','October','10',0,NULL),(50,'MONTH','November','11',0,NULL),(51,'MONTH','December','12',0,NULL),(53,'QIMSERVICE','No Answer','',1,NULL),(54,'QIMSERVICE','AOL IM','0',0,NULL),(55,'QIMSERVICE','Yahoo! IM','1',0,NULL),(56,'QIMSERVICE','MSN Messenger','2',0,NULL),(57,'QIMSERVICE','Jabber','3',0,NULL),(58,'QSTATUS','No Answer','',1,NULL),(59,'QSTATUS','Single','0',0,NULL),(60,'QSTATUS','Single (Not Looking)','1',0,NULL),(61,'QSTATUS','In A Relationship','2',0,NULL),(62,'QSTATUS','Open Relationship','3',0,NULL),(63,'QSTATUS','Married','4',0,NULL),(64,'QSTATUS','Divorced','5',0,NULL),(65,'QSTATUS','Widowed/Widower','6',0,NULL),(66,'QBODYTYPE','No Answer','',1,NULL),(67,'QBODYTYPE','Slim / Slender','0',0,NULL),(68,'QBODYTYPE','Athletic','1',0,NULL),(69,'QBODYTYPE','Average','2',0,NULL),(70,'QBODYTYPE','A little extra.','3',0,NULL),(71,'QBODYTYPE','More to love!','4',0,NULL),(72,'QASTROSIGN','No Answer','',1,NULL),(73,'QASTROSIGN','Aries','0',0,NULL),(74,'QASTROSIGN','Taurus','1',0,NULL),(75,'QASTROSIGN','Gemini','2',0,NULL),(76,'QASTROSIGN','Cancer','3',0,NULL),(77,'QASTROSIGN','Leo','4',0,NULL),(78,'QASTROSIGN','Virgo','5',0,NULL),(79,'QASTROSIGN','Libra','6',0,NULL),(80,'QASTROSIGN','Scorpio','7',0,NULL),(81,'QASTROSIGN','Sagittarius','8',0,NULL),(82,'QASTROSIGN','Capricorn','9',0,NULL),(83,'QASTROSIGN','Aquarius','10',0,NULL),(84,'QASTROSIGN','Pisces','11',0,NULL),(85,'QSMOKER','No Answer','',1,NULL),(86,'QSMOKER','Yes','0',0,NULL),(87,'QSMOKER','No','1',0,NULL),(88,'QSMOKER','Occasionally','2',0,NULL),(89,'QDRINKER','No Answer','',1,NULL),(90,'QDRINKER','Yes','0',0,NULL),(91,'QDRINKER','No','1',0,NULL),(92,'QDRINKER','Occasionally','2',0,NULL),(93,'QHEREFOR','Friends','0',1,NULL),(94,'QHEREFOR','Networking','1',0,NULL),(95,'QHEREFOR','Dating','2',0,NULL),(96,'QHEREFOR','Serious Relationship','3',0,NULL),(103,'QBLOODTYPE','No Answer','',1,NULL),(104,'QBLOODTYPE','A','A',0,NULL),(105,'QBLOODTYPE','B','B',0,NULL),(106,'QBLOODTYPE','AB','AB',0,NULL),(107,'QBLOODTYPE','O','O',0,NULL),(108,'QBLOODTYPE','Looking for a donor','LOOKING',0,NULL),(109,'QBLOODTYPE','A+','A+',0,NULL),(110,'QBLOODTYPE','B+','B+',0,NULL),(111,'QBLOODTYPE','A-','A-',0,NULL),(112,'QBLOODTYPE','B-','B-',0,NULL),(113,'QBLOODTYPE','O+','O+',0,NULL),(114,'QBLOODTYPE','O-','O-',0,NULL),(115,'ACCOUNTSTANDING','Active','0',1,NULL),(116,'ACCOUNTSTANDING','Inactive','1',0,NULL),(117,'ACCOUNTSTANDING','Suspended','2',0,NULL),(118,'ACCOUNTSTANDING','Deleted','3',0,NULL),(119,'ACCOUNTVERIFICATION','Verified','0',1,NULL),(120,'ACCOUNTVERIFICATION','Pending','1',0,NULL),(121,'ACCOUNTVERIFICATION','Denied','2',0,NULL),(124,'PRIVACY','Allowed','0',1,NULL),(125,'PRIVACY','Screened','1',0,NULL),(126,'PRIVACY','Restricted','2',0,NULL),(127,'PRIVACY','Blocked','3',0,NULL),(128,'RESTRICTIONS','Unrestricted','0',1,NULL),(129,'RESTRICTIONS','No Anonymous','1',0,NULL),(130,'RESTRICTIONS','Friends Only','2',0,NULL),(131,'PHOTOLISTING','Default View','1',1,NULL),(132,'PHOTOLISTING','1 Column','2',0,NULL),(133,'PHOTOLISTING','2 Columns','3',0,NULL),(135,'PHOTOLISTING','4 Columns','4',0,NULL),(136,'PHOTOLISTINGEDITOR','Default View','1',1,NULL),(138,'PHOTOLISTINGEDITOR','1 Column','2',0,NULL),(139,'PHOTOLISTINGEDITOR','2 Columns','3',0,NULL),(141,'PHOTOLISTINGEDITOR','4 Columns','4',0,NULL),(142,'VISIBILITY','Visible','0',1,NULL),(143,'VISIBILITY','Hidden','1',0,NULL),(144,'COMMENTVIEW','Default View','1',1,NULL),(145,'COMMENTVIEW','Nested','2',0,NULL),(146,'COMMENTVIEW','Threaded','3',0,NULL),(147,'COMMENTVIEW','Flat','4',0,NULL),(148,'COMMENTVIEW','Compact','5',0,NULL),(149,'COMMENTVIEWADMIN','Editor View','1',1,NULL),(150,'COMMENTVIEWADMIN','Nested','2',0,NULL),(151,'COMMENTVIEWADMIN','Threaded','3',0,NULL),(152,'COMMENTVIEWADMIN','Flat','4',0,NULL),(153,'COMMENTVIEWADMIN','Compact','5',0,NULL),(154,'QIMSERVICE','Google Talk','4',0,NULL),(155,'MESSAGESVIEW','Default','0',1,NULL),(156,'MESSAGESVIEW','Compact','1',0,NULL),(157,'MESSAGESVIEW','Full','2',0,NULL),(158,'MESSAGESVIEW','Mixed','3',0,NULL),(163,'JOURNALVIEW','Default View','1',1,NULL),(164,'JOURNALVIEW','Single','2',0,NULL),(165,'JOURNALVIEW','Multiple','3',0,NULL),(166,'JOURNALVIEW','Listing','4',0,NULL),(167,'JOURNALVIEWADMIN','Default View','1',0,NULL),(168,'JOURNALVIEWADMIN','Single','2',0,NULL),(169,'JOURNALVIEWADMIN','Multiple','3',0,NULL),(170,'JOURNALVIEWADMIN','Listing','4',0,NULL),(171,'JOURNALVIEWADMIN','Editor View','5',1,NULL),(172,'QSTATUS','It\'s Complicated.','7',0,NULL),(173,'LANGUAGE','Italiano','it',0,NULL),(174,'QSTATUS','Engaged','8',0,NULL),(175,'PHOTOLISTINGEDITOR','Editor View','5',0,NULL),(176,'VERIFICATION','Pending','0',1,NULL),(177,'VERIFICATION','Approved','1',0,NULL),(178,'VERIFICATION','Rejected','2',0,NULL),(179,'NOTIFICATION','On','1',1,NULL),(180,'NOTIFICATION','Off','2',0,NULL),(181,'GROUPMEMBERSHIP','Open To Public','1',1,NULL),(182,'GROUPMEMBERSHIP','Approval Required','2',0,NULL),(183,'GROUPMEMBERSHIP','Invite Only','3',0,NULL),(184,'MESSAGESVIEW','Full','2',0,'en'),(185,'GROUPMEMBERACTION','(no action)','0',1,'en'),(186,'GROUPMEMBERACTION','Remove Member','2',0,'en'),(187,'GROUPPENDINGACTION','(no action)','0',1,'en'),(188,'GROUPPENDINGACTION','Approve Member','1',0,'en'),(189,'GROUPPENDINGACTION','Remove Member','2',0,'en'),(190,'GROUPVIEW','Default View','1',1,'en'),(191,'GROUPVIEW','Nested','2',0,'en'),(192,'GROUPVIEW','Threaded','3',0,'en'),(193,'GROUPVIEW','Flat','4',0,'en'),(194,'GROUPVIEW','Compact','5',0,'en'),(195,'GROUPVIEWADMIN','Editor View','1',1,'en'),(196,'GROUPVIEWADMIN','Nested','2',0,'en'),(197,'GROUPVIEWADMIN','Threaded','3',0,'en'),(198,'GROUPVIEWADMIN','Flat','4',0,'en'),(199,'GROUPVIEWADMIN','Compact','5',0,'en');
/*!40000 ALTER TABLE `%PREFIX%systemOptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `systemStrings`
--

DROP TABLE IF EXISTS `%PREFIX%systemStrings`;
CREATE TABLE `%PREFIX%systemStrings` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `Title` varchar(64) NOT NULL default '',
  `Output` text,
  `Context` varchar(32) default NULL,
  `Formatting` int(1) default NULL,
  `Language` char(2) default NULL,
  PRIMARY KEY  (`tID`),
  KEY `systemStrings_index` (`Title`,`Context`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `systemStrings`
--

LOCK TABLES `%PREFIX%systemStrings` WRITE;
/*!40000 ALTER TABLE `%PREFIX%systemStrings` DISABLE KEYS */;
INSERT INTO `%PREFIX%systemStrings` VALUES (3,'CONFIRM.DELETE','Are you sure you want to delete this record?','ADMIN',0,'en'),(4,'ERROR.BADEMAIL','BAD EMAIL ADDRESS.','ADMIN',5,'en'),(5,'ERROR.CANTWRITE','INSUFFICIENT ACCESS TO WRITE DATA.','ADMIN',0,'en'),(6,'ERROR.DENIED','ACCESS DENIED\r\n\r\nYou do not have sufficient permission to access this section.  If you feel this is in error, please contact <a href=\'mailto:%ADMINEMAIL%\'>%ADMINEMAIL%</a> with any questions.\r\n\r\nThank you,\r\nThe Staff @ %SITEDOMAIN%','ADMIN',2,'en'),(7,'ERROR.ILLEGALCHAR','ILLEGAL CHARACTER \'%ILLEGALCHAR%\' IN \'%FIELDNAME%\'','ADMIN',1,'en'),(8,'ERROR.NOTNULL','FIELD CANNOT BE NULL!','ADMIN',0,'en'),(9,'ERROR.TOOLARGE','FIELD IS TOO LARGE! (> %MAXSIZE%)','ADMIN',1,'en'),(10,'ERROR.TOOSHORT','FIELD IS TOO SHORT! (< %MINSIZE%)','ADMIN',1,'en'),(11,'CONFIRM.DELETEALL','Are you sure you want to delete the selected records?','ADMIN',0,'en'),(12,'ERROR.FROMONE','CANNOT MOVE RECORD #1 UP ANY FARTHER.','ADMIN',0,'en'),(13,'ERROR.REQUIREDCHAR','REQUIRED CHARACTER \'%REQUIREDCHAR%\' NOT FOUND IN \'%FIELDNAME%\'','ADMIN',1,'en'),(14,'ERROR.DUPLICATE','ENTRY FOR \'<asd id=\'global\' name=\'FIELDNAME\' case=\'upper\' />\' ALREADY EXISTS IN DATABASE.','ADMIN',1,'en'),(15,'ERROR.INTEGER','FIELD MUST BE AN INTEGER.','ADMIN',1,'en'),(16,'ERROR.PAGE','AN ERROR HAS OCCURRED WHILE ATTEMPTING TO SAVE.','ADMIN',0,'en'),(17,'LABEL.NEW','ADD NEW','ADMIN',1,'en'),(18,'LABEL.EDIT','EDIT','ADMIN',1,'en'),(19,'OBJECT.TITLE','WELCOME TO THE APPLESEED ADMINISTRATION PAGE!','ADMIN.MAIN',0,'en'),(20,'OBJECT.CONTENT','Here you will find everything you need to run and administer your Appleseed site.  If you have any questions, you can access the Appleseed documentation at <a href=\'http://www.appleseedproject.org/docs/\'>http://www.appleseedproject.org/docs/</a>.\r\n\r\nIf you aren\'t supposed to see this page, please contact the administrator at <a href=\'mailto:%ADMINEMAIL%\'>%ADMINEMAIL%</a> immediately.  All interactions are being logged.\r\n\r\nThank you,\r\nThe Staff @ %SITEDOMAIN%','ADMIN.MAIN',2,'en'),(21,'OBJECT.TITLE','SITE STATISTICS','ADMIN.STATS',1,'en'),(22,'OBJECT.TITLE','CHOOSE A SECTION:','ADMIN.SWITCHES',0,'en'),(23,'OBJECT.CONTENT','WELCOME TO THE SYSTEM CONFIGURATION PAGE.\r\n\r\nHere you can change system and security settings.  Please choose an option from the menu above.','ADMIN.SYSTEM',2,'en'),(24,'MESSAGE.NONE','NO RESULTS FOUND.',NULL,0,'en'),(25,'MESSAGE.SAVE','RECORD #%DATAID% HAS BEEN UPDATED.','ADMIN',1,'en'),(26,'MESSAGE.DELETE','RECORD #%DATAID% HAS BEEN DELETED.','ADMIN',1,'en'),(28,'MESSAGE.DELETEALL','RECORDS %DATALIST% HAVE BEEN DELETED.','ADMIN',1,'en'),(29,'ERROR.NONESELECTED','NO OPTIONS HAVE BEEN SELECTED.  NO ACTION TAKEN.','ADMIN.SYSTEM.OPTIONS',1,'en'),(30,'LABEL.NEW','NEW OPTION','ADMIN.SYSTEM.OPTIONS',5,'en'),(35,'ERROR.NONESELECTED','NO STRINGS HAVE BEEN SELECTED.  NO ACTION TAKEN.','ADMIN.SYSTEM.STRINGS',0,'en'),(38,'LABEL.EDIT','EDIT SYSTEM STRING','ADMIN.SYSTEM.STRINGS',5,'en'),(40,'LABEL.NEW','NEW STRING','ADMIN.SYSTEM.STRINGS',5,'en'),(42,'OBJECT.CONTENT','WELCOME TO THE USER CONFIGURATION PAGE.\r\n\r\nHere you can change user settings.  Please choose an option from the menu above.','ADMIN.USERS',2,'en'),(47,'ERROR.NONESELECTED','NO ACCESS RECORDS HAVE BEEN SELECTED.  NO ACTION TAKEN.','ADMIN.USERS.ACCESS',1,'en'),(49,'LABEL.EDIT','EDIT <asd user=\'%EDITUSERNAME%\' />','ADMIN.USERS.ACCOUNTS',3,'en'),(50,'LABEL.PRESERVE',' (To preserve password, leave this blank). ','ADMIN.USERS.ACCOUNTS',0,'en'),(55,'ERROR.NONESELECTED','NO ACCOUNTS HAVE BEEN SELECTED.  NO ACTION TAKEN.','ADMIN.USERS.ACCOUNTS',1,'en'),(61,'ERROR.NONESELECTED','NO QUESTIONS HAVE BEEN SELECTED.  NO ACTION TAKEN.','ADMIN.USERS.QUESTIONS',1,'en'),(63,'FOOTNOTE.PAGES','Pages: %PAGESLIST%','',2,'en'),(64,'LABEL.COPYRIGHT','Copyleft &copy; 2004-2007 by the Appleseed Collective.  All Rights Reversed.','',3,'en'),(65,'LABEL.ADMINLOGIN','You are currently logged in as <asd user=\'%AUTHUSERNAME%\' popup=\'off\' />','',3,'en'),(67,'BROWSER.TITLE','<asd id=\'global\' name=\'SITEDOMAIN\' case=\'proper\' />',NULL,1,'en'),(68,'BROWSER.REFRESH','\r\n<asd id=\'link\' target=\'%SITEURL%\' text=\'Click here to continue\' style=\'outside\' />','',2,'en'),(69,'LABEL.NEWMESSAGES','%MESSAGECOUNT% new message(s).','',1,'en'),(70,'LABEL.NEWFRIENDS','%FRIENDCOUNT% new friend request(s).','',1,'en'),(71,'LABEL.LOCALLOGIN','You are currently logged in as <asd user=\'%AUTHUSERNAME%\' popup=\'off\' />','',3,'en'),(72,'LABEL.REMOTELOGIN','You are remotely logged in as <asd user=\'%AUTHUSERNAME%\' domain=\'%AUTHDOMAIN%\' />','',3,'en'),(73,'FOOTNOTE.PAGEOF','Page %CURRENTPAGE% of %MAXPAGES%','',1,'en'),(74,'OBJECT.RETURN','<a href=\'%SITEURL%\'>CLICK HERE TO RETURN</a>','SITE.ERROR',2,'en'),(75,'BROWSER.SUBTITLE','- 403 Forbidden','SITE.ERROR.403',0,'en'),(76,'OBJECT.CONTENT','ERROR: Forbidden (403)\r\n\r\nYou do not have access to the requested area.  If you feel that this is incorrect, and the problem persists, please contact the administrator at <a href=\'mailto:%ADMINEMAIL%\'>%ADMINEMAIL%</a>\r\n\r\nThank You,\r\nThe Staff @ Appleseed','SITE.ERROR.403',2,'en'),(77,'OBJECT.TITLE','403 ERROR','SITE.ERROR.403',0,'en'),(78,'OBJECT.TITLE','404 ERROR','SITE.ERROR.404',0,'en'),(79,'OBJECT.CONTENT','ERROR: File Not Found (404)\r\n\r\nThe file you are looking for could not be found.  If you feel that this is incorrect, and the problem persists, please contact the administrator at <a href=\'mailto:%ADMINEMAIL%\'>%ADMINEMAIL%</a>\r\n\r\nThank You,\r\nThe Staff @ Appleseed','SITE.ERROR.404',2,'en'),(80,'BROWSER.SUBTITLE',' - 404 File Not Found','SITE.ERROR.404',0,'en'),(81,'BROWSER.SUBTITLE','- Forbidden Area','SITE.ERROR.FORBIDDEN',0,'en'),(82,'OBJECT.CONTENT','ERROR: Forbidden Area\r\n\r\n\r\nYou do not have access to the requested area.  If you feel that this is incorrect, and the problem persists, please contact the administrator at <a href=\'mailto:%ADMINEMAIL%\'>%ADMINEMAIL%</a>\r\n\r\n\r\nThank You,\r\nThe Staff @ %SITEDOMAIN%\r\n\r\n','SITE.ERROR.FORBIDDEN',2,'en'),(83,'OBJECT.TITLE','SECURITY ERROR','SITE.ERROR.FORBIDDEN',0,'en'),(84,'LABEL.USERNAME','Choose a username:','SITE.JOIN',2,'en'),(85,'LABEL.PASSWORD','Choose a password:','SITE.JOIN',2,'en'),(86,'LABEL.CONFIRM','Confirm your password:','SITE.JOIN',2,'en'),(87,'LABEL.EMAIL','Enter your email address:','SITE.JOIN',2,'en'),(88,'LABEL.INVITE','Invite Code:','SITE.JOIN',2,'en'),(89,'OBJECT.CONTENT','Create a new account by filling out the information below.  Once the account is created, you can log in and set up your profile, and upload a profile photo and user icons.','SITE.JOIN',5,'en'),(90,'LABEL.FULLNAME','Enter your full name:','SITE.JOIN',5,'en'),(91,'LABEL.ZIPCODE','Enter your zip/postal code:','SITE.JOIN',5,'en'),(92,'OBJECT.TITLE','JOIN <asd id=\'global\' name=\'SITEDOMAIN\' case=\'upper\' />','SITE.JOIN',1,'en'),(93,'ERROR.PAGE','Error creating account.','SITE.JOIN',5,'en'),(94,'ERROR.NOTNULL','Do not leave %FIELDNAME% blank.','SITE.JOIN',3,'en'),(95,'ERROR.BADINVITE','Your invitation is not valid for this email address.','SITE.JOIN',5,'en'),(96,'ERROR.BADEMAIL','This is not a valid email address.','SITE.JOIN',5,'en'),(97,'ERROR.NOMATCH','These passwords do not match.','SITE.JOIN',5,'en'),(98,'ERROR.TOOSHORT','%FIELDNAME% is too short.','SITE.JOIN',3,'en'),(99,'ERROR.ILLEGALCHAR','Letters and numbers only, no spaces.','SITE.JOIN',5,'en'),(100,'ERROR.DUPLICATE','Somebody already has this username.  Try another.','SITE.JOIN',5,'en'),(101,'MESSAGE.SUCCESS','Your Account Has Been Created!','SITE.JOIN',5,'en'),(102,'ERROR.BADPASS','Invalid password.  Please try another.','SITE.JOIN',5,'en'),(103,'OBJECT.CONTENT','\r\nWelcome to Appleseed @ Nefac.Net!\r\n\r\nYour new account has been created!  Login above to set up your profile.\r\n','SITE.JOIN.CONFIRM',2,'en'),(104,'OBJECT.TITLE','WHY SHOULD YOU JOIN <asd id=\'global\' name=\'SITEDOMAIN\' case=\'upper\' />?','SITE.JOIN.INFO',1,'en'),(105,'OBJECT.CONTENT','Why You Should Join <asd id=\'global\' name=\'SITETITLE\' case=\'proper\' />.','SITE.JOIN.INFO',2,'en'),(106,'ERROR.FAILED','Login Failed.  Please Try Again.','SITE.LOGIN',0,'en'),(107,'ERROR.PAGE','Login Failed.  Please Try Again.','SITE.LOGIN',5,'en'),(108,'ERROR.COOKIE','Cannot write cookie.  Please try again.','SITE.LOGIN',0,'en'),(109,'ERROR.DISABLED','Your account has been disabled.  Please read the <a href=\'/faq/\'>FAQ</a> to find out why.','SITE.LOGIN',2,'en'),(110,'ERROR.INACTIVE','Your account is currently inactive.  Please read the <a href=\'/faq/\'>FAQ</a> for more information.','SITE.LOGIN',2,'en'),(111,'ERROR.NOTNULL','Do not leave any fields blank.  Please try again.','SITE.LOGIN',2,'en'),(112,'ERROR.ILLEGALCHAR','Illegal character \'%ILLEGALCHAR%\' in field \'%FIELDNAME%\'','SITE.LOGIN',3,'en'),(113,'ERROR.TOOSHORT','%FIELDNAME% is too short.','SITE.LOGIN',3,'en'),(114,'ERROR.TOOLARGE','%FIELDNAME% is too large.','SITE.LOGIN',3,'en'),(115,'ERROR.REQUIREDCHAR','Use a full email address to login.','SITE.LOGIN',4,'en'),(116,'ERROR.BADEMAIL','This is an invalid email address.','SITE.LOGIN',2,'en'),(117,'LABEL.PASSWORD','Password:','SITE.LOGIN',5,'en'),(118,'LABEL.USERNAME','Username:','SITE.LOGIN',5,'en'),(119,'LABEL.REMEMBER','Remember Me:','SITE.LOGIN',5,'en'),(120,'LABEL.LOCATION','Remote Address (<i>username@domain</i>):','SITE.LOGIN',5,'en'),(121,'INFO.LOCATION','NOTE: You must be logged in to your main appleseed site for remote login to work.','SITE.LOGIN',5,'en'),(122,'OBJECT.TITLE','NEWEST GROUPS','SITE.MAIN.GROUPS',0,'en'),(123,'OBJECT.VIEW','VIEW NEWEST GROUPS','SITE.MAIN.GROUPS',0,'en'),(124,'OBJECT.TITLE','ABOUT THIS SITE:','SITE.MAIN.INFO',0,'en'),(125,'OBJECT.TITLE','SEND AN INVITE','SITE.MAIN.INVITE',0,'en'),(126,'LABEL.EMAIL','EMAIL:','SITE.MAIN.INVITE',0,'en'),(127,'OBJECT.CONTENT','You have <b>%INVITEAMOUNT%</b> invites left.  Enter an email address below to invite a friend to join the site.','SITE.MAIN.INVITE',3,'en'),(128,'OBJECT.TITLE','RECENTLY UPDATED JOURNALS','SITE.MAIN.JOURNALS',0,'en'),(129,'OBJECT.VIEW','VIEW NEWEST JOURNALS','SITE.MAIN.JOURNALS',0,'en'),(130,'LABEL.PASSWORD','Password:','SITE.MAIN.LOGIN',5,'en'),(131,'LABEL.REMEMBER','Remember Me:','SITE.MAIN.LOGIN',0,'en'),(132,'LABEL.USERNAME','Username:','SITE.MAIN.LOGIN',0,'en'),(133,'OBJECT.TITLE','LOGIN','SITE.MAIN.LOGIN',0,'en'),(134,'OBJECT.TITLE','NEWEST PHOTOS','SITE.MAIN.PHOTOS',0,'en'),(135,'OBJECT.VIEW','VIEW NEWEST PHOTOS','SITE.MAIN.PHOTOS',0,'en'),(136,'OBJECT.TITLE','NEWEST USERS','SITE.MAIN.USERS',1,'en'),(137,'OBJECT.VIEW','VIEW NEWEST USERS','SITE.MAIN.USERS',0,'en'),(139,'LABEL.STAMP','%COMMENTDATE% at %COMMENTTIME%','USER.COMMENTS',1,'en'),(140,'LABEL.BYLINE','by %COMMENTAUTHOR%','USER.COMMENTS',3,'en'),(141,'LABEL.NEW','NEW COMMENT','USER.COMMENTS',5,'en'),(142,'LABEL.SUBJECT','SUBJECT','USER.COMMENTS',5,'en'),(143,'LABEL.BODY','BODY','USER.COMMENTS',1,'en'),(144,'LABEL.ADD','ADD A COMMENT','USER.COMMENTS',1,'en'),(145,'LABEL.ICON','USER ICON','USER.COMMENTS',5,'en'),(146,'OBJECT.TITLE','READ COMMENTS','USER.COMMENTS',1,'en'),(147,'LABEL.REPLY','REPLY TO %PARENTAUTHOR%','USER.COMMENTS',3,'en'),(148,'INFO.REPLY','%PARENTBODY%','USER.COMMENTS',2,'en'),(149,'LABEL.SUBJECTPREFIX','Re:','USER.COMMENTS',1,'en'),(150,'ERROR.PAGE','Unable to save this comment.  See below for errors.','USER.COMMENTS',5,'en'),(151,'ERROR.NOTNULL','You can\'t leave the \'%FIELDNAME%\' field blank.','USER.COMMENTS',1,'en'),(152,'CONFIRM.CANCEL','All changes will be lost!  Are you sure?',NULL,5,'en'),(153,'CONFIRM.DELETE','Are you sure you want to delete this comment?','USER.COMMENTS',5,'en'),(154,'ERROR.NONE','No comments have been posted.','USER.COMMENTS',5,'en'),(155,'LABEL.THREAD','%COMMENTLINK% by %COMMENTAUTHOR%  on %COMMENTDATE% at %COMMENTTIME%','USER.COMMENTS',3,'en'),(156,'MESSAGE.NONE','No friends were found.','USER.FRIENDS',5,'en'),(157,'ERROR.ALREADY','%REQUESTNAME% is already in your friends list!','USER.FRIENDS',1,'en'),(158,'CONFIRM.DELETEALL','Are you sure you want to delete these profiles from your friends list?','USER.FRIENDS',5,'en'),(159,'LABEL.FRIENDS','Friends Of <asd id=\'global\' name=\'FOCUSFULLNAME\' case=\'proper\' />','USER.FRIENDS',1,'en'),(160,'MESSAGE.ADDED','\'<asd id=\'global\' name=\'REQUESTEDUSER\' case=\'proper\' />\' has been added to your friends list.','USER.FRIENDS',1,'en'),(161,'CONFIRM.DENY','Are you sure you want to deny this friends request?','USER.FRIENDS',5,'en'),(162,'MESSAGE.APPROVED','The friend request from <asd id=\'global\' name=\'NEWFRIEND\' case=\'proper\' /> has been approved.','USER.FRIENDS',1,'en'),(163,'MESSAGE.REQUEST','<asd id=\'global\' name=\'REQUESTEDUSER\' case=\'proper\' /> has been sent a friend request.','USER.FRIENDS',1,'en'),(164,'ERROR.NONESELECTED','No friends were selected.','USER.FRIENDS',5,'en'),(165,'MESSAGE.DELETEALL','The selected profiles have been removed from your friends list.','USER.FRIENDS',5,'en'),(166,'MESSAGE.DENIED','Friend request from %DENIEDNAME% has been denied.','USER.FRIENDS',1,'en'),(167,'LABEL.EDIT','EDIT FRIEND','USER.FRIENDS',5,'en'),(168,'INFO.ALIAS','(This alias only shows up on your friends list)','USER.FRIENDS',5,'en'),(169,'LABEL.ALIAS','Alias:','USER.FRIENDS',5,'en'),(170,'CONFIRM.DELETE','Are you sure you want to remove this friend?','USER.FRIENDS',5,'en'),(172,'MESSAGE.SAVE','Friend information has been saved.','USER.FRIENDS',5,'en'),(173,'MESSAGE.DELETE','The profile for %FRIENDUSERNAME% has been removed from your friends list.','USER.FRIENDS',1,'en'),(174,'LABEL.CIRCLES','Member of:','USER.FRIENDS',5,'en'),(175,'LABEL.REMOVE','Remove From Circle:','USER.FRIENDS',5,'en'),(176,'LABEL.APPLY','Add To Circle:','USER.FRIENDS',5,'en'),(177,'MESSAGE.APPLY','This friend has been added to \'%APPLYCIRCLENAME%\'.','USER.FRIENDS',1,'en'),(178,'MESSAGE.REMOVE','This friend has been removed from \'%REMOVECIRCLENAME%\'.','USER.FRIENDS',1,'en'),(179,'MAIL.BODY','Hello, %REQUESTUSER%.\r\n\r\n%REQUESTEDUSER% has approved your friend request!\r\n\r\nClick here to view your Appleseed friends:\r\n%FRIENDSURL%\r\n\r\n--\r\n\r\nAppleseedProject.org\r\nCopyleft (c) 2004-2007 by the Appleseed Collective. All Rights Reversed.','USER.FRIENDS.APPROVE',1,'en'),(180,'MAIL.SUBJECT','%REQUESTEDUSER% has accepted!','USER.FRIENDS.APPROVE',1,'en'),(181,'MAIL.FROM','friends@%SITEDOMAIN%','USER.FRIENDS.APPROVE',1,'en'),(182,'LABEL.CIRCLES','Edit your friend circles.','USER.FRIENDS.CIRCLES',5,'en'),(183,'LABEL.COUNT','%FRIENDSCOUNT% friend(s).','USER.FRIENDS.CIRCLES',1,'en'),(184,'CONFIRM.DELETE','Are you sure you want to delete this circle?','USER.FRIENDS.CIRCLES',5,'en'),(185,'MESSAGE.NONE','No friends circles have been created.','USER.FRIENDS.CIRCLES',5,'en'),(186,'CONFIRM.DELETEALL','Are you sure you want to delete these friend circles?','USER.FRIENDS.CIRCLES',5,'en'),(187,'LABEL.COUNT','%FRIENDSCOUNT% Friend(s)','USER.FRIENDS.CIRCLES',1,'en'),(188,'LABEL.EDIT','Edit this friends circle.','USER.FRIENDS.CIRCLES',5,'en'),(189,'LABEL.NAME','Circle Name:','USER.FRIENDS.CIRCLES',5,'en'),(190,'CONFIRM.DELETE','Are you sure you want to delete this friends circle?','USER.FRIENDS.CIRCLES',5,'en'),(192,'ERROR.PAGE','Unable to save this circle.  See below for errors.','USER.FRIENDS.CIRCLES',5,'en'),(193,'MESSAGE.SAVE','Friends circle \'<asd id=\'global\' name=\'CIRCLENAME\' case=\'proper\' />\' has been saved.','USER.FRIENDS.CIRCLES',1,'en'),(194,'LABEL.DESCRIPTION','Description:','USER.FRIENDS.CIRCLES',5,'en'),(195,'LABEL.NEW','Create a new friends circle.','USER.FRIENDS.CIRCLES',5,'en'),(196,'MESSAGE.NEW','Friends circle \'<asd id=\'global\' name=\'CIRCLENAME\' case=\'proper\' />\' has been added.','USER.FRIENDS.CIRCLES',1,'en'),(197,'MESSAGE.DELETEALL','The selected friends circles have been deleted.','USER.FRIENDS.CIRCLES',5,'en'),(198,'ERROR.NOTNULL','Do not leave this field blank.','USER.FRIENDS.CIRCLES',5,'en'),(199,'ERROR.NONESELECTED','No friend circles were selected.','USER.FRIENDS.CIRCLES',5,'en'),(200,'ERROR.FROMONE','Cannot move the first friends circle up any farther.','USER.FRIENDS.CIRCLES',5,'en'),(201,'ERROR.CANTWRITE','INSUFFICIENT ACCESS TO MODIFY FRIENDS CIRCLES.','USER.FRIENDS.CIRCLES',5,'en'),(202,'MAIL.BODY','Hello, %REQUESTUSER%.\r\n\r\n%REQUESTEDUSER% has removed you from their friends list.\r\n\r\nClick here to view your Appleseed friends:\r\n%FRIENDSURL%\r\n\r\n--\r\n\r\nAppleseedProject.org\r\nCopyleft (c) 2004-2007 by the Appleseed Collective. All Rights Reversed.','USER.FRIENDS.DELETE',1,'en'),(203,'MAIL.SUBJECT','%REQUESTEDUSER% has removed you as a friend.','USER.FRIENDS.DELETE',1,'en'),(204,'MAIL.FROM','friends@%SITEDOMAIN%','USER.FRIENDS.DELETE',1,'en'),(205,'MAIL.FROM','friends@%SITEDOMAIN%','USER.FRIENDS.DENY',1,'en'),(206,'MAIL.SUBJECT','%REQUESTEDUSER% has denied your friend request!','USER.FRIENDS.DENY',1,'en'),(207,'MAIL.BODY','Hello, %REQUESTUSER%.\r\n\r\n%REQUESTEDUSER% has denied your friend request.\r\n\r\nClick here to view your Appleseed friends:\r\n%FRIENDSURL%\r\n\r\n--\r\n\r\nAppleseedProject.org\r\nCopyleft (c) 2004-2007 by the Appleseed Collective. All Rights Reversed.','USER.FRIENDS.DENY',1,'en'),(208,'MAIL.FROM','friends@%SITEDOMAIN%','USER.FRIENDS.REQUEST',1,'en'),(209,'MAIL.SUBJECT','%REQUESTUSER% would like to be added as one of your friends! ','USER.FRIENDS.REQUEST',1,'en'),(210,'MAIL.BODY','Hello, %REQUESTEDUSER%.\r\n\r\n%REQUESTUSER% would like to be added to your friends list.\r\n\r\nClick here to approve your Appleseed friends requests:\r\n<a href=\'%FRIENDSURL%\'>%FRIENDSURL%</a>\r\n\r\n--\r\n\r\nAppleseedProject.org\r\nCopyleft (c) 2004-2007 by the Appleseed Collective. All Rights Reversed.','USER.FRIENDS.REQUEST',3,'en'),(211,'LABEL.STATS','STATISTICS FOR <asd id=\'global\' name=\'FOCUSFULLNAME\' case=\'upper\' />','USER.INFO',1,'en'),(212,'MAIL.SUBJECT','%COMMENTINGUSER% has left a comment on your profile.','USER.INFO.COMMENTS',1,'en'),(213,'MAIL.FROM','comments@%SITEDOMAIN%','USER.INFO.COMMENTS',5,'en'),(214,'MAIL.BODY','Hello, %COMMENTEDUSER%.\r\n\r\n%COMMENTINGUSER% has added a comment to your profile.\r\n\r\nClick here to view your Appleseed comments:\r\n%INFOURL%\r\n\r\n--\r\n\r\nAppleseedProject.org\r\nCopyleft (c) 2004-2007 by the Appleseed Collective. All Rights Reversed.','USER.INFO.COMMENTS',1,'en'),(215,'OBJECT.TITLE','INVITE A FRIEND','USER.INVITE',5,'en'),(216,'OBJECT.CONTENT','You have <b>%INVITECOUNT%</b> invite(s) available!  Enter an email address below and click the \'invite\' button to tell a friend about <asd id=\'global\' name=\'SITETITLE\' case=\'proper\' />.','USER.INVITE',3,'en'),(217,'ERROR.BADEMAIL','This is not a valid email address.','USER.INVITE',2,'en'),(218,'ERROR.DUPLICATE','This user has already been invited.','USER.INVITE',5,'en'),(219,'ERROR.GENERAL','A general system error has occurred.','USER.INVITE',5,'en'),(220,'MESSAGE.SENT','Your Invitation Has Been Sent!','USER.INVITE',5,'en'),(221,'ERROR.PAGE','Unable to send invitation.','USER.INVITE',5,'en'),(222,'OBJECT.CONTENT','Congratulations!  You have invited <u>%RECIPIENT%</u> to <asd id=\'global\' name=\'SITETITLE\' case=\'proper\' />.  \r\n\r\nThey will recieve an email with instructions on how to sign up for an account, and you will recieve an email when their new account is created.','USER.INVITE.CONFIRM',3,'en'),(223,'MESSAGE.NONE','No journal entries were found.','USER.JOURNAL',5,'en'),(224,'LABEL.NEW','Create a new journal entry.','USER.JOURNAL',5,'en'),(225,'LABEL.TITLE','Title:','USER.JOURNAL',5,'en'),(226,'LABEL.CONTENT','Content:','USER.JOURNAL',5,'en'),(227,'LABEL.TAGLIST','<strong>Tags:</strong> %TAGS%','USER.JOURNAL',3,'en'),(228,'LABEL.ICON','User Icon:','USER.JOURNAL',5,'en'),(229,'LABEL.EDIT','Edit this journal entry.','USER.JOURNAL',1,'en'),(231,'ERROR.PAGE','Unable to save this journal entry.  See below for errors.','USER.JOURNAL',5,'en'),(232,'ERROR.NOTNULL','Do not leave \'%FIELDNAME%\' blank.','USER.JOURNAL',1,'en'),(233,'ERROR.ILLEGALCHAR','You cannot use the \'%ILLEGALCHAR%\' character in \'%FIELDNAME%\'.','USER.JOURNAL',1,'en'),(234,'CONFIRM.DELETEALL','Are you sure you want to delete these journal entries?','USER.JOURNAL',5,'en'),(235,'CONFIRM.DELETE','Are you sure you want to delete this journal entry?','USER.JOURNAL',5,'en'),(236,'MESSAGE.DELETE','This journal entry has been deleted.','USER.JOURNAL',5,'en'),(237,'MESSAGE.DELETEALL','The selected journal entries have been deleted.','USER.JOURNAL',5,'en'),(238,'LABEL.POSTED','Posted On:','USER.JOURNAL',5,'en'),(239,'LABEL.TAGS','Tags:','USER.JOURNAL',5,'en'),(240,'LABEL.COUNT','%COMMENTCOUNT% comment(s).','USER.JOURNALS',1,'en'),(241,'ERROR.CANTWRITE','INSUFFICIENT ACCESS TO MODIFY JOURNAL.','USER.JOURNALS',0,'en'),(242,'LABEL.BACK','BACK TO <asd id=\'link\' target=\'%BACKTARGET%\' text=\'%MESSAGELOCATION%\' />','USER.MESSAGES',3,'en'),(243,'MESSAGE.NONE','No messages were found.','USER.MESSAGES',5,'en'),(244,'LABEL.COMPOSE','COMPOSE A NEW MESSAGE','USER.MESSAGES',5,'en'),(245,'LABEL.SUBJECT','Subject:','USER.MESSAGES',5,'en'),(246,'LABEL.BODY','Enter your message:','USER.MESSAGES',5,'en'),(247,'LABEL.RECIPIENT','To:','USER.MESSAGES',1,'en'),(248,'LABEL.INBOX','INBOX','USER.MESSAGES',5,'en'),(250,'ERROR.ACCESS','Access to message #%MESSAGEID% was denied.  Is this a hack attempt?','USER.MESSAGES',1,'en'),(251,'MESSAGE.SENT','Your message has been sent.','USER.MESSAGES',5,'en'),(252,'MESSAGE.UNREAD','This message has been marked as \'unread.\'','USER.MESSAGES',5,'en'),(253,'MESSAGE.INBOX','This message has been moved to your inbox.','USER.MESSAGES',5,'en'),(254,'MESSAGE.TRASH','This message has been moved to the trash.','USER.MESSAGES',5,'en'),(255,'MESSAGE.ARCHIVE','This message has been archived.','USER.MESSAGES',5,'en'),(256,'MESSAGE.DELETE','This message has been deleted and cannot be restored.','USER.MESSAGES',5,'en'),(257,'MESSAGE.SPAM','This message has been marked as spam.','USER.MESSAGES',5,'en'),(258,'MESSAGE.DELETEALL','%MESSAGECOUNT% message(s) have been deleted.','USER.MESSAGES',1,'en'),(259,'ERROR.NONESELECTED','No messages have been selected.','USER.MESSAGES',5,'en'),(260,'MESSAGE.SAVED','Message has been saved to the \'Drafts\' folder.','USER.MESSAGES',5,'en'),(261,'LABEL.RESTORE','RESTORE FROM DRAFT.','USER.MESSAGES',5,'en'),(262,'LABEL.NOSUBJECT','(no subject)','USER.MESSAGES',5,'en'),(263,'LABEL.RE','Re: ','USER.MESSAGES',5,'en'),(264,'MAIL.FROM','messages@%SITEDOMAIN%','USER.MESSAGES',1,'en'),(265,'MAIL.SUBJECT','New message from %SENDERNAME% on <asd id=\'global\' name=\'SITEDOMAIN\' case=\'proper\' />','USER.MESSAGES',1,'en'),(266,'MAIL.BODY','%RECIPIENTFULLNAME%,\r\n\r\nYou have a new message from %SENDERNAME% on AppleseedProject.org!\r\n\r\nClick here to read your Appleseed messages:\r\n%MESSAGESURL%\r\n\r\n--\r\n\r\nAppleseedProject.org\r\nCopyleft (c) 2004-2007 by the Appleseed Collective. All Rights Reversed.','USER.MESSAGES',1,'en'),(267,'LABEL.QUOTE','\r\n\r\n> From: \"%AUTHORFULLNAME%\" (%AUTHORUSERNAME%@%AUTHORDOMAIN%)\r\n> Date: %MESSAGEDATE%\r\n>\r\n','USER.MESSAGES',1,'en'),(268,'OBJECT.TITLE','FOLDERS','USER.MESSAGES.FOLDERS',1,'en'),(269,'LABEL.INBOX','<a href=\'/profile/%FOCUSUSERNAME%/messages/inbox/\'>Inbox</a>','USER.MESSAGES.FOLDERS',3,'en'),(270,'LABEL.SENT','<a href=\'/profile/%FOCUSUSERNAME%/messages/sent/\'>Sent</a>','USER.MESSAGES.FOLDERS',3,'en'),(271,'LABEL.DRAFTS','<a href=\'/profile/%FOCUSUSERNAME%/messages/drafts/\'>Drafts</a>','USER.MESSAGES.FOLDERS',3,'en'),(272,'LABEL.ALL','<a href=\'/profile/%FOCUSUSERNAME%/messages/all/\'>All Messages</a>','USER.MESSAGES.FOLDERS',3,'en'),(273,'LABEL.SPAM','<a href=\'/profile/%FOCUSUSERNAME%/messages/spam/\'>Spam</a>','USER.MESSAGES.FOLDERS',3,'en'),(274,'LABEL.TRASH','<a href=\'/profile/%FOCUSUSERNAME%/messages/trash/\'>Trash</a>','USER.MESSAGES.FOLDERS',3,'en'),(275,'LABEL.NEW','<a href=\'/profile/%FOCUSUSERNAME%/messages/new/\'>Create Message</a>','USER.MESSAGES.FOLDERS',3,'en'),(276,'OBJECT.TITLE','LABELS','USER.MESSAGES.LABELS',1,'en'),(277,'LABEL.LABELNAME','<a href=\'/profile/%FOCUSUSERNAME%/messages/%LABELNAME%/\'>%LABELNAME%</a>','USER.MESSAGES.LABELS',3,'en'),(278,'LABEL.APPLY','Apply Label:','USER.MESSAGES',5,'en'),(279,'LABEL.REMOVE','Remove Label:','USER.MESSAGES',5,'en'),(280,'LABEL.NEW','New Label','USER.MESSAGES.LABELS',5,'en'),(281,'MESSAGE.APPLY','This message has been labeled \'%APPLYLABELNAME%\'','USER.MESSAGES',1,'en'),(282,'MESSAGE.REMOVE','Label \'%REMOVELABELNAME%\' has been removed.','USER.MESSAGES',1,'en'),(283,'LABEL.COUNT','%MESSAGECOUNT% message(s).','USER.MESSAGES.LABELS',1,'en'),(284,'CONFIRM.DELETE','Are you sure you want to delete this label?','USER.MESSAGES.LABELS',5,'en'),(285,'ERROR.NOTNULL','Do not leave this field blank!','USER.MESSAGES.LABELS',5,'en'),(286,'ERROR.PAGE','Could not modify your labels.  See below for more information.','USER.MESSAGES.LABELS',5,'en'),(287,'MESSAGE.NONE','No labels were found.','USER.MESSAGES.LABELS',5,'en'),(288,'LABEL.EDIT','EDIT YOUR LABELS','USER.MESSAGES.LABELS',5,'en'),(289,'ERROR.ILLEGALCHAR','Don\'t use the %ILLEGALCHAR% character when naming a label.','USER.MESSAGES.LABELS',1,'en'),(290,'OBJECT.CONTENT','Here you can change all of your settings for this site.  Click on a label below to open up that section of options.  You can use basic HTML for your profile questions and description.  More information is available in the <a href=\'/faq/\'>FAQ</a>.','USER.OPTIONS',2,'en'),(291,'LABEL.GENERAL','GENERAL PROFILE','USER.OPTIONS',5,'en'),(292,'LABEL.ICONS','USER ICONS','USER.OPTIONS',2,'en'),(293,'LABEL.JOURNAL','JOURNAL OPTIONS','USER.OPTIONS',2,'en'),(294,'LABEL.MESSAGES','MESSAGE OPTIONS','USER.OPTIONS',2,'en'),(295,'LABEL.PHOTOS','PHOTO OPTIONS','USER.OPTIONS',2,'en'),(296,'LABEL.FRIENDS','FRIEND OPTIONS','USER.OPTIONS',2,'en'),(297,'LABEL.GROUPS','GROUP OPTIONS','USER.OPTIONS',2,'en'),(298,'LABEL.CONFIG','CUSTOM SETTINGS','USER.OPTIONS',2,'en'),(299,'ERROR.DUPLICATE','<asd id=\'global\' name=\'FIELDNAME\' case=\'proper\' /> already exists. Please choose another..','USER.OPTIONS',1,'en'),(300,'ERROR.NOTNULL','Do not leave the %FIELDNAME% field blank.','USER.OPTIONS',1,'en'),(301,'LABEL.SECURITY','SECURITY AND PRIVACY','USER.OPTIONS',2,'en'),(302,'ERROR.PAGE','Unable to save your profile options.','USER.OPTIONS',0,'en'),(303,'ERROR.ILLEGALCHAR','%FIELDNAME% cannot include the %ILLEGALCHAR% character.','USER.OPTIONS',1,'en'),(304,'ERROR.TOOLARGE','%FIELDNAME% can\'t be more than %MAXSIZE% characters.','USER.OPTIONS',1,'en'),(305,'ERROR.CANTWRITE','INSUFFICIENT ACCESS TO MODIFY USER OPTIONS.','USER.OPTIONS',0,'en'),(306,'LABEL.EMAIL','Enter your email address:','USER.OPTIONS.GENERAL',5,'en'),(307,'LABEL.FULLNAME','Enter your full name:','USER.OPTIONS.GENERAL',2,'en'),(308,'LABEL.GENDER','Choose a gender:','USER.OPTIONS.GENERAL',2,'en'),(309,'LABEL.BIRTHDAY','Enter your birthday information.','USER.OPTIONS.GENERAL',2,'en'),(310,'LABEL.QUESTION','%FOCUSQUESTION%','USER.OPTIONS.GENERAL',2,'en'),(311,'MESSAGE.SAVE','General options have been saved.','USER.OPTIONS.GENERAL',0,'en'),(312,'LABEL.DESCRIPTION','Enter a custom description of yourself for your info page (HTML is allowed).','USER.OPTIONS.GENERAL',2,'en'),(313,'LABEL.ZIPCODE','Enter your zip/postal code:','USER.OPTIONS.GENERAL',2,'en'),(314,'LABEL.USERNAME','Enter your username:','USER.OPTIONS.GENERAL',2,'en'),(315,'INFO.USERNAME','<i>WARNING</i>: Modifying this field will change your messaging and profile address!','USER.OPTIONS.GENERAL',2,'en'),(316,'ERROR.ONELEFT','You must have at least one icon uploaded.','USER.OPTIONS.ICONS',5,'en'),(317,'LABEL.PROFILE','<b>PROFILE PHOTO</b>\r\n\r\nChoose which profile photo you would like to upload.  The image you upload will be resized to fit the profile photo window, which is %PROFILEPHOTOX% pixels wide.','USER.OPTIONS.ICONS',3,'en'),(318,'ERROR.TOOBIG','The file you uploaded was greater than %MAXSIZE%.  Try another file.','USER.OPTIONS.ICONS',1,'en'),(319,'ERROR.NOTIMAGE','The file you uploaded is not a valid image file.  Try again.','USER.OPTIONS.ICONS',1,'en'),(320,'ERROR.CANTSAVE','Could not save this image.  If problem persists, contact the administrator.','USER.OPTIONS.ICONS',1,'en'),(321,'ERROR.WRONGTYPE','The image you uploaded is not a GIF, JPEG, or PNG file.  Try again.','USER.OPTIONS.ICONS',1,'en'),(322,'ERROR.WRONGSIZE','The image you uploaded is bigger than %MAXWIDTH%x%MAXHEIGHT%.  Try again.','USER.OPTIONS.ICONS',1,'en'),(323,'ERROR.NOUPLOAD','No file was uploaded.  Try again.','USER.OPTIONS.ICONS',1,'en'),(324,'ERROR.NOTEMP','No temporary directory is available.  Please contact the administrator.','USER.OPTIONS.ICONS',1,'en'),(325,'ERROR.NOFILE','No file was uploaded.  Please try again.','USER.OPTIONS.ICONS',1,'en'),(326,'ERROR.PARTIAL','Only a partial file was uploaded.  Try again.','USER.OPTIONS.ICONS',1,'en'),(327,'MESSAGE.UPLOADED','Image has been successfully uploaded.','USER.OPTIONS.ICONS',0,'en'),(328,'LABEL.CURRENT','CURRENT ICONS','USER.OPTIONS.ICONS',2,'en'),(329,'LABEL.ICONS','Choose a new user icon to upload.  Dimensions can be up to %USERICONX% pixels wide by %USERICONY% pixels high.  Your user icons cannot be larger than %USERICONX%x%USERICONY%.','USER.OPTIONS.ICONS',2,'en'),(330,'ERROR.NOICONS','No Icons Found.  Upload A New Icon Below.','USER.OPTIONS.ICONS',1,'en'),(331,'LABEL.KEYWORD','KEYWORD:','USER.OPTIONS.ICONS',2,'en'),(332,'LABEL.COMMENTS','COMMENTS:','USER.OPTIONS.ICONS',2,'en'),(333,'CONFIRM.DELETE','Are you sure you want to delete this icon?','USER.OPTIONS.ICONS',0,'en'),(334,'ERROR.ICONLIMIT','Limit of %MAXICONS% icons reached.  Delete one before uploading.','USER.OPTIONS.ICONS',1,'en'),(335,'ERROR.UPLOAD','An error occurred while uploading the image.','USER.OPTIONS.ICONS',0,'en'),(336,'ERROR.PAGE','Unable to save your icon preferences.','USER.OPTIONS.ICONS',0,'en'),(337,'MESSAGE.DELETE','Icon \'<asd id=\'global\' name=\'ICONFILENAME\' case=\'lower\' />\' has been deleted.','USER.OPTIONS.ICONS',1,'en'),(338,'ERROR.EXISTS','<asd id=\'global\' name=\'ICONFILENAME\' case=\'proper\' /> already exists.  Try renaming the file.','USER.OPTIONS.ICONS',1,'en'),(339,'MESSAGE.SAVE','Icon information has been saved.','USER.OPTIONS.ICONS',0,'en'),(340,'ERROR.FILE','Could not delete icon file \'%ICONFILENAME%\'.  Please contact the administrator.','USER.OPTIONS.ICONS',1,'en'),(341,'LABEL.NEW','UPLOAD A NEW ICON','USER.OPTIONS.ICONS',2,'en'),(342,'LABEL.DESCRIPTION','DESCRIPTION','USER.PHOTO',1,'en'),(343,'LABEL.UPLOAD','Choose a new photo to upload.  Dimensions can be up to %MAXPHOTOX% pixels wide by %MAXPHOTOY% pixels high.  Your photos cannot be larger than %MAXPHOTOX%x%MAXPHOTOY%.','USER.PHOTOS',2,'en'),(344,'ERROR.NONE','<asd id=\'global\' name=\'FOCUSFULLNAME\' case=\'proper\' /> has not uploaded any photos into this album.','USER.PHOTOS',1,'en'),(345,'MESSAGE.UPLOADED','Photo \'%PHOTOFILENAME%\' has been successfully uploaded.','USER.PHOTOS',1,'en'),(346,'ERROR.EXISTS','File \'%PHOTOFILENAME%\' already exists in this album.  Try renaming.','USER.PHOTOS',1,'en'),(347,'ERROR.UPLOAD','An error occurred while uploading the image.','USER.PHOTOS',1,'en'),(348,'MESSAGE.DELETE','Photo \'%PHOTOFILENAME%\' has been deleted.','USER.PHOTOS',1,'en'),(349,'MESSAGE.DELETEALL','The selected photos have been deleted.','USER.PHOTOS',1,'en'),(350,'CONFIRM.DELETEALL','Are you sure you want to delete these photos?','USER.PHOTOS',1,'en'),(351,'CONFIRM.DELETE','Are you sure you want to delete this photo?','USER.PHOTOS',1,'en'),(352,'LABEL.EDIT','EDIT PHOTO INFORMATION','USER.PHOTOS',0,'en'),(353,'INFO.TAGS','<i>NOTE:</i> Separate tags with commas or periods.','USER.PHOTOS',2,'en'),(354,'LABEL.TAGS','TAGS','USER.PHOTOS',2,'en'),(355,'LABEL.DESCRIPTION','DESCRIPTION','USER.PHOTOS',2,'en'),(357,'MESSAGE.SAVE','Photo \'%PHOTOFILENAME%\' has been successfully updated.','USER.PHOTOS',1,'en'),(358,'INFO.FILENAME','<i>NOTE</i>: Only numbers and letters are allowed, no spaces or symbols.','USER.PHOTOS',2,'en'),(359,'LABEL.FILENAME','FILENAME','USER.PHOTOS',2,'en'),(360,'ERROR.FILE','Unable to rename file \'%PHOTOFILENAME%\'.  Please contact the administrator.','USER.PHOTOS',1,'en'),(361,'ERROR.WRONGSIZE','The image you uploaded is bigger than %MAXWIDTH%x%MAXHEIGHT%.  Try again.','USER.PHOTOS',1,'en'),(362,'ERROR.NOTNULL','Do not leave this field blank.','USER.PHOTOS',0,'en'),(363,'ERROR.PAGE','Unable to save this photo.  See below for errors.','USER.PHOTOS',0,'en'),(364,'ERROR.DUPLICATE','<asd id=\'global\' name=\'FIELDNAME\' case=\'proper\' /> already exists. Please choose another..','USER.PHOTOS',1,'en'),(365,'ERROR.NONESELECTED','No photos were selected.','USER.PHOTOS',0,'en'),(366,'ERROR.FROMONE','Cannot move the first photo up any farther.','USER.PHOTOS',0,'en'),(367,'ERROR.ILLEGALCHAR','This field can only contain words and numbers, no symbols or spaces.','USER.PHOTOS',0,'en'),(368,'ERROR.CANTWRITE','INSUFFICIENT ACCESS TO MODIFY PHOTOS.','USER.PHOTOS',0,'en'),(369,'ERROR.WRONGTYPE','The image you uploaded is not a GIF, JPEG, or PNG file.  Try again.','USER.PHOTOS',0,'en'),(370,'LABEL.PHOTOSET','MOVE TO ALBUM:','USER.PHOTOS',2,'en'),(371,'LABEL.HINT','HINT: %IMAGEHINT%','USER.PHOTOS',2,'en'),(372,'INFO.HINT','<i>NOTE: </i>Use this to link this photo: &lt;asd id=\'image\' hint=\'%IMAGEHINT%\' /&gt;','USER.PHOTOS',2,'en'),(373,'ERROR.NOTIMAGE','The file you uploaded is not a valid image file.  Try again.','USER.PHOTOS',0,'en'),(374,'LABEL.COMMENTS','%COMMENTCOUNT% Comment(s).','USER.PHOTOS',1,'en'),(375,'ERROR.NONE','<asd id=\'global\' name=\'FOCUSFULLNAME\' case=\'proper\' /> has not created any photo albums.','USER.PHOTOSETS',1,'en'),(376,'LABEL.NEW','ADD A NEW ALBUM:','USER.PHOTOSETS',0,'en'),(377,'ERROR.NOTNULL','Do not leave this field blank.','USER.PHOTOSETS',1,'en'),(378,'MESSAGE.NEW','Album  \'%SETNAME%\' has been added.','USER.PHOTOSETS',1,'en'),(379,'ERROR.ILLEGALCHAR','This field can only contain words and numbers, no symbols or spaces.','USER.PHOTOSETS',1,'en'),(380,'MESSAGE.SAVE','Album \'%SETNAME%\' has been successfully updated.','USER.PHOTOSETS',1,'en'),(381,'ERROR.DUPLICATE','<asd id=\'global\' name=\'FIELDNAME\' case=\'proper\' /> already exists. Please choose another..','USER.PHOTOSETS',1,'en'),(382,'ERROR.CANTWRITE','INSUFFICIENT ACCESS TO MODIFY PHOTO ALBUMS.','USER.PHOTOSETS',1,'en'),(383,'LABEL.EDIT','EDIT ALBUM INFORMATION','USER.PHOTOSETS',0,'en'),(384,'LABEL.NAME','NAME:','USER.PHOTOSETS',2,'en'),(385,'LABEL.DESCRIPTION','DESCRIPTION:','USER.PHOTOSETS',2,'en'),(386,'LABEL.DIRECTORY','DIRECTORY:','USER.PHOTOSETS',2,'en'),(387,'LABEL.TAGS','TAGS','USER.PHOTOSETS',2,'en'),(388,'LABEL.PRIVACY','PRIVACY:','USER.PHOTOSETS',2,'en'),(389,'LABEL.RESTRICTIONS','RESTRICTIONS:','USER.PHOTOSETS',2,'en'),(390,'INFO.DIRECTORY','<i>NOTE</i>: Only numbers and letters are allowed, no spaces or symbols.','USER.PHOTOSETS',2,'en'),(391,'INFO.TAGS','<i>NOTE:</i> Separate tags with commas or periods.','USER.PHOTOSETS',2,'en'),(392,'ERROR.PAGE','Unable to save this album.  See below for errors.','USER.PHOTOSETS',1,'en'),(393,'CONFIRM.DELETEALL','Are you sure you want to delete these albums?','USER.PHOTOSETS',0,'en'),(394,'CONFIRM.DELETE','Are you sure you want to delete this photo album?','USER.PHOTOSETS',0,'en'),(395,'ERROR.NONESELECTED','No photo albums were selected.','USER.PHOTOSETS',1,'en'),(397,'MESSAGE.DELETE','Album \'%SETNAME%\' has been deleted.','USER.PHOTOSETS',1,'en'),(398,'MESSAGE.DELETEALL','The selected photo albums have been deleted.','USER.PHOTOSETS',0,'en'),(399,'ERROR.DIR','Could not modify album directory.  Please contact the administrator.','USER.PHOTOSETS',0,'en'),(400,'ERROR.TOOLONG','%FIELDNAME% is too long.  Cannot be longer than %MAXSIZE% characters.','USER.PHOTOSETS',1,'en'),(401,'INFO.PHOTOCOUNT','<b>%PHOTOCOUNT% Photo(s).</b>','USER.PHOTOSETS',2,'en'),(402,'ERROR.FROMONE','Cannot move the first photo up any farther.','USER.PHOTOSETS',0,'en'),(403,'LABEL.PHOTOSETS','PHOTO ALBUMS','USER.PHOTOSETS',0,'en'),(404,'ERROR.TOOLARGE','%FIELDNAME% is too large.  Cannot be bigger than %MAXSIZE%.','USER.PHOTOSETS',1,'en'),(405,'LABEL.EVERYONE','Everyone','USER.PRIVACY',0,'en'),(406,'LABEL.LOGGEDIN','Logged In Users','USER.PRIVACY',0,'en'),(407,'LABEL.SETTINGS','PRIVACY SETTINGS:','USER.PRIVACY',0,'en'),(408,'LABEL.ALLOW','Allow','USER.PRIVACY',0,'en'),(409,'LABEL.SCREEN','Screen','USER.PRIVACY',0,'en'),(410,'LABEL.RESTRICT','Restrict','USER.PRIVACY',0,'en'),(411,'LABEL.BLOCK','Block','USER.PRIVACY',0,'en'),(412,'LABEL.HIDE','Hide','USER.PRIVACY',0,'en'),(413,'OBJECT.TITLE','<asd id=\'global\' name=\'FOCUSFULLNAME\' case=\'upper\' />\'S PROFILE','USER.PROFILE',1,'en'),(414,'LABEL.FULLNAME','<strong>FULL NAME:</strong>&nbsp; %FOCUSFULLNAME%','USER.PROFILE',2,'en'),(415,'LABEL.GENDER','<strong>GENDER:</strong>&nbsp; %FOCUSGENDER%','USER.PROFILE',2,'en'),(416,'LABEL.AGE','<strong>AGE:</strong>','USER.PROFILE',2,'en'),(417,'LABEL.QUESTION','<strong>%FOCUSQUESTION%</strong> &nbsp;%FOCUSANSWER%','USER.PROFILE',2,'en'),(418,'OBJECT.TITLE','CONTACT <asd id=\'global\' name=\'FOCUSFULLNAME\' case=\'upper\' />','USER.PROFILE.CONTACT',1,'en'),(419,'OBJECT.TITLE','<asd id=\'global\' name=\'FOCUSFULLNAME\' case=\'upper\' />','USER.PROFILE.PHOTO',1,'en'),(420,'MAIL.FROM','webmaster@%SITEDOMAIN%','USER.INVITE',1,'en'),(421,'MAIL.SUBJECT','Welcome To Appleseed @ Nefac.Net!','USER.INVITE',5,'en'),(422,'MESSAGE.NEW','RECORD #%DATAID% HAS BEEN ADDED.','ADMIN',1,'en'),(423,'MAIL.BODY','Congratulations!\r\n\r\nYou have been invited by %INVITEDBY% to open a free account at %SITEURL%.\r\n\r\nTo accept this invitation and set up an account, click this link:\r\n\r\n %INVITEURL%\r\n\r\nOnce your account has been created, %INVITEDBY% will be notified in order to add you to their friend\'s list!\r\n\r\nAppleseed is a new open source social networking software that is currently in development.  Planned features include: Journals, Photo Albums, Email, and Groups all in one place, and fully distributed social networking.  You can add friends from any Appleseed site.\r\n\r\nAppleseed is still in early beta, and many features are still not available. But if you set up an account, you\'ll be able to help us fix bugs and add features.\r\n\r\nThank You,\r\nThe Appleseed Collective\r\n','USER.INVITE',1,'en'),(425,'ERROR.TOOLONG','%FIELDNAME% IS TOO LONG.  (> %MAXSIZE% CHARACTERS)','ADMIN',1,'en'),(426,'OBJECT.TITLE','NEWEST ARTICLES','CONTENT.ARTICLES',1,'en'),(427,'LABEL.COUNT','%COMMENTCOUNT% comment(s).','CONTENT.ARTICLES',1,'en'),(428,'CONFIRM.DELETEALL','Are  you sure you want to delete these comments?','USER.COMMENTS',5,'en'),(429,'LINK.REPLY','Reply','USER.COMMENTS',5,'en'),(430,'LINK.THREAD','Thread','USER.COMMENTS',5,'en'),(431,'LINK.PARENT','Parent','USER.COMMENTS',5,'en'),(432,'LABEL.DELETED','( Deleted Comment )','USER.COMMENTS',5,'en'),(433,'LABEL.TITLE','Title:','CONTENT.ARTICLES',5,'en'),(434,'LABEL.SUMMARY','Summary:','CONTENT.ARTICLES',5,'en'),(435,'LABEL.FULL','Full Article:','CONTENT.ARTICLES',5,'en'),(436,'LABEL.LANGUAGE','Language:','CONTENT.ARTICLES',5,'en'),(437,'LABEL.SUBMIT','Submit An Article.','CONTENT.ARTICLES',5,'en'),(438,'ERROR.PAGE','Unable to submit this article.  Please check for errors below.','CONTENT.ARTICLES',5,'en'),(439,'ERROR.NOTNULL','Do not leave this field blank.','CONTENT.ARTICLES',5,'en'),(440,'MESSAGE.SUBMITTED','Your article has been submitted.  An editor will have to approve it for viewing.','CONTENT.ARTICLES',5,'en'),(441,'LABEL.ANONYMOUS','(anonymous)',NULL,5,'en'),(442,'MESSAGE.NONE','No pending articles were found.','CONTENT.ARTICLES',5,'en'),(443,'LABEL.EDIT','Article Editor','CONTENT.ARTICLES',5,'en'),(444,'LABEL.SUBMITTED','Submitted by: <asd user=\'%SUBMITTEDUSERNAME%\' domain=\'%SUBMITTEDDOMAIN%\' />','CONTENT.ARTICLES',3,'en'),(445,'LABEL.VERIFICATION','Verification:','CONTENT.ARTICLES',5,'en'),(446,'LABEL.STAMP','Time Stamp:','CONTENT.ARTICLES',5,'en'),(447,'LABEL.LANGUAGE','Language','CONTENT.ARTICLES',5,'en'),(448,'MESSAGE.SAVE','Article has been saved.','CONTENT.ARTICLES',5,'en'),(449,'ERROR.CANTWRITE','INSUFFICIENT ACCESS TO MAKE MODIFICATIONS.',NULL,5,'en'),(450,'MESSAGE.PENDING','%PENDING% article(s) are awaiting editor approval.','CONTENT.ARTICLES',1,'en'),(451,'MAIL.FROM','password@%SITEDOMAIN%','SITE.LOGIN',1,'en'),(452,'MAIL.SUBJECT','Your new password for %SITEDOMAIN%','SITE.LOGIN',1,'en'),(453,'MAIL.BODY','Hello, %FULLNAME%\r\n\r\nYour password has been reset.  Your new password is:\r\n\r\n%PASSWORD%\r\n\r\nThank You,\r\nThe Appleseed Collective\r\n','SITE.LOGIN',1,'en'),(454,'ERROR.UPDATE','Database error while updating account.','SITE.LOGIN',5,'en'),(455,'MESSAGE.SENT','A new password has been emailed.','SITE.LOGIN',5,'en'),(456,'ERROR.USERNAME','You must  enter your username.','SITE.LOGIN',5,'en'),(457,'ERROR.UNKNOWN','No such user on this site.','SITE.LOGIN',5,'en'),(458,'MAIL.SUBJECT','%COMMENTINGUSER% has left a comment on your journal.','USER.JOURNAL.COMMENTS',1,'en'),(459,'MAIL.FROM','comments@%SITEDOMAIN%','USER.JOURNAL.COMMENTS',1,'en'),(460,'MAIL.BODY','Hello, %COMMENTEDUSER%.\r\n\r\n%COMMENTINGUSER% has added a comment to your journal entry.\r\n\r\nClick here to view your Appleseed comments:\r\n%INFOURL%\r\n\r\n--\r\n\r\nAppleseedProject.org\r\nCopyleft (c) 2004-2007 by the Appleseed Collective. All Rights Reversed.','USER.JOURNAL.COMMENTS',1,'en'),(461,'ERROR.NONESELECTED','No Comments Were Selected.','USER.JOURNAL.COMMENTS',5,'en'),(462,'ERROR.NONESELECTED','No Comments Were Selected.','USER.PHOTOS.COMMENTS',5,'en'),(463,'ERROR.NONESELECTED','No Comments Were Selected.','USER.PHOTO.COMMENTS',5,'en'),(464,'ERROR.NONESELECTED','No Comments Were Selected.','USER.INFO.COMMENTS',5,'en'),(465,'LABEL.JOURNAL','Notification for comments on your Journal:','USER.OPTIONS.EMAILS',5,'en'),(466,'MESSAGE.SAVE','Your e-mail notification options have been saved.','USER.OPTIONS.EMAILS',5,'en'),(467,'LABEL.THEME','Default Theme:','USER.OPTIONS.CONFIG',5,'en'),(468,'MESSAGE.SAVE','Your configuration options have been saved.','USER.OPTIONS.CONFIG',5,'en'),(469,'LABEL.QUESTIONS','PROFILE QUESTIONS & ANSWERS','USER.OPTIONS',5,'en'),(470,'LABEL.EMAILS','E-MAIL NOTIFICATION','USER.OPTIONS',5,'en'),(471,'LABEL.PROFILE','Notification for profile comments:','USER.OPTIONS.EMAILS',5,'en'),(472,'LABEL.REPLY','Notification for replies to your comments?','USER.OPTIONS.EMAILS',5,'en'),(473,'MAIL.SUBJECT','%COMMENTINGUSER% has replied to your comment.','USER.COMMENTS',1,'en'),(474,'MAIL.FROM','comments@%SITEDOMAIN%','USER.COMMENTS',1,'en'),(475,'MAIL.BODY','Hello, %COMMENTEDUSER%.\r\n\r\n%COMMENTINGUSER% has replied to your comment.\r\n\r\nClick here to view the thread:\r\n%INFOURL%\r\n\r\n--\r\n\r\nAppleseedProject.org\r\nCopyleft (c) 2004-2007 by the Appleseed Collective. All Rights Reversed.','USER.COMMENTS',1,'en'),(476,'LABEL.SUBJECT','%COMMENTINGUSER% has replied to your comment.','CONTENT.ARTICLES.COMMENT',1,'en'),(477,'MAIL.FROM','comments@%SITEDOMAIN%','CONTENT.ARTICLES.COMMENTS',1,'en'),(478,'MAIL.BODY','Hello, %COMMENTEDUSER%.\r\n\r\n%COMMENTINGUSER% has replied to your article.\r\n\r\nClick here to view the thread:\r\n%INFOURL%\r\n\r\n--\r\n\r\nAppleseedProject.org\r\nCopyleft (c) 2004-2007 by the Appleseed Collective. All Rights Reversed.','CONTENT.ARTICLES.COMMENTS',1,'en'),(479,'MAIL.SUBJECT','%COMMENTINGUSER% has replied to your article.','CONTENT.ARTICLES.COMMENTS',1,'en'),(480,'LABEL.ANONYMOUS.FULLNAME','An Anonymous User',NULL,5,'en'),(481,'ERROR.ALREADY.PENDING','%REQUESTNAME% has already been sent a friend request.','USER.FRIENDS',1,'en'),(482,'LABEL.PHOTO','PROFILE PHOTO','USER.OPTIONS',5,'en'),(483,'MESSAGE.SAVE','Your profile question answers have been saved.','USER.OPTIONS.QUESTIONS',5,'en'),(484,'CONFIRM.DELETE.ICON','Are you sure you want to delete this icon?','USER.OPTIONS',5,'en'),(485,'MESSAGE.SAVE.ALL','All Of Your Options Have Been Saved.','USER.OPTIONS',5,'en'),(486,'MAIL.FROMNAME','New Appleseed Message','USER.MESSAGES',5,'en'),(487,'MAIL.FROMNAME','Appleseed Friend Approval','USER.FRIENDS.APPROVE',5,'en'),(488,'MAIL.FROMNAME','Appleseed Friend Removal','USER.FRIENDS.DELETE',5,'en'),(489,'MAIL.FROMNAME','Appleseed Friend Denied','USER.FRIENDS.DENY',5,'en'),(490,'MAIL.FROMNAME','Appleseed Friend Request','USER.FRIENDS.REQUEST',5,'en'),(491,'MAIL.FROMNAME','Appleseed Comments','USER.INFO.COMMENTS',5,'en'),(492,'MAIL.FROMNAME','Appleseed Webmaster ','USER.INVITE',5,'en'),(493,'MAIL.FROMNAME','Appleseed Password Retrieval','SITE.LOGIN',5,'en'),(494,'MAIL.FROMNAME','Appleseed Comments','USER.JOURNAL.COMMENTS',5,'en'),(495,'MAIL.FROMNAME','Appleseed Comments','USER.COMMENTS',5,'en'),(496,'MAIL.FROMNAME','Appleseed Comments','CONTENT.ARTICLES.COMMENTS',5,'en'),(497,'LABEL.ALIAS','Enter an alias to be displayed:','USER.OPTIONS.GENERAL',5,'en'),(498,'ERROR.RETRIEVE','This message could not be retrieved.','USER.MESSAGES',5,'en'),(499,'ERROR.UNABLE','Unable to send this message.  See below for errors.','USER.MESSAGES',5,'en'),(500,'ERROR.UNKNOWN','No user was found at this address.  Check your spelling and try again.','USER.MESSAGES',5,'en'),(501,'CONFIRM.DELETEALL','Are you sure you want to permanently delete the selected messages?','USER.MESSAGES',5,'en'),(502,'CONFIRM.TRASHALL','Are you sure you want to move the selected messages to the trash?','USER.MESSAGES',5,'en'),(503,'CONFIRM.DELETE','Are you sure you want to permanently delete this message?','USER.MESSAGES',5,'en'),(504,'LABEL.FORWARD','FORWARD MESSAGE','USER.MESSAGES',5,'en'),(505,'LABEL.FWD','Fwd: ','USER.MESSAGES',5,'en'),(506,'LABEL.REPLY','REPLY TO MESSAGE','USER.MESSAGES',5,'en'),(508,'LABEL.CREATE','CREATE A NEW DISCUSSION GROUP','CONTENT.GROUPS',5,'en'),(509,'ERROR.PAGE','An Error Has Occurred While Attempting To Create This Group.  See Below.','CONTENT.GROUPS',5,'en'),(510,'ERROR.NOTNULL','Do not leave this field blank',NULL,5,'en'),(511,'ERROR.DUPLICATE','A group with this name already exists.  Choose another.','CONTENT.GROUPS',5,'en'),(512,'ERROR.ILLEGALCHAR','Use only numbers and letters with no spaces for your group name.','CONTENT.GROUPS',5,'en'),(513,'ERROR.TOOSHORT','Your group name is too short.  Try something longer (at least 6 characters).','CONTENT.GROUPS',5,'en'),(514,'LABEL.NAME','Group URL Name:','CONTENT.GROUPS',5,'en'),(515,'LABEL.FULLNAME','Full Group Name:','CONTENT.GROUPS',5,'en'),(516,'LABEL.DESCRIPTION','Description','CONTENT.GROUPS',5,'en'),(517,'LABEL.ACCESS','Access:','CONTENT.GROUPS',5,'en'),(518,'LABEL.TAGS','Tags:','CONTENT.GROUPS',5,'en'),(519,'LABEL.CREATED','NEW DISCUSSION GROUP HAS BEEN CREATED.','CONTENT.GROUPS',5,'en'),(520,'INFO.CREATED','Your new discussion group has been created.  Below is the direct URL to the new group.  You can edit (or delete) this group at any time by going to this URL and then clicking on the \'EDITOR\' tab.','CONTENT.GROUPS',5,'en'),(521,'MESSAGE.NONE','<asd id=\'global\' name=\'FOCUSFULLNAME\' case=\'proper\' /> Is Not A Member Of Any Groups.','USER.GROUPS',1,'en'),(522,'LABEL.MEMBERS','Members: %MEMBERCOUNT%','USER.GROUPS',1,'en'),(523,'LABEL.FULLNAME','Full Group Name:','CONTENT.GROUP',5,'en'),(524,'LABEL.DESCRIPTION','Description:','CONTENT.GROUP',5,'en'),(525,'LABEL.ACCESS','Access:','CONTENT.GROUP',5,'en'),(526,'LABEL.TAGS','Tags:','CONTENT.GROUP',5,'en'),(527,'LABEL.NAME','Name: %GROUPNAME%','CONTENT.GROUP',1,'en'),(528,'MESSAGE.SAVED','Group options have been saved.','CONTENT.GROUP',5,'en'),(529,'ERROR.PAGE','An Error Has Occurred While Attempting To Save This Group.  See Below.','CONTENT.GROUP',5,'en'),(530,'CONFIRM.DELETE','Are you sure you want to delete this group?  This cannot be undone!','CONTENT.GROUP',5,'en'),(531,'MESSAGE.DELETED','This group has been permanently deleted.','CONTENT.GROUP',5,'en'),(532,'MESSAGE.JOINED','You have joined this group.','CONTENT.GROUP',5,'en'),(533,'MESSAGE.LEFT','You have left this group.','CONTENT.GROUP',5,'en'),(534,'MESSAGE.PENDING','Your membership in this group is currently pending approval.','CONTENT.GROUP',5,'en'),(535,'CONFIRM.LEAVE','Are you sure you want to leave this group?','CONTENT.GROUP',5,'en'),(536,'LABEL.GENERAL','GENERAL','CONTENT.GROUP',5,'en'),(537,'LABEL.MEMBEREDITOR','MEMBERS (%MEMBERCOUNT%)','CONTENT.GROUP',1,'en'),(538,'LABEL.INVITE','INVITE','CONTENT.GROUP',5,'en'),(539,'LABEL.USER','User:','CONTENT.GROUP',5,'en'),(540,'LABEL.DOMAIN','Domain:','CONTENT.GROUP',5,'en'),(542,'LABEL.ACTION','Action:','CONTENT.GROUP',5,'en'),(543,'LABEL.PENDINGEDITOR','PENDING (%PENDINGCOUNT%)','CONTENT.GROUP',1,'en'),(544,'LABEL.STAMP','%DATE% at %TIME%','CONTENT.GROUP',1,'en'),(545,'LABEL.BYLINE','by %AUTHOR%','CONTENT.GROUP',3,'en'),(546,'LABEL.NEW','NEW ENTRY','CONTENT.GROUP',5,'en'),(547,'LABEL.SUBJECT','SUBJECT','CONTENT.GROUP',5,'en'),(548,'LABEL.BODY','BODY','CONTENT.GROUP',1,'en'),(549,'LABEL.ADD','ADD AN ENTRY','CONTENT.GROUP',1,'en'),(550,'LABEL.ICON','USER ICON','CONTENT.GROUP',5,'en'),(551,'OBJECT.TITLE','READ POSTS','CONTENT.GROUP',1,'en'),(552,'LABEL.REPLY','REPLY TO %PARENTAUTHOR%','CONTENT.GROUP',3,'en'),(553,'INFO.REPLY','%PARENTBODY%','CONTENT.GROUP',2,'en'),(554,'LABEL.SUBJECTPREFIX','Re:','CONTENT.GROUP',1,'en'),(555,'ERROR.PAGE','Unable to save this entry.  See below for errors.','CONTENT.GROUP',5,'en'),(556,'ERROR.NOTNULL','You can\'t leave the \'%FIELDNAME%\' field blank.','CONTENT.GROUP',1,'en'),(557,'CONFIRM.CANCEL','All changes will be lost!  Are you sure?',NULL,5,'en'),(558,'CONFIRM.DELETE','Are you sure you want to delete this entry?','CONTENT.GROUP',5,'en'),(559,'ERROR.NONE','No entries have been posted.','CONTENT.GROUP',5,'en'),(560,'LABEL.THREAD','%LINK% by %AUTHOR%  on %DATE% at %TIME%','CONTENT.GROUP',3,'en'),(561,'CONFIRM.DELETEALL','Are  you sure you want to delete these entries?','CONTENT.GROUP',5,'en'),(562,'LINK.REPLY','Reply','CONTENT.GROUP',5,'en'),(563,'LINK.THREAD','Thread','CONTENT.GROUP',5,'en'),(564,'LINK.PARENT','Parent','CONTENT.GROUP',5,'en'),(565,'LABEL.DELETED','( Deleted Entry )','CONTENT.GROUP',5,'en'),(566,'MAIL.FROM','group@apleseed.nefac.net','CONTENT.GROUP',5,'en'),(567,'MAIL.BODY','Hello, %REPLIEDUSER%.\r\n\r\n%REPLYINGUSER% has replied to your group post.\r\n\r\nClick here to view the thread:\r\n%INFOURL%\r\n\r\n--\r\n\r\nAppleseedProject.org\r\nCopyleft (c) 2004-2007 by the Appleseed Collective. All Rights Reversed.','CONTENT.GROUP',1,'en'),(568,'MAIL.SUBJECT','%REPLYINGUSER% has replied to your group post.','CONTENT.GROUp',1,'en'),(569,'MAIL.FROMNAME','Appleseed Groups','CONTENT.GROUP',5,'en'),(570,'ERROR.NONESELECTED','No Entries Were Selected.','CONTENT.GROUP',5,'en'),(571,'CONFIRM.DELETE.ENTRY','Are you sure you want to delete this entry?','CONTENT.GROUP',5,'en'),(572,'MESSAGE.MEMBER.EDITOR','Member list has been saved.','CONTENT.GROUP',5,'en'),(573,'MESSAGE.PENDING.EDITOR','Pending list has been saved.','CONTENT.GROUP',5,'en'),(574,'MESSAGE.CANCELLED','The friend request from %DENIEDNAME% has been cancelled.','USER.FRIENDS',1,'en'),(575,'CONFIRM.CANCEL','Are you sure you want to cancel this friend request?','USER.FRIENDS',5,'en');
/*!40000 ALTER TABLE `%PREFIX%systemStrings` ENABLE KEYS */;
UNLOCK TABLES;
--
-- Table structure for table `systemTooltips`
--

DROP TABLE IF EXISTS `%PREFIX%systemTooltips`;
CREATE TABLE `%PREFIX%systemTooltips` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `Title` varchar(64) NOT NULL default '',
  `Context` varchar(32) default NULL,
  `Output` text,
  `Formatting` int(1) default NULL,
  `Language` char(2) default NULL,
  PRIMARY KEY  (`tID`),
  KEY `systemTooltips_index` (`Title`,`Context`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `systemTooltips`
--

LOCK TABLES `%PREFIX%systemTooltips` WRITE;
/*!40000 ALTER TABLE `%PREFIX%systemTooltips` DISABLE KEYS */;
INSERT INTO `%PREFIX%systemTooltips` VALUES (1,'RECIPIENT','USER.MESSAGES','An appleseed address is just like an email address:  ie, <i>noam</i>@<i>appleseedproject.org</i>.',5,'en'),(3,'SAVE_DRAFT','USER.MESSAGES','Save a draft copy of this message to be completed at a later time.',5,'en'),(6,'USERNAME','ADMIN.USERS.ACCOUNTS','Warning!  Changing a user\'s username can cause a lot of issues!',5,'en'),(7,'LOCATION','SITE.LOGIN','If you are logged in to another appleseed site, you can use that login to gain access to this site.',5,'en'),(8,'NEXT',NULL,'Next %SCROLLSTEP% entries.',1,'en'),(9,'HEADER_LOGGEDIN',NULL,'Click your username for your profile page.',5,'en'),(10,'GENERAL_TOP','USER.OPTIONS','Set your general options here.',5,'en'),(11,'PREV',NULL,'Previous %SCROLLSTEP% entries.',1,'en'),(12,'BODY','USER.MESSAGES','Messages can be up to 64 Kilobytes, and may include most HTML tags.',5,'en'),(13,'NAME','CONTENT.GROUPS','This name will be used for the direct URL to your group.',5,'en'),(14,'OWNER','USER.GROUPS','This user is the owner/creater of this discussion group.',5,'en');
/*!40000 ALTER TABLE `%PREFIX%systemTooltips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userAccess`
--

DROP TABLE IF EXISTS `%PREFIX%userAccess`;
CREATE TABLE `%PREFIX%userAccess` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Location` varchar(128) NOT NULL default '',
  `r` tinyint(1) default '0',
  `w` tinyint(1) default '0',
  `a` tinyint(1) default '0',
  `Inheritance` tinyint(1) default '0',
  `e` tinyint(1) default '0',
  PRIMARY KEY  (`tID`,`userAuth_uID`),
  KEY `userAccess_FKIndex1` (`userAuth_uID`),
  CONSTRAINT `userAccess_ibfk_1` FOREIGN KEY (`userAuth_uID`) REFERENCES `%PREFIX%userAuthorization` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userAccess`
--

LOCK TABLES `%PREFIX%userAccess` WRITE;
/*!40000 ALTER TABLE `%PREFIX%userAccess` DISABLE KEYS */;
INSERT INTO `%PREFIX%userAccess` VALUES (1,0000000001,'/',1,1,1,1,1);
/*!40000 ALTER TABLE `%PREFIX%userAccess` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userQuestions`
--

DROP TABLE IF EXISTS `%PREFIX%userQuestions`;
CREATE TABLE `%PREFIX%userQuestions` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `FullQuestion` varchar(255) default NULL,
  `ShortQuestion` varchar(64) default NULL,
  `TypeOf` int(2) default NULL,
  `Language` char(2) default NULL,
  `Concern` varchar(255) default NULL,
  `Visible` int(1) default NULL,
  PRIMARY KEY  (`tID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `userAnswers`
--

DROP TABLE IF EXISTS `%PREFIX%userAnswers`;
CREATE TABLE `%PREFIX%userAnswers` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `userQuestions_tID` int(10) unsigned NOT NULL default '0',
  `Answer` text,
  PRIMARY KEY  (`tID`,`userAuth_uID`,`userQuestions_tID`),
  KEY `Answers_FKIndex1` (`userAuth_uID`),
  KEY `userAnswers_FKIndex2` (`userQuestions_tID`),
  CONSTRAINT `userAnswers_ibfk_1` FOREIGN KEY (`userAuth_uID`) REFERENCES `%PREFIX%userAuthorization` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `userAnswers_ibfk_2` FOREIGN KEY (`userQuestions_tID`) REFERENCES `%PREFIX%userQuestions` (`tID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `userBlocks`
--

DROP TABLE IF EXISTS `%PREFIX%userBlocks`;
CREATE TABLE `%PREFIX%userBlocks` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `uID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tID`,`userAuth_uID`),
  KEY `Blocks_FKIndex1` (`userAuth_uID`),
  CONSTRAINT `userBlocks_ibfk_1` FOREIGN KEY (`userAuth_uID`) REFERENCES `%PREFIX%userAuthorization` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `userGroups`
--

DROP TABLE IF EXISTS `%PREFIX%userGroups`;
CREATE TABLE `%PREFIX%userGroups` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Name` varchar(32) default NULL,
  `Domain` varchar(128) default NULL,
  PRIMARY KEY  (`tID`),
  KEY `userGroups_FKIndex1` (`userAuth_uID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `userIcons`
--

DROP TABLE IF EXISTS `%PREFIX%userIcons`;
CREATE TABLE `%PREFIX%userIcons` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Filename` varchar(64) NOT NULL default '',
  `Keyword` varchar(32) default NULL,
  `Comments` varchar(128) default NULL,
  PRIMARY KEY  (`tID`,`userAuth_uID`),
  KEY `Icons_FKIndex1` (`userAuth_uID`),
  CONSTRAINT `userIcons_ibfk_1` FOREIGN KEY (`userAuth_uID`) REFERENCES `%PREFIX%userAuthorization` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userIcons`
--

LOCK TABLES `%PREFIX%userIcons` WRITE;
/*!40000 ALTER TABLE `%PREFIX%userIcons` DISABLE KEYS */;
INSERT INTO `%PREFIX%userIcons` VALUES (1,0000000001,'__noicon__','(no icon)',NULL);
/*!40000 ALTER TABLE `%PREFIX%userIcons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userInformation`
--

DROP TABLE IF EXISTS `%PREFIX%userInformation`;
CREATE TABLE `%PREFIX%userInformation` (
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Views` int(10) unsigned default NULL,
  `FirstLogin` datetime default NULL,
  `LastLogin` datetime default NULL,
  `MessageStamp` datetime default NULL,
  `FriendStamp` datetime default NULL,
  `OnlineStamp` datetime default NULL,
  PRIMARY KEY  (`userAuth_uID`),
  KEY `Information_FKIndex1` (`userAuth_uID`),
  CONSTRAINT `userInformation_ibfk_1` FOREIGN KEY (`userAuth_uID`) REFERENCES `%PREFIX%userAuthorization` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `userInvites`
--

DROP TABLE IF EXISTS `%PREFIX%userInvites`;
CREATE TABLE `%PREFIX%userInvites` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Value` varchar(32) default NULL,
  `Active` int(1) default NULL,
  `Recipient` varchar(128) default NULL,
  `Stamp` datetime default NULL,
  PRIMARY KEY  (`tID`,`userAuth_uID`),
  KEY `userInvites_FKIndex1` (`userAuth_uID`),
  CONSTRAINT `userInvites_ibfk_1` FOREIGN KEY (`userAuth_uID`) REFERENCES `%PREFIX%userAuthorization` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `userPreferences`
--

DROP TABLE IF EXISTS `%PREFIX%userPreferences`;
CREATE TABLE `%PREFIX%userPreferences` (
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Theme` int(2) default '0',
  `DefaultProfilePage` int(2) default NULL,
  PRIMARY KEY  (`userAuth_uID`),
  KEY `Preferences_FKIndex1` (`userAuth_uID`),
  CONSTRAINT `userPreferences_ibfk_1` FOREIGN KEY (`userAuth_uID`) REFERENCES `%PREFIX%userAuthorization` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `userPrivacy`
--

DROP TABLE IF EXISTS `%PREFIX%userPrivacy`;
CREATE TABLE `%PREFIX%userPrivacy` (
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `ShowEmail` int(1) default NULL,
  `Visibility` int(1) default NULL,
  PRIMARY KEY  (`userAuth_uID`),
  KEY `Privacy_FKIndex1` (`userAuth_uID`),
  CONSTRAINT `userPrivacy_ibfk_1` FOREIGN KEY (`userAuth_uID`) REFERENCES `%PREFIX%userAuthorization` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `userProfile`
--

DROP TABLE IF EXISTS `%PREFIX%userProfile`;
CREATE TABLE `%PREFIX%userProfile` (
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Email` varchar(128) NOT NULL default '',
  `Fullname` varchar(64) default 'Unknown',
  `Description` text,
  `Gender` int(1) default NULL,
  `Birthday` datetime default '1980-10-31 10:15:15',
  `Zipcode` varchar(12) default NULL,
  `Alias` varchar(64) default NULL,
  PRIMARY KEY  (`userAuth_uID`),
  UNIQUE KEY `userProfile_index` (`Email`),
  KEY `Profile_FKIndex1` (`userAuth_uID`),
  CONSTRAINT `userProfile_ibfk_1` FOREIGN KEY (`userAuth_uID`) REFERENCES `%PREFIX%userAuthorization` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userProfile`
--

LOCK TABLES `%PREFIX%userProfile` WRITE;
/*!40000 ALTER TABLE `%PREFIX%userProfile` DISABLE KEYS */;
INSERT INTO `%PREFIX%userProfile` VALUES (0000000001,'admin@%SITEDOMAIN%','Administrator','<center><b>I am root!</b></center>',NULL,'1969-10-31 00:00:00','11111',NULL);
/*!40000 ALTER TABLE `%PREFIX%userProfile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `userQuestions`
--

LOCK TABLES `%PREFIX%userQuestions` WRITE;
/*!40000 ALTER TABLE `%PREFIX%userQuestions` DISABLE KEYS */;
INSERT INTO `%PREFIX%userQuestions` VALUES (1,'Where are you located?','LOCATION:',4,'en','',1),(2,'What is your screen name?','SCREEN NAME:',2,'en',NULL,1),(3,'What Instant Messaging service do you use?','IM TYPE:',0,'en','QIMSERVICE',1),(4,'What\'s your website address?','WEBSITE:',3,'en','',1),(5,'What is your relationship status?','STATUS:',0,'en','QSTATUS',1),(6,'What kind of relationship are you looking for?','HERE FOR:',1,'en','QHEREFOR',1),(7,'What\'s your hometown?','HOMETOWN:',2,'en','',1),(8,'How tall are you?','HEIGHT:',2,'en','',1),(9,'Describe your body type:','BODY TYPE:',0,'en','QBODYTYPE',1),(10,'What\'s your astrological sign?','SIGN:',0,'en','QASTROSIGN',1),(11,'Are you a smoker?','SMOKER:',0,'en','QSMOKER',1),(12,'Do you drink?','DRINKER:',0,'en','QDRINKER',1),(13,'What is your blood type?','BLOOD TYPE:',0,'en','QBLOODTYPE',1),(14,'Describe your occupation:','OCCUPATON:',2,'en','',1),(15,'List some of your general interests (seperated by commas):','GENERAL:',4,'en','',1),(16,'List some of your favorite music groups:','MUSIC:',4,'en','',1),(17,'List some of your favorite movies:','MOVIES:',4,'en','',1),(18,'List some of your favorite books:','BOOKS:',4,'en','',1);
/*!40000 ALTER TABLE `%PREFIX%userQuestions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userSessions`
--

DROP TABLE IF EXISTS `%PREFIX%userSessions`;
CREATE TABLE `%PREFIX%userSessions` (
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Identifier` varchar(32) default '00000000000000000000000000000000',
  `Stamp` datetime default NULL,
  `Address` varchar(16) default '0.0.0.0',
  `Host` varchar(128) default NULL,
  PRIMARY KEY  (`userAuth_uID`),
  KEY `Sessions_FKIndex1` (`userAuth_uID`),
  CONSTRAINT `userSessions_ibfk_1` FOREIGN KEY (`userAuth_uID`) REFERENCES `%PREFIX%userAuthorization` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `userSettings`
--

DROP TABLE IF EXISTS `%PREFIX%userSettings`;
CREATE TABLE `%PREFIX%userSettings` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Identifier` varchar(32) NOT NULL default '',
  `Value` varchar(128) default NULL,
  PRIMARY KEY  (`tID`,`userAuth_uID`),
  KEY `Preferences_FKIndex1` (`userAuth_uID`),
  CONSTRAINT `userSettings_ibfk_1` FOREIGN KEY (`userAuth_uID`) REFERENCES `%PREFIX%userAuthorization` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `userTokens`
--

DROP TABLE IF EXISTS `%PREFIX%userTokens`;
CREATE TABLE `%PREFIX%userTokens` (
  `tID` int(10) unsigned NOT NULL auto_increment,
  `userAuth_uID` int(10) unsigned zerofill NOT NULL default '0000000000',
  `Domain` varchar(128) NOT NULL default '',
  `Token` varchar(32) default NULL,
  `Stamp` datetime default NULL,
  PRIMARY KEY  (`tID`),
  KEY `Verification_FKIndex1` (`userAuth_uID`),
  CONSTRAINT `userVerification_ibfk_1` FOREIGN KEY (`userAuth_uID`) REFERENCES `%PREFIX%userAuthorization` (`uID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
