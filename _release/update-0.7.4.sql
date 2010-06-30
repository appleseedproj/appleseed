update #__userSettings set Value='default' where Identifier='DefaultTheme';
update #__systemConfig set Value='default' where Concern='Theme';
alter table #__userAuthorization modify Pass VARCHAR(144);