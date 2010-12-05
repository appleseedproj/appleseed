<?php
	//--------------------------------------------------------------------------------
	//	RSS2Writer (v2.1) and sample controller
	//
	//	(c) Copyright Daniel Soutter
	//	
	//	Blog: http://www.code-tips.com/
	//	
	//  This script may be used or developed at your own risk as long as it is 
	//	referenced appropriately.
	//
	//	This script may be used or developed at your own risk as long as it is 
	//	referenced appropriately.
	//	Please post any comments or suggestions for improvement to 
	//	my blog (above).
	//
	//	For technical information about this Php RSS2Writer (v2.1) class, see: 
	//	http://www.web-resource.org/web-programming/free-php-scripts/RSS2Writer/
	//
	//	For usage instructions for the Php RSS2Writer (v2.1) class, see: 
	//	http://www.code-tips.com/2010/01/php-rss2writer-v20-generate-rss-20-feed.html
	//--------------------------------------------------------------------------------

class RSS2Writer
{
	//Declare variables 
	var $xml;				// Used to store the rss xml
	var $indent;
	var $useCDATA = false;
	
	var $feedData = Array("title" => "", "description" => "", "link" => "");
	
	var $channelData = Array();	
	var $channelCategories = Array();
	var $channelImage = null;
	var $channelCloud = null;
	
	
	var $itemsArray = Array();
	
	
	/*********************************************************************************
	* Class Constructor
	*
	* Description:	Creates an instance of the XMLWriter and starts an xml 1.0 
	*				document.  Starts required RSS 2.0 elements (rss, channel)
	*	
	* Paramaters:	String $title			-	Channel title
	*				String $description		-	Channel description
	*				String $link			-	link to channel (unique)
	*				Int $indent				-	Xml indent level
	*				Boolean $useDATA		-	use CDATA for feed title, content
	*
	* Returns:		Void
	**********************************************************************************/
	function __construct($title, $description, $link, $indent = 6, $useCDATA = false)	//Constructor
	{
	
		$this->feedData['title'] = $title;
		$this->feedData['description'] = $description;
		$this->feedData['link'] = $link;
		$this->indent = $indent;
		$this->feedData['useCDATA'] = $useCDATA;
	}
	
	/*********************************************************************************
	* function addItem
	*
	* Description:	Add an item to the feed. 
	*	
	* Paramaters:	String $title			-	Item title
	*				String $description		-	Item description
	*				String $link			-	link to item (unique)
	*				2D Array $optionalElements[][]		
											-    Array[n] = Array("elementName" => (String), "value" => (String))
	*
	* Returns:		Void
	**********************************************************************************/
	function addItem($title = null, $description = null, $link = null)
	{
		$this->itemsArray[] = Array("title" => $title, "description" => $description, "link" => $link, "optionalElements" => Array(), "itemCategories" => Array());
	}

	
	
	/*********************************************************************************
	* function addElement (Optional)
	*
	* Description:	Generic function to add any an Optional element to the Chanel 
	*				or most recent item
	*	
	* Paramaters:	String $elementName			-	Name of the element to add
	*				String $val					-	Value of the element
	*				2D Array: $attributes[n]	-	Array(AttributeName, AttributeValue)
	*
	* Returns:		Void
	**********************************************************************************/
	function addElement($elementName, $val = null, $attributes = Array()){
	
		if (count($this->itemsArray) > 0)
		{
			//Add to most recent item
			$this->itemsArray[count($this->itemsArray)-1]['optionalElements'][] = Array("elementName" => $elementName, "value" => $val, "attributes" => $attributes);
		}
		else
		{
			//Add to channel
			$this->channelData[] = Array("elementName" => $elementName, "value" => $val, "attributes" => $attributes);
		}

	}
	
	
	/*********************************************************************************
	* function addCategory
	*
	* Description:	Generic function to add categories to the channel and/or items.
	*				Call this function before adding items to the feed 
	*				to add categories to the channel.  Call after adding an item
	*				to assign categories/tags to individual feed items.
	*
	*				You can add multiple categories to a channel or item.  This
	*				function will need to be called once to add each category.
	*	
	* Paramaters:	Array $categories [][]	-	Multidimensional array of categories 
	*											and optional domain.  Array details:
	*										 		[n][0] = category name, 
	*												[n][1] = domain (or null)
	* Returns:		Void
	**********************************************************************************/
	function addCategory($categoryName, $domain = null) // 
	{
		if (count($this->itemsArray) > 0)
		{
			//Add category to most recent item
			$this->itemsArray[count($this->itemsArray)-1]['itemCategories'][] = Array($categoryName, $domain);
		}
		else
		{
			//Add category to Channel
			$this->channelCategories[] = Array($categoryName, $domain);
		}

	}
	
	
	/*********************************************************************************
	* function channelImage (Optional)
	*
	* Description:	Add an image to the channel.
	*				
	*	
	* Paramaters:	String $title			-	Channel title
	*				String $link			-	link to send to if clicked
	*				String $url				-	url to image
	*				String $width			-	image width
	*				String $height			-	image height
	*
	* Returns:		Void
	**********************************************************************************/
	function channelImage($title, $link, $url, $width, $height) //All Strings
	{
		$this->channelImage = Array($title, $link, $url, $width, $height);

	}
	
	
	/*********************************************************************************
	* function channelCloud (Optional)
	*
	* Description:	Add cloud connectivity settings to the channel
	*				
	*	
	* Paramaters:	String $domain			-	cloud domain/server
	*				String $port			-	cloud port
	*				String $path			-	path to feed data
	*				String $regProcedure
	*				String $protocol	
	*
	* Returns:		Void
	**********************************************************************************/
	function channelCloud($domain, $port = '80', $path, $regProcedure = 'pingMe', $protocol = 'soap')
	{
		$this->channelCloud = Array($domain, $port, $path, $regProcedure, $protocol);

	}
		
	
	/*********************************************************************************
	* function getXML
	*
	* Description:	Generate and return the full rss feed xml
	*	
	* Paramaters:	none
	*
	* Returns:		Returns the rss feed xml as a String
	**********************************************************************************/
	function getXML()
	{	
	
		//Set the default timezone
		@date_default_timezone_set("GMT"); 
		
		//Create the xml write object
		$writer = new XMLWriter(); 
		
		//XMLWriter Output method:
		//------------------------------------------------------------------------------------------
		$writer->openMemory(); 					//	Xml stored in memory (store in variable, output 
												//  to file, print/echo	to user, etc.
												
		//$this->$writer->openURI('php://output');  	//	Send xml to browser/user
		//-----------------------------------------------------------------------------------------
	
		
		//XML Version
		$writer->startDocument('1.0'); 			
		
		//Indent level
		$writer->setIndent($this->indent); 

		//Create first element / main block	(Xml type - RSS 2.0)
		$writer->startElement('rss');
		//Start RSS--------------------------------------------------------------------------------
		//*****************************************************************************************
		//RSS attribute(s) 
		$writer->writeAttribute('version', '2.0');  
		
		$writer->startElement("channel"); 
		//Start Channel------------------------------------------------------------------------
		
		//Required Channel Elements
		//---------------------------------------------------------
		$writer->writeElement('title', $this->feedData{'title'}); 
		$writer->writeElement('description', $this->feedData['description']); 
		$writer->writeElement('link', $this->feedData['link']); 
		
		
		
		//Optional Channel Elements
		//---------------------------------------------------------

	
		foreach ($this->channelCategories as $category)
		{
			//Category block
			$writer->startElement('category'); 
				if($category[1] != null) //category has an associated domain
					$writer->writeAttribute('domain', $category[1]); 
				$writer->text($category[0]);  //Category Name
			$writer->endElement();
		}

		
		if($this->channelCloud != null)
		{
			//Cloud block - Allow registration with a cloud to recieve notification of feed updates 
			
			$writer->startElement('cloud');
				$writer->writeAttribute('domain', $this->channelCloud[0]); 
				$writer->writeAttribute('port', $this->channelCloud[1]);
				$writer->writeAttribute('path', $this->channelCloud[2]);
				$writer->writeAttribute('registerProcedure', $this->channelCloud[3]);
				$writer->writeAttribute('protocol', $this->channelCloud[4]);
			$writer->endElement();
		}

		
		if($this->channelImage != null)
		{
			//Channel Image (Optional)
			
			$writer->startElement('image'); 
				$writer->writeElement('title', $this->channelImage[0]); 
				$writer->writeElement('link', $this->channelImage[1]); 
				$writer->writeElement('url', $this->channelImage[2]); 
				$writer->writeElement('width', $this->channelImage[3]); 
				$writer->writeElement('height', $this->channelImage[4]); 
			$writer->endElement(); 
		}
		
		

		foreach ($this->channelData as $element)
		{
			//Other Optional Elements
			$writer->startElement($element['elementName']);
			
			foreach ($element['attributes'] as $attribute)
				$writer->writeAttribute($attribute[0], $attribute[1]); 
				
			if ($element['value'] != null)
				$writer->text($element['value']);  //Element Value
			
			$writer->endElement();

		}

		
		//Output the items
		foreach ($this->itemsArray as $item)
		{
			$writer->startElement("item"); 
			//Start Item-----------------------------------------------------------------------
			
			if($this->useCDATA)
			{
				/*=============Changes By abasit83 v2.1 ===============*/
				$writer->startElement("title");	
				$writer->writeCData($item['title']);
				$writer->endElement();	
				
				$writer->startElement("link");	
				$writer->writeCData($item['link']);
				$writer->endElement();	
				
				/* REMOVED so that guid can be set manually : Michael Chisari 12-04-2010 */
				// $writer->startElement("guid");	
				// $writer->writeCData($item['link']);
				// $writer->endElement();	
				/*=============END Changes By abasit83===============*/
			}
			else
			{
				$writer->writeElement('title', $item['title']); 
				$writer->writeElement('link', $item['link']); 
				/* REMOVED so that guid can be set manually : Michael Chisari 12-04-2010 */
				// $writer->writeElement('guid', $item['link']); 
			}
											
			foreach ($item['optionalElements'] as $element)
			{
				$writer->writeElement($element['elementName'], $element['value']); 
			}
			
			foreach ($item['itemCategories'] as $category)
			{
				//Category block
				$writer->startElement('category'); 
					if($category[1] != null) //category has an associated domain
						$writer->writeAttribute('domain', $category[1]); 
					$writer->text($category[0]);  //Category Name
				$writer->endElement();
			}
			
			//Item Content
			if($this->useCDATA)
			{
				/*=============Changes By abasit83 v2.1===============*/
				$writer->startElement("description");	
				$writer->writeCData($item['description']);
				$writer->endElement();	
				/*=============END Changes By abasit83===============*/
			}
			else
			{
				$writer->writeElement('description', $item['description']); 
			}
			$writer->endElement(); 
			//End Item ------------------------------------------------------------------------
		}
	
		/*
		$writer->startElement('atom:link'); 
			$writer->writeAttribute('href', $this->feedData['link']); 
			$writer->writeAttribute('rel', 'self'); 
			$writer->writeAttribute('type', 'application/rss+xml'); 
		$writer->endElement();
		*/
	
		$writer->endElement(); 
		//End channel -------------------------------------------------------------------------
			
		// End rss 
		$writer->endElement(); 
		//-----------------------------------------------------------------------------------------
		//*****************************************************************************************

		//End Xml Document
		$writer->endDocument(); 

		$this->xml = $writer->outputMemory(true);

		return $this->xml;

	}
	
	
	/*********************************************************************************
	* function getXMLFiltered: Comming in the next version (3.0)
	*
	* Description:	generates and returns the rss feed xml filtered by the categories passed to the function.
	* 				The resulting RSS feed will only contain items which have one or more of the
	*				specified categories.
	*	
	* Paramaters:	$categories: Array("category_name1", "category_name2", "category_nam3")
	*
	* Returns:		Returns the rss feed xml as a String
	**********************************************************************************/
	function getXMLFiltered($categories)
	{	
	
	}
	

	/*********************************************************************************
	* function writeToFile
	*
	* Description:	Writes the generated rss xml to a file
	*	
	* Paramaters:	String $fileName		-	Filename to save the rss feed xml
	*				Array $categories (Optional) - write a filtered RSS Feed to a file
	*
	* Returns:		Void
	**********************************************************************************/
	function writeToFile($fileName, $categories = null)
	{
		$this->closeDocument();
		
		$fh = fopen($fileName, 'w') or die("can't open file");
		
		if(!$categories == null)
			fwrite($fh, $this->getXML());
		else
		{
			fwrite($fh, $this->getXML());
			//fwrite($fh, $this->getXMLFiltered($categories));
		}
		
		fclose($fh);
	}
	
	
	
	
	
	
	
	//  Note:
	//--------------------------------------------------------------------------------
	//  The following functions are no longer in use.  They remain in the class for 
	//  cases where they may be used, but can be removed if not required.  Any versions
	//  of the RSS2Writer class published after this one (v2.0) will not include the 
	//  functions below.
	//--------------------------------------------------------------------------------
	
	/*********************************************************************************
	* function closeItem - No longer in use
	*
	* Description:	Closes an item element only if the current has been left open.
	*	
	* Paramaters:	none
	*
	* Returns:		Void
	**********************************************************************************/
	function closeItem()
	{
		if($this->itemOpen)
		{
			$this->xml .= '</item>
';//end item tag with new line
			$this->itemOpen = false;
		}
	}
	
	
	
	/*********************************************************************************
	* private function closeDocument - No longer in use
	*
	* Description:	Closes the Channel and rss elements as well as the document
	*	
	* Paramaters:	none
	*
	* Returns:		Void
	**********************************************************************************/
	private function closeDocument()
	{
		//Create the xml write object
		$writer = new XMLWriter(); 
		$writer->openMemory(); 
		$writer->setIndent(4); 
		
		// Start the xml elements which requiring closing (allow endElement() function to work)
		$writer->startElement('rss');
		$writer->startElement('channel'); 
		$writer->text('.');
		
		//Flush the current xml to remove start tags, but allow correct elements to be closed.
		$writer->flush(); 
		
		$writer->endElement(); 
		//End channel -------------------------------------------------------------------------
			
		// End rss 
		$writer->endElement(); 
		//-----------------------------------------------------------------------------------------
		//*****************************************************************************************

		//End Xml Document
		$writer->endDocument(); 

		//$writer->flush(); 
		$this->xml .= $writer->outputMemory(true);
	}
	
	
}
?>