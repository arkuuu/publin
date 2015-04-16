<?php


namespace publin\src;

class File extends Entity {

	protected $id;
	protected $name;
	protected $title;
	protected $full_text;
	protected $restricted;
	protected $hidden;


	public function getId() {

		return $this->id;
	}


	public function getName() {

		return $this->name;
	}


	public function getTitle() {

		return $this->title;
	}


	public function isFullText() {

		if ($this->full_text) {
			return true;
		}
		else {
			return false;
		}
	}


	public function isRestricted() {

		if ($this->restricted) {
			return true;
		}
		else {
			return false;
		}
	}


	public function isHidden() {

		if ($this->hidden) {
			return true;
		}
		else {
			return false;
		}
	}
}
