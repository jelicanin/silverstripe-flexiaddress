<?php

class FlexiAddressExtension extends DataExtension {

	private static $flexiaddress_tab = 'Root.Address';

	private static $flexiaddress_insertBefore = null;

	private static $flexiaddress_addButton = 'Create New Address';

	private static $flexiaddress_fields = array(
		'StreetLine1',
		'StreetLine2',
		'City',
		// 'State',
		'PostalCode',
		'Country',
		// 'Website',
		// 'Email',
		'MetaType'
	);

	private static $many_many = array(
		'FlexiAddresses' => 'FlexiAddress'
	);

	private static $many_many_extraFields = array(
		'FlexiAddresses' => array(
			'SortOrder' => 'Int'
		)
	);

	/**
	 * @param FieldList $fields
	 */
	public function updateCMSFields(FieldList $fields) {
		$fields->removeByName('FlexiAddresses');

		if ($this->owner->exists()) {
			$enabled_fields = $this->getFlexiAddressFields();
			Config::inst()->update('FlexiAddress', 'flexiaddress_fields', $enabled_fields);

			$config = new GridFieldConfig_FlexiAddresses($enabled_fields, $this->owner);
			$component = $config->getComponentByType('GridFieldAddNewButton');
			$component->setButtonName($this->getFlexiAddressAddButton());

			$list = $this->owner->FlexiAddresses();
			$field_title = ($list->count() > 1) ? 'Addresses' : 'Address';

			$fields->addFieldToTab($this->getFlexiAddressTab(),
				new GridField('FlexiAddresses', $field_title, $list, $config),
				$this->getFlexiAddressInsertBefore());
		} else {
			$fields->addFieldToTab($this->getFlexiAddressTab(),
				new LiteralField('FlexiAddresses', '<p>Please save before managing addresses.</p>'));
		}

	}

	// template
	///////////

	/**
	 * Used to get the first address associated with an object
	 * Returns the FlexiAddress, or null if none are found.
	 *
	 * @return FlexiAddress|null
	 */

	public function FlexiAddress() {
		return $this->owner->FlexiAddresses()->first();
	}

	// getters + setters
	////////////////////

	/**
	 * @return mixed
	 */
	public function getFlexiAddressTab() {
		return $this->lookup('flexiaddress_tab');
	}

	/**
	 * @param  $tab_name
	 * @return mixed
	 */
	public function setFlexiAddressTab($tab_name) {
		return $this->owner->set_stat('flexiaddress_tab', $tab_name);
	}

	/**
	 * @return mixed
	 */
	public function getFlexiAddressInsertBefore() {
		return $this->lookup('flexiaddress_insertBefore');
	}

	/**
	 * @param  $field_name
	 * @return mixed
	 */
	public function setFlexiAddressInsertBefore($field_name) {
		return $this->owner->set_stat('flexiaddress_insertBefore', $field_name);
	}

	/**
	 * @return mixed
	 */
	public function getFlexiAddressAddButton() {
		return $this->lookup('flexiaddress_addButton');
	}

	/**
	 * @param  $button_name
	 * @return mixed
	 */
	public function setFlexiAddressAddButton($button_name) {
		return $this->owner->set_stat('flexiaddress_addButton', $button_name);
	}

	/**
	 * @return mixed
	 */
	public function getFlexiAddressFields() {
		return $this->lookup('flexiaddress_fields', true);
	}

	/**
	 * @param  $fields
	 * @return mixed
	 */
	public function setFlexiAddressFields($fields) {
		return $this->owner->set_stat('flexiaddress_fields', $fields);
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
