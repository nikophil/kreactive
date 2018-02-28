<?php

namespace App\Guzzle;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    /** @var GuzzleClient */
    private $client;

    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $query
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function request(array $query = [])
    {
        $query = array_merge($query, [
            'apikey' => getenv('OMDB_API_KEY'),
        ]);

        return $this->client->get('/', [
            'query' => $query
        ]);
    }
}
