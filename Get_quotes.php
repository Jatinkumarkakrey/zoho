<?php
// // POST this code in cron job to update token in  every 7 hours
// $ch = curl_init();

// curl_setopt($ch, CURLOPT_URL, "https://csgapi.appspot.com/v1/auth.json");
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
// curl_setopt($ch, CURLOPT_HEADER, FALSE);

// curl_setopt($ch, CURLOPT_POST, TRUE);

// curl_setopt($ch, CURLOPT_POSTFIELDS, "{
//   \"api_key\": \"*******************************************************\"
// }");

// curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//   "Content-Type: application/json"
// ));

// $response = curl_exec($ch);
// $res = json_decode($response);

// curl_close($ch);
// echo"<pre>";print_r($res);
// echo $res->status;
// $token = $res->token;
// echo $token;



//Get Quotes
function get_quotes()
{
    $ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://csgapi.appspot.com/v1/med_supp/quotes.json?zip5=15963&age=65&gender=M&tobacco=0"); // change url parameters with actual one
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "x-api-token: ******************************************"
));

$response = curl_exec($ch);
curl_close($ch);
print_r($response);
return $response;
    
}


// Get all Plan 
function get_plans()
{
   $ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://csgapi.appspot.com/v1/med_supp/quotes/plan-details/A.json");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "x-api-token: ***************************************************"
));

$response = curl_exec($ch);
$res= (json_decode($response, true));
curl_close($ch);

return $res;
}
echo "<pre>";print_r(get_plans());
// Generating tokens for zoho 

function zoho_access ()
{
  $zoho_client_id ="1000.************************";
$zoho_secret_id ="***************************************";
$zoho_refresh_token ="1000.**************************.**************************";

$zoho_access = curl_init();

curl_setopt($zoho_access,CURLOPT_URL,"https://accounts.zoho.com/oauth/v2/token?refresh_token=".$zoho_refresh_token."&client_id=".$zoho_client_id ."&client_secret=".$zoho_secret_id."&grant_type=refresh_token");
curl_setopt($zoho_access,CURLOPT_CUSTOMREQUEST, "POST"); //0 for a get request
curl_setopt($zoho_access,CURLOPT_RETURNTRANSFER, true);
curl_setopt($zoho_access,CURLOPT_CONNECTTIMEOUT ,3);
curl_setopt($zoho_access,CURLOPT_TIMEOUT, 30);

$response_zoho = curl_exec($zoho_access);
curl_close($response_zoho);
$res_acc= (json_decode($response_zoho, true));

$zoho_access = $res_acc["access_token"];
return $zoho_access;
}
$ac = zoho_access();
// CREATING LEADS IN ZOHO CRM
function create ($acc)
{
   
    
    $zoho_create = curl_init();
curl_setopt($zoho_create,CURLOPT_URL,"https://www.zohoapis.com/crm/v2/Leads");
curl_setopt($zoho_create,CURLOPT_POST, 1); 
curl_setopt($zoho_create,CURLOPT_RETURNTRANSFER, true);
curl_setopt($zoho_create,CURLOPT_CONNECTTIMEOUT ,3);
//curl_setopt($zoho_upload,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
curl_setopt($zoho_create,CURLOPT_TIMEOUT, 30);
curl_setopt($zoho_create,CURLOPT_HTTPHEADER, array(
    'Authorization: Zoho-oauthtoken '.$acc
    
  ));
  
  
$data =array("data"=>array(array("Age_at_Quote_Request"=> "65","Last_Name"=>"Test","First_Name"=>"Quotes","Mobile"=>"9090909090","Gender"=>"Male","Zip_Code"=>"15963","Tobacco_Status"=>"Tobacco","Date_of_Birth"=>"1999-05-02")));
$import = json_encode($data,true);
 curl_setopt(
$zoho_create,
CURLOPT_POSTFIELDS,
$import);
$response_zoho_create = curl_exec($zoho_create);


curl_close($response_zoho_create);
 return $response_zoho_create;
}
//print_r( create($ac));
?>
