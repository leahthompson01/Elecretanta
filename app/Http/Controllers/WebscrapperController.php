<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
class WebscrapperController extends Controller
{
   
    //
    public function scrape(Request $request){  
            try {
                // Initialize Guzzle client with proper configurations
                $client = new Client([
                    'base_uri' => 'https://www.walmart.com/',
                    'timeout' => 10.0, // Request timeout
                    'connect_timeout' => 5.0,
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                        'Accept-Language' => 'en-US,en;q=0.5',
                    ],
                    'verify' => false, // Disable SSL verification (use cautiously)
                ]);
    
                // Make GET request
                $response = $client->get('search?q=xbox+one');
                $body = $response->getBody()->getContents();
                // Get response body
                // $body = $response->getBody()->getContents();
    
                // Output the scraped content for debugging
                // echo $body;
    
                // Return a success response
                return response()->json([
                    'message' => 'Scraping successful!',
                    'data' => $body,
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