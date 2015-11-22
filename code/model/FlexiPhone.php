<?php

class FlexiPhone extends DataObject {

	private static $db = array(
		'Telephone'    => 'Varchar',
		'Label'        => 'Varchar',
		'MetaType'     => 'Varchar',
		'MetaFax'      => 'Boolean',
		'MetaTollFree' => 'Boolean',
		'SortOrder'    => 'Int'
	);

	private static $summary_fields = array(
		'toString'          => 'Number',
		'getMetaTypeString' => 'Meta Type',
		'MetaFax.Nice'      => 'Is Fax',
		'MetaTollFree.Nice' => 'Is Toll Free'
	);

	private static $default_sort        = 'SortOrder';
	private static $flexiphone_metatype = null;

	/**
	 * @return mixed
	 */
	public function getCMSFields() {
		$fields = singleton('DataObject')->getCMSFields();

		$type_map = $this->config()->flexiphone_metatype;

		$group = FieldGroup::create(
			FieldGroup::create(
				TextField::create('Telephone', 'Phone Number'),
				CheckboxField::create('MetaFax', 'Is Fax'),
				CheckboxField::create('MetaTollFree', 'Toll Free')
			),
			FieldGroup::create(
				TextField::create('Label', 'Label (optional)')
			),
			FieldGroup::create(
				DropdownField::create('MetaType', 'Meta Type', $type_map)
			)
		);

		$fields->addFieldToTab('Root.Main', $group);

		return $fields;
	}

	// @todo limplement internationalizetion of phone number
	/**
	 * @return mixed
	 */
	public function IntlTelephone() {
		return $this->Telephone;
	}

	public function toString() {
		return ($this->Label) ? $this->Telephone.' ('.$this->Label.')' : $this->Telephone;
	}

	/**
	 * @return mixed
	 */
	public function __toString() {
		return $this->toString();
	}

	public function getTitle() {
		return $this->toString(false);
	}

	public function getMetaTypeString() {
		$array = $this->config()->flexiphone_metatype;
		return isset($array[$this->MetaType]) ? $array[$this->MetaType] : false;
	}
}
