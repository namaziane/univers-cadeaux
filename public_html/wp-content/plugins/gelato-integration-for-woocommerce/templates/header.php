<?php
$base_url = '?page=gelato-main-menu';
?>

<h2 class="nav-tab-wrapper">
    <?php foreach ($tabs as $tab) : ?>
        <?php
        $active = '';
        if (!empty($_GET['tab']) && sanitize_text_field( wp_unslash($_GET['tab'])) == $tab['id']) {
            $active = 'nav-tab-active';
        }
        if (empty($_GET['tab']) && $tab['id'] == '') {
            $active = 'nav-tab-active';
        }
        ?>
      <a href="<?php echo esc_url($base_url . ( !empty($tab['id']) ? '&tab=' . esc_html($tab['id']) : '')); ?>"
         class="nav-tab <?php echo esc_attr($active); ?>"><?php echo esc_html($tab['name']); ?></a>
    <?php endforeach; ?>
</h2>
