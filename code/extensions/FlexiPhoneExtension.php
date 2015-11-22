<?php

class FlexiPhoneExtension extends DataExtension {

	private static $flexiphone_tab = 'Root.Phones';

	private static $flexiphone_insertBefore = null;

	private static $flexiphone_addButton = 'Create New Phone';

	private static $flexiphone_fields = array(
		'Telephone',
		'Label',
		'MetaType',
		'MetaFax',
		'MetaTollFree'
	);

	private static $many_many = array(
		'FlexiPhones' => 'FlexiPhone'
	);

	private static $many_many_extraFields = array(
		'FlexiPhones' => array(
			'SortOrder' => 'Int'
		)
	);

	/**
	 * @param FieldList $fields
	 */
	public function updateCMSFields(FieldList $fields) {
		$fields->removeByName('FlexiPhones');

		if ($this->owner->exists()) {
			$enabled_fields = $this->getFlexiPhoneFields();
			Config::inst()->update('FlexiPhone', 'flexiphone_fields', $enabled_fields);

			$config = new GridFieldConfig_FlexiPhones($enabled_fields, $this->owner);
			$component = $config->getComponentByType('GridFieldAddNewButton');
			$component->setButtonName($this->getFlexiPhoneAddButton());

			$list = $this->owner->FlexiPhones();
			$field_title = ($list->count() > 1) ? 'Phones' : 'Phone';

			$fields->addFieldToTab($this->getFlexiPhoneTab(),
				new GridField('FlexiPhones', $field_title, $list, $config),
				$this->getFlexiPhoneInsertBefore());
		} else {
			$fields->addFieldToTab($this->getFlexiPhoneTab(),
				new LiteralField('FlexiPhones', '<p>Please save before managing phones.</p>'));
		}

	}

	// template
	///////////

	/**
	 * Used to get the first address associated with an object
	 * Returns the FlexiPhone, or null if none are found.
	 *
	 * @return FlexiPhone|null
	 */
	public function FlexiPhone() {
		return $this->owner->FlexiPhones()->first();
	}

	/**
	 * Used to get the first phone number associated with an object
	 * Returns the FlexiPhonePhone, or null if nore are found
	 *
	 * @return FlexiPhonePhone|null
	 */
	public function FlexiPhoneFirst() {
		$address = $this->FlexiPhone();
		return ($address->exists()) ? $address->PhoneNumbers()->first() : null;
	}

	public function phoneNumbersToString() {
		$phones = array();
		foreach ($this->PhoneNumbers() as $number) {
			$phones[] = $number->toString();
		}

		return implode(', ', $phones);
	}

	// getters + setters
	////////////////////

	/**
	 * @return mixed
	 */
	public function getFlexiPhoneTab() {
		return $this->lookup('flexiphone_tab');
	}

	/**
	 * @param  $tab_name
	 * @return mixed
	 */
	public function setFlexiPhoneTab($tab_name) {
		return $this->owner->set_stat('flexiphone_tab', $tab_name);
	}

	/**
	 * @return mixed
	 */
	public function getFlexiPhoneInsertBefore() {
		return $this->lookup('flexiphone_insertBefore');
	}

	/**
	 * @param  $field_name
	 * @return mixed
	 */
	public function setFlexiPhoneInsertBefore($field_name) {
		return $this->owner->set_stat('flexiphone_insertBefore', $field_name);
	}

	/**
	 * @return mixed
	 */
	public function getFlexiPhoneAddButton() {
		return $this->lookup('flexiphone_addButton');
	}

	/**
	 * @param  $button_name
	 * @return mixed
	 */
	public function setFlexiPhoneAddButton($button_name) {
		return $this->owner->set_stat('flexiphone_addButton', $button_name);
	}

	/**
	 * @return mixed
	 */
	public function getFlexiPhoneFields() {
		return $this->lookup('flexiphone_fields', true);
	}

	/**
	 * @param  $fields
	 * @return mixed
	 */
	public function setFlexiPhoneFields($fields) {
		return $this->owner->set_stat('flexiphone_fields', $fields);
	}

	/**
	 * @param  $lookup
	 * @param  $do_not_merge
	 * @return mixed
	 */
	private function lookup($lookup, $do_not_merge = false) {
		if ($do_not_merge &&
			$unmerged = Config::inst()->get($this->owner->class, $lookup, Config::EXCLUDE_EXTRA_SOURCES)) {
			return $unmerged;
		}

		return $this->owner->stat($lookup);
	}
}
