<?php

class FlexiEmail extends DataObject {

	private static $db = array(
		'Email'     => 'Varchar',
		'Label'     => 'Varchar',
		'MetaType'  => 'Varchar',
		'SortOrder' => 'Int'
	);

	private static $summary_fields = array(
		'toString'          => 'Email',
		'getMetaTypeString' => 'Meta Type'
	);

	private static $default_sort        = 'SortOrder';
	private static $flexiemail_metatype = null;

	/**
	 * @return mixed
	 */
	public function getCMSFields() {
		$fields = singleton('DataObject')->getCMSFields();

		$type_map = $this->config()->flexiemail_metatype;

		$group = FieldGroup::create(
			FieldGroup::create(
				TextField::create('Email', 'Email address')
			),
			FieldGroup::create(
				TextField::create('Label', 'Label (optional)')
			),
			FieldGroup::create(
				DropdownField::create('MetaType', 'Meta Type', $type_map, 1)
			)
		);

		$fields->addFieldToTab('Root.Main', $group);

		return $fields;
	}

	public function toString() {
		return ($this->Label) ? $this->Email.' ('.$this->Label.')' : $this->Email;
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
		$array = $this->config()->flexiemail_metatype;
		return isset($array[$this->MetaType]) ? $array[$this->MetaType] : false;
	}
}
