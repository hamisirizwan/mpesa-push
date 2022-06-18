<?php
 		header("Content-Type: application/json");

     $response = '{
         "ResultCode": 0, 
         "ResultDesc": "Confirmation Received Successfully"
     }';
 
     // DATA
     $mpesaResponse = file_get_contents('php://input');
 
     // log the response
     $logFile = "M_PESAConfirmationResponse.txt";
    
 
     // write to file
     $log = fopen($logFile, "a");
 
     fwrite($log, $mpesaResponse);
     fclose($log);

     //Processing the Mpesa json response Data
//      $mpesaResponse = file_get_contents('M_PESAConfirmationResponse.json');
     $callbackContent = json_decode($mpesaResponse);

     $Resultcode = $callbackContent->Body->stkCallback->ResultCode;
     $CheckoutRequestID = $callbackContent->Body->stkCallback->CheckoutRequestID;
     $Amount = $callbackContent->Body->stkCallback->CallbackMetadata->Item[0]->Value;
     $MpesaReceiptNumber = $callbackContent->Body->stkCallback->CallbackMetadata->Item[1]->Value;
     $PhoneNumber = $callbackContent->Body->stkCallback->CallbackMetadata->Item[4]->Value;
     $formatedPhone  = str_replace("254", "0" , $PhoneNumber); 
     if ($Resultcode == 0) {

    
      // Connect to DB
      $conn = mysqli_connect("localhost","root","","daraja");
  
  
  
  
      // Check connection
      if ($conn->connect_error) {
          die("<h1>Connection failed:</h1> " . $conn->connect_error);
      } else {
  
  
          $insert = $conn->query("INSERT INTO data(CheckoutRequestID,ResultCode,amount,MpesaReceiptNumber,PhoneNumber) VALUES ('$CheckoutRequestID','$Resultcode','$Amount','$MpesaReceiptNumber','$PhoneNumber')");
          $conn = null;
      }
  }

     

 
     echo $response;
