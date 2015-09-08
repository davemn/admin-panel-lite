<?php
  class RecordSet {
    private $filename;
    private $data;
    private $dataHash; // an MD5 hash of the contents of each record
    
    static function dataPrettyPrint($records){
      $preamble = <<<JSON
{
  "data": [

JSON;

      $body = '';
      $record = null;
      for($i = 0; $i < count($records); $i++){
        $record = $records[$i];
        $body .= '    ' . json_encode($record);
        if($i < count($records)-1)
          $body .= ',';
        
        $body .= PHP_EOL;
      }
      $body = substr($body, 0, strlen($body)-1); // drop trailing comma

      $postscript = <<<JSON
  ]
}
JSON;

      return $preamble . $body . $postscript;
    }
    
    /**
     * Calculate a unique identifier for a record. A record is a flat array 
     * of strings.
     */
    static function hashRecord($record){
      // Drop the file hash, clean up the remaining fields 
      // (only alphanumeric, no whitespace), and concatenate.
      $preHash = array_reduce(array_slice($record, 1), function($carry, $item){
        $valid = trim($item);
        $valid = preg_replace("/[^A-Za-z0-9]/", "", $valid);
        $valid = strtoupper($valid);
        
        return $carry . $valid;
      }, '');
      return md5($preHash);
    }
    
    public function __construct($filename){
      if(!is_string($filename) or strlen($filename) === 0)
        throw new Exception('Must provide a valid filename to read/write.');
      
      $this->filename = $filename;
      
      $raw_data = file_get_contents($this->filename);
      if($raw_data === false)
        throw new Exception('Could not read file!');
      
      $outerData = json_decode($raw_data, true);   // parse into an array
      $this->data = $outerData['data'];
      
      $this->dataHash = array();
      
      // Concat the fields of each record, excluding the ID field, then hash
      foreach($this->data as $curRecord){
        array_push($this->dataHash, RecordSet::hashRecord($curRecord));
      }
    }
    
    private function contains($record){
      if(count($this->data) === 0)
        return false;
      
      $hash = RecordSet::hashRecord($record);
      
      // Try to find a record with the same hash
      foreach($this->dataHash as $curHash){
        if($hash === $curHash)
          return true;
      }
      return false;
    }
    
    public function add($record){
      if($this->contains($record))
        throw new Exception('Record already exists!');
      
      array_push($this->data, $record);
      array_push($this->dataHash, RecordSet::hashRecord($record));
      
      $last = end($this->data); // get last element (freshly added)
      reset($this->data);
      
      return $last;
    }
    
    function save(){
      $pretty = RecordSet::dataPrettyPrint($this->data);
      file_put_contents($this->filename, $pretty);
    }
  }
?>