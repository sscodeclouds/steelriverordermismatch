<?php
namespace App\Lib;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class StickyOrder
{
    private string $username;
    private string $password;
    private string $domain;

    private string $v1BaseURL = '';
    private static array $v1PATH = [
        'ORDER_FIND' => '/v1/order_find',
        'ORDER_VIEW' => '/v1/order_view'
    ];

    public function __construct($domain, $username, $password){
        $this->username = $username;
        $this->password = $password;
        $this->domain = $domain;
        $this->v1BaseURL = "https://$domain.sticky.io/api";
    }
    //date format : mm/dd/yyyy
    public function v1GetOrders(
        $startDate = '01/01/2022', $endDate = '01/02/2022',
        $isTest = false, $isApproved = false
    ): array{
        $response = [
            'success' => false,
            'data' => null,
            'message' => 'Invalid Response!'
        ];
        $data = [
            "search_type" => "all",
            "campaign_id" => "all",
            "start_date" => $startDate,
            "start_time" => "",
            "end_date" => $endDate,
            "end_time" => "",
            "date_type" => "create",
            "product_id" => "all",
            "criteria" => [
                "approved" => $isApproved?"1":"0",
                "is_test" => $isTest?"1":"0"
            ],
            "member_token" => "",
            // "return_type" => "order_view"
        ];

        $credentials = base64_encode($this->username.':'.$this->password);

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'     => 'application/json',
                'Authorization' => ['Basic '.$credentials]
            ],
            'timeout'  => 30,
            'debug' => false
        ]);
        $targetURL = $this->v1BaseURL.self::$v1PATH['ORDER_FIND'];
        try {
            $apiResponse = $client->post(
                $targetURL,
                [
                    'json' => $data
                ]
            );
            $response['success'] = true;
            $apiResponseJsonString = $apiResponse->getBody()->getContents();
            $apiResponseArray = json_decode($apiResponseJsonString, true);
            $response['data'] = $apiResponseArray;
            $response['message'] = 'Successfully Fetched!';
        } catch (GuzzleException $e) {
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    public function v1GetOrderDetails(
        $orderId
    ): array{
        $response = [
            'success' => false,
            'data' => null,
            'message' => 'Invalid Response!'
        ];
        $data = [
            'order_id' => $orderId
        ];

        $credentials = base64_encode($this->username.':'.$this->password);

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'     => 'application/json',
                'Authorization' => ['Basic '.$credentials]
            ],
            'timeout'  => 30,
            'debug' => false
        ]);
        $targetURL = $this->v1BaseURL.self::$v1PATH['ORDER_VIEW'];
        try {
            $apiResponse = $client->post(
                $targetURL,
                [
                    'json' => $data
                ]
            );
            $response['success'] = true;
            $apiResponseJsonString = $apiResponse->getBody()->getContents();
            $apiResponseArray = json_decode($apiResponseJsonString, true);
            $response['data'] = $apiResponseArray;
            $response['message'] = 'Successfully Fetched!';
        } catch (GuzzleException $e) {
            $response['message'] = $e->getMessage();
        }

        return $response;
    }
}
