<?php


function redirect_to($new_location)
{

	header("Location: " . $new_location);
	exit;
}

function check_login($user, $pass)
{

	$pass = base64_encode($pass);
	$sql = "select * from tbl_users where user_name='$user' and password='$pass' ";


	$res = mysqli_query($GLOBALS['con'], $sql);
	//$res2 =mysql_num_rows($res);
	return $res;
}


function get_count_by_sql($sql)
{

	$res = mysqli_query($GLOBALS['con'], $sql);
	$row = mysqli_fetch_array($res);
	return $svc_name = $row["count(*)"];
}
function get_value_by_sql($sql)
{
	$res = mysqli_query($GLOBALS['con'], $sql);

	if (!$res) {
		// Handle query error
		return null;
	}

	// Fetch the result into an array
	$row = mysqli_fetch_array($res);

	// Return the first element as a float (for sums) or as a string (for general values)
	return isset($row[0]) ? $row[0] : 0;
}
function get_payable_price_sum()
{
    $today = date("Y-m-d"); // Get today's date
    $sql = "SELECT COUNT(*) AS total_count FROM sale_info ";
    return get_value_by_sql($sql);
}


function array_push_assoc($array, $key, $value)
{
	$array[$key] = $value;
	return $array;
}




function get_all_category()
{
	$sql = "select id,cat_name from category order by cat_name ASC";

	$res = mysqli_query($GLOBALS['con'], $sql);
	return $res;
}



function get_all_company()
{
	$sql = "select * from company order by company_name ASC";

	$res = mysqli_query($GLOBALS['con'], $sql);
	return $res;
}


function get_all_home_category()
{
	$sql = "select id,home_cat_name from home_category order by home_cat_name ASC";

	$res = mysqli_query($GLOBALS['con'], $sql);
	return $res;
}
function get_all_price()
{
	$sql = "select id,price,price_name from price order by price ASC";

	$res = mysqli_query($GLOBALS['con'], $sql);
	return $res;
}

function get_price_by_id($id)
{

	$sql = "select price from price where id= '$id' limit 1";


	$res = mysqli_query($GLOBALS['con'], $sql);
	$row = mysqli_fetch_array($res);
	return $name = $row["price"];
}


function get_all_subkeyword()
{
	$sql = "select id,sub_keyword from subkeyword order by sub_keyword ASC";

	$res = mysqli_query($GLOBALS['con'], $sql);
	return $res;
}




function get_all_user_role()
{
	$sql = "select * from tbl_user_role where core_id=0 order by id ASC";

	$res = mysqli_query($GLOBALS['con'], $sql);
	return $res;
}



function get_all_menus_by_role_id($id)
{


	$sql = "SELECT DISTINCT m.menu_id,m.menu_name,m.icon_class,m.notification FROM tbl_permission p  INNER join tbl_menu m on m.menu_id = p.menu_id WHERE p.role_id='$id' and m.status =1 order by m.ordering asc";

	$res = mysqli_query($GLOBALS['con'], $sql);

	//echo $sql;
	return $res;
}


function get_all_sub_menus_by_role_id($id)
{


	$sql = "SELECT DISTINCT sm.sub_menu_id,sm.sub_menu_name,sm.notification FROM tbl_permission p  INNER join tbl_sub_menu sm on sm.sub_menu_id = p.sub_menu_id WHERE p.role_id='$id' and sm.status =1 ";

	$res = mysqli_query($GLOBALS['con'], $sql);

	//echo $sql;
	return $res;
}

function get_all_menus()
{


	$sql = "SELECT *  FROM tbl_menu m  WHERE m.status =1 ";

	$res = mysqli_query($GLOBALS['con'], $sql);

	//echo $sql;
	return $res;
}

//========== 19-01-2021

function get_menu_name_by_id($id)
{

	$sql = "select menu_name from tbl_menu where menu_id= '$id' limit 1";


	$res = mysqli_query($GLOBALS['con'], $sql);
	$row = mysqli_fetch_array($res);
	return $name = $row["menu_name"];
}




//============
function get_all_sub_menus_by_menu_id($id)
{


	$sql = "SELECT *  FROM tbl_sub_menu sm  WHERE sm.status =1  and menu_id='$id' order by  sm.ordering asc ";

	$res = mysqli_query($GLOBALS['con'], $sql);

	//echo $sql;
	return $res;
}

function get_all_sub_menus_by_menu_id_role_id($menu_id, $role_id)
{

	$sql = "SELECT * FROM tbl_permission p 
inner join tbl_sub_menu sm on sm.sub_menu_id = p.sub_menu_id

WHERE p.role_id ='$role_id' and p.menu_id ='$menu_id' order by sm.ordering asc ";

	$res = mysqli_query($GLOBALS['con'], $sql);

	//echo $sql;

	return $res;
}

function get_menus_with_permisson_by_role_id($id)
{


	$sql = "SELECT DISTINCT m.menu_id,m.menu_name FROM tbl_menu m left JOIN tbl_permission p on p.menu_id=m.menu_id where p.role_id='$id' ";

	$res = mysqli_query($GLOBALS['con'], $sql);

	//echo $sql;
	return $res;
}

function check_permission_with_url_role_id($url, $role_id)
{


	$sql = "SELECT * FROM tbl_sub_menu sm 
inner join tbl_permission p on sm.sub_menu_id = p.sub_menu_id

WHERE  page_url like '%$url%' and p.role_id='$role_id'

GROUP by sub_menu_name ";

	$res = mysqli_query($GLOBALS['con'], $sql);

	//return $sql;
	//die;
	return mysqli_num_rows($res);
}

function update_to_seen_by_id($contentId)
{

	$sql = "Update applications set flag='seen' where id ='$contentId' limit 1 ;";

	$res = mysqli_query($GLOBALS['con'], $sql);


	return $res;
}



function get_fetched_doctor_details_data_by_doc_id($id)
{

	$id = mysqli_real_escape_string($GLOBALS['con'], $id);
	$sql = "select * from tbl_doctor d
	 where d.DOCID='$id'  limit 1 ";

	$res = mysqli_query($GLOBALS['con'], $sql);

	$row = mysqli_fetch_assoc($res);
	return $row;
}



function get_fetched_patient_details_data_by_p_id($id)
{

	$id = mysqli_real_escape_string($GLOBALS['con'], $id);
	$sql = "SELECT p.*, pf.PFile,pf.fileName FROM `tbl_patient` p 
	left join  tbl_patientfile pf on pf.PatientID= p.OID where p.OID='$id' limit 1 ";

	$res = mysqli_query($GLOBALS['con'], $sql);

	$row = mysqli_fetch_assoc($res);
	return $row;
}

function get_prescriptions_by_p_id($id)
{

	$id = mysqli_real_escape_string($GLOBALS['con'], $id);
	$sql = "SELECT * FROM `tbl_prescriptionfile`  p  where p.PatientID='$id'  ";

	$res = mysqli_query($GLOBALS['con'], $sql);

	return $res;
}
function get_patientreports_by_p_id($id)
{

	$id = mysqli_real_escape_string($GLOBALS['con'], $id);
	$sql = "SELECT * FROM `tbl_patientreport`  p  where p.PatientID='$id'  ";

	$res = mysqli_query($GLOBALS['con'], $sql);

	return $res;
}
function get_appointments_by_p_id($id)
{

	$id = mysqli_real_escape_string($GLOBALS['con'], $id);
	$sql = "SELECT * FROM `appointmentview`   ap  where ap.PatientID='$id'  ";

	$res = mysqli_query($GLOBALS['con'], $sql);

	return $res;
}



//===================================
function get_all_hospital()
{
	$sql = "select * from tbl_hospital order by HospitalName ASC";

	$res = mysqli_query($GLOBALS['con'], $sql);
	return $res;
}
function get_all_specialist()
{
	$sql = "select * from tbl_specialist order by Specialization ASC";

	$res = mysqli_query($GLOBALS['con'], $sql);
	return $res;
}
function get_times()
{
	$sql = "select * from tbl_times order by time ASC";

	$res = mysqli_query($GLOBALS['con'], $sql);
	return $res;
}
function get_all_reasons()
{
	$sql = "select * from tbl_reason order by reason ASC";

	$res = mysqli_query($GLOBALS['con'], $sql);
	return $res;
}
function get_all_op()
{
	$sql = "select * from tbl_otherprofessional order by Professional ASC";

	$res = mysqli_query($GLOBALS['con'], $sql);
	return $res;
}

function get_all_docType()
{
	$sql = "select * from tbl_doctype order by id ASC";

	$res = mysqli_query($GLOBALS['con'], $sql);
	return $res;
}

function get_doc_specials_by_id($docID)
{

	$sql = "select SpecialArea from tbl_doctor where DOCID='$docID' limit 1 ";

	$res = mysqli_query($GLOBALS['con'], $sql);
	$row = mysqli_fetch_array($res);
	return $name = $row["SpecialArea"];
}

function get_special_details_fetched($ID)
{

	$sql = "select * from tbl_specialist where OID='$ID' limit 1 ";

	$res = mysqli_query($GLOBALS['con'], $sql);
	$row = mysqli_fetch_array($res);
	return $row;
}

function get_opf_details_fetched($ID)
{

	$sql = "select * from tbl_otherprofessional where OID='$ID' limit 1 ";

	$res = mysqli_query($GLOBALS['con'], $sql);
	$row = mysqli_fetch_array($res);
	return $row;
}
