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
use publin\src\KeywordRepository;
use publin\src\PDODatabase;
use publin\src\Publication;
use publin\src\PublicationRepository;
use publin\src\Request;
use UnexpectedValueException;

class OAIParser {

	private $use_stylesheet = true;
	private $db;
	private $valid_days = 1;
	private $sets_per_request = 10;
	private $records_per_request = 5;
	private $repositoryName = 'publin Uni Luebeck';
	private $repositoryIdentifier = 'de.localhost';
	private $baseURL = 'http://localhost/publin/oai/';
	private $adminEmail = array('test@localhost', 'test@web.de');
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

		$this->db = new PDODatabase();
		mb_internal_encoding('utf8');
	}


	public function run(Request $request) {

		try {
			$this->cleanResumptionTokens();

			switch ($request->get('verb')) {
				case 'Identify':
					$xml = $this->identify();
					break;

				case 'ListMetadataFormats':
					$xml = $this->listMetadataFormats();
					break;

				case 'ListSets':
					$xml = $this->listSets($request->get('resumptionToken'));
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


	private function cleanResumptionTokens() {

		$query = 'DELETE FROM `oai_tokens` WHERE `created` < NOW() - INTERVAL '.$this->valid_days.' DAY;';

		return $this->db->executeAndReturnAffectedRows($query);
	}


	public function identify() {

		$xml = new DOMDocument('1.0', 'UTF-8');

		$identify = $xml->createElement('Identify');
		$identify->appendChild($xml->createElement('repositoryName', $this->repositoryName));
		$identify->appendChild($xml->createElement('baseURL', $this->baseURL));
		$identify->appendChild($xml->createElement('protocolVersion', '2.0'));
		foreach ($this->adminEmail as $adminEmail) {
			$identify->appendChild($xml->createElement('adminEmail', $adminEmail));
		}
		$identify->appendChild($xml->createElement('earliestDatestamp', $this->getEarliestDatestamp()));
		$identify->appendChild($xml->createElement('deletedRecord', 'no'));
		$identify->appendChild($xml->createElement('granularity', 'YYYY-MM-DD'));
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
		$oai_identifier->appendChild($xml->createElement('sampleIdentifier', 'oai:'.$this->repositoryIdentifier.':42'));

		return $this->createResponse(array('verb' => 'Identify'), $identify);
	}


	private function getEarliestDatestamp() {

		$query = 'SELECT MIN(`date_added`) FROM `publications`;';
		$this->db->query($query);
		$oldest_timestamp = $this->db->fetchColumn();

		return date('Y-m-d', strtotime($oldest_timestamp));
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


	public function listSets($resumptionToken = '') {

		if ($resumptionToken) {
			$token = $this->fetchResumptionToken($resumptionToken);
			if (!$token) {
				throw new BadResumptionTokenException;
			}
			$cursor = $token['cursor'];
			$completeListSize = $token['completeListSize'];
		}
		else {
			$cursor = 0;
			$completeListSize = $this->countSets();
		}

		$xml = new DOMDocument('1.0', 'UTF-8');

		$listSets = $xml->createElement('ListSets');
		$sets = $this->fetchSets();
		foreach ($sets as $data) {
			$set = $xml->createElement('set');
			$set->appendChild($xml->createElement('setSpec', $data['setSpec']));
			$set->appendChild($xml->createElement('setName', $data['setName']));
			$listSets->appendChild($set);
		}

		$newCursor = $cursor + $this->sets_per_request;
		if ($newCursor < $completeListSize) {
			$newToken = $this->storeResumptionToken(null, null, null, null, $newCursor, $completeListSize);
			$resumptionToken_element = $xml->createElement('resumptionToken', $newToken);
			$resumptionToken_element->setAttribute('cursor', $newCursor);
			$resumptionToken_element->setAttribute('completeListSize', $completeListSize);
			$listSets->appendChild($resumptionToken_element);
		}

		return $this->createResponse(array('verb' => 'ListSets'), $listSets);
	}


	private function fetchResumptionToken($token) {

		$this->db->prepare('SELECT `metadata_prefix`, `from`, `until`, `set`, `cursor`, `list_size` FROM `oai_tokens` WHERE `id`=:token LIMIT 0,1;');
		$this->db->bindValue(':token', $token);
		$this->db->execute();

		$result = $this->db->fetchSingle();
		if ($result !== false) {
			return array(
				'metadataPrefix'   => $result['metadata_prefix'],
				'from'             => $result['from'],
				'until'            => $result['until'],
				'set'              => $result['set'],
				'cursor'           => $result['cursor'],
				'completeListSize' => $result['list_size'],
			);
		}
		else {
			return false;
		}
	}


	private function countSets() {

		$repo = new KeywordRepository($this->db);

		return $repo->select()->order('name', 'ASC')->count();
	}


	private function fetchSets($offset = 0) {

		$repo = new KeywordRepository($this->db);
		$keywords = $repo->select()->order('name', 'ASC')->limit($this->sets_per_request, $offset)->find();
		$sets = array();

		foreach ($keywords as $keyword) {
			$sets[] = array('setSpec' => $keyword->getName(),
							'setName' => $keyword->getName());
		}

		return $sets;
	}


	private function storeResumptionToken($metadataPrefix, $from, $until, $set, $cursor, $completeListSize) {

		$this->db->prepare('INSERT INTO `oai_tokens` (`metadata_prefix`, `from`, `until`, `set`, `cursor`, `list_size`) VALUES (:metadata, :from, :until, :set, :cursor, :size)');
		$this->db->bindValue(':metadata', $metadataPrefix);
		$this->db->bindValue(':from', $from);
		$this->db->bindValue(':until', $until);
		$this->db->bindValue(':set', $set);
		$this->db->bindValue(':cursor', $cursor);
		$this->db->bindValue(':size', $completeListSize);
		$this->db->execute();

		return $this->db->lastInsertId();
	}


	public function listIdentifiers($metadataPrefix, $from = null, $until = null, $set = null, $resumptionToken = null) {

		if ($resumptionToken) {
			$token = $this->fetchResumptionToken($resumptionToken);
			if (!$token) {
				throw new BadResumptionTokenException;
			}
			$metadataPrefix = $token['metadataPrefix'];
			$from = $token['from'];
			$until = $token['until'];
			$set = $token['set'];
			$cursor = $token['cursor'];
			$completeListSize = $token['completeListSize'];
		}
		else {
			if (!$metadataPrefix) {
				throw new BadArgumentException;
			}
			if (!array_key_exists($metadataPrefix, $this->metadataFormats)) {
				throw new CannotDisseminateFormatException;
			}

			$cursor = 0;
			$completeListSize = $this->countRecords($from, $until, $set);
		}

		$publications = $this->fetchRecords($from, $until, $set, $cursor);

		$xml = new DOMDocument('1.0', 'UTF-8');

		$listIdentifiers = $xml->createElement('ListIdentifiers');

		foreach ($publications as $publication) {
			$listIdentifiers->appendChild($xml->importNode($this->createRecordHeader($publication), true));
		}

		$newCursor = $cursor + $this->records_per_request;
		if ($newCursor < $completeListSize) {
			$newToken = $this->storeResumptionToken($metadataPrefix, $from, $until, $set, $newCursor, $completeListSize);
			$resumptionToken_element = $xml->createElement('resumptionToken', $newToken);
			$resumptionToken_element->setAttribute('cursor', $newCursor);
			$resumptionToken_element->setAttribute('completeListSize', $completeListSize);
			$listIdentifiers->appendChild($resumptionToken_element);
		}

		$request = array('verb'            => 'ListIdentifiers',
						 'metadataPrefix'  => $metadataPrefix,
						 'from'            => $from,
						 'until'           => $until,
						 'set'             => $set,
						 'resumptionToken' => $resumptionToken);

		return $this->createResponse($request, $listIdentifiers);
	}


	private function countRecords($from = null, $until = null, $set = null) {

		$repo = new PublicationRepository($this->db);
		$repo->select();
		if ($from) {
			$repo->where('date_added', '>=', $from.' 00:00:00');
		}
		if ($until) {
			$repo->where('date_added', '<=', $until.' 23:59:59');
		}
		if ($set) {
			//$repo->where();
		}

		return $repo->count();
	}


	private function fetchRecords($from = null, $until = null, $set = null, $offset = 0) {

		$repo = new PublicationRepository($this->db);
		$repo->select();
		if ($from) {
			$repo->where('date_added', '>=', $from.' 00:00:00');
		}
		if ($until) {
			$repo->where('date_added', '<=', $until.' 23:59:59');
		}
		if ($set) {
			//$repo->where();
		}
		$repo->order('date_added', 'DESC');
		$repo->limit($this->records_per_request, (int)$offset);

		return $repo->find();
	}


	private function createRecordHeader(Publication $publication) {

		$xml = new DOMDocument('1.0', 'UTF-8');
		$header = $xml->createElement('header');
		$header->appendChild($xml->createElement('identifier', $publication->getId()));
		$header->appendChild($xml->createElement('datestamp', $publication->getDateAdded('Y-m-d')));

		$keywords = $publication->getKeywords();
		foreach ($keywords as $keyword) {
			$header->appendChild($xml->createElement('setSpec', $keyword->getName()));
		}

		return $header;
	}


	public function listRecords($metadataPrefix, $from, $until, $set, $resumptionToken) {

		if ($resumptionToken) {
			$token = $this->fetchResumptionToken($resumptionToken);
			if (!$token) {
				throw new BadResumptionTokenException;
			}
			$metadataPrefix = $token['metadataPrefix'];
			$from = $token['from'];
			$until = $token['until'];
			$set = $token['set'];
			$cursor = $token['cursor'];
			$completeListSize = $token['completeListSize'];
		}
		else {
			if (!$metadataPrefix) {
				throw new BadArgumentException;
			}
			if (!array_key_exists($metadataPrefix, $this->metadataFormats)) {
				throw new CannotDisseminateFormatException;
			}

			$cursor = 0;
			$completeListSize = $this->countRecords($from, $until, $set);
		}

		$publications = $this->fetchRecords($from, $until, $set, $cursor);

		$xml = new DOMDocument('1.0', 'UTF-8');

		$listRecords = $xml->createElement('ListRecords');

		foreach ($publications as $publication) {
			$listRecords->appendChild($xml->importNode($this->createRecord($publication, $metadataPrefix), true));
		}

		$newCursor = $cursor + $this->records_per_request;
		if ($newCursor < $completeListSize) {
			$newToken = $this->storeResumptionToken($metadataPrefix, $from, $until, $set, $newCursor, $completeListSize);
			$resumptionToken_element = $xml->createElement('resumptionToken', $newToken);
			$resumptionToken_element->setAttribute('cursor', $newCursor);
			$resumptionToken_element->setAttribute('completeListSize', $completeListSize);
			$listRecords->appendChild($resumptionToken_element);
		}

		$request = array('verb'            => 'ListRecords',
						 'metadataPrefix'  => $metadataPrefix,
						 'from'            => $from,
						 'until'           => $until,
						 'set'             => $set,
						 'resumptionToken' => $resumptionToken);

		return $this->createResponse($request, $listRecords);
	}


	private function createRecord(Publication $publication, $metadataPrefix) {

		$xml = new DOMDocument('1.0', 'UTF-8');

		$record = $xml->createElement('record');
		$record->appendChild($xml->importNode($this->createRecordHeader($publication), true));
		$record->appendChild($xml->importNode($this->createRecordMetadata($publication, $metadataPrefix), true));

		return $record;
	}


	private function createRecordMetadata(Publication $publication, $metadataPrefix) {

		$xml = new DOMDocument('1.0', 'UTF-8');
		$metadata = $xml->createElement('metadata');
		$oai_dc = $xml->createElement('oai_dc:dc');
		$oai_dc->setAttribute('xmlns:oai_dc', 'http://www.openarchives.org/OAI/2.0/oai_dc/');
		$oai_dc->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1');
		$oai_dc->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$oai_dc->setAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd');
		$metadata->appendChild($oai_dc);

		$fields = $this->createRecordMetadataFields($publication, $metadataPrefix);
		foreach ($fields as $field) {
			if ($field[1]) {
				$element = $xml->createElement($field[0]);
				$element->appendChild($xml->createTextNode($field[1]));
				$oai_dc->appendChild($element);
			}
		}

		return $metadata;
	}


	private function createRecordMetadataFields(Publication $publication, $metadataPrefix) {

		if ($metadataPrefix == 'oai_dc') {
			$fields = array();
			$fields[] = array('dc:type', 'Text');
			$fields[] = array('dc:title', $publication->getTitle());
			foreach ($publication->getAuthors() as $author) {
				if ($author->getLastName() && $author->getFirstName()) {
					$fields[] = array('dc:creator', $author->getLastName().', '.$author->getFirstName(true));
				}
			}
			$fields[] = array('dc:description', $publication->getAbstract());
			$fields[] = array('dc:date', $publication->getDatePublished('Y'));
			//$fields[] = array('dcterms:bibliographicCitation', false); // TODO
			$fields[] = array('dc:publisher', $publication->getPublisher());
			$fields[] = array('dc:identifier', $publication->getDoi());
			$fields[] = array('dc:rights', $publication->getCopyright());

			return $fields;
		}
		else {
			throw new UnexpectedValueException('Implementation for '.$metadataPrefix.' missing!');
		}
	}


	public function getRecord($identifier, $metadataPrefix) {

		if (!$identifier || !$metadataPrefix) {
			throw new BadArgumentException;
		}

		if (!array_key_exists($metadataPrefix, $this->metadataFormats)) {
			throw new CannotDisseminateFormatException;
		}

		$repo = new PublicationRepository($this->db);
		$publications = $repo->select()->where('id', '=', $identifier)->find();

		if (count($publications) == 0) {
			throw new IdDoesNotExistException;
		}
		$publication = $publications[0];

		$xml = new DOMDocument('1.0', 'UTF-8');

		$request = array('verb'           => 'GetRecord',
						 'identifier'     => $identifier,
						 'metadataPrefix' => $metadataPrefix);

		$getRecord = $xml->createElement('GetRecord');
		$getRecord->appendChild($xml->importNode($this->createRecord($publication, $metadataPrefix), true));

		return $this->createResponse($request, $getRecord);
	}


	private function createErrorResponse($error_type) {

		$xml = new DOMDocument('1.0', 'UTF-8');

		$error = $xml->createElement('error');
		$error->setAttribute('code', $error_type);

		return $this->createResponse(array(), $error);
	}
}
