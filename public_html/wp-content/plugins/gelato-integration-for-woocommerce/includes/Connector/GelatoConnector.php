<?php

if (!defined('ABSPATH')) {
	exit;
}

class GelatoConnector
{
	/** @var string */
	const GELATO_DASHBOARD_URL = 'https://dashboard.gelato.com';

	private const PREFIX = "Gelato";

	/**
	 * @return string
	 */
	public function getConnectUrl(): string
	{
		return self::GELATO_DASHBOARD_URL . '/stores/woocommerce/connect?domain=' . urlencode(trailingslashit(get_home_url()));
	}

	/**
	 * @return string
	 */
	public function getDashboardUrl(): string
	{
		return self::GELATO_DASHBOARD_URL;
	}

	public function isConnected(): bool
	{
		global $wpdb;

		$key = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}woocommerce_api_keys WHERE description LIKE '%%%s%' ORDER BY last_access LIMIT 1",
			$wpdb->esc_like( self::PREFIX )
		));

		if (!empty($key) && $key->permissions == 'read_write') {
			return true;
		}

		return false;
	}

	public function resetConnection(): void
	{
		global $wpdb;

		$wpdb->query($wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}woocommerce_api_keys WHERE description LIKE '%%%s%'",
			$wpdb->esc_like( self::PREFIX )
		));

		$webhooks = $wpdb->get_results($wpdb->prepare(
			"SELECT webhook_id FROM {$wpdb->prefix}wc_webhooks WHERE name LIKE '%%%s%'",
			$wpdb->esc_like( self::PREFIX )
		));

		foreach ($webhooks as $webhookResult) {
			$webhook = wc_get_webhook( $webhookResult->webhook_id );
			$webhook->delete(true);
		}

		WC_Cache_Helper::invalidate_cache_group( 'webhooks' );
	}
}
