<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class GelatoStatusChecker
 */
class GelatoStatusChecker
{
    public const STATUS_OK = 'OK';
    public const STATUS_FAIL = 'FAIL';
    public const STATUS_WARNING = 'WARNING';
    public const STATUS_SKIPPED = 'SKIPPED';

	public const TEST_SCOPE_FULL = 'FULL';
	public const TEST_SCOPE_CONNECTION = 'CONNECTION';

    public function getTestList() {
        return [
	        'test_permalinks' => [
		        'name'        => __('WordPress Permalinks', 'gelato-integration-for-woocommerce'),
		        'description' => __('Make sure that permalinks are NOT set to “plain” under Settings > Permalinks. Select any setting other than “Plain”.', 'gelato-integration-for-woocommerce'),
		        'method'      => 'test_permalinks',
		        'help'        => [
			        'link' => 'https://apisupport.gelato.com/hc/en-us/articles/360020186039-Getting-Started-with-WooCommerce',
			        'text' => 'Help Article, Step 3'
		        ]
	        ],
	        'test_gelato_webhooks' => [
		        'name'        => __('WooCommerce Webhooks', 'gelato-integration-for-woocommerce'),
		        'description' => __('Gelato requires webhooks to be setup in WooCommerce to update store, products and orders.', 'gelato-integration-for-woocommerce'),
		        'method'      => 'test_gelato_webhooks',
		        'help'        => [
			        'link' => 'https://apisupport.gelato.com/hc/en-us/articles/4408190821138-Gelato-Woocommerce-app-health-status-check-and-webhooks',
			        'text' => 'Help Article, Webhooks section'
		        ]
	        ],
	        'test_wc_api_access_by_gelato' => [
		        'name'        => __('WooCommerce Gelato API keys are set and valid.', 'gelato-integration-for-woocommerce'),
		        'description' => __('Gelato needs access to WooCommerce API. Please try reconnecting if the status is failed.', 'gelato-integration-for-woocommerce'),
		        'method'      => 'test_wc_api_access_by_gelato',
		        'help'        => false
	        ],
	        'test_connection_from_gelato_to_wc' => [
		        'name'        => __('Check connection with Gelato.', 'gelato-integration-for-woocommerce'),
		        'description' => __('Verify that API connection between Gelato and WooCommerce site is accessible.', 'gelato-integration-for-woocommerce'),
		        'method'      => 'test_connection_from_gelato_to_wc',
		        'help'        => false,
	        ],
	        'test_uploads_write' => [
		        'name'        => __('Write permissions', 'gelato-integration-for-woocommerce'),
		        'description' => __('Verify that you have sufficient access for Gelato to upload mockups. Please contact your website hosting provider if the status is failed.', 'gelato-integration-for-woocommerce'),
		        'method'      => 'test_uploads_write',
		        'help'        => false,
	        ],
	        'test_php_memory_limit' => [
		        'name'        => __('PHP memory limit', 'gelato-integration-for-woocommerce'),
		        'description' => __('Verify that allocated memory for PHP is at least 128MB. Please contact your website hosting provider if the status is failed.', 'gelato-integration-for-woocommerce'),
		        'method'      => 'test_php_memory_limit',
		        'help'        => false,
	        ],
	        'test_php_max_execution_time' => [
		        'name'        => __('PHP script time limit', 'gelato-integration-for-woocommerce'),
		        'description' => __('Verify that PHP script execution time limit is set to at least 30 seconds. Please contact your website hosting provider if the status is failed.', 'gelato-integration-for-woocommerce'),
		        'method'      => 'test_php_max_execution_time',
		        'help'        => false,
	        ],
	        'test_ssl' => [
		        'name'        => __('Check SSL', 'gelato-integration-for-woocommerce'),
		        'description' => __('Verify that SSL is setup and working correctly.', 'gelato-integration-for-woocommerce'),
		        'method'      => 'test_ssl',
		        'help'        => [
			        'link' => 'https://woocommerce.com/document/ssl-and-https/',
			        'text' => 'Woocommerce docs'
		        ]
	        ],
	        'test_redirection' => [
		        'name'        => __('Check site redirection.', 'gelato-integration-for-woocommerce'),
		        'description' => __('If your site is configured with redirection to another URL, there might be some issues. Usually, this happens with incorrect HTTP to HTTPS redirects or yourdomain.com to www.yourdomain.com. Please check your settings.', 'gelato-integration-for-woocommerce'),
		        'method'      => 'test_redirection',
		        'help'        => [
			        'link' => 'https://apisupport.gelato.com/hc/en-us/articles/360020186039-Getting-Started-with-WooCommerce',
			        'text' => 'Help Article, Step 1'
		        ]
	        ],
        ];
    }

    public function getTestResults($scope = self::TEST_SCOPE_FULL): array
    {
        $results = [];
	    switch ( $scope ) {
		    case self::TEST_SCOPE_FULL:
			    $tests = $this->getTestList();
			    break;
		    case self::TEST_SCOPE_CONNECTION:
			    $tests = $this->get_connection_allowance_tests();
			    break;
		    default:
			    $tests = $this->getTestList();
	    }

        foreach ($tests as $item) {
            $result = $item;
            $result['status'] = self::STATUS_SKIPPED;

	        if ( method_exists( $this, $item['method'] ) ) {
		        $test_result = $this->{$item['method']}();
		        if ( is_array( $test_result ) ) {
			        $result['status'] = $test_result[0];
			        $result['help']   = [ 'text' => $test_result[1] ];
		        } else {
			        $result['status'] = $test_result;
		        }
	        }
            $results[$result['method']] = $result;
        }

        return $results;
    }

	public function get_connection_allowance_tests() {
		return array_intersect_key( $this->getTestList(), array_flip( [
			'test_redirection',
			'test_ssl',
			'test_php_max_execution_time',
			'test_php_memory_limit',
			'test_permalinks',
		] ) );
	}

	public function get_connection_allowance_test_result() {
		$tests = $this->get_connection_allowance_tests();

		foreach ( $tests as $test ) {
			$test_result = $this->{$test['method']}();
			if ( $test_result != self::STATUS_OK ) {
				return false;
			}
		}

		return true;
	}

    private function test_permalinks()
    {
        $permalinks = get_option('permalink_structure', false);

        if ($permalinks && strlen($permalinks) > 0) {
            return self::STATUS_OK;
        }

        return self::STATUS_FAIL;
    }

    private function test_gelato_webhooks()
    {
        global $wpdb;

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) as webhook_count FROM {$wpdb->prefix}wc_webhooks WHERE name LIKE '%%%s%'",
            "Gelato |"
        ));

        if ($count == 6) {
            return self::STATUS_OK;
        }

        if ($count > 0) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_FAIL;
    }

    private function test_wc_api_access_by_gelato()
    {
        global $wpdb;

        $key = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}woocommerce_api_keys WHERE description LIKE '%%%s%' ORDER BY last_access LIMIT 1",
            $wpdb->esc_like("Gelato")
        ));

        if (!empty($key) && $key->permissions == 'read_write') {
            return self::STATUS_OK;
        }

        return self::STATUS_FAIL;
    }

    private function test_connection_from_gelato_to_wc()
    {
        $url = GelatoApiClientFactory::GELATO_HOST . GelatoApiClientFactory::GELATO_API_STATUS_URL;
        $response = wp_remote_get(
            $url . "?host=" . get_option('home'),
            [
                'host' => get_option('home')
            ]
        );
        $bodyResponse = wp_remote_retrieve_body($response);
        $responseCode = wp_remote_retrieve_response_code($response);

        if ($responseCode !== 200) {
            return self::STATUS_FAIL;
        }

        $response = json_decode($bodyResponse, true);

        if ($response['status'] == 'OK') {
            return self::STATUS_OK;
        }

	    return [ self::STATUS_FAIL, $response['message'] ];
    }

    public function test_php_max_execution_time()
    {
        $maxExecTime = ini_get('max_execution_time');

        if (!$maxExecTime || $maxExecTime >= 30) {
            return self::STATUS_OK;
        }

        return self::STATUS_FAIL;
    }

    private function test_php_memory_limit()
    {
        $memoryLimit = ini_get('memory_limit');

        if (preg_match('/^(\d+)(.)$/', $memoryLimit, $matches)) {
            if ($matches[2] == 'M') {
                $memoryLimit = $matches[1] * 1024 * 1024;
            } else {
                if ($matches[2] == 'K') {
                    $memoryLimit = $matches[1] * 1024;
                }
            }
        }

        $ok = ($memoryLimit >= 128 * 1024 * 1024);

        if ($ok) {
            return self::STATUS_OK;
        }

        return self::STATUS_FAIL;
    }

    private function test_ssl()
    {
        if (is_ssl()) {
            return self::STATUS_OK;
        }

        return self::STATUS_FAIL;
    }

    private function test_uploads_write()
    {
        $uploadDir = wp_upload_dir();
        if (is_writable($uploadDir['basedir'])) {
            return self::STATUS_OK;
        }

        return self::STATUS_OK;
    }

    private function test_redirection()
    {
        $siteUrl = get_option('home');
        $requestBasic = wp_remote_head( $siteUrl . '/wp-json/', ['redirection' => 0]);
        $requestSlashed = wp_remote_head( trailingslashit($siteUrl), ['redirection' => 0]);

        if (is_wp_error($requestBasic) || is_wp_error($requestSlashed)) {
            return self::STATUS_FAIL;
        }

        /** @var WP_HTTP_Requests_Response $response */
        $responseBasic = $requestBasic['http_response'];
        /** @var WP_HTTP_Requests_Response $responseSlashed */
        $responseSlashed = $requestSlashed['http_response'];

        if ($responseBasic->get_status() == 200 || $responseSlashed->get_status() == 200) {
            return self::STATUS_OK;
        }

        if (in_array($responseBasic->get_status(), array(301, 302, 303, 307))) {
            return self::STATUS_FAIL;
        }

        return self::STATUS_WARNING;
    }
}
