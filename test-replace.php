<?php
  require_once('RecordSet.php');
  
  $method = 'PUT';
  $request = array(
    ["161C9FA0F563232E9BE2671D0D282942","Division Directory","ABTPA","PDF","Structure","-","-"],
    ["161C9FA0F563232E9BE2671D0D282942","Division Directory v2","ABTPA","PDF","Structure","-","-"]
  );
  
  try {
    if(count($request) === 0)
      throw new Exception('No data given, or HTTP method not allowed.');
    if(count($request) < 2)
      throw new Exception('Unable to replace record - both original and replacement expected.');
    
    $records = new RecordSet('data/index.json');

    $replaced = $records->replace($request[0], $request[1]);
    $records->save();
    
    echo $records . PHP_EOL;
    
  } catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
  }
?>