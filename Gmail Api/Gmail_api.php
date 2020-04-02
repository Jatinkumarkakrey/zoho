<?php
error_reporting(E_ALL & ~E_NOTICE);

$client_id= "172793160875-mmitndungnjdlvlrlfaph7p1cld8hkmj47.apps.googleusercontent.com";
$client_secret = "n04LYqmfm-3Z8haBKffIxomzo";
$refresh_token ="1//04Od3NU247E_kCgYIARAAGAQSNwF-L9IrMoqI2TDiA75T67EcDeqzzOrEzyzZdznn-hJfwgAigd3_lyhuMGaUp1cEFrZXcCqKrOBQ8";


$ch = curl_init();

curl_setopt($ch,CURLOPT_URL,"https://oauth2.googleapis.com/token?client_id=".$client_id."&client_secret=".$client_secret."&grant_type=refresh_token&refresh_token=".$refresh_token);
curl_setopt($ch,CURLOPT_POST, 1); //0 for a get request
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
curl_setopt($ch,CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);

curl_close($ch);

$res= (json_decode($response, true));
$access_token = $res["access_token"];
$mail = curl_init();

curl_setopt_array($mail, array(
  CURLOPT_URL => "https://www.googleapis.com/gmail/v1/users/jatin@insightcrew.com/messages?labelIds=Label_2782808680098118218",
 // CURLOPT_URL => "https://www.googleapis.com/gmail/v1/users/jatin@insightcrew.com/labels",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer ".$access_token,"Accept: application/json"
  ),
));
$response = curl_exec($mail);
$err = curl_error($mail);
//echo"<pre>"; print_r($response);exit;
$mail_id = (json_decode($response, true));
$msg = $mail_id["messages"] ;
foreach ($msg as $ms) {
    $msg_id = $ms["id"];
    $get_mail = curl_init();

curl_setopt_array($get_mail, array(
  CURLOPT_URL => "https://www.googleapis.com/gmail/v1/users/jatin@insightcrew.com/messages/".$msg_id,
  
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer ".$access_token,"Accept: application/json"
  ),
));
$response1 = curl_exec($get_mail);
$err1 = curl_error($get_mail);
//echo"<pre>"; print_r($response1);exit;
$mail_body = (json_decode($response1,true));
$payload = $mail_body["payload"];
$subject = $payload["headers"][18]["value"];
$date = $payload["headers"][17]["value"];
//$parts = $payload["parts"];
$time = strtotime($date);
 $comdate = date("Y-m-d ", $time);
 
// $subject=="All Advertisers" 
if ($comdate<=date("Y-m-d") && $subject=="All Advertisers")
{
    
$parts = $payload["parts"][1]["body"];
$attachment_id = $parts["attachmentId"];


  $get_attach = curl_init();

curl_setopt_array($get_attach, array(
  CURLOPT_URL => "https://www.googleapis.com/gmail/v1/users/{your email}/messages/".$msg_id."/attachments/".$attachment_id,
  
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer ".$access_token,"Accept: application/json"
  ),
));


$response_attach = curl_exec($get_attach);
$err2 = curl_error($get_attach);
        //echo"<pre>"; print_r($response_attach);
        
        
$attach_body = (json_decode($response_attach,true));

$data = $attach_body["data"];
$bin = base64_decode($data);
$myfile = fopen("newfile.csv", "w") ;
$txt = $bin;
fwrite($myfile, $txt);

fclose($myfile);
$file = fopen('newfile.csv', 'r');
$count = 0;
$data = array(array("Name","Month_and_year","Line_item","Date","Advertiser_ID","Line_item_ID","Advertiser_labels","Advertiser_type","Total_impressions","Total_clicks","Total_CTR","Auto_plays","Click_to_plays"));
//$data = array();
while (($line = fgetcsv($file)) !== FALSE) 
{
   
   $count = $count+1;
   if ($count>9)
   {   
       if($line[0]!="Total")
       {
            //$ar= array("Name"=>$line[0],"Advertiser_ID"=>$line[4],"Advertiser labels"=>$line[6],"Advertiser_type"=>$line[7],"Auto_plays"=>$line[11],"City"=>"-","Click_to_plays"=>$line[12],"Country"=>"-","Date"=>$line[3],"Line_item"=>$line[2],"Line_item_ID"=>$line[5],"Month_and_year"=>$line[1],"Total_clicks"=>$line[9],"Total_CTR"=>$line[10],"Total_impressions"=>$line[8]);
            $ar= array($line[0],$line[1],$line[2],$line[3],$line[4],$line[5],$line[6],$line[7],$line[8],$line[9],$line[10],$line[11],$line[12]);
       array_push($data,$ar);
       if ($count==20)
       {
           break;
       }
       }
   
   }
   
}
   
   
//$data_map = array(array("Name","Advertiser_ID","Advertiser_labels","Advertiser_type","Auto_plays","City","Click_to_plays","Country","Date","Line_item","Line_item_ID","Month_and_year","Total_clicks","Total_CTR","Total_impressions"),$data);
 
  // print_r ($data);
   $file1 = fopen("All_ads_data.csv","w");

foreach ($data as $line)
{
  fputcsv($file1, $line);
}

fclose($file1);

fclose($file);
$zip = new ZipArchive;
if ($zip->open('test_new2.csv.zip', ZipArchive::CREATE) === TRUE)
{
    // Add files to the zip file
    $zip->addFile('All_ads_data.csv');
   
 
    
 
    // All files are added, so close the zip file.
    $zip->close();
}

$zoho_client_id ="bb1000.H5MKTQ3U4M0FNP0YUUSSEXOUFHPMZH";
$zoho_secret_id ="bb3bab7c7c0cf9a0b358da088c367de6aefd9594ffdb";
$zoho_refresh_token ="bb1000.92555a37266a707e733a8e6ad3870866.5d47d8e2b5cc523222588cc5d2cf3734";

$zoho_access = curl_init();

curl_setopt($zoho_access,CURLOPT_URL,"https://accounts.zoho.com/oauth/v2/token?refresh_token=".$zoho_refresh_token."&client_id=".$zoho_client_id ."&client_secret=".$zoho_secret_id."&grant_type=refresh_token");
curl_setopt($zoho_access,CURLOPT_CUSTOMREQUEST, "POST"); //0 for a get request
curl_setopt($zoho_access,CURLOPT_RETURNTRANSFER, true);
curl_setopt($zoho_access,CURLOPT_CONNECTTIMEOUT ,3);
curl_setopt($zoho_access,CURLOPT_TIMEOUT, 30);

$response_zoho = curl_exec($zoho_access);
curl_close($response_zoho);

$res_acc= (json_decode($response_zoho, true));
print_r($res_acc);
$zoho_access = $res_acc["access_token"];
echo $zoho_access;
$zoho_upload = curl_init();
$real_path  = realpath('test_new2.csv.zip');
$cfile = curl_file_create($real_path ,'application/zip','test_name.csv.zip');
print_r(filetype($cfile));

// Assign POST data
$data = array('file' => $cfile);



curl_setopt($zoho_upload,CURLOPT_URL,"https://content.zohoapis.com/crm/v2/upload");
curl_setopt($zoho_upload,CURLOPT_POST, 1); 
curl_setopt($zoho_upload,CURLOPT_RETURNTRANSFER, true);
curl_setopt($zoho_upload,CURLOPT_CONNECTTIMEOUT ,3);

curl_setopt($zoho_upload,CURLOPT_TIMEOUT, 30);
curl_setopt($zoho_upload,CURLOPT_HTTPHEADER, array(
    'Authorization: Zoho-oauthtoken '.$zoho_access,'X-CRM-ORG: 49604660','feature: bulk-write'
  ));

 curl_setopt(
$zoho_upload,
CURLOPT_POSTFIELDS,
$data);



$response_zoho_upload = curl_exec($zoho_upload);
$er = curl_error($zoho_upload);
print_r($er);
print_r($response_zoho_upload);
curl_close($zoho_upload);

 $file= (json_decode($response_zoho_upload, true));
 $file_detail = $file["details"];
 $file_id =$file_detail['file_id']  ;
echo $file_id;

$zoho_create = curl_init();
curl_setopt($zoho_create,CURLOPT_URL,"https://www.zohoapis.com/crm/bulk/v2/write");
curl_setopt($zoho_create,CURLOPT_POST, 1); 
curl_setopt($zoho_create,CURLOPT_RETURNTRANSFER, true);
curl_setopt($zoho_create,CURLOPT_CONNECTTIMEOUT ,3);
//curl_setopt($zoho_upload,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
curl_setopt($zoho_create,CURLOPT_TIMEOUT, 30);
curl_setopt($zoho_create,CURLOPT_HTTPHEADER, array(
    'Authorization: Zoho-oauthtoken '.$zoho_access,
    'Content-Type:application/json'
  ));
  
  
$import_data =array("operation"=>"insert","resource"=>array(array("type"=> "data","module"=>"All_Advertisers","file_id"=>$file_id)));
$import = json_encode($import_data,true);
 curl_setopt(
$zoho_create,
CURLOPT_POSTFIELDS,
$import);
$response_zoho_create = curl_exec($zoho_create);
$er = curl_error($zoho_create);
print_r($er);
print_r($response_zoho_create);
curl_close($response_zoho_create);
}

else
{
  
  echo "old mail".$comdate;

}



}






?>
