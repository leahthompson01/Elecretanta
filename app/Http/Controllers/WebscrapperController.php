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
    
                
                $response = $client->get('');
                $body = $response->getBody()->getContents();


                // to get url to page specific item, 
                //(div) mb0 ph0-xl pt0-xl bb b--near-white w-25 pb3-m ph1"

                $crawler = new Crawler($body);
                $traversableItems = new ArrayObject();
                $links = $crawler->filter('a')->each(function (Crawler $node) use ($traversableItems) {

                   $href = $node->attr("href");
                   $substring = 'track';
                   $itemName = $node->text();

                   if(str_contains($href, $substring) == true){
                    
                       
                    $newItem = [
                           'name' => $itemName,
                           'href' => $href,
                        ];
                    $traversableItems->append($newItem);

                    
                   }
                   else {
                    return null;
                   }


                    // return [
                    //     'text' => $node->text(),
                    //     'href' => $node->attr('href'),
                    // ];
                });
                


               
                // (div) > (div) > (a) -> w-100 h-100 z-1 hide-sibling-opacity  absolute, -> href

                // Container for specific data ->// w_KPWk w_GxNv flex-row undefined
                
                // w_aoqv w_wRee w_fdPt to get name
                // 
    
                // Return a success response
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