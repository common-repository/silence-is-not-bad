<?php
/*
Plugin Name: Silence Is Not Bad 
Plugin URI: http://chinawp.info/silence-is-not-bad/ 
Description: Do not hope your subscriber or co-authoers view admin bar, plugin update notice, upgrade notice... and so on? This plugin can setting these notice for different roles. 
Author: chinawp.info 
Version: 1.0
Author URI: http://chinawp.info  
*/


add_action('admin_menu', 'silenceMenu');
add_filter('init','silenceisnotbatinit');
function silenceisnotbatinit()
{
	if (!(is_user_logged_in()))
	{
		return;
	}
	$nowUser = wp_get_current_user();
	
	$nowUserRoleArray = $nowUser->roles;
	if ((is_array($nowUserRoleArray)) && (count($nowUserRoleArray) > 0))
	{
		$nowUserRole = ucfirst($nowUserRoleArray[0]);
	}
	else
	{
		return;
	}

	$silenceadminbars = get_option('silenceadminbars');
	$silenceupgrade = get_option('silenceupgrade');
	$silenceupdates = get_option('silenceupdates');
	
	if ((!(empty($silenceupdates))) && (is_array($silenceupdates)) && (count($silenceupdates) > 0))
	{
		if (in_array($nowUserRole,$silenceupdates))
		{
			remove_action( 'load-plugins.php', 'wp_update_plugins' );
			remove_action( 'load-update.php', 'wp_update_plugins' );
			remove_action( 'load-update-core.php', 'wp_update_plugins' );
			remove_action( 'admin_init', '_maybe_update_plugins' );
			remove_action( 'wp_update_plugins', 'wp_update_plugins' );
			add_action('pre_site_transient_update_plugins', create_function('$nofunction', "return null;"));
			
			remove_action( 'load-themes.php', 'wp_update_themes' );
			remove_action( 'load-update.php', 'wp_update_themes' );
			remove_action( 'load-update-core.php', 'wp_update_themes' );
			remove_action( 'admin_init', '_maybe_update_themes' );
			remove_action( 'wp_update_themes', 'wp_update_themes' );
			add_action('pre_site_transient_update_themes', create_function('$nofunction', "return null;"));
			remove_action( 'load-update-core.php', 'wp_update_themes' );
			add_filter( 'pre_site_transient_update_themes', create_function('$nofunction', "return null;"));
		}
	}
	
	if ((!(empty($silenceupgrade))) && (is_array($silenceupgrade)) && (count($silenceupgrade) > 0))
	{
		if (in_array($nowUserRole,$silenceupgrade))
		{
			remove_action( 'admin_init', '_maybe_update_core' );
			remove_action( 'wp_version_check', 'wp_version_check' );
			add_action('pre_site_transient_update_core', create_function('$nofunction', "return null;"));			
		}
	}

	if ((!(empty($silenceadminbars))) && (is_array($silenceadminbars)) && (count($silenceadminbars) > 0))
	{
		if (in_array($nowUserRole,$silenceadminbars))
		{

			add_filter( 'show_admin_bar', '__return_false' );			
		}
	}
}

function silenceMenu()
{

	add_menu_page(__('Silence Not Bad','Silence Is Not Bad'), __('Silence Not Bad','Silence Is Not Bad'), 10, 'silenceisnotbad.php','silenceManagement');
	add_submenu_page('silenceisnotbad.php',__('Silence Management','Silence Is Not Bad'), __('Silence Management','Silence Is Not Bad'),10, 'silenceisnotbad.php','silenceManagement');
}

function silenceManagement()
{
	global $wpdb;
	if (isset($_POST['silencesubmit']))
	{
		if (isset($_POST['adminbarscheckbox']))
		{
			$silenceadminbars = $wpdb->escape($_POST['adminbarscheckbox']);
			update_option('silenceadminbars',$silenceadminbars);
		}
		else
		{
			delete_option('silenceadminbars');
		}
		
		if (isset($_POST['upgradecheckbox']))
		{
			$silenceupgrade = $wpdb->escape($_POST['upgradecheckbox']);
			update_option('silenceupgrade',$silenceupgrade);
		}
		else
		{
			delete_option('silenceupgrade');
		}
		
		if (isset($_POST['updatescheckbox']))
		{
			$silenceupdates = $wpdb->escape($_POST['updatescheckbox']);
			update_option('silenceupdates',$silenceupdates);
		}
		else
		{
			delete_option('silenceupdates');
		}
	}
	
?>
<div style='margin:10px 5px;'>
<div style='float:left;margin-right:10px;'>
<img src='<?php echo get_option('siteurl');  ?>/wp-content/plugins/silenceisnotbad/images/new.png' style='width:30px;height:30px;'>
</div> 
<div style='padding-top:5px; font-size:22px;'> <i></>Role Management -- Silence Is Not Bad</i></div>
</div>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:90%;">
								<div class="postbox">
									<h3 class='hndle'><span>
										Remove Notices For These User Group 
									</span>
									</h3>
								
									<div>
										<form id="barsform" name="barsform" action="" method="POST">
										<div  style='padding-left:10px;background:#fff;padding-bottom:50px;padding-top:20px;padding-left:10px;'>
										<h4 ><i><font color= 'gray'>Remove Admin Bar In Front For These User Group</font></i></h4>
										<div >
										<hr />
										</div>
										<div >
										<span><input type='checkbox' name='adminbarscheckbox[]' class='adminbarscheckboxclass' value='Administrator' <?php silencecheckadminbarbox('Administrator'); ?>> Administrator
										</span>
										<span style="margin-left:30px;">
										<input type='checkbox' name='adminbarscheckbox[]' class='adminbarscheckboxclass' value='Editor' <?php silencecheckadminbarbox('Editor'); ?>> Editor
										</span>
										<span style="margin-left:30px;">
										<input type='checkbox' name='adminbarscheckbox[]' class='adminbarscheckboxclass' value='Author'  <?php silencecheckadminbarbox('Author'); ?>> Author
										</span>
										<span style="margin-left:30px;">
										<input type='checkbox' name='adminbarscheckbox[]' class='adminbarscheckboxclass' value='Contributor' <?php silencecheckadminbarbox('Contributor'); ?>> Contributor
										</span>
										<span style="margin-left:30px;">
										<input type='checkbox' name='adminbarscheckbox[]' class='adminbarscheckboxclass' value='Subscriber' <?php silencecheckadminbarbox('Subscriber'); ?>> Subscriber
										</span>
										</div>
										</div>
										
										<div  style='background:#eef;padding-bottom:50px;padding-top:20px;padding-left:10px;'>
										<h4 ><i><font color= 'gray'>Remove Wordpress Upgrade Notice For These User Group</font></i></h4>
										<div style='background:blue;'>
										<hr />
										</div>
										<div>
										<span><input type='checkbox' name='upgradecheckbox[]' class='adminbarscheckboxclass' value='Administrator' <?php silencecheckupgradebox('Administrator'); ?>> Administrator
										</span>
										<span style="margin-left:30px;">
										<input type='checkbox' name='upgradecheckbox[]' class='adminbarscheckboxclass' value='Editor'  <?php silencecheckupgradebox('Editor'); ?>> Editor
										</span>
										<span style="margin-left:30px;">
										<input type='checkbox' name='upgradecheckbox[]' class='adminbarscheckboxclass' value='Author'   <?php silencecheckupgradebox('Author'); ?>> Author
										</span>
										<span style="margin-left:30px;">
										<input type='checkbox' name='upgradecheckbox[]' class='adminbarscheckboxclass' value='Contributor' <?php silencecheckupgradebox('Contributor'); ?>> Contributor
										</span>
										<span style="margin-left:30px;">
										<input type='checkbox' name='upgradecheckbox[]' class='adminbarscheckboxclass' value='Subscriber' <?php silencecheckupgradebox('Subscriber'); ?>> Subscriber
										</span>
										</div>
										</div>


										<div  style='padding-left:10px;background:#fff;padding-bottom:50px;padding-top:20px;padding-left:10px;'>
										<h4 ><i><font color= 'gray'>Remove plugins/Themes Updates Notice For These User Group</font></i></h4>
										<div >
										<hr />
										</div>
										<div >
										<span><input type='checkbox' name='updatescheckbox[]' class='adminbarscheckboxclass' value='Administrator'  <?php silencecheckupdatebox('Administrator'); ?>> Administrator
										</span>
										<span style="margin-left:30px;">
										<input type='checkbox' name='updatescheckbox[]' class='adminbarscheckboxclass' value='Editor' <?php silencecheckupdatebox('Editor'); ?>> Editor
										</span>
										<span style="margin-left:30px;">
										<input type='checkbox' name='updatescheckbox[]' class='adminbarscheckboxclass' value='Author' <?php silencecheckupdatebox('Author'); ?>> Author
										</span>
										<span style="margin-left:30px;">
										<input type='checkbox' name='updatescheckbox[]' class='adminbarscheckboxclass' value='Contributor' <?php silencecheckupdatebox('Contributor'); ?>> Contributor
										</span>
										<span style="margin-left:30px;">
										<input type='checkbox' name='updatescheckbox[]' class='adminbarscheckboxclass' value='Subscriber' <?php silencecheckupdatebox('Subscriber'); ?>> Subscriber
										</span>
										</div>
										</div>										
										<br />
																				
										&nbsp;<input type="submit" class="silencesubmitclass" name="silencesubmit" value="Setting Now">
										</form>
										
										<br />
										<br />
									</div>
								</div>
							</div>
						</div>
					</div>
		    	</div>
			</div>
		</div>
		<div style="clear:both"></div>
		<br />
		

<?php
//</div>			
}

function silencecheckadminbarbox($proles)
{
	$silenceadminbars = get_option('silenceadminbars');
	if ((!(empty($silenceadminbars))) && (is_array($silenceadminbars)) && (count($silenceadminbars) > 0))
	{
		if (in_array($proles,$silenceadminbars))
		{
			echo "checked";
		}
	} 
}

function silencecheckupgradebox($proles)
{
	$silenceupgrade = get_option('silenceupgrade');
	if ((!(empty($silenceupgrade))) && (is_array($silenceupgrade)) && (count($silenceupgrade) > 0))
	{
		if (in_array($proles,$silenceupgrade))
		{
			echo "checked";
		}
	} 	
}

function silencecheckupdatebox($proles)
{
	$silenceupdates = get_option('silenceupdates');
	if ((!(empty($silenceupdates))) && (is_array($silenceupdates)) && (count($silenceupdates) > 0))
	{
		if (in_array($proles,$silenceupdates))
		{
			echo "checked";
		}
	} 	
}