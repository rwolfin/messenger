<?php
// Database_connection.php

class Database_connection
{
	function connect()
	{
		$connect = new PDO("mysql:host=MySQL-8.0; dbname=messenger", "root", "1");

		return $connect;
	}
}

?>