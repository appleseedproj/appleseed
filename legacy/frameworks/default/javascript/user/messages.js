  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: messages.js                             CREATED: 05-17-2007 + 
  // | LOCATION: /frame...script/user/              MODIFIED: 05-17-2007 +
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
  // | VERSION:      0.7.7                                               |
  // | DESCRIPTION:  Client-side script for messages component.          |
  // +-------------------------------------------------------------------+

	// Initialize once the window is loaded.
	window.addEvent('domready', function(){new UserMessagesInitialize();});
	
	// Initialize this javascript view.
	function UserMessagesInitialize () {
	
	  AttachSendToCircleOptionToComposeForm ();
	  
	  return (true);
	} // UserMessagesInitialize
	
	function AddCircleToRecipientList (circle) {
	
	  RecipientElement = $$('.recipientaddress');
	  RecipientValue = RecipientElement[0].value;
	  if (RecipientValue) {
	    RecipientElement[0].value = RecipientElement[0].value + ', circle:' + circle.value;
	  } else {
	    RecipientElement[0].value = 'circle:' + circle.value;
	  } // if
	  
	  return (true);
	} // AddCircleToRecipientList
	
	function AttachSendToCircleOptionToComposeForm () {
	
	  SelectElements = document.getElementsByTagName("select");
	  
	  for (i = 0; i < SelectElements.length; i++) {
	    if (SelectElements[i].className == 'mailcircle') {
	      SelectElements[i].setAttribute('onChange', 'AddCircleToRecipientList(this);'); 
	    } // if
	  } // for
	  
	  return (true);
	} //  AttachSendToCircleOptionToComposeForm 
	
