<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class My_Model extends CI_Model{
	
	public function __construct(){
    parent::__construct();
  }


/*
Get one record

access public
@param mixed $value identification of record. Default is tables primary key
@param array $options 
return mixed 
*/
public function find($value,$options=null){
	if(isset($options))extract($options);
	$options = array(
		'fields' =>'*',
		'returnType'=>'array'
	);
	extract($options,EXTR_SKIP);
	$this->db->select($fields);
	if(!is_array($value)){
		$filter= array($this->primaryKey => $value );
	}else{
		$filter= $value;
	}
	foreach($filter as $key=>$val){
		$this->db->where($key,$val);
	}
	$dataset=$this->db->get($this->table);
	if($dataset->num_rows()==1){
		return $returnType=='object'?$dataset->row(): $dataset->row_array();
	}else{
		return null; 
	}
}

	/*
	Get a list of records 
	access public
	@param array 
	return array
	*/
	public function listRecords($options=NULL){
		if(isset($options))extract($options);
		$options = array(
			'fields'=>'*',
			'filter'=>NULL,
			'inList'=>NULL,
			'limit'=>9999,
			'start'=>0,
			'returnType'=>'array',
			'sort'=> array('key'=>$this->primaryKey,'direction'=>'desc')
		);
		extract($options,EXTR_SKIP);
		$this->db->select($fields);
		
		if(is_array($inList)){
			foreach($inList as $key=>$val){
				$this->db->where_in($key,$val);
			}
		}
		
		if(is_array($filter)){
			foreach($filter as $key=>$val){
				$this->db->where($key,$val);
			}
		}
		
		$sortKey = isset($_GET['sort'])?$_GET['sort']: $sort['key'];
		$direction = isset($_GET['direction'])?$_GET['direction']:$sort['direction'];

		
		if ($this->db->field_exists($sortKey, $this->table))
		{
			$this->db->order_by($sortKey, $direction); 
		}
		
		$dataset=$this->db->get($this->table,$limit,$start);
		return $returnType=='object' ? $dataset->result(): $dataset->result_array();
	}

	/*

	Sample Usage 1 : For default
	$this->sampleModel->update(1,array(
			'data'=> array(
					'field1'=>'value1',
					'field2'=>'value2'
					)
	));

	Sample Usage 2 : For customizing the filter key and value
	$this->sampleModel->update(1,array(
			'data'=> array(
					'field1'=>'value1',
					'field2'=>'value2'
					)
	));
	
	Update one or more records
	WARNING : This can be dangerous. It can update all records if filter is not properly defined. if you are not sure about this you can just use the default usage.
	access	public
	@param	int/array $value filter
	@param 	array
	@return array

*/
	public function update($value,$options){
		if(isset($options))extract($options);
		$options = array(
			'data' =>null
		);
		extract($options,EXTR_SKIP);

		if(!is_array($value)){
			$filter= array($this->primaryKey => $value );
		}else{
			$filter= $value;
		}

		foreach($filter as $key=>$val){
			$this->db->where($key,$val);
		}

		return $this->db->update($this->table,$data);
	}



	/* FOR REVIEW
	 * array structure : 
	 * array(
	 * 	'fieldname'=>value,
	 *  'fieldname2'=>value
	 * )
	 */
	public function insert($data){
		$this->db->insert($this->table,$data);
		return $this->db->insert_id();
	}

	//FOR REVIEW

	//FOR REVIEW
	public function delete($value,$options=null){
		if(isset($options))extract($options);
		$options = array(
			'filter' => $this->primaryKey
		);
		extract($options,EXTR_SKIP);
		
		if(is_array($filter)){
			$filterBy = $filter;
		}else{
			$filterBy = array($this->primaryKey=>$value);
		}
		return $this->db->delete($this->table,$filterBy);
	}

	public function countRows($filter=null){
		if(is_array($filter)){
			foreach($filter as $key=>$val){
				$this->db->where($key,$val);
			}
		}
		return $this->db->count_all_results($this->table);
	}





  /* For Development */


	



	
	public function isExists($value,$options=null){
		if(isset($options))extract($options);
		$options = array(
			'filterBy' => $this->primaryKey
		);
		extract($options,EXTR_SKIP);
		return $this->db->select($this->primaryKey)->from($this->table)->where($filterBy,$value)->count_all_results()?1:0;
	}
	
	

	public function removeAllRecords(){
		return $this->db->truncate($this->table); 
	}
	
	
	/*Stable functions */

	/* 
	Get database field value
	access	public
	@param		int $id record id
	@param 	String $field record column name
	@return	string
	 */
	public function getValue($id,$field){
		$this->db->select($field);
		$this->db->where($this->primaryKey,$id);
		$dataset = $this->db->get($this->table);
		return isset($dataset->row()->$field)?$dataset->row()->$field:'';
	}

	
}
	


/*

Create
	insert single record
	insert multiple record
Read
	view single record
	view multiple record
Update
	update mulitple
	update single record
Delete
	delete single record
	delete multiple record
Truncate
	truncate table
Count
	count records



*/