<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class GelatoApiClientFactory
 */
class GelatoApiClientFactory
{
    const GELATO_HOST = 'https://shipping-rates.ecommerce.ie.live.gelato.tech';
    const GELATO_API_STATUS_URL = '/v1/woocommerce/connection-health';
    const GELATO_API_FLAT_RATE_SHIPPING_URL = '/v1/woocommerce/shipping/flat-rates';
    const GELATO_API_LIVE_RATE_SHIPPING_URL = '/v1/woocommerce/shipping/live-rates';

    public static function create(): GelatoShippingApiClient
    {
        $url = self::GELATO_HOST;

        return new GelatoShippingApiClient($url, get_option('home'));
    }
}
