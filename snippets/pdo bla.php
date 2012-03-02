<?php
	function test() {
		var_dump(App::get()->pdoConnection()->exec("create table test (name char(50))"));
		var_dump(App::get()->pdoConnection()->exec("insert into test (name) values ('Max Mustermann')"));
		var_dump(App::get()->pdoConnection()->query("select * from test")->fetchAll());
	}
		
