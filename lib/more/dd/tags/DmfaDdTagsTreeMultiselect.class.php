<?php

class DmfaDdTagsTreeMultiselect extends DmfaDdTagsAbstract {

  function source2formFormat($v) {
    return $v ? Arr::get($v, 'id') : '';
  }

  function afterUpdate($tagIds, $k) {
    if (empty($tagIds)) {
      $this->deleteTagItems($k);
      return;
    }
    $tagItems = DdTags::items($this->dm->strName, $k);
    if (($currentTags = $this->dm->items->getItem($this->dm->id)[$k])) $currentTagIds = Arr::get($currentTags, 'id');
    $newTagIds = [];
    $deleteTagIds = [];
    foreach ($tagIds as $id) if (!in_array($id, $currentTagIds)) $newTagIds[] = $id;
    if (isset($currentTagIds)) foreach ($currentTagIds as $id) if (!in_array($id, $tagIds)) $deleteTagIds[] = $id;
    $collectionTagIds = (new DdTagsTagsTree(new DdTagsGroup($this->dm->strName, $k)))->getParentIds($newTagIds);
    foreach ($deleteTagIds as $id) $tagItems->deleteByCollection($this->dm->id, $id); // delete tag by id does not work. need to check if it is a collection
    $tagItems->createByIdsCollection($this->dm->id, $collectionTagIds, false);
    $tagItems->updateCounts($deleteTagIds);
  }

  function afterCreate($tagIds, $k) {
    $tagItems = DdTags::items($this->dm->strName, $k);
    $collectionTagIds = (new DdTagsTagsTree(new DdTagsGroup($this->dm->strName, $k)))->getParentIds($tagIds);
    $tagItems->createByIdsCollection($this->dm->id, $collectionTagIds, false);
  }

}
