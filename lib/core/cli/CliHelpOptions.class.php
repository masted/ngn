<?php

abstract class CliHelpOptions extends CliHelp {

  protected function getClasses() {
    return ClassCore::getDescendants('ArrayAccessebleOptions', ucfirst($this->prefix()));
  }

  protected function _getMethods($class) {
    return array_filter(parent::_getMethods($class), function (ReflectionMethod $method) {
      if (!Misc::hasPrefix('a_', $method->getName())) return false;
      return true;
    });
  }

  protected function _getOptions(ReflectionMethod $method, $class) {
    $options = [];
    $options = array_merge($options, $class::$requiredOptions);
    foreach ($options as &$v) $v = $this->option($v);
    return array_merge($options, $this->getMethodOptionsWithMeta($method));
  }

  protected function getMethodOptionsWithMeta(ReflectionMethod $method) {
    if (!($options = ClassCore::getDocComment($method->getDocComment(), 'options'))) return [];
    $r = [];
    foreach (array_map('trim', explode(',', $options)) as $name) $r[] = $this->option($name);
    return $r;
  }

  private function option($name) {
    return [
      'name' => $name,
      'optional' => false,
      'variants' => false,
      'descr' => false
    ];
  }

  protected function getMethod(ReflectionMethod $method) {
    return Misc::removePrefix('a_', $method->getName());
  }

  protected function _run($class, $method, $params) {
    $method = 'a_'.$method;
    if (is_subclass_of($class, 'CliHelpMultiWrapper')) {
      $this->runMultiWrapper($class, $method, $params);
    }
    else {
      $requiredOptions = [];
      foreach ($class::$requiredOptions as $i => $name) $requiredOptions[$name] = $params[$i];
      (new $class(array_merge($requiredOptions, $this->getMethodOptionsWithParams($class, $method, $params))))->$method();
    }
  }

  protected function runMultiWrapper($class, $method, $params) {
    $realClass = method_exists($class, $method) ? $class : $this->getSingleProcessorClass($class);
    $requiredOptions = [];
    foreach ($realClass::$requiredOptions as $i => $name) $requiredOptions[$name] = $params[$i];
    $options = array_merge($requiredOptions, $this->getMethodOptionsWithParams($realClass, $method, $params));
    /* @var CliHelpMultiWrapper $multiWrapper */
    $multiWrapper = (new $class($options));
    $multiWrapper->action($method);
  }

  protected function getMethodOptionsWithParams($class, $method, $params) {
    if (($options = ($this->getMethodOptions((new ReflectionMethod($class, $method)))))) {
      foreach ($options as $i => $opt) $options[$opt['name']] = $params[$i];
    }
    return $options;
  }

  protected function getMethodOptions(ReflectionMethod $method) {
    $optionNames = $this->getMethodOptionsWithMeta($method);
    foreach ($optionNames as &$v) $v = Misc::removePrefix('@', $v);
    return $optionNames;
  }

  protected function getSingleProcessorClass($multipleProcessorClass) {
    return rtrim($multipleProcessorClass, 's');
  }


}