<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Wall
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Page Component
 * 
 * Page Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Page
 */
class cPage extends cComponent {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function AddToProfileTabs ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$return = array ();
		
		$return[] = array ( 'id' => 'page', 'title' => 'Page Tab', 'link' => '/page/' );
		
		return ( $return );
	} 
	
	public function IdentifierExists ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$Identifier = $pData['Identifier'];
		
		$Model = new cModel ( 'PageReferences' );
		
		$Model->Retrieve ( array ( 'Identifier' => $Identifier ) );
		
		if ( $Model->Get ( 'Total' ) > 0 ) {
			return ( true );
		}
		
		return ( false );
	}
	
	public function Status ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		include ( ASD_PATH . 'components/page/models/page.php' );
		$Page = new cPageModel ();
		
		include ( ASD_PATH . 'components/page/models/references.php' );
		$References = new cPageReferencesModel ();
		
		$Page->RetrieveCurrent( $this->_Focus->Id );
		if ( $Page->Get ( "Total" ) == 0 ) return ( false );
		
		$Page->Fetch();
		
		$References->Retrieve ( array ( "Identifier" => $Page->Get ( "Identifier" ) ) );
		if ( $References->Get ( "Total" ) == 0 ) return ( false );
		
		$References->Fetch();
		
		$return = array();
		
		$Identifier = $References->Get ( 'Identifier' );
		$Access = $this->Talk ( 'Privacy', 'Check', array ( 'Type' => 'Post', 'Identifier' => $Identifier ) );
		
		$return['Content'] = $Page->Get ( 'Content' );
		$return['Stamp'] = $References->Get ( 'Stamp' );
		
		if ( $Access ) {
			return ( $return );
		} else {
			// If the person viewing is the focus user, grant access
			if ( $this->_Focus->Account == $this->_Current->Account ) {
				return ( $return );
			} else {
				return ( false );
			}
		}
		
		return ( false );
	}
	
	public function ClearStatus ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$user = $pData['UserId'];
		
		include ( ASD_PATH . 'components/page/models/page.php' );
		$Page = new cPageModel ();
		
		$Page->ClearCurrent ( $user );
		
		return ( true );
	}
	
	public function RegisterPageType ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$post = new stdClass();
		$post->Component = $this->Get ( 'Component' );
		$post->Function = 'GetPost';
		
		$return = array ( 'Post' => $post );
		
		return ( $return );
	}
	
	public function GetPost ( $pData = null ) {
		
		if ( $this->_Source != 'Component' ) return ( false );
		
		$this->_Current = $this->Talk ( 'User', 'Current' );
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		
		$Identifier = $pData['Identifier'];
		$Account = $pData['Account'];
		
		// Check the privacy settings on this item.
		$Access = $this->Talk ( 'Privacy', 'Check', array ( 'Type' => 'Post', 'Identifier' => $Identifier ) );
		
		// Load the Post data
		include_once ( ASD_PATH . 'components/page/models/page.php' );
		$Model = new cPageModel();
		
		$Post = $Model->RetrievePost ( $this->_Focus->Id, $Identifier );
		
		$return['Owner'] = $Post['Owner'];
		$return['Comment'] = $Post['Content'];
		
		// If true, then return the post.
		if ( $Access ) {
			return ( $return );
		} else {
			// If the person viewing is the owner, grant access.
			if ( $this->_Current->Account == $Post['Owner'] ) {
				return ( $return );
			} else if ( $this->_Focus->Account == $this->_Current->Account ) {
				return ( $return );
			} else {
				return ( false );
			}
		}
		
		return ( false );
	}
	
	public function Scrape ( $pData = null ) {
		
		$return = array ();
		
		$url = $pData['url'];
		
		$components = parse_url ( $url );
		
		$host = $components['host'];
		if ( !$host ) {
			$host = 'http';
			$url = 'http://' . $url;
		}
		
		$scheme = $components['scheme'];
		
		$directory = md5 ( rtrim ($url, '/' ) );
		
		$buffer = $this->GetSys ( 'Communication' )->Retrieve ( $url );
		
		$this->GetSys ( 'HTML' )->Load ( $buffer );
		
		// Look for og: metadata
		$title = $this->GetSys ('HTML' )->Find ( 'meta[property=og:title]', 0 )->content;
		$image = $this->GetSys ('HTML' )->Find ( 'meta[property=og:image]', 0 )->content;
		
		if ( $image ) $return['images'][] = $this->_Thumbnail ( $image, md5($image), $directory );
		
		if ( !$title ) $title = $this->GetSys ( 'HTML' )->Find ( 'title', 0 )->plaintext;
		
		$paragraphs = $this->GetSys ( 'HTML' )->Find ( 'p' );
		foreach ( $paragraphs as $p => $paragraph ) {
			if ( strlen ( $paragraph->plaintext ) > 100 ) {
				$description = str_replace ( "\n", " ", $paragraph->plaintext );
				break;
			}
		}
		
		$return['source'] = $url;	
		$return['title'] = $title;	
		$return['description'] = ltrim ( substr ( $description, 0, 512 ) );
		
		$images = $this->GetSys ( 'HTML' )->Find ( 'img' );
		
		foreach ( $images as $i => $img ) {
			
			// Check if src begins with a / and is an absolute url
			$src = $img->src;
			
			// Skip theme and template images
			if ( strstr ( $src, '/themes/' ) ) continue;
			if ( strstr ( $src, '/template/' ) ) continue;
			
			// Only load the first 5 images
			
			if ( substr ( $src, 0, 1 ) == '/' ) {
				$imgLocation = $scheme . '://' . $host . $src;
			} else if ( substr ( $src, 0, 4 ) == 'http' ) {
				$imgLocation = $src;
			} else {
				$imgLocation = $url . '/' . $src;
			}
			
			$filename = md5 ( $src );
			
			$thumbLocation = $this->_Thumbnail ( $imgLocation, $filename, $directory );
			
			if ( $thumbLocation ) $return['images'][] = $thumbLocation;
			
		}
		
		return ( $return );
	}
	
	private function _Thumbnail ( $pLocation, $pFilename, $pDirectory ) {
		
		$location = "/_storage/components/page/thumbnails/" . $pDirectory . "/" . $pFilename;
		
 		$exists = $this->GetSys ( 'Storage' )->Exists ( 'page', 'thumbnails', $pDirectory, $pFilename, $overwrite = false );
 		
 		if ( $exists ) {
 			$im = imagecreatefromjpeg ( ASD_PATH . $location );
 				
			$x = imagesx ( $im ); $y = imagesy ( $im );
			if ( $x < 64 || $y < 64 ) return ( false );
				
 			return ( $location );
 		}
 		
		$imgBuffer = $this->GetSys ( 'Communication' )->Retrieve ( $pLocation );
		
	 	$im = imagecreatefromstring($imgBuffer);
	 	
	 	$x = imagesx ( $im );
	 	$y = imagesy ( $im );
	 	
	 	if ( $x < 64 || $y < 64 ) {
	 		// Resize down to 1px as a placeholder, so we don't keep retrieving the remote file.
	 		$resized = $this->GetSys ( 'Image' )->ResizeAndCrop ( $im, 1, 1 );
 			$saved = $this->GetSys ( 'Storage' )->SaveImage ( $resized, 'page', 'thumbnails', $pDirectory, $pFilename, $overwrite = false );
 		
 			return ( false );
	 	}
	 	
		// Create the local thumbnail.
	 	$resized = $this->GetSys ( 'Image' )->ResizeAndCrop ( $im, 64, 64 );
 		$saved = $this->GetSys ( 'Storage' )->SaveImage ( $resized, 'page', 'thumbnails', $pDirectory, $pFilename, $overwrite = false );
 		
 		imagedestroy ( $im );
 		imagedestroy ( $resized );
 			
 		if ( $saved )
 			return ( $location );
 		else
 			return ( false );
	}
	
}
