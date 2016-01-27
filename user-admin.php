<?php
class ERP_cyber_previewer{

	public function __construct() {
	
	add_action('admin_menu', array( $this, 'cyber_previewer_register' ));
	
	//add_action('init', array( $this, 'export_csv' ));
	//add_action('init', array( $this, 'bulk_action' ));
	//add_action('init', array( $this,'ERP_script_ui') );
	add_action('init', array( $this,'editform_function') );
    add_action( 'admin_init', array( $this,'ERP_cyber_script_admin') );
	add_action('admin_init', array( $this,'add_new_function') );
		}



	public function ERP_cyber_script_admin(){
		//wp_register_script( 'bootstrap.min', plugins_url( '/js/bootstrap.min.js', __FILE__ ), array( 'jquery' ) );
		//wp_register_style( 'bootstrap.min', plugins_url( '/css/bootstrap.min.css', __FILE__ ) );
		wp_register_script( 'jQuery.print', get_template_directory_uri() . '/jQuery.print.js', false, '1.0.0', true );
		wp_enqueue_script('jQuery.print');
		}
	






// register admin 

public function cyber_previewer_register() {

$page_hook_suffix = add_menu_page('Lead Manager', 'Lead Manager', 'manage_options', 'lock_user', array( $this, 'cyber_preview_page' ));

add_action('admin_print_scripts-' . $page_hook_suffix, array($this,'bootstrap_for_cyber'));


}


public function bootstrap_for_cyber(){
	wp_enqueue_script( 'bootstrap.min' );
	wp_enqueue_style( 'bootstrap.min' );
	wp_enqueue_style( 'jQuery.print' );
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('ERP_admin',plugins_url( '/js/ERP_admin.js' , __FILE__ ),array( 'jquery' ));
	wp_enqueue_style('jquery-ui-datepicker',plugins_url( '/css/jquery-ui-datepicker.css' , __FILE__ ));
	
	}





public function export_csv(){

if($_REQUEST['submit_export']) {

if($_REQUEST['export']=='Excel'){

global $wpdb;
require 'export.inc.php';
$table= $wpdb->prefix . "EPR_user"; // this is the tablename that you want to export to csv from mysql.

exportMysqlToCsv($table);

	}

 }

 

}



public function bulk_action(){
    if ( isset($_REQUEST['delete-subscribers']) ) {
        if ( $_REQUEST['action'] == 'delete' && !empty($_REQUEST['subscribers']) ) {
            global $wpdb;
			$ERP_user = $wpdb->prefix . "EPR_user";
            $wpdb->query('DELETE FROM ' . $ERP_user . ' WHERE ID in (' . implode(',', $_REQUEST['subscribers']) . ')');
            
            wp_redirect( admin_url('admin.php?page=lock_user&deleted=1') );
            exit;
        }
        
        wp_redirect( admin_url('admin.php?page=lock_user') );
        exit;
    }
	
	
	}



public function date_filter(){


}



public function editform_function(){
	if(isset($_REQUEST['submit_e'])){
		global $wpdb;
			$ERP_user = $wpdb->prefix . "EPR_user";
				if(isset($_REQUEST['email_e'])){
				$email = stripslashes($_REQUEST['email_e']);
				}

				if(isset($_REQUEST['activity'])){
				$active = stripslashes($_REQUEST['activity']);
				}
				


				if(isset($_REQUEST['expire'])){
				$expire = stripslashes($_REQUEST['expire']);
				}

			//echo $_REQUEST['email_e'];
			

				
            $wpdb->update($ERP_user, array( 'user_email' => $email, 'user_active' => $active, 'user_expire_date' => $expire   ), array( 'ID' => $_REQUEST['id'] ));
		
		}
	}




public function add_new_function(){
	if(isset($_REQUEST['submit_new'])){
			global $wpdb;
			$table_name = $wpdb->prefix . "EPR_user";
				if(isset($_REQUEST['email_n'])){
				$email = stripslashes($_REQUEST['email_n']);
				}

				if(isset($_REQUEST['pass'])){
				$pass = stripslashes($_REQUEST['pass']);
				}



				if(isset($_REQUEST['activity'])){
				$active = stripslashes($_REQUEST['activity']);
				}


				if(isset($_REQUEST['expire'])){
				$expire = stripslashes($_REQUEST['expire']);
				}



			

			$query = "SELECT COUNT(1) FROM ".$table_name." where user_email LIKE '%$email%' ";	
			$users = $wpdb->get_var($query);		
			
			if($users[0] >= 1){
            wp_redirect( admin_url('admin.php?page=lock_user&userfound=1') );
            exit;
			}else{
            $wpdb->insert($table_name, array( 'user_email' => $email, 'user_pass' => $pass, 'user_login' => $email, 'user_active' => $active, 'user_expire_date' => $expire ), array('%s','%s','%s','%s','%s' ));
            wp_redirect( admin_url('admin.php?page=lock_user&newuser=1') );
            exit;
				
			}
		
		}
	}







public function Add_new_button_pop_up(){
		$output = '<span class="btn btn-danger" data-toggle="modal" data-target="#myModal" style="margin-bottom:5px; padding-bottom:4px; padding-top:4px;">Add New User</span>' . "\n";
			$output .= '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' . "\n";
			
			$output .= '<div class="modal-dialog">' . "\n";
			$output .= '<div class="modal-content">' . "\n";
			$output .= '<div class="modal-header">' . "\n";
			$output .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' . "\n";
			$output .= '<h4 class="modal-title" id="myModalLabel">Edit User Information</h4>' . "\n";
			$output .= '</div>' . "\n";
			//$output .= '<form action="" method="post">' . "\n";
			$output .= '<div class="modal-body">' . "\n";
			$output .= '<div class="form-group">' . "\n";
			$output .= '<input type="email" class="form-control" id="exampleInputEmail2" name="email_n" value="" placeholder="Email address">' . "\n";
			$output .= '</div>' . "\n";
			$output .= '<div class="form-group">' . "\n";
			$output .= '<input type="text" class="form-control" id="exampleInputEmail3" name="pass" value="" placeholder="Password">' . "\n";
			$output .= '</div>' . "\n";
			$output .= '<div class="form-group">' . "\n";
			$output .= '<select class="form-control" id="exampleInputPassword2" name="activity" style="width:100%;">';
			$output .= '<option value="" selected>User Activity</option>';
			$output .= '<option value="0" >YES</option>';
			$output .= '<option value="1" >NO</option>';
			$output .= '</select>';
			//$output .= '<input type="text"  value="'.$activity.'" placeholder="'.$activity.'">' . "\n";
			$output .= '</div>' . "\n";

			$output .= '<div class="form-group">' . "\n";
			$output .= '<input type="text" class="form-control" id="erp_date" name="expire" value="" placeholder="User Expire Date">' . "\n";
			//$output .= '<span></span>' . "\n";
			$output .= '</div>' . "\n";
			$output .= '</div>' . "\n";
			$output .= '<div class="modal-footer">' . "\n";
			$output .= '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' . "\n";
			$output .= '<input type="submit" class="btn btn-primary" value="Add New User" name="submit_new">' . "\n";
			$output .=  '</div>' . "\n";
			//$output .=  '</form>' . "\n";
			$output .= '</div>' . "\n";
			$output .= '</div>' . "\n";
			$output .= '</div>' . "\n";
			
			return $output;
	}









public function edit_button_pop_up($id, $email, $activity, $expire){
		$output = '<span class="btn btn-primary" data-toggle="modal" data-target="#myModal_'.$id.'" style="float:right; padding-bottom:4px; padding-top:4px;">Edit</span>' . "\n";
			$output .= '<div class="modal fade" id="myModal_'.$id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' . "\n";
			
			$output .= '<div class="modal-dialog">' . "\n";
			$output .= '<div class="modal-content">' . "\n";
			$output .= '<div class="modal-header">' . "\n";
			$output .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' . "\n";
			$output .= '<h4 class="modal-title" id="myModalLabel">Edit User Information</h4>' . "\n";
			$output .= '</div>' . "\n";
			$output .= '<form action="" method="post">' . "\n";
			$output .= '<div class="modal-body">' . "\n";
			$output .= '<div class="form-group">' . "\n";
			$output .= '<label for="exampleInputEmail2">Email address</label>' . "\n";
			$output .= '<input type="email" class="form-control" id="exampleInputEmail2" name="email_e" value="'.$email.'" placeholder="'.$email.'">' . "\n";
			$output .= '</div>' . "\n";
			$output .= '<div class="form-group">' . "\n";
			$output .= '<label for="exampleInputPassword2">User Activity</label>' . "\n";
			$output .= '<select class="form-control" id="exampleInputPassword2" name="activity">';
			$output .= '<option value="0"';
			if($activity==0)
			$output .= 'selected';
			$output .= '>YES</option>';
			$output .= '<option value="1"';
			if($activity==1)
			$output .= 'selected';						
			$output .= '>NO</option>';
			$output .= '</select>';
			//$output .= '<input type="text"  value="'.$activity.'" placeholder="'.$activity.'">' . "\n";
			$output .= '</div>' . "\n";

			$output .= '<div class="form-group">' . "\n";
			$output .= '<label for="exampleInputPassword2">User Expire Date</label>' . "\n";
			$output .= '<input type="text" class="form-control" id="erp_date_'.$id.'" name="expire" value="'.$expire.'" placeholder="'.$expire.'">' . "\n";
			$output .= '</div>' . "\n";
			$output .= '<input type="hidden" name="id" value="'.$id.'" >' . "\n";
			$output .= '</div>' . "\n";
			$output .= '<div class="modal-footer">' . "\n";
			$output .= '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' . "\n";
			$output .= '<input type="submit" class="btn btn-primary" value="Save changes" name="submit_e">' . "\n";
			$output .=  '</div>' . "\n";
			$output .=  '</form>' . "\n";
			$output .= '</div>' . "\n";
			$output .= '</div>' . "\n";
			$output .= '</div>' . "\n";

			$output .= '<script type="text/javascript">' . "\n";
			$output .= 'jQuery(document).ready(function(){' . "\n";
			$output .= 'jQuery("#erp_date_'.$id.'").datepicker({' . "\n";
			$output .= 'dateFormat : "yy-mm-dd"' . "\n";
			$output .= '});' . "\n";
			$output .= '});' . "\n";
			$output .= '</script>' . "\n";
			
			
			
			return $output;
	}



public function user_active($user_activity){
		if($user_activity=='0'){
		$user_activity = 'Yes';
		}elseif($user_activity=='1'){
		$user_activity = 'No';
		}
		
		return $user_activity;
		
	}




public function cyber_preview_page() {

?>

<?php if($_GET['lock_user_id']){ 
global $wpdb;
$ERP_user = $wpdb->prefix . "EPR_user";

		
$single_user = $wpdb->get_row("SELECT * FROM ".$ERP_user." WHERE ID=".$_GET['lock_user_id']."" );

?>
<div class="wrap">

<a style="float:right;" href="http://<?php echo $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI] ; ?>&print=yes" target="_blank" >Print page</a><br />
<a style="float:right;" href="/wp-admin/admin.php?page=lock_user" >Go Back To Lead Manager</a>
<h2 style="text-transform:capitalize; text-align:center; margin-bottom:20px;">Print Lead - <?php echo $single_user->fname .' '. $single_user->lname .' '. $single_user->phone; ?></h2>
    <table class="widefat" id="print_area" style="max-width:700px; margin:0 auto;">
        <tbody>
        <?php 
		$result = '';
		if($single_user->fname){
		$result .= '<tr><td>' . "\n";
		$result .= 'First Name:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->fname . "\n";
		$result .= '</td></tr>' . "\n";
		}
		
		if($single_user->lname){
		$result .= '<tr><td>' . "\n";
		$result .= 'Last Name:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->lname . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->email){
		$result .= '<tr><td>' . "\n";
		$result .= 'Email Address:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->email . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->staddress){
		$result .= '<tr><td>' . "\n";
		$result .= 'Street Address:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->staddress . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->city){
		$result .= '<tr><td>' . "\n";
		$result .= 'City:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->city . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->state){
		$result .= '<tr><td>' . "\n";
		$result .= 'State:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->state . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Zip){
		$result .= '<tr><td>' . "\n";
		$result .= 'Zip:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Zip . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->phone){
		$result .= '<tr><td>' . "\n";
		$result .= 'Phone:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->phone . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->eve_phone){
		$result .= '<tr><td>' . "\n";
		$result .= 'Evening/Home Phone:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->eve_phone . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->date_of_birth){
		$result .= '<tr><td>' . "\n";
		$result .= 'Primary Life Applicant:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->date_of_birth . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->medication){
		$result .= '<tr><td>' . "\n";
		$result .= 'Medication 1 - Drug Name:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->medication . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->term_option){
		$result .= '<tr><td>' . "\n";
		$result .= ' Term option:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->term_option . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->term_option){
		$result .= '<tr><td>' . "\n";
		$result .= ' Term option:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->term_option . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->term_coverag_amount){
		$result .= '<tr><td>' . "\n";
		$result .= ' Term Coverage Amount:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->term_coverag_amount . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Exterior_Wall_Type){
		$result .= '<tr><td>' . "\n";
		$result .= ' Exterior Wall Type:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Exterior_Wall_Type . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->property_type){
		$result .= '<tr><td>' . "\n";
		$result .= ' Property Type:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->property_type . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->year_build){
		$result .= '<tr><td>' . "\n";
		$result .= ' Year Built:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->year_build . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->squar_footge){
		$result .= '<tr><td>' . "\n";
		$result .= ' Square Footage?:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->squar_footge . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->property_country){
		$result .= '<tr><td>' . "\n";
		$result .= ' Property County:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->property_country . "\n";
		$result .= '</td></tr>' . "\n";
		}
                                                   		
		if($single_user->swimming_pool){
		$result .= '<tr><td>' . "\n";
		$result .= ' Swimming Pool:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->swimming_pool . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->driver_first_name1){
		$result .= '<tr><td>' . "\n";
		$result .= ' 1st Driver First Name:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->driver_first_name1 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->driver_last_name1){
		$result .= '<tr><td>' . "\n";
		$result .= ' 1st Driver Last Name:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->driver_last_name1 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->driver_birthdate1){
		$result .= '<tr><td>' . "\n";
		$result .= ' 1st Driver Birthdate:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->driver_birthdate1 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->driver_maritual_statues1){
		$result .= '<tr><td>' . "\n";
		$result .= ' 1st Driver Marital Status:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->driver_maritual_statues1 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->driver_1st_violation_description1){
		$result .= '<tr><td>' . "\n";
		$result .= ' 1st Driver - 1st Violation - Description:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->driver_1st_violation_description1 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->driver_first_name2){
		$result .= '<tr><td>' . "\n";
		$result .= ' 2nd Driver First Name:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->driver_first_name2 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->driver_last_name2){
		$result .= '<tr><td>' . "\n";
		$result .= ' 2nd Driver Last Name:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->driver_last_name2 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->driver_birthdate2){
		$result .= '<tr><td>' . "\n";
		$result .= ' 2nd Driver Birthdate:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->driver_birthdate2 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->driver_maritual_statues2){
		$result .= '<tr><td>' . "\n";
		$result .= ' 2nd Driver Marital Status:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->driver_maritual_statues2 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->driver_1nd_violation_description2){
		$result .= '<tr><td>' . "\n";
		$result .= ' 2nd Driver - 1st Violation - Description:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->driver_1nd_violation_description2 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->driver_first_name3){
		$result .= '<tr><td>' . "\n";
		$result .= ' 3rd Driver First Name:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->driver_first_name3 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->driver_last_name3){
		$result .= '<tr><td>' . "\n";
		$result .= ' 3rd Driver Last Name:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->driver_last_name3 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->driver_birthdate3){
		$result .= '<tr><td>' . "\n";
		$result .= ' 3rd Driver Birthdate:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->driver_birthdate3 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->driver_maritual_statues3){
		$result .= '<tr><td>' . "\n";
		$result .= ' 3rd Driver Marital Status:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->driver_maritual_statues3 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->driver_1nd_violation_description3){
		$result .= '<tr><td>' . "\n";
		$result .= ' 3rd Driver - 1st Violation - Description:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->driver_1nd_violation_description3 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Vehicle_year1){
		$result .= '<tr><td>' . "\n";
		$result .= ' 1st Vehicle Year:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Vehicle_year1 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Vehicle_Make1){
		$result .= '<tr><td>' . "\n";
		$result .= ' 1st Vehicle Make:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Vehicle_Make1 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Vehicle_Model1){
		$result .= '<tr><td>' . "\n";
		$result .= ' 1st Vehicle Model:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Vehicle_Model1 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Vehicle_Deductible1){
		$result .= '<tr><td>' . "\n";
		$result .= ' 1st Vehicle Desired Comprehensive Deductible:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Vehicle_Deductible1 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Vehicle_year2){
		$result .= '<tr><td>' . "\n";
		$result .= ' 2nd Vehicle Year:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Vehicle_year2 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Vehicle_Make2){
		$result .= '<tr><td>' . "\n";
		$result .= ' 2nd Vehicle Make:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Vehicle_Make2 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Vehicle_Model2){
		$result .= '<tr><td>' . "\n";
		$result .= ' 2nd Vehicle Model:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Vehicle_Model2 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Vehicle_Deductible2){
		$result .= '<tr><td>' . "\n";
		$result .= ' 2nd Vehicle Desired Comprehensive Deductible:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Vehicle_Deductible2 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Vehicle_year3){
		$result .= '<tr><td>' . "\n";
		$result .= ' 3nd Vehicle Year:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Vehicle_year3 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Vehicle_Make3){
		$result .= '<tr><td>' . "\n";
		$result .= ' 3nd Vehicle Make:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Vehicle_Make3 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Vehicle_Model3){
		$result .= '<tr><td>' . "\n";
		$result .= ' 3nd Vehicle Model:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Vehicle_Model3 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Vehicle_Deductible3){
		$result .= '<tr><td>' . "\n";
		$result .= ' 3nd Vehicle Desired Comprehensive Deductible:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Vehicle_Deductible3 . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->What_are_your_current_liability_limits){
		$result .= '<tr><td>' . "\n";
		$result .= ' What are your current liability limits?:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->What_are_your_current_liability_limits . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Current_Resident_Status){
		$result .= '<tr><td>' . "\n";
		$result .= ' Current Resident Status:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Current_Resident_Status . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Requested_Policy_Start_Date){
		$result .= '<tr><td>' . "\n";
		$result .= ' Requested Policy Start Date:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Requested_Policy_Start_Date . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Design_Type){
		$result .= '<tr><td>' . "\n";
		$result .= ' Design Type:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Design_Type . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Square_Footage){
		$result .= '<tr><td>' . "\n";
		$result .= ' Square Footage:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Square_Footage . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Number_of_Stories){
		$result .= '<tr><td>' . "\n";
		$result .= ' Number of Stories:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Number_of_Stories . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Foundation_Type){
		$result .= '<tr><td>' . "\n";
		$result .= ' Foundation Type:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Foundation_Type . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Roof_Type){
		$result .= '<tr><td>' . "\n";
		$result .= ' Roof Type:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Roof_Type . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Property_Extras){
		$result .= '<tr><td>' . "\n";
		$result .= ' Property Extras:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Property_Extras . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Heating_Type){
		$result .= '<tr><td>' . "\n";
		$result .= ' Heating Type:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Heating_Type . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Garage_Type){
		$result .= '<tr><td>' . "\n";
		$result .= ' Garage Type:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Garage_Type . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Bathrooms){
		$result .= '<tr><td>' . "\n";
		$result .= ' Bathrooms:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Bathrooms . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Additional_Comments){
		$result .= '<tr><td>' . "\n";
		$result .= ' Additional Comments:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Additional_Comments . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->Promotion_Code){
		$result .= '<tr><td>' . "\n";
		$result .= ' Promotion Code:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->Promotion_Code . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->dogs){
		$result .= '<tr><td>' . "\n";
		$result .= ' If you have any dogs please list breed below:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->dogs . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->hear_about_us){
		$result .= '<tr><td>' . "\n";
		$result .= ' How did you hear about us:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->hear_about_us . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->method_to_contuct){
		$result .= '<tr><td>' . "\n";
		$result .= ' Please select Method by which you wish to be contacted:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->method_to_contuct . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->insurance_experience){
		$result .= '<tr><td>' . "\n";
		$result .= ' In your insurance experience with us, what could we have done differently to make your experience better?:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->insurance_experience . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->think_did_best){
		$result .= '<tr><td>' . "\n";
		$result .= ' What do you think we did Best?:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->think_did_best . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->vote_counts){
		$result .= '<tr><td>' . "\n";
		$result .= ' Your vote counts, which agent would you nominate for Employee of the year?:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->vote_counts . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->like_a_quote){
		$result .= '<tr><td>' . "\n";
		$result .= ' We Pride ourselves in giving you our valued customer the very best service. We will provide you with the best insurance at the lowest price possible. In the up coming year what would you like a quote on?:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->like_a_quote . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->date_lead){
		$result .= '<tr><td>' . "\n";
		$result .= ' Date:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->date_lead . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->help){
		$result .= '<tr><td>' . "\n";
		$result .= ' Help:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->help . "\n";
		$result .= '</td></tr>' . "\n";
		}
		if($single_user->massage){
		$result .= '<tr><td>' . "\n";
		$result .= ' Massage:' . "\n";
		$result .= '</td><td>' . "\n";
		$result .= $single_user->massage . "\n";
		$result .= '</td></tr>' . "\n";
		}
                                                   		
		
		
		
		echo $result;
		
		 ?>
        
        </tbody>
    </table>

</div>
<?php if($_GET['print']): ?>
<script>
jQuery(document).ready(function($) {

$(".wrap").print({
    addGlobalStyles : true,
    stylesheet : null,
    rejectWindow : true,
    noPrintSelector : ".no-print",
    iframe : true,
    append : null,
    prepend : null
});
});
</script>

<?php endif; ?>





</div>
<?php }else{ ?>


<div class="wrap">

<div id="icon-users" class="icon32"></div>

<h2>Lead Manager <!--<a href="#" class="add-new-h2">Add New</a>--><?php echo $_GET['subscribers']; ?></h2>
        <form method="post" action="" id="subscribers-filter">
        <div class="tablenav top">
            <!--<div class="alignleft actions">
                <select name="action">
                    <option value="" selected="selected">Bulk Actions</option>
                    <option value="delete">Delete</option>
                </select>
                <input type="submit" value="Apply" class="button-secondary action" id="doaction" name="delete-subscribers">
                
                
            </div>-->
<!--            <div class="alignleft actions">
				From <input type="text" value="" name="startdate" class="cmb_datepicker" placeholder="Start Date" />
                To <input type="text" value="" name="enddate" class="cmb_datepicker" placeholder="End Date" />  
                <input type="submit" value="Search" name="submit_search" />
           
			</div>            
-->
            <!--<div class="alignleft actions">
                 <select name="export">
                    <option value="Excel">Excel Subscriber Query</option>
                </select>
                <input type="submit" value="Export" name="submit_export" />
           
			</div>-->   
            
            
            <div class="alignright">
            <?php // echo $this->Add_new_button_pop_up(); ?>
            </div>
                     
            
        </div>
        

        <?php if ( $_REQUEST['deleted'] == '1' ): ?>
        <div class="updated below-h2" id="message">
            <p>Removed subscribers.</p>
        </div>
        <?php endif; ?>
        
        <?php if ( $_REQUEST['userfound'] == '1' ): ?>
        <div class="updated below-h2" id="message">
            <p>User is already exist.</p>
        </div>
        <?php endif; ?>
        

        <?php if ( $_REQUEST['newuser'] == '1' ): ?>
        <div class="updated below-h2" id="message">
            <p>New User Added.</p>
        </div>
        <?php endif; ?>




<table class="widefat">

<thead>

    <tr>
     <!--<th class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>-->
        <!--<th>ID</th>-->      
        <th>Name</th>
        <th>Email Address</th>
        <th>Mailing Address</th>
        <th>Phone</th>
        <th>Type</th>
        <th>Date</th>
        <th></th>
    </tr>

</thead>

<tfoot>

    <tr>
     <!--<th class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>-->
       <!-- <th>ID</th>    -->  
        <th>Name</th>
        <th>Email Address</th>
        <th>Mailing Address</th>
        <th>Phone</th>
        <th>Type</th>
        <th>Date</th>
        <th></th>
    </tr>

</tfoot>

<?php

function array_flt($ver){
	$array = explode("-", $ver);
	$output = $array[1].'-'.$array[2].'-'.$array[0];
	return $output;
	}

global $wpdb;
$ERP_user = $wpdb->prefix . "EPR_user";
$pagenum = isset( $_GET['pagenum'] ) ? $_GET['pagenum'] : 1;
$limit = 15;
$offset = ( $pagenum - 1 ) * $limit;
		
$subscrivers = $wpdb->get_results("SELECT * FROM ".$ERP_user." ORDER BY `".$ERP_user."`.`date_lead` DESC LIMIT ".$offset.", ".$limit."" );


//printf($subscrivers);

//$user_ID = get_current_user_id();

$i=0;
$count = 1;
$class = '';

foreach ($subscrivers as $subscriver) { 
		
	$startdate= $_REQUEST['startdate'];	
	$enddate= $_REQUEST['enddate'];	
	//$actual_link = '/wp-admin/admin.php?page=lock_user'.'&';
	$datepayment= substr($subscriver->date_of_payment, 0, -9);
		
		

	$i++;
	echo '<tbody><tr>';
	//echo '<th class="column-cb check-column" scope="row"><input type="checkbox" value="' .$subscriver->ID . '" name="subscribers[]"></th>';
    //echo '<td>' .$i. '</td>' ; 
    echo '<td>' .$subscriver->fname.' '.$subscriver->lname .'</td>';
    echo '<td>' . $subscriver->email. '</td>';
    echo '<td>' .$subscriver->staddress.', '. $subscriver->staddress .', '.$subscriver->state . ' ' .$subscriver->Zip.'</td>';
    echo '<td>'.$subscriver->phone.'</td>';
    echo '<td>'.$subscriver->type.'</td>';
    echo '<td>'.array_flt($subscriver->date_lead).'</td>';
    echo '<td><a href="/wp-admin/admin.php?page=lock_user&lock_user_id=' . $subscriver->ID.'">View All</a></td>';
	echo '</tr></tbody>';
	
	
	$count++;
	
}

 ?>

</table>

        </form>



<?php
global $wpdb;
$ERP_user = $wpdb->prefix . "EPR_user";

$total = $wpdb->get_var( "SELECT COUNT(`ID`) FROM ".$ERP_user."" );
$num_of_pages = ceil( $total / $limit );

//echo "<a href='/wp-admin/admin.php?page=lock_user&pagenum=1'>".'|<'."</a> ";
echo "<div class='tablenav'>";
echo "<div class='tablenav-pages' style='margin: 1em 0'>";

for ($i=1; $i<=$num_of_pages; $i++) { 
            echo "<a class='page-numbers ";
			if($i==$_GET['pagenum']){ echo' hover_section';}
			echo "' href='/wp-admin/admin.php?page=lock_user&pagenum=".$i."'>".$i."</a> "; 
}; 
//echo "<a href='/wp-admin/admin.php?page=lock_user&pagenum=".$num_of_pages."'>".'>|'."</a> "; // Goto last page
echo "</div>";
echo "</div>";
?>

<style>
.page-numbers.hover_section { color: #fff;background: #2ea2cc !important;}
</style>
</div>

<?php } ?>

<?php

}



}



$ERP_cyber_previewer= new ERP_cyber_previewer();
?>