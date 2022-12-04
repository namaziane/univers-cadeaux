<?php
if (!defined('ABSPATH')) {
    exit;
}

class GelatoShipping extends WC_Shipping_Method
{
    /**
     * The ID of the shipping method.
     *
     * @var string
     */
    public $id = 'gelato_shipping';

    /**
     * The title of the method.
     *
     * @var string
     */
    public $method_title = 'Gelato Shipping';

    /**
     * The description of the method.
     *
     * @var string
     */
    public $method_description = 'Gelato Shipping';

    /**
     * The supported features.
     *
     * @var array
     */
    public $supports = [
        'settings',
    ];

    const WOO_TRUE = 'yes';
    const WOO_FALSE = 'no';
    const DEFAULT_ENABLED = self::WOO_TRUE;
    const DEFAULT_OVERRIDE = self::WOO_TRUE;
    const VENDOR_GELATO = 'gelato';
    const VENDOR_WOO = 'woo';
    const FULFILLED_BY_GELATO = 'fulfilled_by_gelato';
    const GELATO_SHIPPING_ID = 'gelato_shipping';
    const GELATO_SHIPPING_TTL = 3600;

    private $shipping_enabled;
    private $shipping_override;
    private $enable_live_rates;
    private $is_fulfilled_by_gelato;
    private $log;

    /** @var GelatoShippingApiClient */
    private $gelatoShippingClient;

    public static function init()
    {
        new self();

        if (!get_transient('clean_live_rates')) {
            self::cleanInvalidCache();
        }
    }

    protected static function cleanInvalidCache()
    {
        global $wpdb;

        $wpdb->get_results("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%live-rates-response-%'");
        $wpdb->get_results("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%flat-rates-response-%'");

        set_transient('clean_live_rates', 1);
    }

    public function __construct()
    {
        parent::__construct();
        $this->init_settings();

        $this->gelatoShippingClient = GelatoApiClientFactory::create();

        $this->title = $this->settings['title'] ?? $this->method_title;
        $this->method_description = __(
            'Calculate shipping rates based on Gelato shipping costs.',
            'gelato-integration-for-woocommerce'
        );
        $this->shipping_enabled = $this->settings['enabled'] ?? self::DEFAULT_ENABLED;
        $this->shipping_override = $this->settings['override_defaults'] ?? self::DEFAULT_OVERRIDE;
        $this->enable_live_rates = $this->settings['enable_live_rates'] ?? self::WOO_FALSE;
        $this->is_fulfilled_by_gelato = false;

        $this->init_form_fields();
        $this->register_hooks();
    }

    /**
     * Initialize the form fields.
     *
     * @return void
     */
    public function init_form_fields()
    {
        $this->form_fields = [
            'enabled' => [
                'title' => __('Gelato shipping method', 'gelato-integration-for-woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable', 'gelato-integration-for-woocommerce'),
                'default' => self::DEFAULT_ENABLED,
            ],
            'enable_live_rates' => array(
                'title' => __('Gelato live shipping rates', 'gelato-integration-for-woocommerce'),
                'type' => 'checkbox',
                'label' => __('Enable', 'gelato-integration-for-woocommerce'),
                'default' => self::WOO_FALSE,
            ),
            'override_defaults' => array(
                'title' => __('Woocommerce rates', 'gelato-integration-for-woocommerce'),
                'type' => 'checkbox',
                'label' => __('Disable standard Woocommerce rates for products fulfilled by Gelato', 'gelato-integration-for-woocommerce'),
                'default' => self::DEFAULT_OVERRIDE,
            ),
        ];
    }

    /**
     * Register the shipping method hooks.
     *
     * @return void
     */
    public function register_hooks()
    {
        add_action("woocommerce_update_options_shipping_{$this->id}", [$this, 'process_admin_options']);
        //Initialize shipping methods for specific package (or no package)
        add_filter('woocommerce_load_shipping_methods', [$this, 'gelato_load_shipping_methods']);
        add_filter('woocommerce_shipping_methods', [$this, 'gelato_shipping_methods']);
        add_filter('woocommerce_cart_shipping_packages', [$this, 'gelato_cart_shipping_packages']);
    }

    /**
     * Calculate the shipping fees.
     *
     * @param array $package
     * @return void
     */
    public function calculate_shipping($package = [])
    {
        if (isset($package[self::FULFILLED_BY_GELATO]) && $package[self::FULFILLED_BY_GELATO] === true) {
            foreach ($package['gelato_shipping_rate'] as $rate) {
                $this->id = self::GELATO_SHIPPING_ID . "_" . $rate['service_code'];
                $this->add_rate([
                    'id' => $this->id . "_" . $rate['service_code'],
                    'label' => $rate['service_name'],
                    'cost' => $rate['total_price'],
                    'calc_tax' => 'per_order',
                ]);
                $this->id = self::GELATO_SHIPPING_ID;
            }
        }
    }

    public function gelato_shipping_methods($methods)
    {
        return ($this->shipping_override === self::WOO_TRUE
            && $this->is_fulfilled_by_gelato === true
            && version_compare(WC()->version, '2.6', '<')
        )
            ? []
            : $methods;
    }

    public function gelato_cart_shipping_packages($packages = [])
    {
        if ($this->shipping_enabled !== self::WOO_TRUE) {
            return $packages;
        }

        $splitVendors = [
            self::VENDOR_GELATO => [],
            self::VENDOR_WOO => []
        ];

        $gelatoRatesRequest = [
            'products' => [],
            'destination' => [],
            'currency' => get_woocommerce_currency(),
            'lang' => get_locale()
        ];

        foreach ($packages as $package) {
            foreach ($package['contents'] as $cartItem) {
                if (isset($cartItem['data'])) {
                    /** @var WC_Product_Variation $productVariation */
                    $productVariation = $cartItem['data'];
                    if ($productVariation->needs_shipping()) {
                        $gelatoRatesRequest['products'][] = [
                            'id' => $productVariation->get_id(),
                            'quantity' => $cartItem['quantity']
                        ];
                    }
                }
            }
            $gelatoRatesRequest['destination'] = $package['destination'];
        }

        if (empty($gelatoRatesRequest['destination']['country'])) {
            return $packages;
        }

        if ($this->enable_live_rates === self::WOO_TRUE) {
            $key = 'live-rates-response-' . md5(json_encode($gelatoRatesRequest));
            $gelatoRatesResponse = get_transient($key);
            if (!$gelatoRatesResponse) {
                $this->log(json_encode($gelatoRatesRequest));
                $gelatoRatesResponse = $this->gelatoShippingClient->calculate_gelato_shipping_live_rates($gelatoRatesRequest);
                // Save to cache
                set_transient($key, $gelatoRatesResponse, static::GELATO_SHIPPING_TTL);
                $this->log(json_encode($gelatoRatesResponse));
            }
        } else {
            $key = 'flat-rates-response-' . md5(json_encode($gelatoRatesRequest));
            $gelatoRatesResponse = get_transient($key);
            if (!$gelatoRatesResponse) {
                $this->log(json_encode($gelatoRatesRequest));
                $gelatoRatesResponse = $this->gelatoShippingClient->calculate_gelato_shipping_flat_rates($gelatoRatesRequest);
                // Save to cache
                set_transient($key, $gelatoRatesResponse, static::GELATO_SHIPPING_TTL);
                $this->log(json_encode($gelatoRatesResponse));
            }
        }

        if (empty($gelatoRatesResponse['products'])) {
            return $packages;
        }


        $this->log("Handle response");
        foreach ($packages as $package) {
            foreach ($package['contents'] as $itemKey => $cartItem) {
                /** @var WC_Product_Variation $productVariation */
                $productVariation = $cartItem['data'];

                $this->log("Searching product " . $productVariation->get_id() . " in " . json_encode($gelatoRatesResponse['products']));
                if (in_array($productVariation->get_id(), $gelatoRatesResponse['products'])) {
                    $splitVendors[self::VENDOR_GELATO]['items'][$itemKey] = $cartItem;
                    $splitVendors[self::VENDOR_GELATO]['gelato_shipping_rate'] = $gelatoRatesResponse['rates'];
                } else {
                    $splitVendors[self::VENDOR_WOO]['items'][$itemKey] = $cartItem;
                }
            }
        }

        $splitPackages = [];
        foreach ($packages as $package) {
            foreach ($splitVendors as $vendor => $info) {
                if (!count($info)) {
                    continue;
                }

                $splitPackage = $package;
                $splitPackage['contents_cost'] = 0;
                $splitPackage['contents'] = [];
                if ($vendor == self::VENDOR_GELATO) {
                    $splitPackage[self::FULFILLED_BY_GELATO] = true;
                    $splitPackage['gelato_shipping_rate'] = $info['gelato_shipping_rate'];
                }

                foreach ($info['items'] as $key => $item) {
                    /** @var WC_Product_Variation $productVariation */
                    $productVariation = $item['data'];

                    $splitPackage['contents'][$key] = $item;

                    if ($productVariation->needs_shipping() && isset($product['line_total'])) {
                        $splitPackage['contents_cost'] += $product['line_total'];
                    }
                }

                $splitPackages[] = $splitPackage;
            }
        }

        return $splitPackages;
    }

    public function gelato_load_shipping_methods($package = [])
    {
        $this->is_fulfilled_by_gelato = false;

        if (!$package) {
            WC()->shipping()->register_shipping_method($this);

            return;
        }

        if ($this->enabled === self::WOO_FALSE) {
            return;
        }

        if (isset($package[self::FULFILLED_BY_GELATO]) && true === $package[self::FULFILLED_BY_GELATO]) {
            if ($this->shipping_override === self::WOO_TRUE) {
                WC()->shipping()->unregister_shipping_methods();
            }

            $this->is_fulfilled_by_gelato = true;

            WC()->shipping()->register_shipping_method($this);
        }
    }

    /**
     * Logging method.
     *
     * @param string $message Log message.
     * @param string $level Log level.
     *                        Available options: 'emergency', 'alert',
     *                        'critical', 'error', 'warning', 'notice',
     *                        'info' and 'debug'.
     *                        Defaults to 'info'.
     */
    private function log($message, $level = 'info')
    {
        if (is_null($this->log)) {
            $this->log = wc_get_logger();
        }

        $this->log->log($level, $message, array('source' => 'gelato'));
    }
}
