<?php


namespace publin\src;

/**
 * Class CitationRepository
 *
 * @package publin\src
 */
class CitationRepository extends Repository {


	/**
	 * @return $this
	 */
	public function select() {

		$this->select = 'SELECT self.*';
		$this->from = 'FROM `citations` self';
		
		return $this;
	}


	/**
	 * @return Citation[]
	 */
	public function find() {

		$result = parent::find();
		$citations = array();

		foreach ($result as $row) {
			$citation = new Citation($row);
			
			$repo = new PublicationRepository($this->db);
			$citation->setCitationPublication($repo->select()->where('id', '=', $citation->getCitationId())->findSingle());
			
			$citations[] = $citation;

		}

		return $citations;
	}

	/**
	 * @return Citation|false
	 */
/*	public function findSingle() {

		if ($result = parent::findSingle()) {
			$citation = new Citation($result);
			
			$repo = new PublicationRepository($this->db);
			$citation->setCitationPublication($repo->select()->where('publication_id', '=', $citation->getCitation())->findSingle());
			
			return $citation;
		}
		else {
			return false;
		}
	}*/
}
