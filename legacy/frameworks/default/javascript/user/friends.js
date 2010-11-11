  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: friends.js                              CREATED: 05-01-2007 + 
  // | LOCATION: /frame...script/user/              MODIFIED: 05-01-2007 +
  // +-------------------------------------------------------------------+
  // | Copyright (c) 2004-2008 Appleseed Project                         |
  // +-------------------------------------------------------------------+
  // | This program is free software; you can redistribute it and/or     |
  // | modify it under the terms of the GNU General Public License       |
  // | as published by the Free Software Foundation; either version 2    |
  // | of the License, or (at your option) any later version.            |
  // |                                                                   |
  // | This program is distributed in the hope that it will be useful,   |
  // | but WITHOUT ANY WARRANTY; without even the implied warranty of    |
  // | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the     |
  // | GNU General Public License for more details.                      |	
  // |                                                                   |
  // | You should have received a copy of the GNU General Public License |
  // | along with this program; if not, write to:                        |
  // |                                                                   |
  // |   The Free Software Foundation, Inc.                              |
  // |   59 Temple Place - Suite 330,                                    | 
  // |   Boston, MA  02111-1307, USA.                                    |
  // |                                                                   |
  // |   http://www.gnu.org/copyleft/gpl.html                            |
  // +-------------------------------------------------------------------+
  // | AUTHORS: Michael Chisari <michael.chisari@gmail.com>              |
  // +-------------------------------------------------------------------+
  // | VERSION:      0.7.8                                               |
  // | DESCRIPTION:  Client-side script for friends component.           |
  // +-------------------------------------------------------------------+

	// Initialize once the window is loaded.
	window.addEvent('domready', function(){new userFriendsInitialize();});
	
	// Initialize this javascript view.
	function userFriendsInitialize () {
	  
	  LoadRemoteFriends (); 
	  
	  AttachLinksToElements (); 
	  
	  return (true);
	} // userFriendsInitialize
	
	function ChangeLoadingState (element, state) {
      var classes = element.className;
    
      var classlist = classes.split (" ");
      
      for (var count in classlist) {
        if (classlist[count] == 'unloaded') classlist[count] = state;
        if (classlist[count] == 'loading') classlist[count] = state;
        if (classlist[count] == 'loaded') classlist[count] = state;
        if (classlist[count] == 'failed') classlist[count] = state;
      } // for
      
      element.className = classlist.join (" ");
      
      return (true);
	} // ChangeLoadingState
	
	function LoadRemoteFriends () {
	  var remotefriendslist = $$('.unloaded');
	  
	  // No remote friends were found.
	  if (remotefriendslist.length == 0) return (false);
	  
	  var count = 0;
	  for (var i in remotefriendslist) {
	    
	    var username = $$('.unloaded .fullname')[0].innerHTML;
	    var domain   = $$('.unloaded .domain')[0].innerHTML;
	    
        var jsonString = Json.toString( { 
          action: 'AJAX_GET_USER_INFORMATION', 
          username: username, 
          domain: domain
        });
      
	    var currentElement   = $$('.unloaded')[0];
	    var currentElementFullname = $$('.unloaded .fullname')[0];
	    var currentElementOnline = $$('.unloaded .online')[0];
	    var currentElementImage = $$('.unloaded .icon .icon_img')[0];
	    
	    ChangeLoadingState (currentElement, 'loading');
	    
	    var ajaxObject = new Ajax('/ajax/', {
	      postBody: jsonString, 
	      onComplete: function(req) {
	      
	        if (req) {
	          if (req == 'ERROR.SHUTDOWN') {
                this.currentFullname.innerHTML = loadString ('System Is Shut Down');
     	        ChangeLoadingState (this.current, 'failed');
	          } else {
	            var userInfo = Json.evaluate (req);
	          
	            if (userInfo.fullname) {
	              this.currentFullname.innerHTML = userInfo.fullname;
	              if (userInfo.online == 'ONLINE') {
	                var online = new Element ('img');
	                online.setProperty ('src', '/legacy/themes/' + asdTheme + '/images/icons/onlinenow.gif'); 
	                this.currentOnline.adopt (online);
	              } // if
	            
    	          ChangeLoadingState (this.current, 'loaded');
    	        } else {
    	          ChangeLoadingState (this.current, 'failed');
    	        } // if
    	      } // if
    	    } else {
  	        ChangeLoadingState (this.current, 'failed');
    	    } // if
	      }
	    });
	    
	    ajaxObject.username = username;
	    ajaxObject.domain = domain;
	    ajaxObject.currentFullname = currentElementFullname;
	    ajaxObject.currentOnline = currentElementOnline;
	    ajaxObject.currentImage = currentElementImage;
	    ajaxObject.current = currentElement;
	    
	    ajaxObject.request();
	    
	    count++;
	    if (count >= remotefriendslist.length) {
	      return (false);
	    } 
	  };
	  
	  return (true);
	} // LoadRemoteFriends
	
	function AttachLinksToElements () {
	
	  // Attach links to all SPAN elements.
	  spanElements = document.getElementsByTagName("span");
	   
	  for (i = 0; i < spanElements.length; i++) {
	    var target = spanElements[i].getAttribute ('href');
	    var post = spanElements[i].getAttribute ('post');
	    var confirm = spanElements[i].getAttribute ('confirm');
	    if (!confirm) confirm = '';
	    if (target) {
	      spanElements[i].setAttribute('onClick', 'jPOSTLINK ("' + target + '", "' + post + '", "' + confirm + '");'); 
	    }
	  } // for
	  
	  // Attach links to all DIV elements.
	  divElements = document.getElementsByTagName("div");
	   
	  for (i = 0; i < divElements.length; i++) {
	    var target = divElements[i].getAttribute ('href');
	    var post = divElements[i].getAttribute ('post');
	    var confirm = divElements[i].getAttribute ('confirm');
	    if (!confirm) confirm = '';
	    if (target) {
	      divElements[i].setAttribute('onClick', 'jPOSTLINK ("' + target + '", "' + post + '", "' + confirm + '");'); 
	    }
	  } // for
	  
	  return (true);
	} // AttachLinksToElements
