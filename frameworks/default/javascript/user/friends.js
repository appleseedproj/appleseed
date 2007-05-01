	// Initialize once the window is loaded.
	window.addEvent('domready', function(){new userFriendsInitialize();});
	
	// Initialize this javascript view.
	function userFriendsInitialize () {
	  
	  LoadRemoteFriends (); 
	  
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
	    ChangeLoadingState (currentElement, 'loading');
	    
	    var ajaxObject = new Ajax('/ajax/', {
	      postBody: jsonString, 
	      onComplete:  function(req) {
	        var userInfo = Json.evaluate (req);
	        if (userInfo.fullname) {
	          this.currentFullname.innerHTML = userInfo.fullname;
	          if (userInfo.online == 'ONLINE') {
	            var online = new Element ('img');
	            online.setProperty ('src', '/themes/' + asdTheme + '/images/icons/onlinenow.gif'); 
	            this.currentOnline.adopt (online);
	          } // if
	          
    	      ChangeLoadingState (this.current, 'loaded');
    	    } else {
    	      ChangeLoadingState (this.current, 'failed');
    	    } // if
	      }
	    });
	    
	    ajaxObject.currentFullname = currentElementFullname;
	    ajaxObject.currentOnline = currentElementOnline;
	    ajaxObject.current = currentElement;
	    
	    ajaxObject.request();
	    
	    count++;
	    if (count >= remotefriendslist.length) {
	      return (false);
	    } 
	  };
	  
	  return (true);
	} // LoadRemoteFriends