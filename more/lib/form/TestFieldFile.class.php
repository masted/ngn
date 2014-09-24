<?php

class TestFieldFile extends TestFieldDd {

  static function enable() {
    return true;
  }

  static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    O::di('DdFieldsManager', static::$strName)->create([
      'name'  => 'sample2',
      'title' => 'sample2',
      'type'  => 'file'
    ]);
  }

  function createData() {
    return [
      'sample' => TestCore::tempImageFixture(),
      'sample2' => TestCore::tempImageFixture()
    ];
  }

  function runTests($request = false) {
    output("request: $request");
    $item = static::$im->items->getItem($this->itemId);
    $this->assertTrue($item['sample'] == '/u/dd/a/1/sample.jpg');
    $this->assertTrue($item['sample2'] == '/u/dd/a/1/sample2.jpg');
    //$this->updateItem(['sample' => TestCore::tempImageFixture()], $request);
    //print static::$im->form->html();
  }

}