<?php if ($is_connected): ?>
  <div class="wrap" style="margin: 0;">
  <div style="padding: 10px 10px 60px;background: #fff;text-align: center;">
    
  <div><img alt="<?php esc_attr_e('Gelato Integration for WooCommerce', 'gelato-integration-for-woocommerce');?>" src="<?php echo esc_attr($asset_folder); ?>images/woocommerce-banner.png" style="max-width: 480px;"></div>
  <div class="" style="
    width: 25%;
    box-shadow: rgb(0 0 0 / 16%) 0px 1px 4px;
    padding: 20px;
    text-align: center;
    display: inline-block;
    margin: 0 10px;
    min-height: 128px;
    vertical-align: top;
">
		<h2 style="margin: 0;"><?php echo __('24/7 support anywhere, anytime', 'gelato-integration-for-woocommerce'); ?></h2>
		<p style="min-height: 60px;"><?php echo __('Our customer support team works relentlessly to help you scale your business and get the most out of the platform.', 'gelato-integration-for-woocommerce'); ?></p>
    <a href="https://apisupport.gelato.com/hc/en-us/articles/360017261160-How-do-I-contact-Gelato-" target="_blank" style="
    border-radius: 22px;
    line-height: 1.5;
    padding: 6px 15px;
    color:#fff;
    text-decoration: none;
    font-size: 13px;
    background: #e5468c;
    border-color: #e5468c;
"><?php echo __('Contact Support', 'gelato-integration-for-woocommerce'); ?></a>
	</div>
    
  <div class="" style="
    width: 25%;
    box-shadow: rgb(0 0 0 / 16%) 0px 1px 4px;
    padding: 20px;
    text-align: center;
    display: inline-block;
    margin: 0 10px;
    min-height: 128px;
    vertical-align: top;
">
		<h2 style="MARGIN: 0;"><?php echo __('Check out our Help Center', 'gelato-integration-for-woocommerce'); ?></h2>
		<p style="min-height: 60px;"><?php echo __('Are you experiencing technical issues? You will find answers to many questions and video tutorials.', 'gelato-integration-for-woocommerce'); ?></p>
		
			<a href="https://apisupport.gelato.com/hc/en-us" target="_blank" style="
    border-radius: 22px;
    line-height: 1.5;
    padding: 6px 15px;
    color:#fff;
    text-decoration: none;
    font-size: 13px;
    background: #e5468c;
    border-color: #e5468c;
"><?php echo __('Go to Help Center', 'gelato-integration-for-woocommerce'); ?></a>
		
	</div></div>
</div>
<?php else: ?>
<div class="wrap" style="margin:0;">
  <div style="padding:10px;background:#FFF;">
    <div style="width:100%;text-align:center;padding: 10px 0 30px;">
      <img alt="<?php esc_attr_e('Gelato Integration for WooCommerce', 'gelato-integration-for-woocommerce');?>" src="<?php echo esc_attr($asset_folder); ?>images/woocommerce-banner.png" style="max-width: 480px;">
      <h1><?php echo __('You\'re almost done', 'gelato-integration-for-woocommerce'); ?></h1>
      <p style="font-size: 16px; margin-bottom:34px;"><?php echo __('Just a few more steps to connect your WooCommerce store to Gelato', 'gelato-integration-for-woocommerce'); ?><br></p>
      <a href="<?php echo $url_connect; ?>" style="border-radius: 22px; line-height: 1.5; padding: 8px 15px; color:#fff; text-decoration: none;; font-size: 15px; background: #e5468c;border-color: #e5468c;"target="_blank"><?php echo __('Connect to Gelato', 'gelato-integration-for-woocommerce'); ?></a>
    </div>
  </div>
</div>

<?php endif; ?>
