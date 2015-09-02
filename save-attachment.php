<?php
  require_once('response.util.php');

  $isJsonSubmit = strpos($_SERVER['CONTENT_TYPE'], 'application/json') === 0;
  $isMultipartSubmit = strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') === 0;
  
  // JSON form submit + file attachment
  $files = array();
  
  if($isMultipartSubmit){
    // File attachment(s)                
    foreach($_FILES as $partName => $part){
      if(!is_uploaded_file($_FILES[$partName]['tmp_name']))
        continue;
      $files[$partName] = $part;
    }
  }
  
  // Handle file attachments, if any
  
  try {
    $attachmentName = 'attachment';   // name of the HTML form control associated with the file upload
    
    if(!array_key_exists($attachmentName, $files))
      throw new Exception('No attachment given!.');
    
    if($files[$attachmentName]['error'] > 0)
      throw new Exception('An error occurred while trying to upload the attached file.');

    $name_parts = pathinfo($files[$attachmentName]['name']);
    $basename   = $name_parts['filename'];
    $extension  = $name_parts['extension'];
    
    // if($extension !== 'pdf'){
    //   ...
    // }
    
    $canonical_name = md5_file($files[$attachmentName]['tmp_name']);
    if($canonical_name === false)
      throw new Exception('Unable to compute a hash for the attached file.');
        
    $moveResult = move_uploaded_file($files[$attachmentName]['tmp_name'], 'attachments/' . $canonical_name . '.' . $extension);
    if($moveResult === false)
      throw new Exception('Unable to move attached file to permanent storage.');
    
    // $changed = array('id' => $id);
    // if(array_key_exists('file_id', $this->request) and strlen($this->request['file_id']) !== 0)
    //   $changed['file_id'] = $this->request['file_id'];
    //   
    // return $changed;
    _response(array('id' => $canonical_name),200);
      
  } catch (Exception $e) {
      _response(
        array('error' => $e->getMessage()), 
        500);
  }
?>