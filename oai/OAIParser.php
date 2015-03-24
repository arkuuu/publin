<?php


namespace publin\oai;

use DOMDocument;
use DOMElement;
use publin\src\Database;
use publin\src\Publication;
use publin\src\PublicationModel;
use publin\src\Request;

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

		$this->db = new Database(false);
	}


	public function run(Request $request) {

		switch ($request->get('verb')) {
			case 'Identify':
				$dom = $this->identify();
				break;

			case 'ListMetadataFormats':
				$dom = $this->listMetadataFormats();
				break;

			case 'ListSets':
				$dom = $this->listSets();
				break;

			case 'ListIdentifiers':
				$dom = $this->listIdentifiers();
				break;

			case 'ListRecords':
				$dom = $this->listRecords($request);
				break;

			case 'GetRecord':
				$dom = $this->getRecord($request);
				break;

			default:
				$dom = $this->badVerb();
				break;
		}

		return $dom->saveXML();
	}


	public function identify() {

		$dom = new DOMDocument('1.0', 'UTF-8');

		$identify = $dom->createElement('Identify');
		$identify->appendChild($dom->createElement('repositoryName', $this->repositoryName));
		$identify->appendChild($dom->createElement('baseURL', $this->baseURL));
		$identify->appendChild($dom->createElement('protocolVersion', $this->protocolVersion));
		foreach ($this->adminEmail as $adminEmail) {
			$identify->appendChild($dom->createElement('adminEmail', $adminEmail));
		}
		$identify->appendChild($dom->createElement('earliestDatestamp', $this->earliestDatestamp));
		$identify->appendChild($dom->createElement('deletedRecord', $this->deletedRecord));
		$identify->appendChild($dom->createElement('granularity', $this->granularity));
		//$identify->appendChild($dom->createElement('compression', '')); //optional

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

		return $this->createResponse(array('verb' => 'Identify'), $identify);
	}


	private function createResponse(array $request_parameters, DOMElement $content) {

		$dom = new DOMDocument('1.0', 'UTF-8');
		$oai = $dom->createElement('OAI-PMH');
		$oai->setAttribute('xmlns', 'http://www.openarchives.org/OAI/2.0/');
		$oai->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$oai->setAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd');
		$dom->appendChild($oai);
		$oai->appendChild($dom->createElement('responseDate', date('Y-m-d')));

		$request = $dom->createElement('request', $this->baseURL);
		foreach ($request_parameters as $name => $value) {
			if (!empty($value)) {
				$request->setAttribute($name, $value);
			}
		}

		$oai->appendChild($request);
		$oai->appendChild($dom->importNode($content, true));

		return $dom;
	}


	public function listMetadataFormats() {

		$dom = new DOMDocument('1.0', 'UTF-8');

		$listMetadataFormats = $dom->createElement('ListMetadataFormats');

		foreach ($this->metadataFormats as $data) {
			$metadataFormat = $dom->createElement('metadataFormat');
			$metadataFormat->appendChild($dom->createElement('metadataPrefix', $data['metadataPrefix']));
			$metadataFormat->appendChild($dom->createElement('schema', $data['schema']));
			$metadataFormat->appendChild($dom->createElement('metadataNamespace', $data['metadataNamespace']));
			$listMetadataFormats->appendChild($metadataFormat);
		}

		return $this->createResponse(array('verb' => 'ListMetadataFormats'), $listMetadataFormats);
	}


	public function listSets() {

		$sets = array(
			array('setSpec' => 'todo',
				  'setName' => 'name'),
		); //TODO: replace with database?

		$dom = new DOMDocument('1.0', 'UTF-8');

		$listSets = $dom->createElement('ListSets');
		foreach ($sets as $data) {
			$set = $dom->createElement('set');
			$set->appendChild($dom->createElement('setSpec', $data['setSpec']));
			$set->appendChild($dom->createElement('setName', $data['setName']));
			$listSets->appendChild($set);
		}

		return $this->createResponse(array('verb' => 'ListSets'), $listSets);
	}


	public function listIdentifiers() {

		$dom = new DOMDocument('1.0', 'UTF-8');

		$listIdentifiers = $dom->createElement('ListIdentifiers');

		$header = $dom->createElement('header');
		$listIdentifiers->appendChild($header);

		$resumptionToken = $dom->createElement('resumptionToken');
		$listIdentifiers->appendChild($resumptionToken);

		return $this->createResponse(array('verb' => 'ListIdentifiers'), $listIdentifiers);
	}


	public function listRecords(Request $request) {

		if ($request->get('metadataPrefix')) {
			if (!array_key_exists($request->get('metadataPrefix'), $this->metadataFormats)) {
				return $this->cannotDisseminateFormat();
			}
		}
		else if ($request->get('resumptionToken')) {
			// TODO;
		}
		else {
			return $this->badArgument();
		}

		$dom = new DOMDocument('1.0', 'UTF-8');

		$listRecords = $dom->createElement('ListRecords');

		$record = $dom->createElement('record');
		$listRecords->appendChild($record);

		$resumptionToken = $dom->createElement('resumptionToken');
		$listRecords->appendChild($resumptionToken);

		$parameters = array('verb'            => 'ListRecords',
							'metadataPrefix'  => $request->get('metadataPrefix'),
							'resumptionToken' => $request->get('resumptionToken'));

		return $this->createResponse($parameters, $listRecords);
	}


	public function cannotDisseminateFormat() {

		$dom = $this->createErrorResponse('cannotDisseminateFormat');

		return $dom;
	}


	private function createErrorResponse($error_type) {

		$dom = new DOMDocument('1.0', 'UTF-8');

		$error = $dom->createElement('error');
		$error->setAttribute('code', $error_type);

		return $this->createResponse(array(), $error);
	}


	public function badArgument() {

		return $this->createErrorResponse('badArgument');
	}


	public function getRecord(Request $request) {

		if (!$request->get('identifier') || !$request->get('metadataPrefix')) {
			return $this->badArgument();
		}

		if (!array_key_exists($request->get('metadataPrefix'), $this->metadataFormats)) {
			return $this->cannotDisseminateFormat();
		}

		$db = new Database();
		$model = new PublicationModel($db);
		$publication = $model->fetch(true, array('id' => $request->get('identifier')));

		if (count($publication) == 0) {
			return $this->idDoesNotExist();
		}
		$publication = $publication[0];

		$dom = new DOMDocument('1.0', 'UTF-8');

		$parameters = array('verb'           => 'GetRecord',
							'identifier'     => $request->get('identifier'),
							'metadataPrefix' => $request->get('metadataPrefix'));

		$get_record = $dom->createElement('GetRecord');
		$get_record->appendChild($dom->importNode($this->export($publication), true));

		return $this->createResponse($parameters, $get_record);
	}


	public function idDoesNotExist() {

		return $this->createErrorResponse('idDoesNotExist');
	}


	private function export(Publication $publication) {

		$dom = new DOMDocument('1.0', 'utf-8');
		$record = $dom->createElement('record');
		$header = $record->appendChild($dom->createElement('header'));
		$header->appendChild($dom->createElement('identifier', $publication->getId()));
		$header->appendChild($dom->createElement('datestamp', $publication->getDatePublished('Y-m-d')));
		$header->appendChild($dom->createElement('setSpec', 'TODO'));

		$metadata = $record->appendChild($dom->createElement('metadata'));
		$oai_dc = $dom->createElement('oai_dc:dc');
		$oai_dc->setAttribute('xmlns:oai_dc', 'http://www.openarchives.org/OAI/2.0/oai_dc/');
		$oai_dc->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1');
		$oai_dc->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$oai_dc->setAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd');
		$metadata->appendChild($oai_dc);

		$fields = array();
		$fields[] = array('dc:type', 'Text');
		$fields[] = array('dc:title', $publication->getTitle());
		foreach ($publication->getAuthors() as $author) {
			if ($author->getLastName() && $author->getFirstName()) {
				$fields[] = array('dc:creator', $author->getLastName().', '.$author->getFirstName(true));
			}
		}
		//$fields[] = array('dcterms:issued', $publication->getDatePublished('Y-m-d'));
		//$fields[] = array('dcterms:bibliographicCitation', false); // TODO
		$fields[] = array('dc:publisher', $publication->getPublisher());
		$fields[] = array('dc:identifier', $publication->getDoi());

		foreach ($fields as $field) {
			if ($field[1]) {
				$oai_dc->appendChild($dom->createElement($field[0], $field[1]));
			}
		}

		return $record;
	}


	public function badVerb() {

		return $this->createErrorResponse('badVerb');
	}


	public function noMetadataFormats() {

		return $this->createErrorResponse('noMetadataFormats');
	}


	public function noRecordsMatch() {

		return $this->createErrorResponse('noRecordsMatch');
	}


	public function badResumptionToken() {

		return $this->createErrorResponse('badResumptionToken');
	}


	public function noSetHierarchy() {

		return $this->createErrorResponse('noSetHierarchy');
	}
}
