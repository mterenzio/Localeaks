<?php

//header('Content-Type: application/rss+xml');
$doc = new DOMDocument("1.0");

//create the rss element
$root = $doc->createElement('rss');
$root = $doc->appendChild($root);
$root->setAttribute('version','2.0');

//create the channel element
$channel = $doc->createElement("channel");
$channel = $root->appendChild($channel);

$elements = array();
$elements["title"] = "Latest Localeaks";
$elements["link"] = "https://localeaks.com/feeds/";
$elements["description"] = "Loacleaks secure feed";
$elements["language"] = "en-us";
$elements["copyright"] = "Localeaks";
$elements["docs"] = "http://backend.userland.com/rss";
$elements["generator"] = "localeaks secure rss generator v. 2.0";
$elements["managingEditor"] = "news@localeaks.com";
$elements["webMaster"] = "webmaster@localeaks.com";
//$elements["lastBuildDate"] = "Sat, 05 Feb 2005 23:39:07 EST";

foreach ($elements as $elementname => $elementvalue)
{
 $elementname = $doc->createElement($elementname);
 $elementname = $channel->appendChild($elementname);
 $elementname->appendChild($doc->createTextNode($elementvalue));
}
//profile pic
$image = $doc->createElement("image");
$channel->appendChild($image);
//profile pic location
$imageurl = $doc->createElement("url");
$imageurl->appendChild($doc->createTextNode('https://localeaks.com/img/logo.png'));
$image->appendChild($imageurl);
//profile pic title
$imagetitle = $doc->createElement("title");
$imagetitle->appendChild($doc->createTextNode("Localeaks"));
$image->appendChild($imagetitle);
//profile pic link
$imagelink = $doc->createElement("link");
$imagelink->appendChild($doc->createTextNode("https://localeaks.com"));
$image->appendChild($imagelink);
//clear the array
unset($elements);
$elements = array();
//$cloud = $doc->createElement("cloud");
//$cloud->setAttribute('domain','brisk.ly');
//$cloud->setAttribute('port','80');
//$cloud->setAttribute('path','/rpc/');
//$cloud->setAttribute('registerProcedure', 'subscribe');
//$cloud->setAttribute('protocol','xml-rpc');
//$channel->appendChild($cloud);
//now create the first feed item
//instead, you could just pull some items from a db into an elements[] array.
$itemnumber = 0;
//echo print_r($leaks, true);
foreach ($leaks as $leak) {

$elements[$itemnumber]["title"] = $leak->leak_view_time;
$elements[$itemnumber]["description"] = "Tip for".$leak->org_name;
//$elements[$itemnumber]["link"] = "http://www.thehour.com/story/".$story['id'];
//$elements[$itemnumber]["author"] = "Staff";
//$date = new DateTime($story['timestamp']);
//$rssdate = $date->format(DateTime::RSS);
//$elements[$itemnumber]["pubDate"] = $rssdate;
//$elements[$itemnumber]["category"] = "tech";
//$elements[$itemnumber]["guid"] = "http://www.thehour.com/story/".$leak['g'];
$elements[$itemnumber]["enclosure"] = "https://localeaks.com/account/download/".$leak->leak_file;
//$catname = $category->getNameFromID($story['category']);
//$elements[$itemnumber]["category"] = $story['priority'];//$catname;
	//add priority to see if breaking
    //$prioritycat = $doc->createElement("category");
    //$prioritycat->appendChild($doc->createTextNode($story['priority']));
    //$item->appendChild($prioritycat);
$itemnumber++;
}

//loop through each item and add its elements to the tree
foreach ($elements as $element)
{
	//create the item element
	$item = $doc->createElement("item");
	$item = $channel->appendChild($item);
	
	foreach ($element as $elementname => $elementvalue)
	{
		$elementname = $doc->createElement($elementname);
 		$elementname = $item->appendChild($elementname);
 		$elementname->appendChild($doc->createTextNode($elementvalue));
	}

}


//output the xml

echo $doc->saveXML();

?>