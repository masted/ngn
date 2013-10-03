<?php

class UsersForm extends Form {
use FormDbUnicCheck;

  protected $filterFields = ['login', 'user', 'pass', 'email', 'name', 'phone', 'extra'];
  public $strName = UsersCore::extraStrName;

  protected function defineOptions() {
    return [
      'subscribeOnReg' => true,
      'active'         => !(bool)Config::getVarVar('userReg', 'activation')
    ];
  }

  public function __construct(array $options = []) {
    parent::__construct(new Fields($this->_getFields()), $options);
  }

  protected function _getFields() {
    $fields = [];
    if (Config::getVarVar('userReg', 'loginEnable')) $fields[] = UserRegCore::getLoginField();
    if (Config::getVarVar('userReg', 'emailEnable')) {
      $fields[] = [
        'name'     => 'email',
        'title'    => 'E-mail',
        'type'     => 'email',
        'required' => true
      ];
    }
    if (Config::getVarVar('userReg', 'phoneEnable')) {
      $fields[] = [
        'name'     => 'phone',
        'title'    => 'Телефон',
        'type'     => 'phone',
        'disabled' => !empty($this->req->r['phone']),
        'required' => true
      ];
    }
    $fields[] = [
      'name'     => 'pass',
      'title'    => 'Пароль',
      'type'     => 'password',
      'required' => true
    ];
    if (Config::getVarVar('userReg', 'extraData')) {
      $fields = array_merge($fields, [
        [
          'type'  => 'header',
          'title' => 'Дополнительно'
        ]
      ], array_map(function($v) {
        $v['name'] = 'extra['.$v['name'].']';
        return $v;
      }, (new DdFields(UsersCore::extraStrName, $this->extraFieldsOptions()))->getFormFields()));
    }
    return $fields;
  }

  protected function extraFieldsOptions() {
    return [];
  }

  protected function unicCheckCond() {
    return 'users';
  }

  protected function initErrors() {
    $this->unicCheck('email', 'Такой имейл уже зарегистрирован');
    $this->unicCheck('login', Config::getVarVar('userReg', 'loginAsFullName') ? 'Такое Ф.И.О. уже зарегистрировано' : 'Такой логин уже зарегистрирован');
    $this->unicCheck('phone', 'Пользователь с таким телефоном уже существует');
  }

  protected function init() {
    parent::init();
    if (!empty($this->fields->fields['phone'])) $this->fields->fields['phone']['options']['disabled'] = true;
    $this->initRole();
    $this->initSubscribe();
  }

  /*
  protected function afterUserUpdate($userId, array $data) {
    if ($this->options['subscribeOnReg'] and isset($data['subsList'])) {
      foreach ($data['subsList'] as $listId => $subscribed) {
        if (!$subscribed) continue;
        db()->query('REPLACE INTO subs_users SET userId=?d, listId=?d', $userId, $listId);
      }
    }
  }
  */

  protected function initRole() {
    if (Config::getVarVar('role', 'enable', true)) {
      $this->fields = Arr::append([$this->getRoleField()], $this->fields);
    }
  }

  protected function getRoleField() {
    return [
      'title' => 'Тип профиля',
      'name'  => 'role',
      'type'  => 'userRole'
    ];
  }

  protected function initSubscribe() {
    $this->subscribeOnReg = (!empty($this->options['subscribeOnReg']) and Config::getVarVar('subscribe', 'onReg'));
    if (!$this->subscribeOnReg) return;
    $subscribes = db()->query('SELECT id, title FROM subsList WHERE active=1 AND useUsers=1');
    if (!$subscribes) return;
    $this->fields[] = [
      'name'  => 'subscribes',
      'title' => Config::getVarVar('subscribe', 'regHeaderTitle'),
      'type'  => 'header'
    ];
    foreach ($subscribes as $v) {
      $this->fields[] = [
        'name'    => 'subsList['.$v['id'].']',
        'title'   => $v['title'],
        'type'    => 'bool',
        'default' => true
      ];
    }
  }

}