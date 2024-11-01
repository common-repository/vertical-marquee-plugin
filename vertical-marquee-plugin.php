<?php
/*
Plugin Name: Vertical marquee plugin
Plugin URI: http://www.gopiplus.com/work/2012/06/30/vertical-marquee-wordpress-plugin/
Description:  You can use this vertical marquee plugin to make your text scroll upward or downwards. This plugin will work all leading browsers. 
Version: 7.2
Author: Gopi Ramasamy
Author URI: http://www.gopiplus.com/work/2012/06/30/vertical-marquee-wordpress-plugin/
Donate link: http://www.gopiplus.com/work/2012/06/30/vertical-marquee-wordpress-plugin/
Tags: vertical, marquee
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: vertical-marquee-plugin
Domain Path: /languages
*/

global $wpdb, $wp_version;
define("WP_VM_TABLE", $wpdb->prefix . "verticalmarquee");
define('WP_vm_FAV', 'http://www.gopiplus.com/work/2012/06/30/vertical-marquee-wordpress-plugin/');

if ( ! defined( 'WP_vm_BASENAME' ) )
	define( 'WP_vm_BASENAME', plugin_basename( __FILE__ ) );
	
if ( ! defined( 'WP_vm_PLUGIN_NAME' ) )
	define( 'WP_vm_PLUGIN_NAME', trim( dirname( WP_vm_BASENAME ), '/' ) );
	
if ( ! defined( 'WP_vm_PLUGIN_URL' ) )
	define( 'WP_vm_PLUGIN_URL', WP_PLUGIN_URL . '/' . WP_vm_PLUGIN_NAME );
	
if ( ! defined( 'WP_vm_ADMIN_URL' ) )
	define( 'WP_vm_ADMIN_URL', get_option('siteurl') . '/wp-admin/options-general.php?page=vertical-marquee-plugin' );

function vmarquee( $setting="1", $group="group1" ) 
{
	$arr = array();
	$arr["setting"] = $setting;
	$arr["group"] = $group;
	echo vm_shortcode($arr);
}

function verticalmarquee( $atts )
{
	global $wpdb;
	$link = "";
	
	$vm_scrollamount = 2;
	$vm_scrolldelay = 5;
	$vm_direction = "up";
	$vm_style = "height:150px;";
	$vm_group = "";
	
	if ( is_array( $atts ) )
	{
		foreach(array_keys($atts) as $key)
		{
			if($key == "vm_scrollamount")
			{
				$vm_scrollamount = $atts["vm_scrollamount"];
			}
			elseif($key == "vm_scrolldelay")
			{
				$vm_scrolldelay = $atts["vm_scrolldelay"];
			}
			elseif($key == "vm_direction")
			{
				$vm_direction = $atts["vm_direction"];
			}
			elseif($key == "vm_style")
			{
				$vm_style = $atts["vm_style"];
			}
			elseif($key == "vm_group")
			{
				$vm_group = $atts["vm_group"];
			}
		}
	}
	
	if($vm_scrollamount == "" || !is_numeric($vm_scrollamount))
	{
		$vm_scrollamount = 2;
	}
	
	if($vm_scrolldelay == "" || !is_numeric($vm_scrolldelay))
	{
		$vm_scrolldelay = 5;
	}
	
	if($vm_direction <> "up" && $vm_direction <> "down")
	{
		$vm_direction = "up";
	}
	
	if($vm_style == "")
	{
		$vm_style = "height:150px;";
	}
	
	$sSql = "select vm_text,vm_link from ".WP_VM_TABLE." where 1=1 ";
	if($vm_group <> "")
	{
		$sSql = $sSql . " and vm_group = %s ";
		$sSql = $wpdb->prepare($sSql, $vm_group);
	}
	$sSql = $sSql . " and (`vm_date` >= NOW())";
	$sSql = $sSql . " order by vm_id desc";
	$data = $wpdb->get_results($sSql);
	if ( ! empty($data) ) 
	{
		$cnt = 0;
		$vsm = "";
		foreach ( $data as $data ) 
		{
			$link = $data->vm_link;	
			if($cnt==0) 
			{  
				if($link != "" && $link != "#") { $vsm = $vsm . "<a target='_blank' href='".$link."'>"; } 
				$vsm = $vsm . stripslashes($data->vm_text);
				if($link != "" && $link != "#") { $vsm = $vsm . "</a><br />"; }
			}
			else
			{
				$vsm = $vsm . "   <br /><br />   ";
				if($link != "" && $link != "#") { $vsm = $vsm . "<a target='_blank' href='".$link."'>"; } 
				$vsm = $vsm . stripslashes($data->vm_text);
				if($link != "" && $link != "#") { $vsm = $vsm . "</a><br />"; }
			}			
			$cnt = $cnt + 1;
		}
	}
	else
	{
		$vsm = __('No data found, please check the expiration date.', 'vertical-marquee-plugin');
	}
	
	$what_marquee = "";	
	$what_marquee = $what_marquee . "<div style='padding:3px;'>";
	$what_marquee = $what_marquee . "<marquee style='$vm_style' scrollamount='$vm_scrollamount' scrolldelay='$vm_scrolldelay' direction='$vm_direction' onmouseover='this.stop()' onmouseout='this.start()'>";
	$what_marquee = $what_marquee . $vsm;
	$what_marquee = $what_marquee . "</marquee>";
	$what_marquee = $what_marquee . "</div>";
	echo $what_marquee;
}

function vm_shortcode( $atts ) 
{
	global $wpdb;
	//[vertical-marguee setting="1" group="widget"]
	if ( ! is_array( $atts ) )	{ return ''; }
	$setting = $atts['setting'];
	$group = $atts['group'];
	switch ($setting) 
	{ 
		case 1: 
			$vm_setting = get_option('vm_setting1');
			break;
		case 2: 
			$vm_setting = get_option('vm_setting2');
			break;
		default:
			$vm_setting = get_option('vm_setting3');
	}
	list($vm_scrollamount, $vm_scrolldelay, $vm_direction, $vm_style) = explode("~~", $vm_setting);
	
	// Database query
	$what_marquee = "";	
	$link = "";
	$sSql = "select vm_text,vm_link from ".WP_VM_TABLE." where vm_group = %s ";
	$sSql = $sSql . " and (`vm_date` >= NOW())";
	$sSql = $sSql . " ORDER BY vm_id desc";
	
	$sSql = $wpdb->prepare($sSql, $group);
	
	$data = $wpdb->get_results($sSql);
	if ( ! empty($data) ) 
	{
		$cnt = 0;
		$vsm = "";
		foreach ( $data as $data ) 
		{
			$link = $data->vm_link;
			if($cnt==0) 
			{  
				if($link != "" && $link != "#") { $vsm = $vsm . "<a target='_blank' href='".$link."'>"; } 
				$vsm = $vsm . stripslashes($data->vm_text);
				if($link != "" && $link != "#") { $vsm = $vsm . "</a><br />"; }
			}
			else
			{
				$vsm = $vsm . "   <br /><br />   ";
				if($link != "" && $link != "#") { $vsm = $vsm . "<a target='_blank' href='".$link."'>"; } 
				$vsm = $vsm . stripslashes($data->vm_text);
				if($link != "" && $link != "#") { $vsm = $vsm . "</a><br />"; }
			}			
			$cnt = $cnt + 1;
		}
		
		// Marquee text display
		$what_marquee = $what_marquee . "<div style='padding:3px;'>";
		$what_marquee = $what_marquee . "<marquee style='$vm_style' scrollamount='$vm_scrollamount' scrolldelay='$vm_scrolldelay' direction='$vm_direction' onmouseover='this.stop()' onmouseout='this.start()'>";
		$what_marquee = $what_marquee . $vsm;
		$what_marquee = $what_marquee . "</marquee>";
		$what_marquee = $what_marquee . "</div>";
	}
	else
	{
		$what_marquee = __('Please check your short code, no records available.', 'vertical-marquee-plugin');	
	}
	return $what_marquee;
}

function vm_deactivate() 
{
	delete_option('vm_title');
	delete_option('vm_setting1');
	delete_option('vm_setting2');
	delete_option('vm_setting3');
}

function vm_activation() 
{
	global $wpdb;
	if($wpdb->get_var("show tables like '". WP_VM_TABLE . "'") != WP_VM_TABLE) 
	{
		$wpdb->query("
			CREATE TABLE IF NOT EXISTS `". WP_VM_TABLE . "` (
			  `vm_id` int(11) NOT NULL auto_increment,
			  `vm_text` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
			  `vm_link` VARCHAR( 1024 ) NOT NULL default '#',
			  `vm_group` VARCHAR( 10 ) NOT NULL default 'GROUP1',
			  `vm_date` datetime NOT NULL default '0000-00-00 00:00:00',
			  PRIMARY KEY  (`vm_id`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			");
		$sSql = "INSERT INTO `". WP_VM_TABLE . "` (vm_text, vm_link, vm_group, vm_date) VALUES ('This is sample text for Vertical marquee plugin.', '#', 'WIDGET', '9999-01-01');"; 
		$wpdb->query($sSql);
		$sSql = "INSERT INTO `". WP_VM_TABLE . "` (vm_text, vm_link, vm_group, vm_date) VALUES ('This is sample text for Vertical marquee plugin.', '#', 'WIDGET', '9999-01-01');"; 
		$wpdb->query($sSql);
		$sSql = "INSERT INTO `". WP_VM_TABLE . "` (vm_text, vm_link, vm_group, vm_date) VALUES ('This is sample text for Vertical marquee plugin.', '#', 'GROUP1', '9999-01-01');"; 
		$wpdb->query($sSql);
		$sSql = "INSERT INTO `". WP_VM_TABLE . "` (vm_text, vm_link, vm_group, vm_date) VALUES ('This is sample text for Vertical marquee plugin.', '#', 'GROUP1', '9999-01-01');"; 
		$wpdb->query($sSql);
	}
	// vm_scrollamount~~vm_scrolldelay~~vm_direction~~vm_style
	add_option('vm_title', "Vertical marquee");
	add_option('vm_setting1', "2~~5~~up~~height:100px;");
	add_option('vm_setting2', "2~~5~~up~~color:#FF0000;font:Arial;height:100px;");
	add_option('vm_setting3', "2~~5~~down~~color:#FF0000;font:Arial;height:100px;");
}

function vm_admin_options() 
{
	global $wpdb;
	$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
	switch($current_page)
	{
		case 'edit':
			include('pages/content-management-edit.php');
			break;
		case 'add':
			include('pages/content-management-add.php');
			break;
		case 'set':
			include('pages/content-setting.php');
			break;
		default:
			include('pages/content-management-show.php');
			break;
	}
}

function vm_add_to_menu() 
{
	add_options_page( __('Vertical marquee','vertical-marquee-plugin'), __('Vertical marquee','vertical-marquee-plugin'), 'manage_options', 'vertical-marquee-plugin', 'vm_admin_options' );
}

class vm_widget_register extends WP_Widget 
{
	function __construct() 
	{
		$widget_ops = array('classname' => 'vm_widget', 'description' => __('Vertical marquee', 'vertical-marquee-plugin'), 'vertical-marquee-plugin');
		parent::__construct('VerticalMarquee', __('Vertical marquee', 'vertical-marquee-plugin'), $widget_ops);
	}
	
	function widget( $args, $instance ) 
	{
		extract( $args, EXTR_SKIP );
		$vm_Title 			= apply_filters( 'widget_title', empty( $instance['vm_Title'] ) ? '' : $instance['vm_Title'], $instance, $this->id_base );
		$vm_scrollamount	= $instance['vm_scrollamount'];
		$vm_scrolldelay		= $instance['vm_scrolldelay'];
		$vm_direction		= $instance['vm_direction'];
		$vm_style			= $instance['vm_style'];
		$vm_group			= $instance['vm_group'];
		
		echo $args['before_widget'];
		if ( ! empty( $vm_Title ) )
		{
			echo $args['before_title'] . $vm_Title . $args['after_title'];
		}
		
		// Call widget method
		$arr = array();
		$arr["vm_scrollamount"] = $vm_scrollamount;
		$arr["vm_scrolldelay"] 	= $vm_scrolldelay;
		$arr["vm_direction"] 	= $vm_direction;
		$arr["vm_style"] 		= $vm_style;
		$arr["vm_group"] 		= $vm_group;
		verticalmarquee($arr);
		// Call widget method
		echo $args['after_widget'];
	}
	
	function update( $new_instance, $old_instance ) 
	{
		$instance 						= $old_instance;
		$instance['vm_Title'] 			= ( ! empty( $new_instance['vm_Title'] ) ) ? strip_tags( $new_instance['vm_Title'] ) : '';
		$instance['vm_scrollamount']	= ( ! empty( $new_instance['vm_scrollamount'] ) ) ? strip_tags( $new_instance['vm_scrollamount'] ) : '';
		$instance['vm_scrolldelay'] 	= ( ! empty( $new_instance['vm_scrolldelay'] ) ) ? strip_tags( $new_instance['vm_scrolldelay'] ) : '';
		$instance['vm_direction'] 		= ( ! empty( $new_instance['vm_direction'] ) ) ? strip_tags( $new_instance['vm_direction'] ) : '';
		$instance['vm_style'] 			= ( ! empty( $new_instance['vm_style'] ) ) ? strip_tags( $new_instance['vm_style'] ) : '';
		$instance['vm_group'] 			= ( ! empty( $new_instance['vm_group'] ) ) ? strip_tags( $new_instance['vm_group'] ) : '';
		return $instance;
	}
	
	function form( $instance ) 
	{
		$defaults = array(
			'vm_Title' 			=> '',
            'vm_scrollamount' 	=> '',
            'vm_scrolldelay' 	=> '',
            'vm_direction' 		=> '',
			'vm_style' 			=> '',
			'vm_group' 			=> ''
        );
		$instance 			= wp_parse_args( (array) $instance, $defaults);
        $vm_Title 			= $instance['vm_Title'];
        $vm_scrollamount 	= $instance['vm_scrollamount'];
        $vm_scrolldelay 	= $instance['vm_scrolldelay'];
        $vm_direction 		= $instance['vm_direction'];
		$vm_style 			= $instance['vm_style'];
		$vm_group 			= $instance['vm_group'];
		?>
		<p>
            <label for="<?php echo $this->get_field_id('vm_Title'); ?>"><?php _e('Widget Title', 'vertical-marquee-plugin'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('vm_Title'); ?>" name="<?php echo $this->get_field_name('vm_Title'); ?>" type="text" value="<?php echo $vm_Title; ?>" />
        </p>	
		<p>
            <label for="<?php echo $this->get_field_id('vm_scrollamount'); ?>"><?php _e('Scroll amount', 'vertical-marquee-plugin'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('vm_scrollamount'); ?>" name="<?php echo $this->get_field_name('vm_scrollamount'); ?>" type="text" value="<?php echo $vm_scrollamount; ?>" maxlength="2" />
			<?php _e('Scroll Amount, together with Scroll Delay, sets the speed of the scrolling (Example: 2)', 'vertical-marquee-plugin'); ?>
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('vm_scrolldelay'); ?>"><?php _e('Scroll delay', 'vertical-marquee-plugin'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('vm_scrolldelay'); ?>" name="<?php echo $this->get_field_name('vm_scrolldelay'); ?>" type="text" value="<?php echo $vm_scrolldelay; ?>" maxlength="2" />
			<?php _e('Scroll Delay, together with Scroll Amount, sets the speed of the scrolling (Example: 5)', 'vertical-marquee-plugin'); ?>
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('vm_direction'); ?>"><?php _e('Scroll Direction', 'vertical-marquee-plugin'); ?></label><br />
			<select class="" id="<?php echo $this->get_field_id('vm_direction'); ?>" name="<?php echo $this->get_field_name('vm_direction'); ?>">
				<option value="up" <?php $this->vm_render_selected($vm_direction=='up'); ?>>Up</option>
				<option value="down" <?php $this->vm_render_selected($vm_direction=='down'); ?>>Down</option>
			</select>
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('vm_style'); ?>"><?php _e('Style', 'vertical-marquee-plugin'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('vm_style'); ?>" name="<?php echo $this->get_field_name('vm_style'); ?>" type="text" value="<?php echo $vm_style; ?>" />
			<?php _e('This style attribute can contain any CSS property. (Example: height:100px;)', 'vertical-marquee-plugin'); ?>
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('vm_group'); ?>"><?php _e('Message group', 'vertical-marquee-plugin'); ?></label><br />
			<select class="" id="<?php echo $this->get_field_id('vm_group'); ?>" name="<?php echo $this->get_field_name('vm_group'); ?>">
				<option value="">Select</option>
				<?php
				$arrData = array();
				$arrData = $this->vm_loadtype();
				if(count($arrData) > 0)
				{
					foreach ($arrData as $arrData)
					{
						?><option value="<?php echo $arrData["vm_group"]; ?>" <?php $this->vm_render_selected($arrData["vm_group"] == $vm_group); ?>><?php echo $arrData["vm_group"]; ?></option><?php
					}
				}
				?>
			</select>
        </p>
		<p><?php _e('For more information', 'vertical-marquee-plugin'); ?> <a target="_blank" href="<?php echo WP_vm_FAV; ?>"><?php _e('click here', 'vertical-marquee-plugin'); ?></a></p>
		<?php
	}
	
	function vm_render_selected($var) 
	{
		if ($var==1 || $var==true) 
		{
			echo 'selected="selected"';
		}
	}
	
	function vm_loadtype() 
	{
		global $wpdb;
		$arrData = array();
		$sSql 	 = "SELECT distinct(vm_group) as vm_group FROM ".WP_VM_TABLE." order by vm_group";
		$myData  = $wpdb->get_results($sSql, ARRAY_A);
		$i 		 = 0;
		if(count($myData) > 0 )
		{
			foreach ($myData as $data)
			{
				$arrData[$i]["vm_group"] = stripslashes($data['vm_group']);
				$i=$i+1;
			}
		}
		return $arrData;
	}
}

function vm_widget_loading()
{
	register_widget( 'vm_widget_register' );
}

function vm_textdomain() 
{
	  load_plugin_textdomain( 'vertical-marquee-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function vm_adminscripts() 
{
	if( !empty( $_GET['page'] ) ) 
	{
		switch ( $_GET['page'] ) 
		{
			case 'vertical-marquee-plugin':
				wp_register_script( 'vertical-marquee-adminscripts', WP_vm_PLUGIN_URL . '/pages/setting.js', '', '', true );
				wp_enqueue_script( 'vertical-marquee-adminscripts' );
				$vertical_marquee_select_params = array(
					'vm_text'  		=> __( 'Please enter marquee message.', 'vertical-marquee-select', 'vertical-marquee-plugin' ),
					'vm_group'  	=> __( 'Please select message group.', 'vertical-marquee-select', 'vertical-marquee-plugin' ),
					'vm_delete'  	=> __( 'Do you want to delete this record?', 'vertical-marquee-select', 'vertical-marquee-plugin' ),
				);
				wp_localize_script( 'vertical-marquee-adminscripts', 'vertical_marquee_adminscripts', $vertical_marquee_select_params );
				break;
		}
	}
}

add_action( 'widgets_init', 'vm_widget_loading');
add_action('plugins_loaded', 'vm_textdomain');
add_shortcode( 'vertical-marguee', 'vm_shortcode' );
register_activation_hook(__FILE__, 'vm_activation');
add_action('admin_menu', 'vm_add_to_menu');
register_deactivation_hook( __FILE__, 'vm_deactivate' );
add_action('admin_enqueue_scripts', 'vm_adminscripts');
?>