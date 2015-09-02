<?php
  require_once('response.util.php');
  require_once('RecordSet.php');
  
  header("Access-Control-Allow-Orgin: *");
  header("Access-Control-Allow-Methods: *");
  header("Content-Type: application/json");

  $method = $_SERVER['REQUEST_METHOD'];
  $request = array();
  
  if($method === 'POST'){
    $isJsonSubmit = strpos($_SERVER['CONTENT_TYPE'], 'application/json') === 0;
    
    // JSON form submit
    if($isJsonSubmit){
      $reqPayload = file_get_contents('php://input');
      $request = json_decode($reqPayload, true);
    }
  }

  try {
    if(count($request) === 0)
      throw new Exception('No data given, or HTTP method not allowed.');
    
    $records = new RecordSet('data/index.json');

    $added = $records->add($request);
    $records->save();
    
    _response($added, 200);
    
  } catch (Exception $e) {
      _response(
        array('error' => $e->getMessage()), 
        500);
  }
?>