  // +-------------------------------------------------------------------+
  // | Appleseed Web Community Management Software                       |
  // | http://appleseed.sourceforge.net                                  |
  // +-------------------------------------------------------------------+
  // | FILE: main.js                                 CREATED: 05-01-2007 + 
  // | LOCATION: /frame...script/user/              MODIFIED: 05-01-2007 +
  // +-------------------------------------------------------------------+
  // | Copyright (c) 2004-2007 Appleseed Project                         |
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
  // | VERSION:      0.7.0                                               |
  // | DESCRIPTION:  Client-side script for user components.             |
  // +-------------------------------------------------------------------+

	// Initialize once the window is loaded.
	window.addEvent('domready', function(){new userInitialize();});
	
	// Initialize this javascript view.
	function userInitialize () {
	
	  // Loop through images and set error handler.
	  var images = document.getElementsByTagName ('img');
	  
	  for (var counter in images) {
	    images[counter].onerror = badImage;
	  } // for
	  
	  return (true);
	} // userInitialize
	
	function badImage () {
	  this.src = 'themes/' + asdTheme + '/images/common/unknown.gif';
	  this.alt = 'Image Not Found!';
	} // BadImage