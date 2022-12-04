<?php
if (!defined('ABSPATH')) {
    exit;
}

class GelatoShippingApiClient
{
    private $url;
    private $storeDomain;

    public function __construct($url, $storeDomain)
    {
        $this->url = $url;
        $this->storeDomain = $storeDomain;
    }

    public function calculate_gelato_shipping_flat_rates(array $rateRequestDto): array
    {
        $response = wp_remote_post(
            $this->url . GelatoApiClientFactory::GELATO_API_FLAT_RATE_SHIPPING_URL,
            [
                'timeout' => 60,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-wc-webhook-source' => $this->storeDomain
                ],
                'body' => isset($rateRequestDto) ? json_encode($rateRequestDto) : "",
            ]
        );

        if (wp_remote_retrieve_response_code($response) == 200) {
            return json_decode(wp_remote_retrieve_body($response), true);
        }

        return [];
    }

    public function calculate_gelato_shipping_live_rates(array $rateRequestDto): array
    {
        $response = wp_remote_post(
            $this->url . GelatoApiClientFactory::GELATO_API_LIVE_RATE_SHIPPING_URL,
            [
                'timeout' => 60,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-wc-webhook-source' => $this->storeDomain
                ],
                'body' => isset($rateRequestDto) ? json_encode($rateRequestDto) : "",
            ]
        );

        if (wp_remote_retrieve_response_code($response) == 200) {
            return json_decode(wp_remote_retrieve_body($response), true);
        }

        return [];
    }
}
