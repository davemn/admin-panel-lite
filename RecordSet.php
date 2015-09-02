<?php
  class RecordSet {
    private $filename;
    private $data;
    private $dataHash; // an MD5 hash of the contents of each record

    /**
     * Pre- PHP 5.4 JSON pretty printing.
     * From http://stackoverflow.com/questions/6054033/pretty-printing-json-with-php
     */
    static function jsonPrettyPrint($json){
      $result = '';
      $level = 0;
      $in_quotes = false;
      $in_escape = false;
      $ends_line_level = NULL;
      $json_length = strlen( $json );

      for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
          $new_line_level = $ends_line_level;
          $ends_line_level = NULL;
        }
        if ( $in_escape ) {
          $in_escape = false;
        } else if( $char === '"' ) {
          $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
          switch( $char ) {
            case '}': case ']':
              $level--;
              $ends_line_level = NULL;
              $new_line_level = $level;
              break;

            case '{': case '[':
              $level++;
            case ',':
              $ends_line_level = $level;
              break;

            case ':':
              $post = " ";
              break;

            case " ": case "\t": case "\n": case "\r":
              $char = "";
              $ends_line_level = $new_line_level;
              $new_line_level = NULL;
              break;
          }
        } else if ( $char === '\\' ) {
          $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
          $result .= "\n".str_repeat( "  ", $new_line_level );
        }
        $result .= $char.$post;
      }

      return $result;
    }
    
    static function hashRecord($record){
      $preHash = array_reduce(array_slice($record, 1), function($carry, $item){
        return $carry . $item;
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
      // file_put_contents($this->filename, json_encode(array('data' => $this->data), JSON_PRETTY_PRINT));
      
      $pretty = RecordSet::jsonPrettyPrint(json_encode(array('data' => $this->data)));
      file_put_contents($this->filename, $pretty);
    }
  }
?>