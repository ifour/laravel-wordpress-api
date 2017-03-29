<?php

namespace ifour\LaravelWordpressApi\Services;

use Unirest;
use Illuminate\Support\Facades\Cache;

class WordpressApi
{
    protected $client;

    /**
     * config the APi class
     * @param array $config configuration variables
     */
     public function __construct($config)
     {
         $this->client = new Unirest\Request;
         $this->endpoint = $config['endpoint'];
         $this->timeout = $config['timeout'];
     }

     public function pages()
     {
         return $this->_get('wp/v2/pages');
     }

    /**
     * Process the required request and return a suitable json response
     * @param  string - $method - the api method to call
     * @return json - JSON response from the request
     */
     public function _get($method)
     {
         try {
             //Check if there's a valid cache entry
             return Cache::remember($method, $this->timeout, function() use ($method) {
                 //If not send the request
                 $response = $this->client->get($this->endpoint . $method);

                 //check the response code
                 if ($response->code = 200) {
                     /*
                      * Return the response as a collection to make manipulation easier
                      * later on in the application
                      */
                     return collect($response->body);
                 } else {
                     throw new Exception($response->body);
                 }

             });

         } catch (Exception $e) {
             return 'API request failed: ' . $e->getMessage();
         }
     }
}
