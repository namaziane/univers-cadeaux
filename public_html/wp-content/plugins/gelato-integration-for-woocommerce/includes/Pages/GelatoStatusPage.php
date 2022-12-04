<?php
if (!defined('ABSPATH')) {
    exit;
}

class GelatoStatusPage extends GelatoPage
{
    protected $page = GelatoPage::PAGE_ID_STATUS;

    /**
     * @var GelatoStatusChecker
     */
    private $gelatoStatusChecker;

    public function __construct($gelatoStatusChecker)
    {
        $this->gelatoStatusChecker = $gelatoStatusChecker;
    }

    public function view()
    {
		$tab = ( ! empty( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : GelatoPage::PAGE_ID_MAIN );
		$scope = $tab == GelatoPage::PAGE_ID_MAIN ? GelatoStatusChecker::TEST_SCOPE_CONNECTION : GelatoStatusChecker::TEST_SCOPE_FULL;
        $statusResults = $this->gelatoStatusChecker->getTestResults($scope);
        $this->variables = [
            'status_results' => $statusResults,
            'status_log' => $this->get_status_log($statusResults)
        ];

        parent::view();
    }

    private function get_status_log(array $statusResults)
    {
        ob_start();

        echo "##### General settings #####\n";
        foreach ($this->get_general_settings() as $name => $value) {
            echo "* ";
            echo str_pad(esc_html($name), 30) . '=> ' . esc_html($value) . "\n";
        }

        echo "\n\n##### Gelato Status Checklist #####\n";
        foreach ($statusResults as $result) {
            echo "* ";
            echo str_pad(esc_html($result['name']), 30) . '=> ' . esc_html($result['status']) . "\n";
        }

        if ((defined('WP_DEBUG') && WP_DEBUG == true)
            && (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG == true)
        ) {
            echo "\n\n##### Wordpress Error log (last 60 entries) #####\n";
            $contents = $this->get_error_log_contents();
            if ($contents) {
                print_r($contents);
            }
        }

        $report = ob_get_contents();
        ob_end_clean();

        return $report;
    }

    public function get_general_settings()
    {
        $curl_version = 'Not found.';
        if ( function_exists( 'curl_version' ) ) {
            $curl_version = curl_version();
            $curl_version = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
        }

        return array(
            'Home Url'                  => get_option( 'home' ),
            'Site Url'                  => get_option( 'siteurl' ),
            'WP Version'                => get_bloginfo( 'version' ),
            'WP multisite'              => is_multisite(),
            'WC Version'                => WC()->version,
            'Language'                  => get_locale(),
            'wp_memory_limit'           => wc_let_to_num( WP_MEMORY_LIMIT ),
            'external_object_cache'     => wp_using_ext_object_cache(),
            'wp_debug_mode'             => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
            'wp_cron'                   => ! ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ),
            'server_info'               => isset( $_SERVER['SERVER_SOFTWARE'] ) ? wc_clean( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '',
            'php_version'               => phpversion(),
            'php_post_max_size'         => wc_let_to_num( ini_get( 'post_max_size' ) ),
            'php_max_execution_time'    => (int) ini_get( 'max_execution_time' ),
            'php_max_input_vars'        => (int) ini_get( 'max_input_vars' ),
            'max_upload_size'           => wp_max_upload_size(),
            'upload_enabled'            => ini_get('file_uploads'),
            'curl_version'              => $curl_version,
            'default_timezone'          => date_default_timezone_get(),
            'fsockopen_or_curl_enabled' => ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ),
            'gzip_enabled'              => is_callable( 'gzopen' ),
            'WC Log folder'             => WC_LOG_DIR,
            'WC is log folder writable' => (bool) @fopen( WC_LOG_DIR . 'test-log.log', 'a' ), // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_fopen
        );
    }

    /**
     * Get last 60 lines of error log
     * @return bool|string
     */
    public function get_error_log_contents() {

        if ( ! function_exists( 'fopen' ) ) {
            return false;
        }

        return $this->file_tail( WP_CONTENT_DIR . '/debug.log', 60 );
    }

    /**
     * source: https://gist.github.com/lorenzos/1711e81a9162320fde20
     * @param $filepath
     * @param int $lines
     * @param bool $adaptive
     *
     * @return bool|string
     */
    function file_tail( $filepath, $lines = 1, $adaptive = true ) {

        $f = @fopen( $filepath, "rb" );
        if ( $f === false ) {
            return false;
        }

        // Sets buffer size, according to the number of lines to retrieve.
        if ( ! $adaptive ) {
            $buffer = 4096;
        } else {
            $buffer = ( $lines < 2 ? 64 : ( $lines < 10 ? 512 : 4096 ) );
        }

        // Jump to last character
        fseek( $f, - 1, SEEK_END );
        if ( fread( $f, 1 ) != "\n" ) {
            $lines -= 1;
        }

        $output = '';
        $chunk  = '';
        while ( ftell( $f ) > 0 && $lines >= 0 ) {
            // Figure out how far back we should jump
            $seek = min( ftell( $f ), $buffer );
            // Do the jump (backwards, relative to where we are)
            fseek( $f, - $seek, SEEK_CUR );
            $output = ( $chunk = fread( $f, $seek ) ) . $output;
            fseek( $f, - mb_strlen( $chunk, '8bit' ), SEEK_CUR );
            $lines -= substr_count( $chunk, "\n" );
        }
        while ( $lines ++ < 0 ) {
            $output = substr( $output, strpos( $output, "\n" ) + 1 );
        }
        fclose( $f );

        return trim( $output );
    }
}
