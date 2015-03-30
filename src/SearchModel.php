<?php


namespace publin\src;

class SearchModel {

	private $db;


	public function __construct(Database $db) {

		$this->db = $db;
	}
}
