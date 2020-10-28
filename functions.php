<?php

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ) );
	wp_enqueue_style( 'bootstrap-style' , 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css' );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
//add_filter('widget_text', 'do_shortcode');

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

function connectDB(){
	$dbname = "arissto_testsite";
	$username = "arissto_testsite";
	$password = "*5j5Uil5";
	return new wpdb( $username, $password, $dbname, "localhost" );	
}

add_shortcode('ratingInfo','ratingInfo');
function ratingInfo(){
	if ( is_user_logged_in() ) {
		$db = connectDB();
		
		$username = wp_get_current_user()->user_login;
		
		$sql = "SELECT Sales_Partner_Name, Sales_Partner_Code, Sales_Partner_Contact FROM ct_myaccount_flutter WHERE Email = '".$username."'";
		$result = $db->get_results($sql);
		foreach($result as $val){
			$CAname = $val->Sales_Partner_Name;
			$CAcode = $val->Sales_Partner_Code;
			$CAcontact = $val->Sales_Partner_Contact;
		}
		//$name = "SITI BIN MOHAMAD ALI BIN HARISSA";
		//$code = "AR-XXXXX1";
		//$contact = "012 XXX XXXX";
		$status = "Active";

		return '<div class="info"><font>
				Dealer Name: &ensp;'.$CAname.'<br/>
				Dealer Code: &ensp;&nbsp;'.$CAcode.'<br/>
				Contact No: &emsp;&nbsp;'.$CAcontact.'<br/>
				Dealer Status: &nbsp;'.$status.'</font></div>';
		}
}

//executes for users that are logged in.
add_action ( 'wp_ajax_submitRate', 'submitRate' );
function submitRate($atts){
	$data = shortcode_atts( array(
		'rate' => $_REQUEST['rate'],
		'comment' => $_REQUEST['comment'],
		//'name' => $_REQUEST['CAname'],
		//'code' => $_REQUEST['CAcode'],
	), $atts );
	
	$dbname = "arissto_testsite";
	$username = "arissto_testsite";
	$password = "*5j5Uil5";
	$db = new wpdb( $username, $password, $dbname, "localhost" );
	
	$username = wp_get_current_user()->user_login;
	
	$query = "SELECT Membership_No, Name, Sales_Partner_Name, Sales_Partner_Code FROM ct_myaccount_flutter WHERE Email = '".$username."'";
	$results = $db->get_results($query);
	foreach($results as $val){
		$membershipNo = $val->Membership_No;
		$name = $val->Name;
		$email = $username;
		$CAname = $val->Sales_Partner_Name;
		$CAcode = $val->Sales_Partner_Code;
	}
	
	$sql = "INSERT INTO user_rating (Membership_No, Username, Email, rating, comment, Sales_Partner_Code, Sales_Partner_Name, date) VALUES ('".$membershipNo."', '".$name."', '".$email."','".$data['rate']."', '".$data['comment']."', '".$CAcode."', '".$CAname."', now())";
	//$sql = "DELETE FROM user_rating WHERE Email = ''";
	$result = $db->get_results($sql);
	
	if(is_null($result)){
		echo "Fail";
	}else{
		//echo "$data['rate']." ".$data['comment']";
		echo "Success";
	}
	
	wp_die();
}

//executes for users that are logged in.
add_action ( 'wp_ajax_changeDealer', 'changeDealer' );
function changeDealer($atts){
	$data = shortcode_atts( array(
		'reason' => $_REQUEST['reason'],
	), $atts );
	
	$dbname = "arissto_testsite";
	$username = "arissto_testsite";
	$password = "*5j5Uil5";
	$db = new wpdb( $username, $password, $dbname, "localhost" );
	
	$username = wp_get_current_user()->user_login;
	
	$query = "SELECT Membership_No, Name, Sales_Partner_Name, Sales_Partner_Code FROM ct_myaccount_flutter WHERE Email = '".$username."'";
	$results = $db->get_results($query);
	foreach($results as $val){
		$membershipNo = $val->Membership_No;
		$name = $val->Name;
		$email = $username;
		$CAname = $val->Sales_Partner_Name;
		$CAcode = $val->Sales_Partner_Code;
	}
	$type = "Change Dealer";
	
	$sql = "INSERT INTO user_request (Membership_No, Username, Email, type, request, Sales_Partner_Code, Sales_Partner_Name, date) VALUES ('".$membershipNo."', '".$name."', '".$email."','".$type."', '".$data['reason']."', '".$CAcode."', '".$CAname."', now())";
	
	$result = $db->get_results($sql);
	
	if(is_null($result)){
		echo "Fail";
	}else{
		//echo "$data['rate']." ".$data['comment']";
		echo "Success";
	}
	
	wp_die();
}

//unnecessary if the jquery url="http://arissto.com/test/wp-admin/admin-ajax.php"
add_action('wp_head', 'make_on_init');
function make_on_init(){ 
  echo "<script>var ajaxhandle = '".admin_url('admin-ajax.php')."';</script>"; 
} 

add_shortcode('displayData','displayData');
function displayData(){
	$dbname = "arissto_testsite";
	$username = "arissto_testsite";
	$password = "*5j5Uil5";
	$db = new wpdb( $username, $password, $dbname, "localhost" );
	
	$query = "SELECT * FROM user_rating";
	$result = $db->get_results($query);
	$count = $db->num_rows;
	foreach($result as $val){
		$name = $val->Sales_Partner_Name;	
	};
	return '<font>'.$count.'</font>';
}

add_shortcode('displayData1','displayData1');
function displayData1(){
	$dbname = "arissto_testsite";
	$username = "arissto_testsite";
	$password = "*5j5Uil5";
	
	$db = new wpdb( $username, $password, $dbname, "localhost" );
	//$newdb->show_errors();
	$query = "SELECT * FROM user_rating";
	$result = $db->get_results($query);
	$count = $db->num_rows;
	foreach($result as $val){
		$name = $val->Sales_Partner_Name;	
	};
	return '<font>Count: '.$count.'</font>';
}

add_shortcode( 'userRating', 'userRating' );
add_action( 'wp_ajax_userRating', 'userRating' );
function userRating(){
	
	$dbname = "arissto_testsite";
	$username = "arissto_testsite";
	$password = "*5j5Uil5";
	$db = new wpdb( $username, $password, $dbname, "localhost" );
	
	$sql = "SELECT * FROM user_rating;";
	$result = $db->get_results($sql);

	foreach($result as $val){
		$membershipNo = $val->Membership_No;
		$username = $val->Username;
		$email = $val->Email;
		$rating = $val->rating;
		$comment = $val->comment;
		$CAname = $val->Sales_Partner_Name;
		$CAcode = $val->Sales_Partner_Code;
		$date = $val->date;

		$array[] = array("Membership_No" => $membershipNo, "Username" => $username, "Email" => $email, "Rating" => $rating, "Comment" => $comment, "Sales_Partner_Name" => $CAname, "Sales_Partner_Code" => $CAcode, "Date" => $date);
	}
	 
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.6">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Datatable</title>
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"/>
	</head>
	<style>
		table, th, td {
			border: 0.5px solid #d9d9d9;
			border-collapse: collapse;
			font-size:15px;
		}
		.dataTables_filter input { height: 30px;margin-bottom: 20px; margin-top:20px;}
		.dataTables_length select{
			height: 30px;
			width: 100px;
			margin-bottom: 20px; margin-top:20px;
		}
	</style>
	<body>
		<table id="userTable" class="stripe" width="100%">
			<thead>
				<th>Membership_No</th>
				<th>Username</th>
				<th>Email</th>
				<th>Rating</th>
				<th>Comment</th>
				<th>Sales_Partner_Name</th>
				<th>Sales_Partner_Code</th>
				<th>Date</th>
			</thead>
			<tbody>
				<?php if(!empty($array)) { 
		foreach($array as $data) { ?>
				<tr>
					<td><?php echo $data['Membership_No']; ?></td>
					<td><?php echo $data['Username']; ?></td>
					<td><?php echo $data['Email']; ?></td>
					<td><?php echo $data['Rating']; ?></td>
					<td><?php echo $data['Comment']; ?></td>
					<td><?php echo $data['Sales_Partner_Name']; ?></td>
					<td><?php echo $data['Sales_Partner_Code']; ?></td>
					<td><?php echo $data['Date']; ?></td>
				</tr>
				<?php } ?>
				<?php } ?>
			</tbody>
		</table>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script type="text/javascript" src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
		
		<script> 
			$(document).ready(function() {
				$('#userTable').DataTable({
					"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
					"iDisplayLength": 25, // to display 25 rows
					"bAutoWidth": true,
					"maxWidth": screen.width,
				});
			});
		</script>
	</body>
</html>
<?php
}

add_shortcode( 'userRequest', 'userRequest' );
add_action( 'wp_ajax_userRequest', 'userRequest' );
function userRequest(){
	
	$dbname = "arissto_testsite";
	$username = "arissto_testsite";
	$password = "*5j5Uil5";
	$db = new wpdb( $username, $password, $dbname, "localhost" );
	
	$sql = "SELECT * FROM user_request;";
	$result = $db->get_results($sql);

	foreach($result as $val){
		$membershipNo = $val->Membership_No;
		$username = $val->Username;
		$email = $val->Email;
		$type = $val->type;
		$reason = $val->request;
		$CAname = $val->Sales_Partner_Name;
		$CAcode = $val->Sales_Partner_Code;
		$date = $val->date;

		$array[] = array("Membership_No" => $membershipNo, "Username" => $username, "Email" => $email, "Type" => $type, "Reason" => $reason, "Sales_Partner_Name" => $CAname, "Sales_Partner_Code" => $CAcode, "Date" => $date);
	}
	 
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.6">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Datatable</title>
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"/>
	</head>
	<style>
		table, th, td {
			border: 0.5px solid #d9d9d9;
			border-collapse: collapse;
			font-size:12px;
		}
		.dataTables_filter input { height: 30px;margin-bottom: 20px; margin-top:20px;}
		.dataTables_length select{
			height: 30px;
			width: 100px;
			margin-bottom: 20px; margin-top:20px;
		}
	</style>
	<body>
		<table id="userTable1" class="stripe" style="width:100%;">
			<thead>
				<th>Membership_No</th>
				<th>Username</th>
				<th>Email</th>
				<th>Type</th>
				<th>Reason</th>
				<th>Sales_Partner_Name</th>
				<th>Sales_Partner_Code</th>
				<th>Date</th>
			</thead>
			<tbody>
				<?php if(!empty($array)) { 
		foreach($array as $data) { ?>
				<tr>
					<td><?php echo $data['Membership_No']; ?></td>
					<td><?php echo $data['Username']; ?></td>
					<td><?php echo $data['Email']; ?></td>
					<td><?php echo $data['Type']; ?></td>
					<td><?php echo $data['Reason']; ?></td>
					<td><?php echo $data['Sales_Partner_Name']; ?></td>
					<td><?php echo $data['Sales_Partner_Code']; ?></td>
					<td><?php echo $data['Date']; ?></td>
				</tr>
				<?php } ?>
				<?php } ?>
			</tbody>
		</table>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script type="text/javascript" src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
		
		<script> 
			$(document).ready(function() {
				$('#userTable1').DataTable({
					"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
					"iDisplayLength": 25, // to display 25 rows
					"bAutoWidth": true,
					"maxWidth": screen.width,
				});
			});
		</script>
	</body>
</html>
<?php
}

add_shortcode( 'myAccount', 'myAccount' );
add_action( 'wp_ajax_myAccount', 'myAccount' );
function myAccount(){
	
	$dbname = "arissto_testsite";
	$username = "arissto_testsite";
	$password = "*5j5Uil5";
	$db = new wpdb( $username, $password, $dbname, "localhost" );
	
	$sql = "SELECT * FROM ct_myaccount_flutter;";
	$result = $db->get_results($sql);

	foreach($result as $val){
		$membershipNo = $val->Membership_No;
		$membershipType = $val->Membership_Type;
		$name = $val->Name;
		$username = $val->Username;
		$email = $val->Email;
		$contact = $val->Contact;
		$m_addr1 = $val->M_Addr1;
		$m_addr2 = $val->M_Addr2;
		$m_addr3 = $val->M_Addr3;
		$m_city = $val->M_City;
		$m_state = $val->M_State;
		$m_postcode = $val->M_Postcode;
		$d_addr1 = $val->D_Addr1;
		$d_addr2 = $val->D_Addr2;
		$d_addr3 = $val->D_Addr3;
		$d_city = $val->D_City;
		$d_state = $val->D_State;
		$d_postcode = $val->D_Postcode;
		$userID = $val->User_ID;
		$CAcode = $val->Sales_Partner_Code;
		$CAname = $val->Sales_Partner_Name;
		$CAcontact = $val->Sales_Partner_Contact;
		$refCode = $val->Ref_iPartner_Code;
		$refName = $val->Ref_iPartner_Name;
	  
		$array[] = array(
			"Membership_No" => $membershipNo, 
			"Membership_Type" => $membershipType, 
			"Name" => $name, 
			"Username" => $username, 
			"Email" => $email,  
			"Contact" => $contact,
			"M_Addr1" => $m_addr1,
			"M_Addr2" => $m_addr2,
			"M_Addr3" => $m_addr3,
			"M_City" => $m_city,
			"M_State" => $m_state,
			"M_Postcode" => $m_postcode,
			"D_Addr1" => $d_addr1,
			"D_Addr2" => $d_addr2,
			"D_Addr3" => $d_addr3,
			"D_City" => $d_city,
			"D_State" => $d_state,
			"D_Postcode" => $d_postcode,
			"User_ID" => $userID,
			"Sales_Partner_Code" => $CAcode,
			"Sales_Partner_Name" => $CAname, 
			"Sales_Partner_Contact" => $CAcontact, 
			"Ref_iPartner_Code" => $refCode,
			"Ref_iPartner_Name" => $refName);
	}
	 
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.6">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Datatable</title>
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"/>
	</head>
	<style>
		table, th, td {
			border: 0.5px solid #d9d9d9;
			border-collapse: collapse;
			font-size:12px;
		}
		.dataTables_filter input { height: 30px;margin-bottom: 20px; margin-top:20px;}
		.dataTables_length select{
			height: 30px;
			width: 100px;
			margin-bottom: 20px; margin-top:20px;
		}
	</style>
	<body>
		<table id="userTable1" class="stripe" style="width:100%;">
			<thead>
				<th>Membership_No</th>
				<th>Membership_Type</th>
				<th>Name</th>
				<th>Username</th>
				<th>Email</th>
				<th>Contact</th>
				<th>M_Addr1</th>
				<th>M_Addr2</th>
				<th>M_Addr3</th>
				<th>M_City</th>
				<th>M_State</th>
				<th>M_Postcode</th>
				<th>D_Addr1</th>
				<th>D_Addr2</th>
				<th>D_Addr3</th>
				<th>D_City</th>
				<th>D_State</th>
				<th>D_Postcode</th>
				<th>User_ID</th>
				<th>Sales_Partner_Code</th>
				<th>Sales_Partner_Name</th>
				<th>Sales_Partner_Contact</th>
				<th>Ref_iPartner_Code</th>
				<th>Ref_iPartner_Name</th>
			</thead>
			<tbody>
				<?php if(!empty($array)) { 
		foreach($array as $data) { ?>
				<tr>
					<td><?php echo $data['Membership_No']; ?></td>
					<td><?php echo $data['Membership_Type']; ?></td>
					<td><?php echo $data['Name']; ?></td>
					<td><?php echo $data['Username']; ?></td>
					<td><?php echo $data['Email']; ?></td>
					<td><?php echo $data['Contact']; ?></td>
					<td><?php echo $data['M_Addr1']; ?></td>
					<td><?php echo $data['M_Addr2']; ?></td>
					<td><?php echo $data['M_Addr3']; ?></td>
					<td><?php echo $data['M_City']; ?></td>
					<td><?php echo $data['M_State']; ?></td>
					<td><?php echo $data['M_Postcode']; ?></td>
					<td><?php echo $data['D_Addr1']; ?></td>
					<td><?php echo $data['D_Addr2']; ?></td>
					<td><?php echo $data['D_Addr3']; ?></td>
					<td><?php echo $data['D_City']; ?></td>
					<td><?php echo $data['D_State']; ?></td>
					<td><?php echo $data['D_Postcode']; ?></td>
					<td><?php echo $data['User_ID']; ?></td>
					<td><?php echo $data['Sales_Partner_Code']; ?></td>
					<td><?php echo $data['Sales_Partner_Name']; ?></td>
					<td><?php echo $data['Sales_Partner_Contact']; ?></td>
					<td><?php echo $data['Ref_iPartner_Code']; ?></td>
					<td><?php echo $data['Ref_iPartner_Name']; ?></td>
				</tr>
				<?php } ?>
				<?php } ?>
			</tbody>
		</table>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script type="text/javascript" src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
		
		<script> 
			$(document).ready(function() {
				$('#userTable1').DataTable({
					"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
					"iDisplayLength": 25, // to display 25 rows
					"bAutoWidth": true,
					"maxWidth": screen.width,
					"scrollX": true
				});
			});
		</script>
	</body>
</html>
<?php
}

add_shortcode( 'myAccountSubs', 'myAccountSubs' );
add_action( 'wp_ajax_myAccountSubs', 'myAccountSubs' );
function myAccountSubs(){
	
	$dbname = "arissto_testsite";
	$username = "arissto_testsite";
	$password = "*5j5Uil5";
	$db = new wpdb( $username, $password, $dbname, "localhost" );
	
	$sql = "SELECT * FROM ct_myaccount_subscription;";
	$result = $db->get_results($sql);

	foreach($result as $val){
		$Membership_No = $val->Membership_No;
		$Document_No = $val->Document_No;
		$PlanCode = $val->PlanCode;
		$Subs_DateFrom = $val->Subs_DateFrom;
		$TotalSubs_Amount = $val->TotalSubs_Amount;
		$Char_Every = $val->Char_Every;
		$Char_Period = $val->Char_Period;
		$Char_Times = $val->Char_Times;
		$Customer_Type = $val->Customer_Type;
		$FormType = $val->FormType;
		$Milk = $val->Milk;
		$Choco = $val->Choco;
		$Peace = $val->Peace;
		$InLove = $val->InLove;
		$Lonely = $val->Lonely;
		$MoonLight = $val->MoonLight;
		$Passion = $val->Passion;
		$Sunrise = $val->Sunrise;
		$Luna = $val->Luna;
		$Amico = $val->Amico;
		$The_King = $val->The_King;
		$The_Queen = $val->The_Queen;
		$Prince = $val->Prince;
		$Princess = $val->Princess;
		$Earl = $val->Earl;
		$Butter_Croissant = $val->Butter_Croissant;
		$Rasp_Butter_Croissant = $val->Rasp_Butter_Croissant;
		$Matcha_Butter_Croissant = $val->Matcha_Butter_Croissant;
		$Charcoal_Butter_Croissant = $val->Charcoal_Butter_Croissant;
		$Focaccia = $val->Focaccia;
		$Golden_Roti = $val->Golden_Roti;
		$CharGold_Roti = $val->CharGold_Roti;
		$Golden_Cake = $val->Golden_Cake;
	  
		$array[] = array(
			"Membership_No" => $Membership_No,
			"Document_No" => $Document_No,
			"PlanCode" => $PlanCode,
			"Subs_DateFrom" => $Subs_DateFrom,
			"TotalSubs_Amount" => $TotalSubs_Amount,
			"Char_Every" => $Char_Every,
			"Char_Period" => $Char_Period,
			"Char_Times" => $Char_Times,
			"Customer_Type" => $Customer_Type,
			"FormType" => $FormType,
			"Milk" => $Milk,
			"Choco" => $Choco,
			"Peace" => $Peace,
			"InLove" => $InLove,
			"Lonely" => $Lonely,
			"MoonLight" => $MoonLight,
			"Passion" => $Passion,
			"Sunrise" => $Sunrise,
			"Luna" => $Luna,
			"Amico" => $Amico,
			"The_King" => $The_King,
			"The_Queen" => $The_Queen,
			"Prince" => $Prince,
			"Princess" => $Princess,
			"Earl" => $Earl,
			"Butter_Croissant" => $Butter_Croissant,
			"Rasp_Butter_Croissant" => $Rasp_Butter_Croissant,
			"Matcha_Butter_Croissant" => $Matcha_Butter_Croissant,
			"Charcoal_Butter_Croissant" => $Charcoal_Butter_Croissant,
			"Focaccia" => $Focaccia,
			"Golden_Roti" => $Golden_Roti,
			"CharGold_Roti" => $CharGold_Roti,
			"Golden_Cake" =>$Golden_Cake,);
	}
	 
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.6">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Datatable</title>
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"/>
	</head>
	<style>
		table, th, td {
			border: 0.5px solid #d9d9d9;
			border-collapse: collapse;
			font-size:12px;
		}
		.dataTables_filter input { height: 30px;margin-bottom: 20px; margin-top:20px;}
		.dataTables_length select{
			height: 30px;
			width: 100px;
			margin-bottom: 20px; margin-top:20px;
		}
	</style>
	<body>
		<table id="userTable1" class="stripe" style="width:100%;">
			<thead>
				<th>Membership_No</th>
				<th>Document_No</th>
				<th>PlanCode</th>
				<th>Subs_DateFrom</th>
				<th>TotalSubs_Amount</th>
				<th>Char_Every</th>
				<th>Char_Period</th>
				<th>Char_Times</th>
				<th>Customer_Type</th>
				<th>FormType</th>
				<th>Milk</th>
				<th>Choco</th>
				<th>Peace</th>
				<th>InLove</th>
				<th>Lonely</th>
				<th>MoonLight</th>
				<th>Passion</th>
				<th>Sunrise</th>
				<th>Luna</th>
				<th>Amico</th>
				<th>The_King</th>
				<th>The_Queen</th>
				<th>Prince</th>
				<th>Princess</th>
				<th>Earl</th>
				<th>Butter_Croissant</th>
				<th>Rasp_Butter_Croissant</th>
				<th>Matcha_Butter_Croissant</th>
				<th>Charcoal_Butter_Croissant</th>
				<th>Focaccia</th>
				<th>Golden_Roti</th>
				<th>CharGold_Roti</th>
				<th>Golden_Cake</th>
			</thead>
			<tbody>
				<?php if(!empty($array)) { 
		foreach($array as $data) { ?>
				<tr>
					<td><?php echo $data['Membership_No']; ?></td>
					<td><?php echo $data['Document_No']; ?></td>
					<td><?php echo $data['PlanCode']; ?></td>
					<td><?php echo $data['Subs_DateFrom']; ?></td>
					<td><?php echo $data['TotalSubs_Amount']; ?></td>
					<td><?php echo $data['Char_Every']; ?></td>
					<td><?php echo $data['Char_Period']; ?></td>
					<td><?php echo $data['Char_Times']; ?></td>
					<td><?php echo $data['Customer_Type']; ?></td>
					<td><?php echo $data['FormType']; ?></td>
					<td><?php echo $data['Milk']; ?></td>
					<td><?php echo $data['Choco']; ?></td>
					<td><?php echo $data['Peace']; ?></td>
					<td><?php echo $data['InLove']; ?></td>
					<td><?php echo $data['Lonely']; ?></td>
					<td><?php echo $data['MoonLight']; ?></td>
					<td><?php echo $data['Passion']; ?></td>
					<td><?php echo $data['Sunrise']; ?></td>
					<td><?php echo $data['Luna']; ?></td>
					<td><?php echo $data['Amico']; ?></td>
					<td><?php echo $data['The_King']; ?></td>
					<td><?php echo $data['The_Queen']; ?></td>
					<td><?php echo $data['Prince']; ?></td>
					<td><?php echo $data['Princess']; ?></td>
					<td><?php echo $data['Earl']; ?></td>
					<td><?php echo $data['Butter_Croissant']; ?></td>
					<td><?php echo $data['Rasp_Butter_Croissant']; ?></td>
					<td><?php echo $data['Matcha_Butter_Croissant']; ?></td>
					<td><?php echo $data['Charcoal_Butter_Croissant']; ?></td>
					<td><?php echo $data['Focaccia']; ?></td>
					<td><?php echo $data['Golden_Roti']; ?></td>
					<td><?php echo $data['CharGold_Roti']; ?></td>
					<td><?php echo $data['Golden_Cake']; ?></td>
				</tr>
				<?php } ?>
				<?php } ?>
			</tbody>
		</table>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script type="text/javascript" src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
		
		<script> 
			$(document).ready(function() {
				$('#userTable1').DataTable({
					"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
					"iDisplayLength": 25, // to display 25 rows
					"bAutoWidth": true,
					"maxWidth": screen.width,
					"scrollX": true
				});
			});
		</script>
	</body>
</html>
<?php
}

add_shortcode( 'test', 'test' );
function test(){
	echo "Hello World!";
	echo "testing";
}

add_shortcode( 'basicInfo','basicInfo' );
function basicInfo(){
	if ( is_user_logged_in() ) {
		$db = connectDB();
		$username = wp_get_current_user()->user_login;
		$sql = "SELECT Name, Email, Contact, M_Addr1, M_Addr2, M_City, M_State, M_Postcode FROM ct_myaccount_flutter WHERE Email = '".$username."'";
		$result = $db->get_results($sql);
		foreach($result as $val){
			$membername = $val->Name;
			$memberemail = $val->Email;
			$memberhp = $val -> Contact;
			$maddress1 = $val->M_Addr1;
			$maddress2 = $val->M_Addr2;
			$mpcode = $val->M_Postcode."&nbsp".$val->M_City;
			$mstate = $val->M_State;
		}
		return '<p class="title">Name</p>
			<p class="txtdb" id="membername">'.$membername.'</p>
			<p class="title">Email:</p>
			<p class="txtdb" id="memberemail">'.$memberemail.'</p>
			<p class="title">Contact Number:</p>
			<p class="txtdb" id="memberhp">'.$memberhp.'</p>
			<p class="title">Billing Address</p>
			<p class="txtdb" id="memberaddress">'.$maddress1.'<br \>
			'.$maddress2.'<br \>
			'.$mpcode.'<br \>
			'.$mstate.'</p>';
	}
}

add_shortcode('subPlan', 'subPlan');
function subPlan() {
	if (is_user_logged_in()) {
		$db = connectDB();
		$username = wp_get_current_user() -> user_login;
		$sql = "SELECT Membership_Type FROM ct_myaccount_flutter WHERE Email = '".$username."'";
		$result = $db -> get_results($sql);
		foreach($result as $val){
			$Membership_Type = $val->Membership_Type;
			$array[] = array("Membership_Type" => $Membership_Type);
		}
		ob_start();
		if(!empty($array)) {
			foreach($array as $data) {
				//echo "1 ".$data['Membership_Type']."<br>";
				if (stripos($data['Membership_Type'], "AP") !== FALSE) {
					//echo "AP ".$data['Membership_Type']."<br>";
					echo "<div class='plan-detail-odd'>
					<img src='http://arissto.com/test/wp-content/uploads/2020/10/happy_maker_2.0.png'class='plan-image'/>
					<span class='plan-words'>RM1 Home Coffee Plan<br/><span class='plan-type'>"
					.$data['Membership_Type'].
					"</span><br/></span></div>";
				} elseif (stripos($data['Membership_Type'],"SS") !== FALSE) {
					//echo "SS ".$data['Membership_Type']."<br>";
					echo "<div class='plan-detail-even'>
					<img src='http://arissto.com/test/wp-content/uploads/2020/10/happy_maker_2.0.png'class='plan-image'/>
					<span class='plan-words'>RM1 Office Buddies Plan<br/><span class='plan-type'>"
					.$data['Membership_Type'].
					"</span><br/></span></div>";
				}
			}
		
		$result=ob_get_clean();
		return $result;
		}
	}
}

add_shortcode('testmeta', 'testmeta');
function testmeta() {
	$nname = get_user_meta(4);
	print_r( $nname );
	//echo $nname;
}


