<?php

class DdTagsHtml {

  static function treeUl($nodes, $dddd = '$title', $selectedIds = [], $showNullCountTags = true) {
    $html = '<ul>';
    if ($nodes) {
      $n = 0;
      foreach ($nodes as $v) {
        // Если нет детей, если аргумент ф-ии $showNullCountTags = false и если 
        // кол-во записей по этому тэгу = 0, не отображаем этот тэг
        if (!$showNullCountTags and !$v['cnt']) continue;
        $class = !empty($v['childNodes']) ? 'folder' : '';
        $v['selected'] = in_array($v['id'], $selectedIds);
        if ($v['selected']) $class = empty($class) ? 'active' : $class.' active';
        if (!empty($v['childNodes'])) $class = empty($class) ? 'hasChildren' : $class.' hasChildren';
        $html .= '<li'.($class ? ' class="'.$class.'"' : '').' id="ti'.$v['id'].'">'.St::dddd($dddd, $v);
        if (!empty($v['childNodes'])) {
          $html .= self::treeUl($v['childNodes'], $dddd, $selectedIds, $showNullCountTags);
        }
        $html .= '</li>';
        $n++;
      }
    }
    else {
      $html .= '<li>Тэги не существуют</li>';
    }
    $html .= '</ul>';
    return $html;
  }

  static function treeArrows($nodes, $dddd = '$title', $showNullCountTags = true) {
    $n = 0;
    $titles = [];
    foreach ($nodes as $v) {
      if (!is_array($v)) throw new Exception('$v not an array');
      if (empty($v['childNodes']) and !$v['cnt'] and !$showNullCountTags) continue;
      $titles[$n] = [St::dddd($dddd, $v)];
      if (!empty($v['childNodes'])) {
        $titles[$n] = Arr::append($titles[$n], TreeCommon::getFlatDddd($v['childNodes'], $dddd));
      }
      $n++;
    }
    $html = '';
    foreach ($titles as $items) $html .= '<span>'.implode(' → ', $items).'</span>';
    return $html;
  }

  static function treeArrowsLinks($v) {
    return self::treeArrows($v['tags'], '`<a href="'.Tt()->getPath(0).$v['pagePath'].'/t2.`.$groupName.`.`.$id.`">`.$title.`</a>`');
  }

  static function treeArrows2($v) {
    return Tt()->enumDddd($v['tags'], '`<a href="'.Tt()->getPath(0).$v['pagePath'].'/t2.`.$groupName.`.`.$id.`">`.$title.`</a>`', ' → ');
  }

  static function treeArrows3($v, $dddd = '`<li>`.$v.`</li>`') {
    $r = '';
    foreach ($v['tags'] as $tags) {
      $html = Tt()->enumDddd([$tags], '`<a href="'.Tt()->getPath(0).$v['pagePath'].'/t2.`.$groupName.`.`.$id.`">`.$title.`</a>`', ' → ');

      $r .= St::dddd($dddd, ['v' => $html]);
    }
    return $r;
  }

  static function tagsTreeArrowsNode(array $node, $dddd = '$title', $showNullCountTags = true) {
    $titles = [];
    self::_tagsTreeArrowsNode($titles, $node, $dddd, $showNullCountTags);
    return implode(' → ', $titles);
  }

  static function lastInBranches(array $nodes, $dddd) {
    foreach ($nodes as $v) {
      Tt()->enum(TreeCommon::lastInBranch($v), '', $dddd);
    }
  }

  private static function _tagsTreeArrowsNode(array &$titles, array $node, $dddd, $showNullCountTags) {
    $titles[] = St::dddd($dddd, $node);
    if (isset($node['childNodes'][0])) {
      self::_tagsTreeArrowsNode($titles, $node['childNodes'][0], $dddd, $showNullCountTags);
    }
  }

  private static function _treeToList(array $nodes, array &$list, $depth) {
    if (!is_array($nodes)) die2('$nodes: '.getPrr($nodes));
    foreach ($nodes as $v) {
      $list[] = $v;
      [
        'id'    => $v['id'],
        'title' => $v['title'],
        'name'  => $v['name'],
        'cnt'   => empty($v['cnt']) ? 0 : $v['cnt'],
        'depth' => $depth
      ];
      if (!empty($v['childNodes'])) {
        self::_treeToList($v['childNodes'], $list, $depth + 1);
      }
    }
  }

  static function treeToList(array $nodes) {
    $list = [];
    self::_treeToList($nodes, $list, 0);
    return $list;
  }

}
