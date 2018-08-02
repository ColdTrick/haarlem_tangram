<?php

class TangramVacancy extends ElggObject {
	
	const SUBTYPE = 'tangram_vacancy';
	
	protected $xml_source;
	
	/**
	 * (non-PHPdoc)
	 * @see ElggObject::initializeAttributes()
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		
		$this->attributes['subtype'] = self::SUBTYPE;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function save() {
		// for now can't be saved
		return false;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getURL() {
		
		$vacaturenummer = $this->getVacatureNummer();
		if ($vacaturenummer === false) {
			return false;
		}
		
		return elgg_normalize_url("vacaturebank/view/{$vacaturenummer}");
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getIconURL($size = 'medium') {
		$icon_url = '_graphics/spacer.gif';
		
		$xml = $this->getXmlSource();
		if (empty($xml)) {
			return $icon_url;
		}
		
		switch ($size) {
			case 'tiny':
			case 'small':
			case 'medium':
				$logo_url = $this->getXMLPath(array(
					'Groepspecifiek',
					'LOGOURL',
				));
				if (empty($logo_url)) {
					break;
				}
				
				$icon_url = (string) $logo_url;
				break;
			default:
				$logo_url = $this->getXMLPath(array(
					'Groepspecifiek',
					'LOGO2URL',
				));
				if (empty($logo_url)) {
					break;
				}
				
				$icon_url = (string) $logo_url;
				break;
		}
		
		return $icon_url;
	}
	
	/**
	 * Get the vacatuenummer (unique ID) from the XML
	 *
	 * @return false|string
	 */
	public function getVacatureNummer() {
		
		if (isset($this->vacaturenummer)) {
			return $this->vacaturenummer;
		} elseif (!empty($this->xml_source)) {
			$xml = $this->getXmlSource();
			
			$vacaturenummer = $xml->Vacaturenummer;
			if (!empty($vacaturenummer)) {
				$this->vacaturenummer = $vacaturenummer;
				
				return $this->vacaturenummer;
			}
		}
		
		return false;
	}
	
	/**
	 * Store the 'raw' xml for later use
	 *
	 * @param SimpleXMLElement $xml
	 *
	 * @return void
	 */
	public function setXmlSource($xml) {
		$this->xml_source = $xml;
	}
	
	/**
	 * Get the 'raw' xml source
	 *
	 * @return void|SimpleXMLElement
	 */
	public function getXmlSource() {
		return $this->xml_source;
	}
	
	/**
	 * Get an XML path from the source xml
	 *
	 * @param array $path the path to get
	 *
	 * @return false|SimpleXMLElement
	 */
	public function getXMLPath($path = array()) {
		
		if (empty($path) || !is_array($path)) {
			return false;
		}
		
		$xml = $this->getXmlSource();
		if (empty($xml)) {
			return false;
		}
		
		foreach ($path as $part) {
			if (empty($xml->$part)) {
				return false;
			}
			
			$xml = $xml->$part;
		}
		
		return $xml;
	}
	
	/**
	 * Check if the vacancy is internal
	 *
	 * @return bool
	 */
	public function isInternal() {
		
		$start_date = $this->getXMLPath(array(
			'Administratie',
			'Datum_publ_internstart',
		));
		
		if (empty($start_date)) {
			// no internal start date
			return false;
		}
		
		$start_date = strtotime($start_date);
		if ($start_date > time()) {
			// start date is in the future
			return false;
		}
		
		$end_date = $this->getXMLPath(array(
			'Administratie',
			'Datum_publ_internstop',
		));
		if (empty($end_date)) {
			// no end date
			return true;
		}
		
		$end_date = strtotime($end_date);
		if ($end_date < time()) {
			// end date passed
			return false;
		}
		
		return true;
	}
	
	/**
	 * Check if the vacancy is external
	 *
	 * @return bool
	 */
	public function isExternal() {
		
		$start_date = $this->getXMLPath(array(
			'Administratie',
			'Datum_publ_bpstart',
		));
		
		if (empty($start_date)) {
			// no external start date
			return false;
		}
		
		$start_date = strtotime($start_date);
		if ($start_date > time()) {
			// start date is in the future
			return false;
		}
		
		$end_date = $this->getXMLPath(array(
			'Administratie',
			'Datum_publ_stop',
		));
		if (empty($end_date)) {
			// no end date
			return true;
		}
		
		$end_date = strtotime($end_date);
		if ($end_date < time()) {
			// end date passed
			return false;
		}
		
		return true;
	}
}
