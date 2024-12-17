<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use ArrayObject;
class WebscrapperController extends Controller
{
   
    //
    public function scrape(Request $request){  
            try {
                // Initialize Guzzle client with proper configurations
                // Create new client to add alternate paths based on traversable items array
                $client = new Client([
                    'base_uri' => 'https://www.walmart.com/search?q=camping+supplies',
                    'timeout' => 10.0,
                    'connect_timeout' => 5.0,
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                        'Accept-Language' => 'en-US,en;q=0.5',
                    ],
                    'verify' => false, // Disable SSL verification
                ]);
    
                // Fetch all items based on the search parameters
                $response = $client->get('');
                $body = $response->getBody()->getContents();


                
                // Initiate Dom Crawler from Sympony
                $crawler = new Crawler($body);
                $traversableItems = new ArrayObject();
                $itemPrice = 0;
                // Fetch all anchor tags on page
                $crawler->filter('a')->each(function (Crawler $node) use ($traversableItems) {
                    $href = $node->attr("href");
                    $substring = 'track';
                    $itemName = $node->text();
                
                    $itemNameArray = explode(' ', $itemName);
                    // Return all anchor tags that contains the substring 'track' in the url link
                    // For Example https://www.walmart.com/track because all links that include the string 'track' is a commonality between purchaseable items found by query
                    // Also checking to see if any of the items have an empty name field if so dont return name with url (likely a url to another part of the website if this is true)
                    // IMPORTANT: Splitting each item's name into an array of strings and getting the first word of each itemNameArray to target items with valid urls instead of returning urls that may not be associated to an item!
                   if(str_contains($href, $substring) == true && str_contains($href, $itemNameArray[0]) == true && $itemName !== ''){
                    
                    // For Each Item that has the url substring 'track' create a new item and append it too traversable array
                    $newItem = [
                           'name' => $itemName,
                           'href' => $href,
                        ];
                    $traversableItems->append($newItem);

                    
                   }
                   else {
                    return false;
                   }

                });

                
                // get all images
                $crawler->filter('img')->each(function (Crawler $node, $index) use ($traversableItems) {
                   
                    $imageUrl = $node->attr("src");
                    $substring = 'href';
                    $itemName = $node->text();

                    while(count($traversableItems) > $index) {
                        // var_dump($traversableItems[$index]);
                       
                        $traversableItems[$index]["imageUrl"] = $imageUrl;

                        return;
                    };
 
                 });
                // get all prices
                $itemPriceArray = [];
                 $crawler->filter('div > span')->each(function (Crawler $node, $i) use ($traversableItems) {
                    $itemPrice = $node->text();
                    $pricesArray = new ArrayObject();
                    if(str_contains($itemPrice, "current price") == true){
                        echo "NEW LINE", $itemPrice, PHP_EOL;
                        $itemPriceArray = explode(' ', $itemPrice);
                        $lastIndex = count($itemPriceArray) - 1;
                        $itemPrice = $itemPriceArray[$lastIndex];
                        $pricesArray->append($itemPrice);
                    };

                    
                });
                    foreach($traversableItems as $key => $item){
                        $itemPrice = $itemPriceArray[$key];
                        $traversableItems[$key]["price"] = $itemPrice; 
                }
                

                return response()->json([
                    'message' => 'Scraping successful!',
                    'data' => $traversableItems,
                ]);
    
            } catch (RequestException $e) {
                // Handle Guzzle-specific request exceptions
                return response()->json([
                    'error' => 'Request failed',
                    'message' => $e->getMessage(),
                ], 500);
    
            } catch (\Exception $e) {
                // Handle any other exceptions
                return response()->json([
                    'error' => 'An unexpected error occurred',
                    'message' => $e->getMessage(),
                ], 500);
            }
        }
}