<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BinanceService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;
    private string $apiSecret;

    public function __construct(HttpClientInterface $httpClient, string $apiKey, string $apiSecret)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    // Adding the getAccountInfo method with debugging
    public function getAccountInfo()
    {
        $timestamp = round(microtime(true) * 1000); // Current timestamp in milliseconds
        $queryString = 'timestamp=' . $timestamp;

        // Generate the signature
        $signature = $this->generateSignature($queryString);

        // Construct the URL with the signature
        $url = 'https://api.binance.com/api/v3/account?' . $queryString . '&signature=' . $signature;
        //echo "zMif1R7js8PGYtp5yQ89jxmRmP4Mbnf16Jixrg0NF8PfXIdPOWSohJq8QTpDuj3i";
        //die;

        try {
            $response = $this->httpClient->request(
                'GET',
                $url,
                [
                    'headers' => [
                        'X-MBX-APIKEY' => "zMif1R7js8PGYtp5yQ89jxmRmP4Mbnf16Jixrg0NF8PfXIdPOWSohJq8QTpDuj3i",
                    ],
                ]
            );

            // Check the response status
            if ($response->getStatusCode() !== Response::HTTP_OK) {
                throw new HttpException($response->getStatusCode(), 'Binance API request failed');
            }

            // Return the response as an array
            return $response->toArray();
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            throw new HttpException(500, 'Error with Binance API request: ' . $e->getMessage());
        }
    }

    // Generate the signature using your API secret
    private function generateSignature(string $queryString): string
    {
        return hash_hmac('sha256', $queryString, $this->apiSecret);
    }
}
