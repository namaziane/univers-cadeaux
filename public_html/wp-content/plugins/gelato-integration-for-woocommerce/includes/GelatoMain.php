<?php
if (!defined('ABSPATH')) {
    exit;
}

class GelatoMain
{
    public static function init()
    {
        $gelatoMain = new GelatoMain();
        $gelatoMain->create();
    }

    public function create()
    {
        add_action('admin_menu', [$this, 'add_gelato_main_menu']);
        add_action('admin_enqueue_scripts', [$this, 'add_styles']);
        add_action('gelato_reset_connection', [$this, 'reset_connection']);
	    register_uninstall_hook('gelato-integration-for-woocommerce/gelato-integration-for-woocommerce.php', [__CLASS__, 'reset_connection']);
	    register_deactivation_hook('gelato-integration-for-woocommerce/gelato-integration-for-woocommerce.php', [__CLASS__, 'reset_connection']);
	    add_action('rest_api_init', function () {
		    register_rest_route('wc/v3', 'gelato_reset', [
			    'methods' => 'GET',
			    'callback' => [$this, 'reset_connection_endpoint'],
			    'permission_callback' => [$this, 'check_permission'],
		    ]);
	    });
    }

    public function add_gelato_main_menu()
    {
        add_menu_page(
            __('Dashboard', 'gelato-integration-for-woocommerce'),
            'Gelato',
            'manage_options',
            'gelato-main-menu',
            ['GelatoMain', 'tab_routing'],
            GelatoPlugin::get_asset_path() . 'images/gelato-menu-icon.svg',
            56
        );
    }

    public function add_styles()
    {
        wp_enqueue_style('gelato-status', plugins_url('../assets/css/status.css', __FILE__));
    }

	public static function reset_connection()
	{
		$connector = new GelatoConnector();
		$connector->resetConnection();
	}

	public static function reset_connection_endpoint()
	{
		self::reset_connection();

		// return any valid json
		return json_encode(['status' => 'ok']);
	}

	public function check_permission()
	{
		if ( ! wc_rest_check_manager_permissions( 'webhooks', 'delete' ) ) {
			return new WP_Error( 'gelato_cannot_reset', __( 'Sorry, you cannot reset this plugin.', 'gelato' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

    public static function tab_routing()
    {
	    $tab = ( ! empty( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : GelatoPage::PAGE_ID_MAIN );
	    if ( isset( $_GET['reset_plugin'] ) ) {
		    do_action( 'gelato_reset_connection' );
		    $tab = GelatoPage::PAGE_ID_MAIN;
	    }
	    if ( $tab == GelatoPage::PAGE_ID_MAIN && ! self::check_main_tests() ) {
		    $tab = GelatoPage::PAGE_ID_STATUS;
	    }
        $gelatoPage = GelatoPageFactory::create($tab);
        $gelatoPage->view();
    }

	private static function check_main_tests() {
		$connector = new GelatoConnector();
		if ( $connector->isConnected() ) {
			return true;
		}

		$statusChecker = new GelatoStatusChecker();

		return $statusChecker->get_connection_allowance_test_result();
	}
}
