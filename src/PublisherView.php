<?php


namespace publin\src;

class PublisherView extends ViewWithPublications {

	public function __construct(Publisher $publisher) {

		parent::__construct($publisher, 'publisher');
	}
}
