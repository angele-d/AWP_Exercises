
<?php

require_once __DIR__ . '/vendor/autoload.php'; // Load the Composer autoloader, which know where to find the simplehtmldom library

use simplehtmldom\HtmlDocument; // Use the HtmlDocument class from the simplehtmldom namespace

require_once __DIR__ . '/src/GraduateThesis.php';
require_once __DIR__ . '/database.php'; // Load the database connection file

// Function to fetch HTML from a URL using cURL 
function getHTML(string $url): string|false { 

    $curl = curl_init($url);  // Initialize a cURL session with the provided URL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  // Set the option to return the response as a string instead of outputting it directly
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);  // Set the option to follow any "Location: " header that the server sends as part of the HTTP header
    curl_setopt($curl, CURLOPT_TIMEOUT, 60);  // Set a timeout for the request (in seconds)

    $html = curl_exec($curl); 
    if ($html === false)
        echo "Error : " . curl_error($curl) . "\n"; 

    return $html; 
}

for ($number = 2; $number <= 6; $number++) {

    // Create the URL for the current page number
    $url = "https://stup.ferit.hr/zavrsni-radovi/page/$number/";

    echo "========== Page $number analyze ==========\n\n";

    // Initialize cURL
    $curl = curl_init();

    // Configure the request
    curl_setopt($curl, CURLOPT_URL, $url); // Set the URL to fetch
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the response as a string instead of outputting it directly
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    curl_setopt($curl, CURLOPT_TIMEOUT, 60); // Set a timeout for the request (in seconds)

    // Execute the cURL request and store the response in $html
    $html = curl_exec($curl);

    // Check for errors
    if ($html === false) {
        // If there's an error, display it and continue to the next page
        echo "Error : " . curl_error($curl) . "\n";
        continue;
    }

    // Check the HTTP status code of the response
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    // Check if the HTTP status code indicates success (200 OK)
    if ($status !== 200) {
        // If the status code is not 200, display an error message and continue to the next page
        echo "Error HTTP : $status\n";
        continue;
    }

    $dom = new HtmlDocument(); // Create a new instance of HtmlDocument
    $dom->load($html); // Load the HTML content into the DOM parser
    
    foreach ($dom->find('article') as $article) {

        // Find the title link within the article element
        $titleLink = $article->find('h2 a', 0); // Get the first <a> element within the <h2> tag of the article

        if ($titleLink === null) // If no title link is found, skip to the next article
            continue;

        $workName = trim($titleLink->plaintext); // Get the text content of the title link and trim whitespace
        $workLink = $titleLink->href; // Get the href attribute of the title link, which is the URL to the work

        // Find the image within the article element
        $image = $article->find('img', 0); // Get the first <img> element within the article

        $identificationNumber = null;
        // If an image is found, extract its identification number
        if ($image !== null) {
            $imagePath = parse_url($image->src, PHP_URL_PATH); // Get the path component of the image URL
            $identificationNumber = pathinfo($imagePath, PATHINFO_FILENAME); // Extract the filename (without extension) from the image path, which is used as the identification number
        }

        // Find the description within the article element
        $workTextLink = getHTML($workLink); // Fetch the HTML content of the work link
        
        if ($workTextLink === false) { // If fetching the work link fails, skip to the next article
            echo "Error fetching work link: $workLink\n";
            continue;
        }

        $workTextDom = new HtmlDocument(); // Create a new instance of HtmlDocument for the work text
        $workTextDom->load($workTextLink); // Load the fetched HTML content into the DOM parser
        
        $workText = $workTextDom->find('div.post-content', 0); // Find the first <div> element with class "post-content" in the work text DOM
        $workText = $workText ? trim($workText->plaintext) : ''; // If the post-content div is found, get its text content and trim whitespace; otherwise, set work

        // Create a new GraduateThesis object and add it to the $theses array
        $thesis = GraduateThesis::create($workName, $workText, $workLink, (int)$identificationNumber);
        
        echo "Identification number : " . $thesis->identification_number . " is being inserted to the table.\n";

        // Save the GraduateThesis object to the database
        $thesis->save($pdo);
    }
}

echo "========== End of scraping ==========\n\n";

?>
