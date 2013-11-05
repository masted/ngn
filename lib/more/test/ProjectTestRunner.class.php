<?php

class ProjectTestRunner extends TestRunnerAbstract {

  protected $project;

  function __construct(array $filterNames = null) {
    $this->project = PROJECT_KEY;
    parent::__construct($filterNames);
  }

  protected function getClasses() {
    return array_filter(parent::getClasses(), function($class) {
      $r = ClassCore::hasAncestor($class, 'ProjectTestCase');
      return $r;
    });
  }

  function _local() {
    $this->_run(array_filter($this->getClasses(), function($class) {
      return strstr(Lib::getClassPath($class), "projects/$this->project/") or !empty($class::$local);
    }));
  }

  function _global() {
    $this->_run(array_filter($this->getClasses(), function($class) {
      return !strstr(Lib::getClassPath($class), "projects/$this->project/");
    }));
  }

  function _all() {
    $this->_run($this->getClasses());
  }

}