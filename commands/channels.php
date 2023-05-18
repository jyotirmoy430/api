<?php
function init(){
    $URL = 'http://bdiptv.net/';
    getChannelsFromUrl($URL);

}


function getChannelsFromUrl($url){
    $html = file_get_contents($url);

    if(!$html)
        return [];

    // Create a DOM document object
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);  // Disable error reporting for invalid HTML

// Load the HTML content into the DOM document
    $dom->loadHTML($html);
    libxml_clear_errors();  // Clear any parsing errors

// Create a DOMXPath object to query the DOM document
    $xpath = new DOMXPath($dom);

// Search for all div elements with the class name "item_content"
    $divClassName = "item_content";
    $divQuery = "//div[contains(@class, '$divClassName')]";
    $divNodes = $xpath->query($divQuery);

// Iterate through the matched div nodes and find anchor tags within them
    foreach ($divNodes as $divNode) {
        $anchorTags = $divNode->getElementsByTagName('a');

        // Iterate through the anchor tags and display their attributes or text
        foreach ($anchorTags as $anchorTag) {
            // Display the anchor tag's attributes or text
            $href = $anchorTag->getAttribute('onclick');
            $text = $anchorTag->nodeValue;
            echo "Link: $href, Text: $text" . PHP_EOL;
        }
    }

    exit;

}
