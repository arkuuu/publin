<?php


namespace publin\oai;

use DOMDocument;
use DOMElement;
use publin\oai\exceptions\BadArgumentException;
use publin\oai\exceptions\BadResumptionTokenException;
use publin\oai\exceptions\BadVerbException;
use publin\oai\exceptions\CannotDisseminateFormatException;
use publin\oai\exceptions\IdDoesNotExistException;
use publin\oai\exceptions\NoMetadataFormatsException;
use publin\oai\exceptions\NoRecordsMatchException;
use publin\oai\exceptions\NoSetHierarchyException;
use publin\src\Database;
use publin\src\Publication;
use publin\src\PublicationModel;
use publin\src\Request;

class OAIParser {

	private $use_stylesheet = false;
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
	}


	public function run(Request $request) {

		try {
			switch ($request->get('verb')) {
				case 'Identify':
					$xml = $this->identify();
					break;

				case 'ListMetadataFormats':
					$xml = $this->listMetadataFormats();
					break;

				case 'ListSets':
					$xml = $this->listSets();
					break;

				case 'ListIdentifiers':
					$xml = $this->listIdentifiers($request->get('metadataPrefix'),
												  $request->get('from'),
												  $request->get('until'),
												  $request->get('set'),
												  $request->get('resumptionToken'));
					break;

				case 'ListRecords':
					$xml = $this->listRecords($request->get('metadataPrefix'),
											  $request->get('from'),
											  $request->get('until'),
											  $request->get('set'),
											  $request->get('resumptionToken'));
					break;

				case 'GetRecord':
					$xml = $this->getRecord($request->get('identifier'),
											$request->get('metadataPrefix'));
					break;

				default:
					throw new BadVerbException;
					break;
			}
		}
		catch (BadArgumentException $e) {
			$xml = $this->createErrorResponse('badArgument');
		}
		catch (BadResumptionTokenException $e) {
			$xml = $this->createErrorResponse('badResumptionToken');
		}
		catch (BadVerbException $e) {
			$xml = $this->createErrorResponse('badVerb');
		}
		catch (CannotDisseminateFormatException $e) {
			$xml = $this->createErrorResponse('cannotDisseminateFormat');
		}
		catch (IdDoesNotExistException $e) {
			$xml = $this->createErrorResponse('idDoesNotExist');
		}
		catch (NoMetadataFormatsException $e) {
			$xml = $this->createErrorResponse('noMetadataFormats');
		}
		catch (NoRecordsMatchException $e) {
			$xml = $this->createErrorResponse('noRecordsMatch');
		}
		catch (NoSetHierarchyException $e) {
			$xml = $this->createErrorResponse('noSetHierarchy');
		}

		return $xml->saveXML();
	}


	public function identify() {

		$xml = new DOMDocument('1.0', 'UTF-8');

		$identify = $xml->createElement('Identify');
		$identify->appendChild($xml->createElement('repositoryName', $this->repositoryName));
		$identify->appendChild($xml->createElement('baseURL', $this->baseURL));
		$identify->appendChild($xml->createElement('protocolVersion', $this->protocolVersion));
		foreach ($this->adminEmail as $adminEmail) {
			$identify->appendChild($xml->createElement('adminEmail', $adminEmail));
		}
		$identify->appendChild($xml->createElement('earliestDatestamp', $this->earliestDatestamp));
		$identify->appendChild($xml->createElement('deletedRecord', $this->deletedRecord));
		$identify->appendChild($xml->createElement('granularity', $this->granularity));
		//$identify->appendChild($dom->createElement('compression', '')); //optional

		$description = $xml->createElement('description');
		$identify->appendChild($description);

		$oai_identifier = $xml->createElement('oai-identifier');
		$oai_identifier->setAttribute('xsi:schemaLocation',
									  'http://www.openarchives.org/OAI/2.0/oai-identifier http://www.openarchives.org/OAI/2.0/oai-identifier.xsd');
		$description->appendChild($oai_identifier);

		$oai_identifier->appendChild($xml->createElement('scheme', 'oai'));
		$oai_identifier->appendChild($xml->createElement('repositoryIdentifier', $this->repositoryIdentifier));
		$oai_identifier->appendChild($xml->createElement('delimiter', ':'));
		$oai_identifier->appendChild($xml->createElement('sampleIdentifier', 'oai:'.$this->repositoryIdentifier.':TODO'));

		return $this->createResponse(array('verb' => 'Identify'), $identify);
	}


	private function createResponse(array $request, DOMElement $content) {

		$xml = new DOMDocument('1.0', 'UTF-8');

		if ($this->use_stylesheet) {
			$xsl = $xml->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="oai2.xsl"');
			$xml->appendChild($xsl);
		}

		$oai = $xml->createElement('OAI-PMH');
		$oai->setAttribute('xmlns', 'http://www.openarchives.org/OAI/2.0/');
		$oai->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$oai->setAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd');
		$xml->appendChild($oai);
		$oai->appendChild($xml->createElement('responseDate', date('Y-m-d')));

		$request_element = $xml->createElement('request', $this->baseURL);
		foreach ($request as $name => $value) {
			if (!empty($value)) {
				$request_element->setAttribute($name, $value);
			}
		}

		$oai->appendChild($request_element);
		$oai->appendChild($xml->importNode($content, true));

		return $xml;
	}


	public function listMetadataFormats() {

		$xml = new DOMDocument('1.0', 'UTF-8');

		$listMetadataFormats = $xml->createElement('ListMetadataFormats');

		foreach ($this->metadataFormats as $data) {
			$metadataFormat = $xml->createElement('metadataFormat');
			$metadataFormat->appendChild($xml->createElement('metadataPrefix', $data['metadataPrefix']));
			$metadataFormat->appendChild($xml->createElement('schema', $data['schema']));
			$metadataFormat->appendChild($xml->createElement('metadataNamespace', $data['metadataNamespace']));
			$listMetadataFormats->appendChild($metadataFormat);
		}

		return $this->createResponse(array('verb' => 'ListMetadataFormats'), $listMetadataFormats);
	}


	public function listSets() {

		$sets = array(
			array('setSpec' => 'todo',
				  'setName' => 'name'),
		); //TODO: replace with database?

		$xml = new DOMDocument('1.0', 'UTF-8');

		$listSets = $xml->createElement('ListSets');
		foreach ($sets as $data) {
			$set = $xml->createElement('set');
			$set->appendChild($xml->createElement('setSpec', $data['setSpec']));
			$set->appendChild($xml->createElement('setName', $data['setName']));
			$listSets->appendChild($set);
		}

		return $this->createResponse(array('verb' => 'ListSets'), $listSets);
	}


	public function listIdentifiers($metadataPrefix, $from = '', $until = '', $set = '', $resumptionToken = '') {

		if ($resumptionToken) {
			// TODO assign parameters from token db
			$metadataPrefix = 'oai_dc';
			$from = '';
			$until = '';
			$set = '';
		}

		if (!$metadataPrefix) {
			throw new BadArgumentException;
		}
		if (!array_key_exists($metadataPrefix, $this->metadataFormats)) {
			throw new CannotDisseminateFormatException;
		}

		$publications = $this->fetchRecords($from, $until, $set);

		$xml = new DOMDocument('1.0', 'UTF-8');

		$listIdentifiers = $xml->createElement('ListIdentifiers');

		foreach ($publications as $publication) {
			$header = $xml->createElement('header');
			$header->appendChild($xml->createElement('identifier', $publication->getId()));
			$header->appendChild($xml->createElement('datestamp', $publication->getDateAdded('Y-m-d')));
			$header->appendChild($xml->createElement('setSpec', $publication->getStudyField()));
			$listIdentifiers->appendChild($header);
		}

		$resumptionToken_element = $xml->createElement('resumptionToken');
		$listIdentifiers->appendChild($resumptionToken_element);

		$request = array('verb'            => 'ListIdentifiers',
						 'metadataPrefix'  => $metadataPrefix,
						 'from'            => $from,
						 'until'           => $until,
						 'set'             => $set,
						 'resumptionToken' => $resumptionToken);

		return $this->createResponse($request, $listIdentifiers);
	}


	private function fetchRecords($from, $until, $set, $limit = 0) {

		$db = new Database();
		$model = new PublicationModel($db);

		return $model->fetch(false);
	}


	public function listRecords($metadataPrefix, $from, $until, $set, $resumptionToken) {

		if ($resumptionToken) {
			// TODO assign parameters from token db
			$metadataPrefix = 'oai_dc';
			$from = '';
			$until = '';
			$set = '';
		}

		if (!$metadataPrefix) {
			throw new BadArgumentException;
		}
		if (!array_key_exists($metadataPrefix, $this->metadataFormats)) {
			throw new CannotDisseminateFormatException;
		}

		$request = array('verb'            => 'ListRecords',
						 'metadataPrefix'  => $metadataPrefix,
						 'from'            => $from,
						 'until'           => $until,
						 'set'             => $set,
						 'resumptionToken' => $resumptionToken);

		$xml = new DOMDocument('1.0', 'UTF-8');

		$listRecords = $xml->createElement('ListRecords');

		$publications = $this->fetchRecords($from, $until, $set);
		foreach ($publications as $publication) {
			$listRecords->appendChild($xml->importNode($this->createRecord($publication), true));
		}

		$NewResumptionToken = $xml->createElement('resumptionToken', '123');
		$listRecords->appendChild($NewResumptionToken);

		return $this->createResponse($request, $listRecords);
	}


	private function createRecord(Publication $publication) {

		$xml = new DOMDocument('1.0', 'UTF-8');

		$record = $xml->createElement('record');
		$record->appendChild($xml->importNode($this->createRecordHeader($publication), true));
		$record->appendChild($xml->importNode($this->createRecordMetadata($publication), true));

		return $record;
	}


	private function createRecordHeader(Publication $publication) {

		$xml = new DOMDocument('1.0', 'UTF-8');
		$header = $xml->createElement('header');
		$header->appendChild($xml->createElement('identifier', $publication->getId()));
		$header->appendChild($xml->createElement('datestamp', $publication->getDateAdded('Y-m-d')));
		$header->appendChild($xml->createElement('setSpec', $publication->getStudyField()));

		return $header;
	}


	private function createRecordMetadata(Publication $publication) {

		$xml = new DOMDocument('1.0', 'UTF-8');
		$metadata = $xml->createElement('metadata');
		$oai_dc = $xml->createElement('oai_dc:dc');
		$oai_dc->setAttribute('xmlns:oai_dc', 'http://www.openarchives.org/OAI/2.0/oai_dc/');
		$oai_dc->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1');
		$oai_dc->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$oai_dc->setAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd');
		$metadata->appendChild($oai_dc);

		$fields = $this->createRecordMetadataFields($publication);
		foreach ($fields as $field) {
			if ($field[1]) {
				$element = $xml->createElement($field[0]);
				$element->appendChild($xml->createTextNode($field[1]));
				$oai_dc->appendChild($element);
			}
		}

		return $metadata;
	}


	private function createRecordMetadataFields(Publication $publication) {

		$fields = array();
		$fields[] = array('dc:type', 'Text');
		$fields[] = array('dc:title', $publication->getTitle());
		foreach ($publication->getAuthors() as $author) {
			if ($author->getLastName() && $author->getFirstName()) {
				$fields[] = array('dc:creator', $author->getLastName().', '.$author->getFirstName(true));
			}
		}
		$fields[] = array('dc:description', $publication->getAbstract()); //TODO: check if correct
		$fields[] = array('dc:date', $publication->getDatePublished('Y')); //TODO: check if correct
		//$fields[] = array('dcterms:bibliographicCitation', false); // TODO
		$fields[] = array('dc:publisher', $publication->getPublisher());
		$fields[] = array('dc:identifier', $publication->getDoi());

		return $fields;
	}


	public function getRecord($identifier, $metadataPrefix) {

		if (!$identifier || !$metadataPrefix) {
			throw new BadArgumentException;
		}

		if (!array_key_exists($metadataPrefix, $this->metadataFormats)) {
			throw new CannotDisseminateFormatException;
		}

		$db = new Database();
		$model = new PublicationModel($db);
		$publication = $model->fetch(true, array('id' => $identifier));

		if (count($publication) == 0) {
			throw new IdDoesNotExistException;
		}
		$publication = $publication[0];

		$xml = new DOMDocument('1.0', 'UTF-8');

		$request = array('verb'           => 'GetRecord',
						 'identifier'     => $identifier,
						 'metadataPrefix' => $metadataPrefix);

		$getRecord = $xml->createElement('GetRecord');
		$getRecord->appendChild($xml->importNode($this->createRecord($publication), true));

		return $this->createResponse($request, $getRecord);
	}


	private function createErrorResponse($error_type) {

		$xml = new DOMDocument('1.0', 'UTF-8');

		$error = $xml->createElement('error');
		$error->setAttribute('code', $error_type);

		return $this->createResponse(array(), $error);
	}


	private function createResumptionToken($metadataPrefix, $from, $until, $set, $limit) {
		// TODO store to db
	}


	private function fetchResumptionToken($resumptionToken) {

		// TODO fetch this from db
		return array(
			'metadataPrefix' => 'oai_dc',
			'from'           => '',
			'until'          => '',
			'set'            => '',
			'limit'          => 0,
		);
	}


	private function createSet() {
	}
}
