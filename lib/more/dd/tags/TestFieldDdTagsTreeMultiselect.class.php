<?php

class TestFieldDdTagsTreeMultiselect extends TestFieldDdTagsTreeAbstract {

  protected $tagId4, $v4 = 'four';

  function createTags() {
    parent::createTags();
    $this->tagId4 = DdTags::get('a', 'sample')->create([
      'title' => $this->v4,
      'parentId' => $this->tagId1
    ]);
  }

  function runTests() {
    $this->a($this->tagId3, $this->v2);
    $this->updateItem();
    $this->a($this->tagId4, $this->v4);
  }

  function createItem() {
    return static::$im->create(['sample' => [$this->tagId2, $this->tagId3]]);
  }

  function updateItem() {
    static::$im->update($this->itemId, ['sample' => [$this->tagId2, $this->tagId4]]);
  }

  function a($tagId2, $v) {
    $item = static::$im->items->getItemF($this->itemId);
    $values = array_values($item['sample']);
    $this->assertTrue($values[0][1]['tagId'] == $this->tagId2, "{$values[0][1]['tagId']} != $this->tagId2");
    $this->assertTrue($values[1][1]['tagId'] == $tagId2, "{$values[1][1]['tagId']} != $tagId2");
    static::$im->requestUpdate($this->itemId);
    $html = static::$im->form->html();
    $this->assertTrue((bool)strstr($html, 'id="sample_'.$this->tagId2.'" checked />'));
    $this->assertTrue((bool)strstr($html, 'id="sample_'.$tagId2.'" checked />'));
    $html = (new Ddo('a', 'siteItem'))->setItem($item)->els();
    $this->assertTrue((bool)strstr($html, "$this->v1 → $this->v2"), "$this->v1 → $this->v2");
    $this->assertTrue((bool)strstr($html, "$this->v1 → $v"), "$this->v1 → $v");
  }

}