<?php

class FlexiEmailExtension extends DataExtension {

	private static $flexiemail_tab = 'Root.Emails';

	private static $flexiemail_insertBefore = null;

	private static $flexiemail_addButton = 'Create New Email';

	private static $flexiemail_fields = array(
		'Email',
		'Label',
		'MetaType',
	);

	private static $many_many = array(
		'FlexiEmails' => 'FlexiEmail'
	);

	private static $many_many_extraFields = array(
		'FlexiEmails' => array(
			'SortOrder' => 'Int'
		)
	);

	/**
	 * @param FieldList $fields
	 */
	public function updateCMSFields(FieldList $fields) {
		$fields->removeByName('FlexiEmails');

		if ($this->owner->exists()) {
			$enabled_fields = $this->getFlexiEmailFields();
			Config::inst()->update('FlexiEmail', 'flexiemail_fields', $enabled_fields);

			$config = new GridFieldConfig_FlexiEmails($enabled_fields, $this->owner);
			$component = $config->getComponentByType('GridFieldAddNewButton');
			$component->setButtonName($this->getFlexiEmailAddButton());

			$list = $this->owner->FlexiEmails();
			$field_title = ($list->count() > 1) ? 'Emails' : 'Email';

			$fields->addFieldToTab($this->getFlexiEmailTab(),
				new GridField('FlexiEmails', $field_title, $list, $config),
				$this->getFlexiEmailInsertBefore());
		} else {
			$fields->addFieldToTab($this->getFlexiEmailTab(),
				new LiteralField('FlexiEmails', '<p>Please save before managing emails.</p>'));
		}

	}

	// template
	///////////

	/**
	 * Used to get the first address associated with an object
	 * Returns the FlexiEmail, or null if none are found.
	 *
	 * @return FlexiEmail|null
	 */
	public function FlexiEmail() {
		return $this->owner->FlexiEmails()->first();
	}

	/**
	 * Used to get the first phone number associated with an object
	 * Returns the FlexiEmailEmail, or null if nore are found
	 *
	 * @return FlexiEmailEmail|null
	 */
	public function FlexiEmailFirst() {
		$email = $this->FlexiEmail();
		return ($email->exists()) ? $email->FlexiEmails()->first() : null;
	}

	public function emailsToString() {
		$emails = array();
		foreach ($this->FlexiEmails() as $email) {
			$emails[] = $email->toString();
		}

		return implode(', ', $emails);
	}

	// getters + setters
	////////////////////

	/**
	 * @return mixed
	 */
	public function getFlexiEmailTab() {
		return $this->lookup('flexiemail_tab');
	}

	/**
	 * @param  $tab_name
	 * @return mixed
	 */
	public function setFlexiEmailTab($tab_name) {
		return $this->owner->set_stat('flexiemail_tab', $tab_name);
	}

	/**
	 * @return mixed
	 */
	public function getFlexiEmailInsertBefore() {
		return $this->lookup('flexiemail_insertBefore');
	}

	/**
	 * @param  $field_name
	 * @return mixed
	 */
	public function setFlexiEmailInsertBefore($field_name) {
		return $this->owner->set_stat('flexiemail_insertBefore', $field_name);
	}

	/**
	 * @return mixed
	 */
	public function getFlexiEmailAddButton() {
		return $this->lookup('flexiemail_addButton');
	}

	/**
	 * @param  $button_name
	 * @return mixed
	 */
	public function setFlexiEmailAddButton($button_name) {
		return $this->owner->set_stat('flexiemail_addButton', $button_name);
	}

	/**
	 * @return mixed
	 */
	public function getFlexiEmailFields() {
		return $this->lookup('flexiemail_fields', true);
	}

	/**
	 * @param  $fields
	 * @return mixed
	 */
	public function setFlexiEmailFields($fields) {
		return $this->owner->set_stat('flexiemail_fields', $fields);
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
