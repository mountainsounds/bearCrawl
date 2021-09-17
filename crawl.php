<?php
include("config.php");
include("classes/DomDocumentParser.php");
include("helpers/createLink.php");
include("helpers/linkExists.php");
include("helpers/insertData.php");

$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImages = array();

function getDetails($url) {
	global $alreadyFoundImages;

	$parser = new DomDocumentParser($url);

	$titleArray = $parser->getTitleTags();

	if(sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) return;

	$title = $titleArray->item(0)->nodeValue;
	$title = str_replace("\n", "", $title);

	if($title == "") return;

	$description = "";
	$keywords = "";

	$metasArray = $parser->getMetatags();

	foreach($metasArray as $meta) {

		if($meta->getAttribute("name") == "description") {
			$description = $meta->getAttribute("content");
		}

		if($meta->getAttribute("name") == "keywords") {
			$keywords = $meta->getAttribute("content");
		}
	}

	$description = str_replace("\n", "", $description);
	$keywords = str_replace("\n", "", $keywords);

	if (linkExists($url)) {
		echo "$url already exists<br>";
	}
	else if (insertLink($url, $title, $description, $keywords)) {
		echo "SUCCESS: $url<br>";
	} else {
		echo "ERROR: FAILED TO Insert $url<br>";
	}

	$imageArray = $parser->getImages();
	foreach($imageArray as $image) {
		$src = $image->getAttribute("src");
		$alt = $image->getAttribute("alt");
		$title = $image ->getAttribute("title");

		if (!$title && !$alt) continue;

		$src = createLink($src, $url);

		if (!in_array($src, $alreadyFoundImages)) {
			$alreadyFoundImages[] = $src;
			insertImage($url, $src, $alt, $title);
		}
	}

}

function followLinks($url) {

	global $alreadyCrawled;
	global $crawling;

	$parser = new DomDocumentParser($url);

	$linkList = $parser->getLinks();

	foreach($linkList as $link) {
		$href = $link->getAttribute("href");

		if(strpos($href, "#") !== false) {
			continue;
		}
		else if(substr($href, 0, 11) == "javascript:") {
			continue;
		}

		$href = createLink($href, $url);

		if(!in_array($href, $alreadyCrawled)) {
			$alreadyCrawled[] = $href;
			$crawling[] = $href;

			getDetails($href);
		}
	}

	array_shift($crawling);

	foreach($crawling as $site) {
		followLinks($site);
	}
}

$startUrl = "https://en.wikipedia.org/wiki/Dog";
followLinks($startUrl);
?>