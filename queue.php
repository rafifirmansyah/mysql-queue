<?php
class Queue
{
	$conn = null;

	public void __construct($servername, $username, $password, $dbname){
		$this->conn = new mysqli($servername, $username, $password, $dbname);
		if ($conn->connect_error) {
			throw("Connection failed: " . $conn->connect_error);
		}
	}

	public void push($payload){
		$sql = 'SELECT pushJob(' . $conn->escape_string($payload) . ') as pushResult;';
		$result = $conn->query($sql);
		if($row = $result->fetch_assoc()) {
        		if($row['pushResult'] > 0){
				return $row['pushResult'];
			}
			throw('Could not push job, could not create!');
		}
		throw('Could not push job, query failed!');
	}

	public void pop($clientId){
		$sql = 'CALL popJob(' . $conn->escape_string($clientId) . ');';
		$result = $conn->query($sql);
		$out = array();	
		while($row = $result->fetch_assoc()) {
			$out[] = $row;      		
			if(!empty($row['error'])){
				throw('popJob failed: ' . $row['error']);
			}
		}
		return $out;
	}
}
?>
