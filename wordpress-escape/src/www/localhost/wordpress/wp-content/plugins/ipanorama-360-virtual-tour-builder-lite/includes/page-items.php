<?php

// If this file is called directly, abort.
if(!defined('ABSPATH')) {
	exit;
}

$list_table = new iPanorama_List_Table_Items();
$list_table->prepare_items();

?>
<!-- /begin ipanorama app -->
<div class="ipanorama-root" id="ipanorama-app-items">
	<?php require 'page-info.php'; ?>
	<div class="ipanorama-page-header">
		<div class="ipanorama-title"><i class="xfa fa-cubes"></i><?php esc_html_e('iPanorama 360 Items', IPANORAMA_PLUGIN_NAME); ?></div>
		<div class="ipanorama-actions">
			<a class="ipanorama-blue" href="?page=<?php echo IPANORAMA_PLUGIN_NAME . '_item'; ?>" title="<?php esc_html_e('Create a new item', IPANORAMA_PLUGIN_NAME); ?>"><?php esc_html_e('Add Item', IPANORAMA_PLUGIN_NAME); ?></a>
		</div>
	</div>
	<div class="ipanorama-messages" id="ipanorama-messages">
	</div>
	<div class="ipanorama-app">
		<form method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>">
			<?php $list_table->display(); ?>
		</form>
	</div>
</div>
<!-- /end ipanorama app -->