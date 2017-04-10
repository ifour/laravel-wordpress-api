<?php

namespace ifour\LaravelWordpressApi\Services;

use Unirest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;

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
        return $this->_get('wp/v2/pages?_embed', [
            '_embed' => 1,
            'page' => $page
        ], $lifetime);
     }

     public function posts($page=1, $lifetime = null)
     {
        return $this->_get('wp/v2/posts', [
            '_embed' => 1,
            'page' => $page
        ], $lifetime);
     }

     public function page($slug, $lifetime = null)
     {
        return $this->_get('wp/v2/pages?slug='. $slug, [
            '_embed' => 1
        ], $lifetime);
     }

     public function post($slug, $lifetime = null)
     {
        return $this->_get('wp/v2/posts?slug='. $slug, [
            '_embed' => 1
        ], $lifetime);
     }

     public function get_category_by_id(int $id, $lifetime = null) {
         return $this->_get('wp/v2/categories/' . $id, [], $lifetime);
     }

     public function categories($lifetime = null)
     {
         return $this->_get('wp/v2/categories', [], $lifetime);
     }



     public function get_custom_post_by_name($post_type, $post_name, $lifetime = null)
     {
        return $this->_get('wp/v2/' . $post_type . '?_embed&slug=' . $post_name, [], $lifetime);
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
                        'totalResults' => isset($response->headers['X-WP-Total']) ? $response->headers['X-WP-Total']: false,
                        'totalPages' => isset($response->headers['X-WP-TotalPages']) ? $response->headers['X-WP-TotalPages']: false,
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
