<?php

class UsersRegForm extends UsersForm {

  public $create = true;
  
  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'name' => 'userReg',
      'submitTitle' => 'Зарегистрироваться',
      'active' => !Config::getVarVar('userReg', 'activation')
    ]);
  }

  function id() {
    return 'formUserReg';
  }

  protected function _getFields() {
    $fields = parent::_getFields();
    if (Config::getVarVar('userReg', 'phoneConfirm')) {
      $fields[] = [
        'name' => 'code',
        'type' => 'hidden'
      ];
    }
    return $fields;
  }

  protected function _update(array $data) {
    $data = Arr::filterByKeys($data, $this->filterFields);
    $data['active'] = $this->options['active'];
    $id = DbModelCore::create('users', $data);
    Ngn::fireEvent('users.new', $id);
  }

  protected function initErrors() {
    parent::initErrors();
    $this->initCodeError();
  }

  protected function initCodeError() {
    if (!Config::getVarVar('userReg', 'phoneConfirm')) return;
    $code = $this->req->rq('code');
    $phone = $this->req->rq('phone');
    $exists = db()->selectCell('SELECT id FROM userPhoneConfirm WHERE phone=? AND code=?', $phone, $code);
    if (!$exists) $this->globalError('Неверный код подтверждения');
  }

}