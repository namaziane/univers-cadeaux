<div class="gelato-status-log-container">
  <p>
      <?php esc_html_e('Please copy the information below and send it to support. Thank you!', 'gelato-integration-for-woocommerce'); ?>
  </p>
  <textarea id="gelato_status_log" class="gelato-status-log"><?php echo esc_html($status_log); ?></textarea>
  <button onclick="copyToClipboard()" class="button button-primary button-large"><?php esc_html_e('Copy', 'gelato-integration-for-woocommerce'); ?></button>
  <script type="text/javascript">
    function copyToClipboard() {
      var copyText = document.getElementById('gelato_status_log');
      copyText.select();
      copyText.setSelectionRange(0, 99999); /* For mobile devices */
      try{
        document.execCommand("copy");
      } catch (error) {
        // tbd
      }
      alert("Log is copied.");
    }
  </script>
</div>

<table class="wp-list-table widefat fixed striped">
  <thead>
  <tr>
    <td><?php esc_html_e('Name', 'gelato-integration-for-woocommerce'); ?></td>
    <td><?php esc_html_e('Description', 'gelato-integration-for-woocommerce'); ?></td>
    <td><?php esc_html_e('Status', 'gelato-integration-for-woocommerce'); ?></td>
  </tr>
  </thead>
  <tbody>
  <?php
  foreach ($status_results as $result) : ?>
    <tr>
      <td><?php echo esc_html($result['name']); ?></td>
      <td><?php echo esc_html($result['description']); ?>
	      <?php
	      if ($result['status'] != GelatoStatusChecker::STATUS_OK && $result['help']) {
		      if (isset($result['help']['link'])) {

			      echo '<a target="_blank" href="'.esc_html__($result['help']['link'], 'gelato-integration-for-woocommerce') . '">'. esc_html__('How to fix', 'gelato-integration-for-woocommerce') . '</a>';
		      } else {
			      echo esc_html__($result['help']['text'], 'gelato-integration-for-woocommerce');
		      }
	      }
	      ?>
      </td>
      <td>
          <?php
          if ($result['status'] == GelatoStatusChecker::STATUS_OK) {
              echo '<span class="gelato-status-check gelato-status-check-ok"></span>' . esc_html__('OK', 'gelato-integration-for-woocommerce');
          } else if ($result['status'] == GelatoStatusChecker::STATUS_WARNING) {
              echo '<span class="gelato-status-check gelato-status-check-warning"></span>' . esc_html__('WARNING', 'gelato-integration-for-woocommerce') . '&#42;';
          } elseif ($result['status'] == GelatoStatusChecker::STATUS_FAIL) {
              echo '<span class="gelato-status-check gelato-status-check-fail"></span>' . esc_html__('FAIL', 'gelato-integration-for-woocommerce');
          } else {
              echo '<span class="gelato-status-check"></span>' . esc_html__('SKIPPED', 'gelato-integration-for-woocommerce');
          }
          ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php if ($status_results['test_gelato_webhooks']['status'] == GelatoStatusChecker::STATUS_FAIL
    || $status_results['test_wc_api_access_by_gelato']['status'] == GelatoStatusChecker::STATUS_FAIL
    || $status_results['test_connection_from_gelato_to_wc']['status'] == GelatoStatusChecker::STATUS_FAIL
) {
    echo sprintf('<br><a href="%s" style="border-radius: 22px; line-height: 1.5; padding: 8px 15px; color:#fff; text-decoration: none;; font-size: 15px; background: #e5468c;border-color: #e5468c;">%s</a>', esc_url('?page=gelato-main-menu&reset_plugin=1'), __('Reset plugin', 'gelato-integration-for-woocommerce'));
}
?>