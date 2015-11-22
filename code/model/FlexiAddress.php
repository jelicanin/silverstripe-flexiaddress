<?php

class FlexiAddress extends DataObject {

	private static $db = array(
		'StreetLine1' => 'Varchar',
		'StreetLine2' => 'Varchar',
		'City'        => 'Varchar',
		'State'       => 'Varchar',
		'PostalCode'  => 'Varchar',
		'Country'     => 'Varchar',
		'Website'     => 'ExternalURL',
		'Email'       => 'Varchar',
		'MetaType'    => 'Varchar'
	);

	private static $searchable_fields = array(
		'StreetLine1' => array(
			'field'  => 'TextField',
			'filter' => 'PartialMatchFilter',
			'title'  => 'Street Line 1'
		),
		'Country'     => array(
			'field' => 'CountryDropdownField'
		)
	);

	private static $template              = null;
	private static $flexiaddress_metatype = null;

	/**
	 * @return mixed
	 */
	public function summaryFields() {
		$enabled_fields = $this->getEnabledFields();

		$fields = array(
			'toString' => 'Address'
		);

		if (in_array('Website', $enabled_fields)) {
			$fields['Website'] = 'Website';
		}

		if (in_array('Email', $enabled_fields)) {
			$fields['Email'] = 'Email';
		}

		if (in_array('MetaType', $enabled_fields)) {
			$fields['MetaType'] = 'MetaType';
		}

		return $fields;
	}

	/**
	 * @return mixed
	 */
	public function getCMSFields() {
		$fields = singleton('DataObject')->getCMSFields();

		$enabled_fields = $this->getEnabledFields();

		foreach ($this->db() as $field_name => $field_type) {
			if (in_array($field_name, $enabled_fields)) {
				$fields->addFieldToTab('Root.Main', $this->getFieldForName($field_name));
			}
		}

		$this->extend('updateCMSFields', $fields);

		return $fields;
	}

	/**
	 * @return mixed
	 */
	public function searchableFields() {
		$search_fields  = $this->stat('searchable_fields');
		$enabled_fields = $this->getEnabledFields();
		$fields         = array();

		foreach ($search_fields as $field_name) {
			if (!in_array($field_name, $enabled_fields)) {
				continue;
			}

			$field_instance = $this->getFieldForName($field_name);

			$fields[$field_name] = array(
				'title'  => $field_instance->Title(),
				'field'  => $field_instance->class,
				'filter' => ($field_instance->is_a('DropdownField')) ? 'ExactMatchFilter' : 'PartialMatchFilter'
			);
		}

		return $fields;
	}

	/**
	 * @param  $field_name
	 * @return mixed
	 */
	public function getFieldForName($field_name) {
		$field_title = preg_replace('/(?<=\\w)(?=[A-Z])/', ' $1', $field_name);

		switch ($field_name) {
			case 'Country':
				$field = CountryDropdownField::create($field_name, $field_title);
				break;

			case 'State':
				$field = (class_exists('USStateDropdownField')) ?
				USStateDropdownField::create($field_name, $field_title) :
				TextField::create($field_name, $field_title);
				break;

			case 'Email':
				$field = EmailField::create($field_name, $field_title);
				break;

			case 'MetaType':
				$field = DropdownField::create('MetaType', 'Meta Type', $this->config()->flexiaddress_metatype);
				break;

			default:
				$field = TextField::create($field_name, $field_title);
				break;
		}

		return $field;
	}

	/**
	 * @return mixed
	 */
	public function getEnabledFields() {
		if (!$fields = $this->stat('flexiaddress_fields')) {
			$fields = Config::inst()->get('FlexiAddressExtension', 'flexiaddress_fields');
		}
		return $fields;
	}

	public function getTemplate() {
		return ($this->stat('template')) ?: $this->ClassName;
	}

	/**
	 * @param $template
	 */
	public function setTemplate($template) {
		$this->set_stat('template', $template);
	}

	public function FullStateName() {
		if ($state = $this->State) {
			return (class_exists('USStateDropdownField')) ?
			USStateDropdownField::$states[$this->State] :
			$this->State;
		}
	}

	public function AddressMapLink() {
		return 'https://maps.google.com/?q='.$this->toString();
	}

	/**
	 * @param $with_phones
	 */
	public function toString($with_phones = false) {
		$fields = array(
			'StreetLine1',
			'StreetLine2',
			'City',
			'State',
			'PostalCode'
		);

		$params = array();

		foreach ($fields as $field) {
			if (!empty($this->$field)) {
				$params[] = $this->$field;
			}
		}

		return implode(',', $params);
	}

	/**
	 * @return mixed
	 */
	public function forTemplate() {
		return $this->renderWith(
			array(
				$this->getTemplate(),
				'FlexiAddress'
			));
	}

	/**
	 * @return mixed
	 */
	public function __toString() {
		return $this->toString();
	}

	/**
	 * @return mixed
	 */
	public function getTitle() {
		return $this->toString(false);
	}
}
