<?php
class ERP_database {
	
	public function __construct(){
    add_action('init', array($this, 'ERP_DB'));
		}
		
		/*Creating a custom table*/
    public function ERP_DB(){
    global $wpdb;
     
    //create the name of the table including the wordpress prefix (wp_ etc)
    $EPR_user = $wpdb->prefix . "EPR_user";
    $EPR_user_meta = $wpdb->prefix . "EPR_user_meta";
    //$wpdb->show_errors();
     
    //check if there are any tables of that name already
    if($wpdb->get_var("show tables like '$EPR_user'") !== $EPR_user)
    {
    //create your sql
    $sql = "CREATE TABLE ". $EPR_user . " (
    ID bigint(20) NOT NULL AUTO_INCREMENT,
    fname VARCHAR (10) NOT NULL,
    lname VARCHAR(10) NOT NULL,
    email VARCHAR(20) NOT NULL,
    staddress VARCHAR(20) NOT NULL,
    city VARCHAR(20) NOT NULL,
    state VARCHAR(20) NOT NULL,
    Zip VARCHAR(20) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    eve_phone VARCHAR(20) NOT NULL,
    date_of_birth VARCHAR(20) NOT NULL,
    medication VARCHAR(20) NOT NULL,
    term_option VARCHAR(20) NOT NULL,
    term_coverag_amount VARCHAR(20) NOT NULL,
    Exterior_Wall_Type VARCHAR(20) NOT NULL,
    property_type VARCHAR(20) NOT NULL,
    year_build VARCHAR(20) NOT NULL,
    squar_footge VARCHAR(20) NOT NULL,
    property_country VARCHAR(20) NOT NULL,
    swimming_pool VARCHAR(20) NOT NULL,
    driver_first_name1 VARCHAR(20) NOT NULL,
    driver_last_name1 VARCHAR(20) NOT NULL,
    driver_birthdate1 VARCHAR(20) NOT NULL,
    driver_maritual_statues1 VARCHAR(20) NOT NULL,
    driver_1st_violation_description1 VARCHAR(20) NOT NULL,
    driver_first_name2 VARCHAR(20) NOT NULL,
    driver_last_name2 VARCHAR(20) NOT NULL,
    driver_birthdate2 VARCHAR(20) NOT NULL,
    driver_maritual_statues2 VARCHAR(20) NOT NULL,
    driver_1nd_violation_description2 VARCHAR(20) NOT NULL,
    driver_first_name3 VARCHAR(20) NOT NULL,
    driver_last_name3 VARCHAR(20) NOT NULL,
    driver_birthdate3 VARCHAR(20) NOT NULL,
    driver_maritual_statues3 VARCHAR(20) NOT NULL,
    driver_1nd_violation_description3 VARCHAR(20) NOT NULL,
    Vehicle_year1 VARCHAR(20) NOT NULL,
    Vehicle_Make1 VARCHAR(20) NOT NULL,
    Vehicle_Model1 VARCHAR(20) NOT NULL,
    Vehicle_Deductible1 VARCHAR(20) NOT NULL,
    Vehicle_year2 VARCHAR(20) NOT NULL,
    Vehicle_Make2 VARCHAR(20) NOT NULL,
    Vehicle_Model2 VARCHAR(20) NOT NULL,
    Vehicle_Deductible2 VARCHAR(20) NOT NULL,
    Vehicle_year3 VARCHAR(20) NOT NULL,
    Vehicle_Make3 VARCHAR(20) NOT NULL,
    Vehicle_Model3 VARCHAR(20) NOT NULL,
    Vehicle_Deductible3 VARCHAR(20) NOT NULL,
    What_are_your_current_liability_limits VARCHAR(20) NOT NULL,
    Current_Resident_Status VARCHAR(20) NOT NULL,
    Requested_Policy_Start_Date VARCHAR(20) NOT NULL,
    Design_Type VARCHAR(20) NOT NULL,
    Square_Footage VARCHAR(20) NOT NULL,
    Number_of_Stories VARCHAR(20) NOT NULL,
    Foundation_Type VARCHAR(20) NOT NULL,
    Roof_Type VARCHAR(20) NOT NULL,
    Property_Extras VARCHAR(20) NOT NULL,
    Heating_Type VARCHAR(20) NOT NULL,
    Garage_Type VARCHAR(20) NOT NULL,
    Bathrooms VARCHAR(20) NOT NULL,
    Additional_Comments VARCHAR(20) NOT NULL,
    square_ft VARCHAR(10) NOT NULL,
    Promotion_Code VARCHAR(10) NOT NULL,
    dogs VARCHAR(10) NOT NULL,
    hear_about_us VARCHAR(50) NOT NULL,
    method_to_contuct VARCHAR(50) NOT NULL,
    type VARCHAR(50) NOT NULL,
    UNIQUE KEY ID (ID));";
    }


    if($wpdb->get_var("show tables like '$EPR_user_meta'") !== $EPR_user_meta)
    {
    //create your sql
    $EPR_user_meta_sql = "CREATE TABLE ". $EPR_user_meta . " (
    ID bigint(20) NOT NULL AUTO_INCREMENT,
    fname VARCHAR (10) NOT NULL,
    lname VARCHAR(10) NOT NULL,
    email VARCHAR(20) NOT NULL,
    staddress VARCHAR(20) NOT NULL,
    city VARCHAR(20) NOT NULL,
    state VARCHAR(20) NOT NULL,
    Zip VARCHAR(20) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    eve_phone VARCHAR(20) NOT NULL,
    date_of_birth VARCHAR(20) NOT NULL,
    medication VARCHAR(20) NOT NULL,
    term_option VARCHAR(20) NOT NULL,
    term_coverag_amount VARCHAR(20) NOT NULL,
    Exterior_Wall_Type VARCHAR(20) NOT NULL,
    property_type VARCHAR(20) NOT NULL,
    year_build VARCHAR(20) NOT NULL,
    squar_footge VARCHAR(20) NOT NULL,
    property_country VARCHAR(20) NOT NULL,
    swimming_pool VARCHAR(20) NOT NULL,
    1driver_first_name VARCHAR(20) NOT NULL,
    1driver_last_name VARCHAR(20) NOT NULL,
    1st_driver_birthdate VARCHAR(20) NOT NULL,
    1driver_maritual_statues VARCHAR(20) NOT NULL,
    1st_driver_1st_violation_description VARCHAR(20) NOT NULL,
    2driver_first_name VARCHAR(20) NOT NULL,
    2driver_last_name VARCHAR(20) NOT NULL,
    2nd_driver_birthdate VARCHAR(20) NOT NULL,
    2driver_maritual_statues VARCHAR(20) NOT NULL,
    2nd_driver_1nd_violation_description VARCHAR(20) NOT NULL,
    3driver_first_name VARCHAR(20) NOT NULL,
    3driver_last_name VARCHAR(20) NOT NULL,
    3nd_driver_birthdate VARCHAR(20) NOT NULL,
    3driver_maritual_statues VARCHAR(20) NOT NULL,
    3rd_driver_1nd_violation_description VARCHAR(20) NOT NULL,
    1_Vehicle_year VARCHAR(20) NOT NULL,
    1_Vehicle_Make VARCHAR(20) NOT NULL,
    1_Vehicle_Model VARCHAR(20) NOT NULL,
    1_Vehicle_Deductible VARCHAR(20) NOT NULL,
    2_Vehicle_year VARCHAR(20) NOT NULL,
    2_Vehicle_Make VARCHAR(20) NOT NULL,
    2_Vehicle_Model VARCHAR(20) NOT NULL,
    2_Vehicle_Deductible VARCHAR(20) NOT NULL,
    3_Vehicle_year VARCHAR(20) NOT NULL,
    3_Vehicle_Make VARCHAR(20) NOT NULL,
    3_Vehicle_Model VARCHAR(20) NOT NULL,
    3_Vehicle_Deductible VARCHAR(20) NOT NULL,
    What_are_your_current_liability_limits VARCHAR(20) NOT NULL,
    Current_Resident_Status VARCHAR(20) NOT NULL,
    Requested_Policy_Start_Date VARCHAR(20) NOT NULL,
    Design_Type VARCHAR(20) NOT NULL,
    Square_Footage VARCHAR(20) NOT NULL,
    Number_of_Stories VARCHAR(20) NOT NULL,
    Foundation_Type VARCHAR(20) NOT NULL,
    Roof_Type VARCHAR(20) NOT NULL,
    Property_Extras VARCHAR(20) NOT NULL,
    Heating_Type VARCHAR(20) NOT NULL,
    Garage_Type VARCHAR(20) NOT NULL,
    Bathrooms VARCHAR(20) NOT NULL,
    Additional_Comments VARCHAR(20) NOT NULL,
    square_ft VARCHAR(10) NOT NULL,
    Promotion_Code VARCHAR(10) NOT NULL,
    dogs VARCHAR(10) NOT NULL,
    hear_about_us VARCHAR(50) NOT NULL,
    method_to_contuct VARCHAR(50) NOT NULL,
    UNIQUE KEY ID (ID));";
    }
     
    //include the wordpress db functions
    require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
    dbDelta($sql);
    dbDelta($EPR_user_meta_sql);
     
    //register the new table with the wpdb object
    if (!isset($wpdb->EPR_user_meta))
    {
    $wpdb->EPR_user_meta = $EPR_user_meta;
    //add the shortcut so you can use $wpdb->EPR_user
    $wpdb->tables[] = str_replace($wpdb->prefix, '', $EPR_user_meta);
    }
	
    if (!isset($wpdb->EPR_user))
    {
    $wpdb->EPR_user = $EPR_user;
    //add the shortcut so you can use $wpdb->EPR_user
    $wpdb->tables[] = str_replace($wpdb->prefix, '', $EPR_user);
    }
}
     

}
$ERP_database = new ERP_database();