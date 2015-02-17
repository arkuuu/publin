<?php

namespace publin\src;

interface ModelInterface {

	public function fetch(array $filter = array());


	// TODO: problem here, can't use Objects, but array won't be good for Publication
	public function store(array $data);


	public function update($id, array $data);


	public function delete($id);


	public function validate(array $data);
}
