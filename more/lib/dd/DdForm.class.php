<?php

class DdForm extends Form {

  public $strName;

  function __construct($fields, $strName, array $options = []) {
    $this->strName = $strName;
    if (Sflm::frontendName()) Sflm::frontend('js')->addClass('Ngn.DdForm');
    parent::__construct($fields, $options);
  }

  protected function dataParams() {
    return [
      'class' => 'DdForm',
      'strName' => $this->strName
    ];
  }

  protected function setElementsDataDefault() {
    $r = parent::setElementsDataDefault();
    if ($r) {
      foreach (Hook::paths('dd/formInit') as $path) require $path;
    }
    return $r;
  }

  protected function jsInitTagValues() {
    return "Ngn.toObj('Ngn.Form.El.DdTags.values.{$this->id()}', {});";
  }

}