<?php

/**
 * The response from running a Submit query
 * @author Tyler
 * @package Factual
 * @license Apache 2.0
 */
class SubmitResponse extends FactualResponse {

	protected $newEntity = false;
	protected $factualID = null;
	protected $commitID = null;
	protected $isDelayed = false;

	/**
	 * Parses JSON as array and assigns object values
	 * @param string json JSON returned from API
	 * @return array structured JSON
	 */
	protected function parseJSON($json){
		$rootJSON = parent::parseJSON($json);
		//in some instances writes to the system will be delayed, and we cannot provide a committ ID
		if ( array_key_exists( 'error_type', $rootJSON ) && $rootJSON['error_type'] == "DelayedResponse"){
			//throw new Exception($rootJSON['message']);
			$this->isDelayed = true;
		} else {
			$this->commitID = $rootJSON['response']['commit_id'];
			if (isset($this->newEntity)){
				$this->newEntity = (bool)$rootJSON['response']['new_entity'];
			}
			if (isset($rootJSON['response']['factual_id'])){
				$this->factualID = $rootJSON['response']['factual_id'];
			}
		}
		return $rootJSON;
	}

	/**
	 * Is this a new entity (inserted) or extant entity (updated)
	 * @return bool
	 */
	public function isNew(){
		return $this->newEntity;
	}	
	
	/**
	 * Is the write delayed?
	 * @return bool
	 */
	public function isDelayed(){
		return $this->isDelayed;
	}	
		
	/**
	 * Get the Factual ID (extant entity only)
	 * @return string | null on no ID
	 */
 	public function getFactualID(){
 		return $this->factualID;
 	} 

	/**
	 * Get the Commit ID (transaction identifier)
	 * @return string | null on no ID
	 */
 	public function getCommitID(){
 		return $this->commitID;
 	} 

}
?>
