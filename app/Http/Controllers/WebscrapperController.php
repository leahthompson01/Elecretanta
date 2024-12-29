<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use EchoLabs\Prism\Prism;
use EchoLabs\Prism\Enums\Provider;
use ArrayObject;

class WebscrapperController extends Controller
{
    public function scrape($geminiGiftSuggestions)
    {   
        $responseArray = [];
        if(is_array($geminiGiftSuggestions)){
            foreach ($geminiGiftSuggestions as $suggestion) {
                $itemName = $suggestion['item'];
                
                try {
                    // Initialize Guzzle client
                    $client = new Client([
                        'base_uri' => "https://www.walmart.com/search?q=$itemName",
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
        
                        if (str_contains($href, $substring) && $itemName !== '' && str_contains($itemName, "Sponsored") == false && str_contains($itemName, "Shop now") == false && str_contains($itemName, "Options") !== true) {
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
                            foreach ($imageArray as $imageURL){
                                $itemNameString = preg_replace("/[^a-zA-Z0-9\s]|[!:]/", "", $product['itemName']);
                                $itemNameArray = explode(" ", $itemNameString);
        
                                
                                
        
                                switch (count($itemNameArray)){
                                    case 1:
                                        if(str_contains($imageURL, $itemNameArray[0]) == true){
                                            
                                            $traversableItems[$index]["imageUrl"] = $imageURL;
                                        }
                                    break;
                                    case 2:
                                        if(str_contains($imageURL, $itemNameArray[0]) == true && str_contains($imageURL, $itemNameArray[1]) == true ){
                                           
                                            $traversableItems[$index]["imageUrl"] = $imageURL;
                                        }
                                        
                                    //code block;
                                    break;
                                    case 3:
                                        if(str_contains($imageURL, $itemNameArray[0]) == true && str_contains($imageURL, $itemNameArray[1]) == true && str_contains($imageURL, $itemNameArray[2]) == true ){
                                         
                                            $traversableItems[$index]["imageUrl"] = $imageURL;
                                        }
                                    //code block
                                    break;
                                    default:
                                        if(str_contains($imageURL, $itemNameArray[0]) == true && str_contains($imageURL, $itemNameArray[1]) == true && str_contains($imageURL, $itemNameArray[2]) == true && str_contains($imageURL, $itemNameArray[3]) == true ){
                                            
                                            $traversableItems[$index]["imageUrl"] = $imageURL;
                                        }
                                    //code block
                                }
                                
          
                               
                            }
                            
                            $index++;
                        }
                        
                    }
                    $itemPriceArray = [];
                        $pricesArray = new ArrayObject();
                         $crawler->filter('div > span')->each(function (Crawler $node, $i) use ( $pricesArray) {
                            $itemPrice = $node->text();
                            if(str_contains($itemPrice, "current price") == true){
                                $itemPriceArray = explode(' ', $itemPrice);
                                $lastIndex = count($itemPriceArray) - 1;
                                $itemPrice = $itemPriceArray[$lastIndex];
                                $pricesArray->append($itemPrice);
                            }
                            else {
                                return;
                            }
                        });
                           // get and attach all prices
                            foreach($pricesArray as $key => $itemValue) {
                                $numberOfProducts = count($traversableItems) - 1;
                                if($key <= $numberOfProducts){
                                 
                                    $traversableItems[$key]["price"] = $pricesArray[$key]; 
                                    if($traversableItems[$key]['itemName'] !== '' && $traversableItems[$key]['link'] !== '' && $traversableItems[$key]['price'] !== '') {
                                        echo "NOT NULL";
                                    }   
                        }
                    };
                            // Return all anchor tags that contains the substring 'track' in the url link
                            // For Example https://www.walmart.com/track because all links that include the string 'track' is a commonality between purchaseable items found by query
                            // Also checking to see if any of the items have an empty name field if so dont return name with url (likely a url to another part of the website if this is true)
                            // IMPORTANT: Splitting each item's name into an array of strings and getting the first word of each itemNameArray to target items with valid urls instead of returning urls that may not be associated to an item!
                      
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
            

            
        } else {
            echo "NOT AN ARRAY";
        }
        
    }

    public function generateGiftIdeas(Request $request){
        try{
            $response = Prism::text()->using(Provider::Gemini, "gemini-1.5-flash")->withPrompt(
                "I am looking to gift an age appropriate gift for a 20 year old with a budget less than $20, and the person has the following interests: hoodies, sports, TV shows, Netflix, and ice cream. Please provide only a valid JSON array without any additional text or wrapping elements like code block markers or comments. The JSON should only include the data in array form with objects containing the keys `item` and `reason`. Do not add anything else.\n\nFor example:\n\n[\n    {\n        \"item\": \"Gift card for streaming service (Netflix etc.)\",\n        \"reason\": \"Appeals to their interest in TV shows and Netflix, and is easily adjustable to their budget.\"\n    },\n    {\n        \"item\": \"Sports-themed socks or small accessory\",\n        \"reason\": \"Relatively inexpensive and caters to their interest in sports.\"\n    }\n]"
            )->generate();
        
            $response = $response->text;
            $geminiGiftSuggestion = json_decode($response, true);
                
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "ERRORRRRR";
                throw new \Exception('Invalid JSON: ' . json_last_error_msg());
            }
            return $this->scrape($geminiGiftSuggestion);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
};
