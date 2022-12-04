<?php
if (!defined('ABSPATH')) {
    exit;
}

class GelatoMainPage extends GelatoPage
{
    protected $page = GelatoPage::PAGE_ID_MAIN;

    public function view()
    {
        $connector = new GelatoConnector();

        $this->variables = [
            'asset_folder' => GelatoPlugin::get_asset_path(),
            'url_connect' => $connector->getConnectUrl(),
            'url_dashboard' => $connector->getDashboardUrl(),
            'is_connected' => $connector->isConnected()
        ];
        parent::view();
    }
}
