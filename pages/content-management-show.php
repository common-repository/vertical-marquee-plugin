<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php if ( ! empty( $_POST ) && ! wp_verify_nonce( $_REQUEST['wp_create_nonce'], 'content-management-show-nonce' ) )  { die('<p>Security check failed.</p>'); } ?>
<?php
// Form submitted, check the data
if (isset($_POST['frm_vm_display']) && $_POST['frm_vm_display'] == 'yes')
{
	$did = isset($_GET['did']) ? sanitize_text_field($_GET['did']) : '0';
	if(!is_numeric($did)) { die('<p>Are you sure you want to do this?</p>'); }
	
	$vm_success = '';
	$vm_success_msg = FALSE;
	
	// First check if ID exist with requested ID
	$sSql = $wpdb->prepare(
		"SELECT COUNT(*) AS `count` FROM ".WP_VM_TABLE."
		WHERE `vm_id` = %d",
		array($did)
	);
	$result = '0';
	$result = $wpdb->get_var($sSql);
	
	if ($result != '1')
	{
		?>
		<div class="error fade">
		  <p><strong><?php _e('Oops, selected details doesnt exist','vertical-marquee-plugin'); ?></strong></p>
		</div>
		<?php
	}
	else
	{
		// Form submitted, check the action
		if (isset($_GET['ac']) && $_GET['ac'] == 'del' && isset($_GET['did']) && $_GET['did'] != '')
		{
			//	Just security thingy that wordpress offers us
			check_admin_referer('vm_form_show');
			
			//	Delete selected record from the table
			$sSql = $wpdb->prepare("DELETE FROM `".WP_VM_TABLE."`
					WHERE `vm_id` = %d
					LIMIT 1", $did);
			$wpdb->query($sSql);
			
			//	Set success message
			$vm_success_msg = TRUE;
			$vm_success = __('Selected record was successfully deleted.', 'vertical-marquee-plugin');
		}
	}
	
	if ($vm_success_msg == TRUE)
	{
		?>
		<div class="updated fade">
		  <p><strong><?php echo $vm_success; ?></strong></p>
		</div>
		<?php
	}
}
?>
<div class="wrap">
  <div id="icon-edit" class="icon32 icon32-posts-post"></div>
  <h2><?php _e('Vertical marquee plugin','vertical-marquee-plugin'); ?>
  <a class="add-new-h2" href="<?php echo WP_vm_ADMIN_URL; ?>&amp;ac=add"><?php _e('Add New','vertical-marquee-plugin'); ?></a></h2>
  <div class="tool-box">
    <?php
	$sSql = "SELECT * FROM `".WP_VM_TABLE."` order by vm_id desc";
	$myData = array();
	$myData = $wpdb->get_results($sSql, ARRAY_A);
	?>
    <form name="frm_vm_display" method="post">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
            <th scope="col"><?php _e('Marquee message','vertical-marquee-plugin'); ?></th>
            <th scope="col"><?php _e('Group','vertical-marquee-plugin'); ?></th>
            <th scope="col"><?php _e('Expiration','vertical-marquee-plugin'); ?></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th scope="col"><?php _e('Marquee message','vertical-marquee-plugin'); ?></th>
            <th scope="col"><?php _e('Group','vertical-marquee-plugin'); ?></th>
            <th scope="col"><?php _e('Expiration','vertical-marquee-plugin'); ?></th>
          </tr>
        </tfoot>
        <tbody>
          <?php 
			$i = 0;
			if(count($myData) > 0 )
			{
				foreach ($myData as $data)
				{
					  $vm_date = "";
					  if($data['vm_date'] <> "")
					  {
						$vm_date = substr($data['vm_date'], 0, 10);
					  }
					?>
					  <tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
						<td>
						<?php echo stripslashes($data['vm_text']); ?>
						<div class="row-actions">
						<span class="edit"><a title="Edit" href="<?php echo WP_vm_ADMIN_URL; ?>&amp;ac=edit&amp;did=<?php echo $data['vm_id']; ?>"><?php _e('Edit','vertical-marquee-plugin'); ?></a> | </span>
						<span class="trash"><a onClick="javascript:_vm_delete('<?php echo $data['vm_id']; ?>')" href="javascript:void(0);"><?php _e('Delete','vertical-marquee-plugin'); ?></a></span> </div></td>
						<td><?php echo $data['vm_group']; ?></td>
						<td><?php echo $vm_date; ?></td>
					  </tr>
					  <?php 
					$i = $i+1; 
				}
			}
			else
			{
				?><tr><td colspan="3" align="center"><?php _e('No records available.','vertical-marquee-plugin'); ?></td></tr><?php 
			}
			?>
        </tbody>
      </table>
      <?php wp_nonce_field('vm_form_show'); ?>
      <input type="hidden" name="frm_vm_display" value="yes"/>
	  <input type="hidden" name="wp_create_nonce" id="wp_create_nonce" value="<?php echo wp_create_nonce( 'content-management-show-nonce' ); ?>"/>
    </form>
    <div class="tablenav bottom">
	  <a href="<?php echo WP_vm_ADMIN_URL; ?>&amp;ac=add"><input class="button action" type="button" value="<?php _e('Add New','vertical-marquee-plugin'); ?>" /></a> 
	  <a href="<?php echo WP_vm_ADMIN_URL; ?>&amp;ac=set"><input class="button action" type="button" value="<?php _e('Setting Management','vertical-marquee-plugin'); ?>" /></a> 
	  <a target="_blank" href="<?php echo WP_vm_FAV; ?>"><input class="button action" type="button" value="<?php _e('Help','vertical-marquee-plugin'); ?>" /></a> 
	  <a target="_blank" href="<?php echo WP_vm_FAV; ?>"><input class="button button-primary" type="button" value="<?php _e('Short Code','vertical-marquee-plugin'); ?>" /></a> 
    </div>
  </div>
</div>