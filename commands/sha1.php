<?php
class sha1 extends command {

	public function __construct(){
		$this->name = 'sha1';
		$this->public = true;
		$this->server = 'irc.freenode.net';
		$this->usesSQL = true;
		$this->tablenames = array('hashes');
		$this->sql = array('create table hashes(string TEXT NOT NULL,md5 TEXT NOT NULL,sha1 TEXT NOT NULL, primary key(string))');
	}

	public function help(){
		return 'Uso: !sha1 <cadena>. Devuelve el hash sha1 de la cadena.';
	}

	public function process($args){
		$this->output = "El sha1 de la cadena '{$args}' es '" . sha1($args) . "'";
		$this->save($args);
	}

	protected function save($args){
		//store the sha1 into a database;
		global $dbh;

		$sql = "select count(1) as count from hashes where string = :string";
		$r = $dbh->prepare($sql);
		$r->bindParam("string", $args);
		$r->Execute();
		$row = $r->fetch();
		if ( $row['count'] == 0 ){
			$md5 = md5($args);
			$sha1 = sha1($args);
			$sql = "insert into hashes ( string , md5, sha1) values (:string, :md5, :sha1)";
			$r = $dbh->prepare($sql);
			$r->bindParam("string", $args);
			$r->bindParam("md5", $md5);
			$r->bindParam("sha1", $sha1);
			$r->Execute();
		}
	}

}
