<?php

namespace App\Http;

use Illuminate\Support\Facades\Http;

class GuzzleRequest {
    private $url;
    private $response;
    private $headers;

    public function __construct($url) {
        $this->url = $url;

        $this->headers = collect([
            'Content-Type' => 'application/json'
        ]);
    }

    public function get()
    {
        $this->response = Http::withHeaders([
            
        ])->get($this->url);

        return $this->response->body();
    }

    public function post($data)
    {
        $this->response = Http::withHeaders($this->headers->toArray())->acceptJson()->post($this->url, $data);

        return $this->response->body();
    }

    public function addHeader($key, $value)
    {
        $this->headers->put($key, $value);
    }

    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }
}
