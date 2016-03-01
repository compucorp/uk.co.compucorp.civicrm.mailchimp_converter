<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_MailchimpConverter_Form_ConvertMailing extends CRM_Core_Form {
  function buildQuickForm() {
    $this->add('textarea', 'original_template', 'Paste Mailchimp HTML Here: ', 
            array('rows' => 4,
                  'cols' => 50,
                  'style' => 'width: 100%; height: 300px;',
                ), true);
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Convert'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  function postProcess() {
    $values = $this->exportValues();

    $tagConverter = new CRM_MailchimpConverter_TagConverter();
    $converted = $tagConverter->convert($values['original_template']);

    $this->assign('convertedTemplate', $converted);

    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
