<?php


namespace publin\oai;

use DOMDocument;
use publin\src\Database;

class OAIParser {

	private $db;
	private $repositoryName = 'publin Uni Luebeck';
	private $repositoryIdentifier = 'de.localhost';
	private $baseURL = 'http://localhost/publin/oai/';
	private $protocolVersion = '2.0';
	private $earliestDatestamp = '2011-01-01'; // TODO: get from database
	private $deletedRecord = 'no';
	private $granularity = 'YYYY-MM-DD';
	private $adminEmail = array('test@localhost', 'test@web.de');
	//private $maxRecords = 10;
	private $metadataFormats = array(
		'oai_dc' => array(
			'metadataPrefix'    => 'oai_dc',
			'schema'            => 'http://www.openarchives.org/OAI/2.0/oai_dc.xsd',
			'metadataNamespace' => 'http://www.openarchives.org/OAI/2.0/oai_dc/',
			'record_prefix'     => 'dc',
			'record_namespace'  => 'http://purl.org/dc/elements/1.1/'
		)
	);


	public function __construct() {

		$this->db = new Database();
	}


	public function run($request) {

		if (isset($request['verb'])) {
			switch ($request['verb']) {
				case 'Identify':
					return $this->identify();
					break;

				case 'ListMetadataFormats':
					return $this->listMetadataFormats();
					break;

				case 'ListSets':
					return $this->listSets();
					break;

				case 'GetRecord':
					return $this->getRecord();
					break;

				default:
					return false;
					break;
			}
		}
		else {
			return false;
		}
	}


	public function identify() {

		$dom = new DOMDocument('1.0', 'utf-8');
		$oai = $dom->createElement('OAI-PMH');
		$oai->setAttribute('xmlns', 'http://www.openarchives.org/OAI/2.0/');
		$oai->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$oai->setAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd');
		$dom->appendChild($oai);

		$oai->appendChild($dom->createElement('responseDate', date('Y-m-d')));

		$request = $dom->createElement('request', $this->baseURL);
		$request->setAttribute('verb', 'Identify');
		$oai->appendChild($request);

		$identify = $dom->createElement('Identify');
		$oai->appendChild($identify);

		$identify->appendChild($dom->createElement('repositoryName', $this->repositoryName));
		$identify->appendChild($dom->createElement('baseURL', $this->baseURL));
		$identify->appendChild($dom->createElement('protocolVersion', $this->protocolVersion));
		foreach ($this->adminEmail as $adminEmail) {
			$identify->appendChild($dom->createElement('adminEmail', $adminEmail));
		}
		$identify->appendChild($dom->createElement('earliestDatestamp', $this->earliestDatestamp));
		$identify->appendChild($dom->createElement('deletedRecord', $this->deletedRecord));
		$identify->appendChild($dom->createElement('granularity', $this->granularity));

		$description = $dom->createElement('description');
		$identify->appendChild($description);

		$oai_identifier = $dom->createElement('oai-identifier');
		$oai_identifier->setAttribute('xsi:schemaLocation',
									  '<oai-identifier xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai-identifier http://www.openarchives.org/OAI/2.0/oai-identifier.xsd');
		$description->appendChild($oai_identifier);

		$oai_identifier->appendChild($dom->createElement('scheme', 'oai'));
		$oai_identifier->appendChild($dom->createElement('repositoryIdentifier', $this->repositoryIdentifier));
		$oai_identifier->appendChild($dom->createElement('delimiter', ':'));
		$oai_identifier->appendChild($dom->createElement('sampleIdentifier', 'oai:'.$this->repositoryIdentifier.':TODO'));

		return $dom->saveXML();
	}


	public function listMetadataFormats() {

		$dom = new DOMDocument('1.0', 'utf-8');
		$oai = $dom->createElement('OAI-PMH');
		$oai->setAttribute('xmlns', 'http://www.openarchives.org/OAI/2.0/');
		$oai->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$oai->setAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd');
		$dom->appendChild($oai);

		$oai->appendChild($dom->createElement('responseDate', date('Y-m-d')));

		$request = $dom->createElement('request', $this->baseURL);
		$request->setAttribute('verb', 'ListMetadataFormats');
		$oai->appendChild($request);

		$listMetadataFormats = $dom->createElement('ListMetadataFormats');
		$oai->appendChild($listMetadataFormats);

		foreach ($this->metadataFormats as $data) {
			$metadataFormat = $dom->createElement('metadataFormat');
			$listMetadataFormats->appendChild($metadataFormat);

			$metadataFormat->appendChild($dom->createElement('metadataPrefix', $data['metadataPrefix']));
			$metadataFormat->appendChild($dom->createElement('schema', $data['schema']));
			$metadataFormat->appendChild($dom->createElement('metadataNamespace', $data['metadataNamespace']));
		}

		return $dom->saveXML();
	}


	public function listSets() {

		$dom = new DOMDocument('1.0', 'utf-8');
		$oai = $dom->createElement('OAI-PMH');
		$oai->setAttribute('xmlns', 'http://www.openarchives.org/OAI/2.0/');
		$oai->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$oai->setAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd');
		$dom->appendChild($oai);

		$oai->appendChild($dom->createElement('responseDate', date('Y-m-d')));

		$request = $dom->createElement('request', $this->baseURL);
		$request->setAttribute('verb', 'ListSets');
		$oai->appendChild($request);

		$listSets = $dom->createElement('ListSets');
		$oai->appendChild($listSets);

		return $dom->saveXML();
	}


	public function getRecord() {
	}


	public function listIdentifiers() {
	}


	public function listRecords() {
	}


	public function createXML($request_verb) {
	}
}
