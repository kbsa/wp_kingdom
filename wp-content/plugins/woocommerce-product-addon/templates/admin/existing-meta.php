<?php
/*
 * this file showing existing meta group
* in admin
*/

// echo '<hr/>';
echo '<div class="wrapper import-export-block" >';
echo '<div class="col col-4">';
echo '<h3  style="text-align: center; margin: 5px;">'.__('Import PPOM Meta :', "ppom").'</h3>';
echo '</div>';

	
echo '<div class="col col-6">';

echo '<form method="post" action="admin-post.php" enctype="multipart/form-data">';
echo '<input type="hidden" name="action" value="ppom_import_meta" />';

echo '<label for="file-upload" class="btn btn-success btn-sm custom-file-upload" style="margin-right: 5px;">';
echo '<span>Choose a file…</span>';
echo '<input id="file-upload" type="file" name="ppom_csv" style="display: none;">';
echo '</label>';

echo '<input type="submit" class="btn btn-primary btn-sm" value="'.__ ( 'Import Meta', "ppom" ).'">';
echo '</form>';

echo '</div>'; //col-5 end
echo '<div class="col col-2">'; //col-2
echo '<button class="btn btn-red btn-sm cancle-import-export-btn"> '.__( 'Cancel', "ppom" ).'</button>';
echo '</div>'; //col-2 end
echo '<div class="clear" ></div>';
echo '</div>'; //wrapper end
// echo '<hr>';
echo '<div class="wrapper">';
echo '<h1>'.__('PPOM Meta List', "ppom").'</h1>';
echo '</div>'; //wrapper end

$all_forms = PPOM() -> get_product_meta_all();
?>

<div class="ppom-existing-meta-wrapper">
	
	<form method="post" action="admin-post.php" enctype="multipart/form-data">
	<input type="hidden" name="action" value="ppom_export_meta" />

	<div class="product-table-header">
		
		<span><strong> <?php _e( 'With selected', 'ppom'); ?> "<span id="selected_products_count">0</span>"</strong></span>
		<a class="btn btn-sm btn-yellow" id="delete_selected_products_btn"><?php _e( 'Delete', 'ppom' ) ?></a>
		<button class="btn btn-sm btn-yellow" id="export_selected_products_btn"><?php _e( 'Export', 'ppom'); ?></button>
		<span class="pull-right"><strong><?php echo count($all_forms); ?> <?php _e( 'Items', 'ppom' ); ?></strong></span>
		<span class="clear"></span>
	</div>
	<table id="ppom-meta-table" border="0" class="wp-list-table widefat plugins products-table">
		<thead>
			<tr class="bg-info">
				<th style="width: 3%"><input type="checkbox" name="allselected" id="all-select-products-head-btn"></th>
				<th style="width: 5%;"><?php _e('Meta ID.', "ppom")?></th>
				<th style="width: 12%;"><?php _e('Name.', "ppom")?></th>
				<th style="width: 25;"><?php _e('Meta.', "ppom")?></th>
				<th style="width: 300px;"><?php _e('How to link?', "ppom")?></th>
				<th><?php _e('Delete.', "ppom")?></th>
			</tr>
		</thead>
		<tfoot>
			<tr class="bg-info">
				<th style="width: 3%"><input type="checkbox" name="allselected" id="all-select-products-foot-btn"></th>
				<th style="width: 5%;"><?php _e('Meta ID.', "ppom")?></th>
				<th style="width: 12%;"><?php _e('Name.', "ppom")?></th>
				<th style="width: 25;"><?php _e('Meta.', "ppom")?></th>
				<th style="width: 300px;"><?php _e('How to link?', "ppom")?></th>
				<th><?php _e('Delete.', "ppom")?></th>
			</tr>
		</tfoot>
		
		<?php 
		
		foreach ($all_forms as $productmeta):
		
		$url_edit = add_query_arg(array('productmeta_id'=> $productmeta ->productmeta_id, 'do_meta'=>'edit'));
		$url_clone = add_query_arg(array('productmeta_id'=> $productmeta ->productmeta_id, 'do_meta'=>'clone'));
		$url_products = admin_url( 'edit.php?post_type=product', (is_ssl() ? 'https' : 'http') );
		$product_link = '<a href="'.esc_url($url_products).'">'.__('Products', 'ppom').'</a>';
		?>
		<tr>
			<td style="width: 20px;margin-left: 8px;">
				<input class="product_checkbox" style="margin-left: 8px;" type="checkbox" name="ppom_meta[]" value="<?php _e( $productmeta ->productmeta_id, 'ppom'); ?>">
			</td>
			<td><?php echo $productmeta ->productmeta_id; ?></td>
			<td>
				<a href="<?php echo $url_edit?>" style="display: block;">
					<?php echo stripcslashes($productmeta -> productmeta_name)?>
				</a><br>
			<a href="<?php echo $url_edit?>"><span class="dashicons dashicons-edit"></span> <?php _e('Edit', "ppom")?></a> |
			<a href="<?php echo $url_clone?>"><span class="dashicons dashicons-image-rotate-right"></span> <?php _e('Clone', "ppom")?></a><br> 
			</td>
			<td><?php echo ppom_admin_simplify_meta($productmeta -> the_meta)?></td>
			<td><?php printf(__("To link this meta with %s, open any product and you see these meta on right side. Select and Save product", "ppom"), $product_link);?></td>
			<td><a href="javascript:are_sure(<?php echo $productmeta -> productmeta_id?>)"><span id="del-file-<?php echo $productmeta -> productmeta_id?>" class="dashicons dashicons-no"></span></a></td>
		</tr>
		<?php 
		endforeach;
		?>
	</table>
	</form>
</div>
