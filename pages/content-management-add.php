<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php if ( ! empty( $_POST ) && ! wp_verify_nonce( $_REQUEST['wp_create_nonce'], 'content-management-add-nonce' ) )  { die('<p>Security check failed.</p>'); } ?>
<div class="wrap">
<?php
$vm_errors = array();
$vm_success = '';
$vm_error_found = FALSE;

// Preset the form fields
$form = array(
	'vm_text' => '',
	'vm_link' => '',
	'vm_group' => '',
	'vm_date' => ''
);

// Form submitted, check the data
if (isset($_POST['vm_form_submit']) && $_POST['vm_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('vm_form_add');
	
	$form['vm_text'] = isset($_POST['vm_text']) ? wp_filter_post_kses($_POST['vm_text']) : '';
	if ($form['vm_text'] == '')
	{
		$vm_errors[] = __('Please enter marquee message.', 'vertical-marquee-plugin');
		$vm_error_found = TRUE;
	}

	$form['vm_link'] = isset($_POST['vm_link']) ? sanitize_text_field($_POST['vm_link']) : '';
	$form['vm_link'] = esc_url_raw( $form['vm_link'] );
	
	$form['vm_group'] = isset($_POST['vm_group']) ? sanitize_text_field($_POST['vm_group']) : '';
	
	$form['vm_date'] = isset($_POST['vm_date']) ? sanitize_text_field($_POST['vm_date']) : '';
	if (!preg_match("/\d{4}\-\d{2}-\d{2}/", $form['vm_date'])) 
	{
		$vm_errors[] = __('Please enter valid expiration date, YYYY-MM-DD.', 'vertical-marquee-plugin');
		$vm_error_found = TRUE;
	}
			
	//	No errors found, we can add this Group to the table
	if ($vm_error_found == FALSE)
	{
		$sql = $wpdb->prepare(
			"INSERT INTO `".WP_VM_TABLE."`
			(`vm_text`, `vm_link`, `vm_group`, `vm_date`)
			VALUES(%s, %s, %s, %s)",
			array($form['vm_text'], $form['vm_link'], $form['vm_group'], $form['vm_date'])
		);
		$wpdb->query($sql);
		
		$vm_success = __('New details was successfully added.', 'vertical-marquee-plugin');
		
		// Reset the form fields
		$form = array(
			'vm_text' => '',
			'vm_link' => '',
			'vm_group' => '',
			'vm_date' => ''
		);
	}
}

if ($vm_error_found == TRUE && isset($vm_errors[0]) == TRUE)
{
	?>
	<div class="error fade">
		<p><strong><?php echo $vm_errors[0]; ?></strong></p>
	</div>
	<?php
}
if ($vm_error_found == FALSE && strlen($vm_success) > 0)
{
	?>
	  <div class="updated fade">
		<p><strong><?php echo $vm_success; ?> <a href="<?php echo WP_vm_ADMIN_URL; ?>"><?php _e('Click here to view the details','vertical-marquee-plugin'); ?></a></strong></p>
	  </div>
	  <?php
	}
?>
<div class="form-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php _e('Vertical marquee plugin','vertical-marquee-plugin'); ?></h2>
	<form name="vm_form" method="post" action="#" onsubmit="return _vm_submit()"  >
      <h3><?php _e('Add message','vertical-marquee-plugin'); ?></h3>
      
	  <label for="tag-image"><?php _e('Enter marquee message','vertical-marquee-plugin'); ?></label>
      <textarea name="vm_text" id="vm_text" cols="80" rows="5"></textarea>
      <p><?php _e('We can enter HTML content also.','vertical-marquee-plugin'); ?></p>
	  
	  <label for="tag-image"><?php _e('Enter link','vertical-marquee-plugin'); ?></label>
      <input name="vm_link" type="text" id="vm_link" value="#" size="82" />
      <p><?php _e('When someone clicks on the message, where do you want to send them.','vertical-marquee-plugin'); ?></p>
	  
      <label for="tag-select-gallery-group"><?php _e('Select group','vertical-marquee-plugin'); ?></label>
      <select name="vm_group" id="vm_group">
	  <option value=''>Select</option>
	  <?php
		$sSql = "SELECT distinct(vm_group) as vm_group FROM `".WP_VM_TABLE."` order by vm_group";
		$myDistinctData = array();
		$arrDistinctDatas = array();
		$myDistinctData = $wpdb->get_results($sSql, ARRAY_A);
		$i = 0;
		if(count($myDistinctData) > 0)
		{
			foreach ($myDistinctData as $DistinctData)
			{
				$arrDistinctData[$i]["vm_group"] = strtoupper($DistinctData['vm_group']);
				$i = $i+1;
			}
		}
		for($j=$i; $j<$i+7; $j++)
		{
			$arrDistinctData[$j]["vm_group"] = "GROUP" . $j;
		}
		$arrDistinctData[$j+1]["vm_group"] = "WIDGET";
		$arrDistinctDatas = array_unique($arrDistinctData, SORT_REGULAR);
		foreach ($arrDistinctDatas as $arrDistinct)
		{
			?><option value='<?php echo $arrDistinct["vm_group"]; ?>'><?php echo $arrDistinct["vm_group"]; ?></option><?php
		}
		?>
      </select>
      <p><?php _e('This is to group the message. Select your group.','vertical-marquee-plugin'); ?></p>
	  
	  <label for="tag-image"><?php _e('Expiration date','vertical-marquee-plugin'); ?></label>
      <input name="vm_date" type="text" id="vm_date" size="20" value="<?php echo date('Y-m-d', strtotime('+1 years')); ?>" maxlength="10" />
      <p><?php _e('Enter expiration date for the message as per format.','vertical-marquee-plugin'); ?> (Format: YYYY-MM-DD).</p>

      <input name="vm_id" id="vm_id" type="hidden" value="">
      <input type="hidden" name="vm_form_submit" value="yes"/>
      <p class="submit">
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Insert Details','vertical-marquee-plugin'); ?>" type="submit" />
        <input name="publish" lang="publish" class="button add-new-h2" onclick="_vm_redirect()" value="<?php _e('Cancel','vertical-marquee-plugin'); ?>" type="button" />
        <input name="Help" lang="publish" class="button add-new-h2" onclick="_vm_help()" value="<?php _e('Help','vertical-marquee-plugin'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('vm_form_add'); ?>
	  <input type="hidden" name="wp_create_nonce" id="wp_create_nonce" value="<?php echo wp_create_nonce( 'content-management-add-nonce' ); ?>"/>
    </form>
</div>
<p class="description">
	<?php _e('Check official website for more information', 'vertical-marquee-plugin'); ?>
	<a target="_blank" href="<?php echo WP_vm_FAV; ?>"><?php _e('click here', 'vertical-marquee-plugin'); ?></a>
</p>
</div>