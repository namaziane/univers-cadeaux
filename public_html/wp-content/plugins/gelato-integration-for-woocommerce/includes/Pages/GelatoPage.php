<?php

class GelatoPage
{
    const PAGE_ID_MAIN = 'main';
    const PAGE_ID_STATUS = 'status';

    protected $page;
    protected $variables = [];

    public static function get_tabs()
    {
        return [
            'main' => [
                'id' => '',
                'handler' => 'GelatoMain',
                'name' => __('Home', 'gelato-integration-for-woocommerce')
            ],
            'status' => [
                'id' => 'status',
                'handler' => 'GelatoStatus',
                'name' => __('Status', 'gelato-integration-for-woocommerce')
            ]
        ];
    }

    public function view()
    {
        $this->render_page($this->page, $this->variables);
    }

    private function render_page($page, $variables = [])
    {
        $this->load_template('header', ['tabs' => self::get_tabs()]);
        $this->load_template($page, $variables);
        $this->load_template('footer');
    }

    private function load_template($name, $variables = array())
    {
        if (!empty($variables)) {
            extract($variables);
        }

        $filename = plugin_dir_path(__FILE__) . '../../templates/' . $name . '.php';
        if (file_exists($filename)) {
            include($filename);
        }
    }
}
