<?php
  $cliOpts = getopt('f:', array('field:'));
  if($cliOpts === false){
    print 'Unable to parse command-line options!' . PHP_EOL;
    exit(0);
  }
  
  try {
    if(!array_key_exists('f', $cliOpts) or !array_key_exists('field', $cliOpts))
      throw new Exception('Missing arguments');
    
    if(strlen($cliOpts['f']) === 0)
      throw new Exception('Must provide a valid JSON filename.');
    
    $filename = $cliOpts['f'];
    
    if(!is_numeric($cliOpts['field']))
      throw new Exception('Field must be a positive integer');
    
    $fieldIdx = intval($cliOpts['field']);
    
    // ---
    
    $raw_data = file_get_contents($filename);
    if($raw_data === false)
      throw new Exception('Could not read file!');
    
    $outerData = json_decode($raw_data, true);   // parse into an array
    $data = $outerData['data'];
    
    $unique = array();
    $field = NULL;
    
    // Build a list of unique field values
    foreach($data as $record){
      $field = $record[$fieldIdx];
      
      if(!array_key_exists($field, $unique))
        $unique[$field] = 1;
      else
        $unique[$field] = $unique[$field]+1;
    }
    
    // Print out unique field values, and their frequencies
    foreach($unique as $key => $val){
      print $key . "\t" . $val . PHP_EOL;
    }
  }
  catch(Exception $e){
    print 'Bad arguments given! Expects:' . PHP_EOL;
    print '$>php __cli-unique-fields.php -f <PATH TO JSON> --field <0-BASED INDEX>' . PHP_EOL;
    print PHP_EOL;
    print $e->getMessage();
    exit(0);    
  }
?>