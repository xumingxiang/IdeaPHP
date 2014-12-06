<?php 
class IdeaException extends Exception { 
	public function __toString() { 
	//error message 
	  $errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile() .': <b>'.$this->getMessage().'</b> '; 
	  return $errorMsg; 
	  } 
	} 

	 function __construct($message,$code=0) {
        parent::__construct($message,$code);
		try{}
		catch(IdeaException $ix){
		
		
		}
		
    }

?> 
