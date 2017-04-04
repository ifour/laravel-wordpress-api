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
         $this->lifetime = $config['lifetime'];
     }

     public function pages($page=1, $lifetime = null)
     {
         return $this->_get('wp/v2/pages', ['page' => $page], $lifetime);
     }

     public function posts($page=1, $lifetime = null)
     {
        return $this->_get('wp/v2/posts', ['page' => $page], $lifetime);
     }

     public function page($slug)
     {
         return $this->_get('wp/v2/posts?slug='. $slug, [], $lifetime);
     }

     /**
      * Process the required request and return a suitable json response
      * @param  string - $method - the api method to call
      * @return json - JSON response from the request
      */
      public function _get($method, $params = [], $lifetime)
      {
         //Work out the cache lifetime is
         $lifetime = is_null($lifetime) ? $this->lifetime : $lifetime;

         //Build a name for this request to store in cache
         $cacheKey = $method . '-' . implode('-', array_flatten($params));

         try {
             //Check if there's a valid cache entry
             return Cache::remember($cacheKey, $lifetime, function() use ($method, $params) {
                 //If not send the request
                 $response = $this->client->get($this->endpoint . $method, [], $params);

                 //check the response code
                 if ($response->code === 200) {
                     /**
                       * Include the total results and the number of pages
                       * in the returned dataset
                       */
                     return collect([
                         'totalResults' => $response->headers['X-WP-Total'],
                         'totalPages' => $response->headers['X-WP-TotalPages'],
                         'results' => $response->body
                     ]);
                  } else {
                     throw new Exception($response->body);
                  }

              });

         } catch (Exception $e) {
             return 'API request failed: ' . $e->getMessage();
         }
      }
}
