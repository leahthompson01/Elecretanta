<?php
namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use EchoLabs\Prism\Prism;
use EchoLabs\Prism\Enums\Provider;
use EchoLabs\Prism\ValueObjects\Messages\UserMessage;
use EchoLabs\Prism\ValueObjects\Messages\Support\Image;
use EchoLabs\Prism\Exceptions\PrismException;
use ArrayObject;

class WebscrapperController extends Controller
{
    public function scrape($giftSuggestion)
    {    
   
        try {
                    
                    // Initialize Guzzle client
                    $client = new Client([
                        'base_uri' => "https://www.walmart.com/search?q=$giftSuggestion",
                        'timeout' => 10.0,
                        'connect_timeout' => 5.0,
                        'headers' => [
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                            'Accept-Language' => 'en-US,en;q=0.5',
                        ],
                        'verify' => false, // Disable SSL verification
                    ]);


                    
                    // fetching html doc using client
                    $response = $client->get('');
                    $body = $response->getBody()->getContents();

                 
                   
                    
                    // Initialize DomCrawler and passing current html doc into it so we can start searching for data
                    $crawler = new Crawler($body);

              

                    $traversableItems = [];
                    $imageArray = [];
        
                     // Return all anchor tags that contains the substring 'track' in the url link
                            // For Example https://www.walmart.com/track because all links that include the string 'track' is a commonality between purchaseable items found by query
                            // Also checking to see if any of the items have an empty name field if so dont return name with url (likely a url to another part of the website if this is true)
                    $crawler->filter('a')->each(function (Crawler $node, $index) use (&$traversableItems, &$imageArray, $crawler) {
                        
                        $itemName = $node->text();
                     
                        $href = $node->attr("href");
                        $substring = 'track';
                        $secondarySubString = "ip";

                        $imageUrl = $crawler->filter("#is-0-productImage-$index")->attr("src", "");
                        
                        if (
                            str_contains($href, $substring) && $itemName !== '' 
                            && 
                            str_contains($itemName, "Sponsored") == false 
                            && 
                            str_contains($itemName, "Shop now") == false 
                            && 
                            str_contains($itemName, "Options") !== true
                            ) {
                               
                            $traversableItems[] = [
                                "itemName" => $itemName,
                                "link" => $href,
                                "imageUrl" => "",
                            ];
                        } else {
                            $imageArray[] = $imageUrl;
                        }
        
                    });
                     // IMPORTANT: Splitting each item's name into an array of strings and getting the first word of each itemNameArray to target items with valid urls instead of returning urls that may not be associated to an item!
                    if (count($traversableItems) > 1 && count($imageArray) > 1) {
                        $index = 0;
                     
                        foreach ($traversableItems as $product){
                           
                            foreach ($imageArray as $imageUrl){

                                // removing undesired characters such as ": !" which is commonly found in product names because urls dont contain these characters and can lead to inconsistent results
                                $itemNameString = preg_replace("/[^a-zA-Z0-9\s]|[!:]/", "", $product['itemName']);
                                $itemNameArray = explode(" ", $itemNameString);
                            
                                // Currently only trying to match items to urls with a maximum precision of four words or less currently
                                switch (count($itemNameArray)){
                                    
                                    case 1:
                                        if(str_contains($imageUrl, $itemNameArray[0]) == true){
                                           
                                            $traversableItems[$index]["imageUrl"] = $imageUrl;
                                        }
                                    break;
                                    case 2:
                                        if(str_contains($imageUrl, $itemNameArray[0]) == true && str_contains($imageUrl, $itemNameArray[1]) == true ){
                                           
                                            $traversableItems[$index]["imageUrl"] = $imageUrl;
                                        }
                                        
                            
                                    break;
                                    case 3:
                                        if(str_contains($imageUrl, $itemNameArray[0]) == true && str_contains($imageUrl, $itemNameArray[1]) == true && str_contains($imageUrl, $itemNameArray[2]) == true ){
                                         
                                            $traversableItems[$index]["imageUrl"] = $imageUrl;
                                        }
                              
                                    break;
                                    default:
                                        if(str_contains($imageUrl, $itemNameArray[0]) == true && str_contains($imageUrl, $itemNameArray[1]) == true && str_contains($imageUrl, $itemNameArray[2]) == true && str_contains($imageUrl, $itemNameArray[3]) == true ){
                                            
                                            $traversableItems[$index]["imageUrl"] = $imageUrl;
                                        }
                                   
                                }
                                
          
                               
                            }
                            
                            $index++;
                        }
                        
                    }
                    // // Fetching all prices found on page and assigning them too $itemPriceArray to be iternated over later
                    // $itemPriceArray = [];
                    //     $pricesArray = new ArrayObject();
                       
                           
                    //      $crawler->filter('div > span')->each(function (Crawler $node, $i) use ( $pricesArray) {
                            
                    //         $itemPrice = $node->text();
                    //     // If price included current price string then remove everything but price 
                    //         if(str_contains($itemPrice, "current price") == true){
                    //             $itemPriceArray = explode(' ', $itemPrice);
                    //             $lastIndex = count($itemPriceArray) - 1;
                    //             $itemPrice = $itemPriceArray[$lastIndex];
                    //             $pricesArray->append($itemPrice);
                               
                    //         }
                    //         else {
                            
                    //             return;
                    //         }
                    //     });
                    //        // attach all prices
                    //        $numberOfProducts = count($traversableItems);
                    //         if($numberOfProducts !== 0){
                    //             foreach($pricesArray as $key => $itemValue) {
                                   
                    //                 $numberOfProducts = count($traversableItems) - 1;
                    //         }
                    //                 if($key <= $numberOfProducts){
                    //                     $traversableItems[$key]["price"] = $pricesArray[$key];
                    //         };
                    //        }
                           
                        
                             
                    
                    //    if(isset($giftSuggestion["imageUrl"]) == true) {
                            
                           
                    //         return response()->json([
                    //             "status" => "SUCCESS",
                    //             "data" => $giftSuggestion,
                    //         ]);

                    //    } else {
                    //     "WE STILL GOING";
                    //    }

                    if(count($traversableItems) >= 1){
                        foreach($traversableItems as $foundItem) {
                           
                            if($foundItem["imageUrl"] != "" && $foundItem["link"] != "" && $foundItem["itemName"] != ""){
                                return response()->json([
                                    "status" => "SUCCESS",
                                    "data" => $foundItem
                                ], 200);
                            } else {
                                return response()->json([
                                    "status"=> "No Item Could Be found",
                                    "data" => []
                                ]);
                            }
                        }
                    } else {
                        return response()->json([
                            "status"=> "No Item Could Be found",
                            "data" => []
                        ]);
                    }
                    
                    
                    
                
                } catch (RequestException $e) {
                    // Handle Guzzle exceptions
                    return response()->json([
                        'error' => 'Request failed',
                        'message' => $e->getMessage(),
                    ], 500);
                } catch (Exception $e) {
                    // Handle other exceptions
                    return response()->json([
                        'error' => 'An unexpected error occurred',
                        'message' => $e->getMessage(),
                    ], 500);
                }
        }
            

            
       
        
    

    public function generateGiftIdeas(Request $request){
            // $userHobbies = auth()->user()->hobbies();
            // echo "user_hobbies: ", var_dump($userHobbies), PHP_EOL;
        try {
            
            $allFoundGifts = new ArrayObject();

            $queryGeminiLLM = function () {

                for($x = 0; $x <= 5; $x++){
                  
                    $response = Prism::text()
                    ->using(Provider::Gemini, "gemini-1.5-flash")
                    ->withPrompt(
                       "I am looking to gift an age appropriate gift for a 20 year old with a budget less than $150, and the person has the following interests: hiking, running, sleeping, eating, videogames. Please provide only a valid JSON array without any additional text or wrapping elements like code block markers or comments. Must contain 4 unique gift ideas. The JSON should only include the data in array form with objects containing the keys `item` Do not add anything else.\n\nFor example:\n\n[\n    {\n        \"item\": \"Gift card for streaming service (Netflix etc.)\",\n        \"reason\": \"Appeals to their interest in TV shows and Netflix, and is easily adjustable to their budget.\"\n    },\n    {\n        \"item\": \"Sports-themed socks or small accessory\",\n        \"reason\": \"Relatively inexpensive and caters to their interest in sports.\"\n    }\n]"
                    )->generate();

                    $responseText = $response->text;
       
                    $parsedResponse = str_replace("`", "", $responseText );
                    $parsedResponse = preg_replace('/^json\s*/', '', $parsedResponse);
                
                    $decodedResponse = json_decode($parsedResponse, true);
                   
                    if (json_last_error() === JSON_ERROR_NONE) {
                       
                       return $decodedResponse;

                    } elseif(json_last_error() !== JSON_ERROR_NONE && $x <= 5) {
                       
                        continue;

                    } else {
                        return null;
                    };
            };
        };


        
        $geminiGiftSuggestions = $queryGeminiLLM();
        // echo "GEMINI GIFT SUGGESTIONS", var_dump($geminiGiftSuggestions);
        var_dump($geminiGiftSuggestions);
        if($geminiGiftSuggestions == null){
            $userHobbies = ["basketball", "pingpong, trivia"];
            foreach($userHobbies as $hobby){
                
                $result = $this->scrape($hobby);
                $content = $result->getContent();
                $data = json_decode($content, true);
                $arrayLength = count($data["data"]);
                if($arrayLength > 0){
                    $allFoundGifts->append($data["data"]);
                }
               
            }
        } else {
            if(is_array($geminiGiftSuggestions)){
                foreach($geminiGiftSuggestions as $giftSuggestion){
           
                    $result = $this->scrape($giftSuggestion['item']);
                    $content = $result->getContent();
                    $data = json_decode($content, true);
                    $arrayLength = count($data["data"]);
                    if($arrayLength > 0){
                        $allFoundGifts->append($data["data"]);
                    }
                
                }
            }
        }
        



        
       
        return response()->json([
            'message' => 'Scraping successful!',
            "data" => $allFoundGifts
        ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
            
    }

    public function generateGiftIdeasByImage(Request $request) {
    
        $imageUrl = $request -> imageUrl;
        $userHobbies = ["basketball"];
        // $userHobbies = auth()->user()->hobbies();
        $allFoundGifts = new ArrayObject();
  
        try {
            
            $message = new UserMessage(
                'I am looking to gift an age appropriate gift for a 20 year old with a budget less than $150, Please genearte 4 unique gift ideas based on the image that can be bought on walmart.com Please provide only a valid JSON array without any additional text or wrapping elements like code block markers or comments. Must contain 4 unique gift ideas with providing no more than 2 words. And ensure there are no strings within a string in your response. The JSON should only include the data in array form with objects containing the keys `item` Do not add anything else.\n\nFor example:\n\n[\n    {\n        \"item\": \"Gift card for streaming service (Netflix etc.)\",\n        \"reason\": \"Appeals to their interest in TV shows and Netflix, and is easily adjustable to their budget.\"\n    },\n    {\n        \"item\": \"Sports-themed socks or small accessory\",\n        \"reason\": \"Relatively inexpensive and caters to their interest in sports.\"\n    }\n]',
                [Image::fromUrl($request->imageUrl, mimeType: "image/jpeg")]
            );
            
       
                $response = Prism::text()
                ->using(Provider::Gemini, "gemini-1.5-flash")
                ->withMessages([$message])
                ->generate();
              
                $responseText = $response->text;
            
            
            // $decodedResponse = [
            //     [
            //         "item" => "Videogames"
            //     ],
            //     [
            //         "item" => "Gourmet bacon gift basket"
            //     ]
            // ];



            //EXAMPLE RESPONSE 
                // "```json
                // [
                //     {
                //         "item": "Beach Boys concert tickets"
                //     },
                //     {
                //         "item": "Gourmet bacon gift basket"
                //     }
                // ]
                // ```
               
                $cleanedResponse = str_replace("`", "", $responseText );
                $cleanedResponse = preg_replace('/^json\s*/', '', $cleanedResponse);
               
                $decodedResponse = json_decode($cleanedResponse, true);
                if (json_last_error() === JSON_ERROR_NONE) {
              
                    foreach($decodedResponse as $giftSuggestion){

                        echo $giftSuggestion['item'];
                     
                        $result = $this->scrape($giftSuggestion['item']);
                        $content = $result->getContent();
                        var_dump($content);
                        $data = json_decode($content, true);
                        var_dump($data);
                        $arrayLength = count($data["data"]);
                        if($arrayLength > 0){
                            $allFoundGifts->append($data["data"]);
                        }
                    
                    }
                    if(count($allFoundGifts) < 1){
                            echo "WE ARE IN HERE";
                        foreach($userHobbies as $hobby){
                
                            $result = $this->scrape($hobby);
                            $content = $result->getContent();
                            $data = json_decode($content, true);
                            $arrayLength = count($data["data"]);
                            if($arrayLength > 0){
                                $allFoundGifts->append($data["data"]);
                            }
                    };
                    return response()->json([
                        "status" => "Success",
                        "data" => $allFoundGifts,
                    ]);

                } elseif(json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        "status" => "An Error Occurred",
                        "data" =>json_last_error(),
                    ], 500);
                   

                } else {
                    return null;
                };
                
            };
        } catch (Exception $e) {
            return response()->json([
                "status" => "An Error Occurred",
                "data" =>$e->getMessage(),
            ], 500);
        } catch (PrismException $e) {
            response()->json(["message"=> $e->getMessage()]
               );
        }
    }
   
};
