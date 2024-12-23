<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class WebscrapperController extends Controller
{
    public function scrape(Request $request)
    {
        try {
            // Initialize Guzzle client
            $client = new Client([
                'base_uri' => 'https://www.walmart.com/search?q=basketball',
                'timeout' => 10.0,
                'connect_timeout' => 5.0,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                ],
                'verify' => false, // Disable SSL verification
            ]);

            // Fetch all items
            $response = $client->get('');
            $body = $response->getBody()->getContents();

            // Initialize DomCrawler
            $crawler = new Crawler($body);
            $traversableItems = [];
            $imageArray = [];

            // Process all anchor tags
            $crawler->filter('a')->each(function (Crawler $node, $index) use (&$traversableItems, &$imageArray, $crawler) {
                $itemName = $node->text();
                $href = $node->attr("href");
                $substring = 'track';
                $imageURL = $crawler->filter("#is-0-productImage-$index")->attr("src", "");

                if (str_contains($href, $substring) && $itemName !== '') {
                    $traversableItems[] = [
                        "itemName" => $itemName,
                        "link" => $href,
                    ];
                } else {
                    $imageArray[] = $imageURL;
                }

            });
            
            if (count($traversableItems) > 1 && count($imageArray) > 1) {
                $lastIndex = count($traversableItems) - 1; 
                $index = 0;
                foreach ($traversableItems as $product){
                    echo $index;
                    $traversableItems[$index]["imageUrl"] = $imageArray[$index];
                    $index++;
                }
                
            }
            
                        #is-0-productImage-$index
                        // $itemNameArray = explode(' ', $itemName);
                     
                        // if(str_contains($href, $substring) == true && str_contains($href, $itemNameArray[0]) == true && $itemName !== ''){
                    
                        //     // For Each Item that has the url substring 'track' create a new item and append it too traversable array
                        //    }
            
                    // Return all anchor tags that contains the substring 'track' in the url link
                    // For Example https://www.walmart.com/track because all links that include the string 'track' is a commonality between purchaseable items found by query
                    // Also checking to see if any of the items have an empty name field if so dont return name with url (likely a url to another part of the website if this is true)
                    // IMPORTANT: Splitting each item's name into an array of strings and getting the first word of each itemNameArray to target items with valid urls instead of returning urls that may not be associated to an item!
          

                
                // get all images
               
                // get all prices
                // $itemPriceArray = [];
                // $pricesArray = new ArrayObject();
                //  $crawler->filter('div > span')->each(function (Crawler $node, $i) use ($traversableItems, $pricesArray) {
                //     $itemPrice = $node->text();
                //     if(str_contains($itemPrice, "current price") == true){
                //         $itemPriceArray = explode(' ', $itemPrice);
                //         $lastIndex = count($itemPriceArray) - 1;
                //         $itemPrice = $itemPriceArray[$lastIndex];
                //         $pricesArray->append($itemPrice);
                //         echo count($itemPriceArray);
                //     };
                // });
                //    // get and attach all prices
                //     foreach($pricesArray as $key => $itemValue){
                //         $traversableItems[$key]["price"] = $pricesArray[$key]; 
                // }

            return response()->json([
                'message' => 'Scraping successful!',
                'data' => $traversableItems,
            ]);
        } catch (RequestException $e) {
            // Handle Guzzle exceptions
            return response()->json([
                'error' => 'Request failed',
                'message' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json([
                'error' => 'An unexpected error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
