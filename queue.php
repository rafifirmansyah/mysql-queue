<?php
class Queue
{
	var $conn = null;

	function __construct($servername, $username, $password, $dbname){
		$this->conn = new mysqli($servername, $username, $password, $dbname);
		if ($this->conn->connect_error) {
			throw(new Exception("Connection failed: " . $this->conn->connect_error));
		}
	}

	function push($payload){
		$sql = 'SELECT pushJob(\'' . $this->conn->escape_string($payload) . '\') as pushResult;';
		$result = $this->conn->query($sql);
		if($result && $row = $result->fetch_assoc()) {
        		if($row['pushResult'] > 0){
				return $row['pushResult'];
			}
			throw(new Exception('Could not push job, could not create!'));
		}
		throw(new Exception('Could not push job, query failed: ' . $sql));
	}

	function pop($clientId){
		$sql = 'CALL popJob(' . $this->conn->escape_string($clientId) . ');';
		$result = $this->conn->query($sql);
		$out = array();	
		while($result && $row = $result->fetch_assoc()) {
			$out[] = $row;      		
			if(!empty($row['error'])){
				throw(new Exception('popJob failed: ' . $row['error']));
			}
		}
		return $out;
	}

	function show(){
		$sql = 'SELECT * FROM job;';
		$result = $this->conn->query($sql);
		$out = array();	
		while($result && $row = $result->fetch_assoc()) {
			$out[] = $row;      		
		}
		return $out;
	}
}
?>
