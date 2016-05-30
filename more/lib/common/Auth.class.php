<?php

class Auth {

  static $expires = 30000000;

  /**
   * Не сохранять пароль (авторизация только для текущей сессии)
   *
   * @var bool
   */
  static $doNotSavePass;

  static $loginFieldName = 'authLogin';

  static $passFieldName = 'authPass';

  static $errors;

  /**
   * @var bool|DbModelUsers
   */
  static $auth;

  const ERROR_AUTH_NO_LOGIN = 1;

  const ERROR_AUTH_WRONG_PASS = 2;

  const ERROR_AUTH_USER_NOT_ACTIVE = 3;

  const ERROR_EMPTY_LOGIN_OR_PASS = 4;

  static $errorsText;

  static function cryptPass($pass) {
    return md5(md5(md5($pass)));
  }

  /**
   * Проверяет логин-закриптованый пароль в БД
   *
   * @param   string  Логин
   * @param   string  Закриптованиый пароль
   * @return  bool|DbModelUsers
   */
  static function checkLoginPass($login, $encryptedPass) {
    $login = trim($login);
    if (($user = DbModelCore::get('users', $login, 'login')) === false) {
      if (($user = DbModelCore::get('users', $login, 'email')) === false) {
        if (($user = DbModelCore::get('users', trim($login, '+'), 'phone')) === false) {
          self::error(Locale::get('loginIsAbsent'));
          return false;
        }
      }
    }
    if ($user['pass'] == $encryptedPass) {
      if ($user['active']) {
        return $user;
      }
      else {
        self::error(Locale::get('userIsNotActive'));
        return false;
      }
    }
    else {
      $wrongPass = true;
    }
    // Если для всех перебраных пользователей пароль неверен
    if ($wrongPass) {
      self::error(Locale::get('wrongPassword'));
      return false;
    }
  }

  static function error($code) {
    self::$errors[] = [
      'code' => $code,
      'text' => isset(self::$errorsText[$code]) ? self::$errorsText[$code] : 'unknown error with code '.$code
    ];
  }


  /**
   * Кол-во раз, которое проводилась авторизация
   *
   * @var integer
   */
  static $n = 0;

  /**
   * Проверяет пару логин-закриптованый пароль, устанавливает cookie, если
   * прошла проверка, заполняет глобальный массив $_AUTH данными текущего
   * авторизованого пользователя
   *
   * @param   string  Логин
   * @param   string  Закриптованиый пароль
   * @param   bool    Если авторизация происходит после начала вывода
   * @return  bool|DbModelUsers
   */
  static function login($login, $encryptedPass, $afterOutput = false) {
    self::$n++;
    if (($result = self::checkLoginPass($login, $encryptedPass)) !== false) {
      if (!$afterOutput) self::save($result);
      return $result;
    }
    else {
      return false;
    }
  }

  static function save(DbModelUsers $user) {
    self::saveSession($user->r);
    Ngn::fireEvent('auth', $user);
  }

  static function pack($data) {
    foreach ($data as $k => $v) {
      $d[] = $k.'---===---'.$v;
    }
    return implode('|||===|||', $d);
  }

  static function unpack($data) {
    $data = explode('|||===|||', $data);
    foreach ($data as $v) {
      list($k, $v) = explode('---===---', $v);
      $d[$k] = $v;
    }
    return $d;
  }

  static private function saveSession($user) {
    Session::init();
    $_SESSION['auth'] = $user;
  }

  static function relogin() {
    if (!$login = self::get('login')) return false;
    return self::loginByLogin($login);
  }

  static function loginByLogin($login) {
    self::$auth = null;
    return self::login($login, db()->selectCell('SELECT pass FROM users WHERE login=?', $login));
  }

  static function loginById($id) {
    $user = Misc::checkEmpty(DbModelCore::get('users', $id));
    self::save($user);
    return self::$auth = $user;
  }

  static function logout() {
    Session::init();
    $_SESSION['auth'] = null;
  }

  static function clear() {
    Session::init();
    $_SESSION = null;
    Session::delete();
    foreach ($_COOKIE as $k => $v) {
      setcookie($k, '', time() + self::$expires, '/', SITE_DOMAIN);
    }
  }

  /**
   * Производит авторизацию по данным из cookie
   *
   * @return bool|DbModelUsers
   */
  static private function loginByCookie() {
    if (isset($_COOKIE['auth'])) {
      $_COOKIE['auth'] = self::unpack($_COOKIE['auth']);
      return self::login($_COOKIE['auth']['login'], $_COOKIE['auth']['pass']);
    }
    else {
      return false;
    }
  }

  static private function loginBySession() {
    return isset($_SESSION['auth']) ? $_SESSION['auth'] : false;
  }

  static $postAuth = false;

  /**
   * Производит авторизацию по данным из поста
   *
   * @param null $login
   * @param null $pass
   * @return bool|DbModelUsers
   */
  static function loginByRequest($login = null, $pass = null) {
    if (!$login and isset($_REQUEST[self::$loginFieldName])) $login = $_REQUEST[self::$loginFieldName];
    if (!$pass and isset($_REQUEST[self::$passFieldName])) $pass = $_REQUEST[self::$passFieldName];
    if (!empty($login) and !empty($pass)) {
      if (!$login or !$pass) {
        self::error(Locale::get('wrongLoginOrPassword'));
        return false;
      }
      $r = self::login($login, self::cryptPass($pass));
      if ($r) self::$postAuth = true;
      return $r;
    }
    else {
      return false;
    }
  }

  /**
   * Производит авторизацию по данным, отосланным с формы авторизации
   *
   * @return bool|DbModelUsers
   */
  static function loginPage() {
    if (($r = self::loginByRequest()) === false) {
      return self::loginBySession();
    }
    return $r;
  }

  static function setAuth() {
    if (isset(self::$auth)) return self::$auth;
    self::$expires = self::$doNotSavePass ? 0 : 60 * 60 * 24 * 10;
    if (($auth = self::loginPage())) {
      //
    }
    if (self::$errors[0]) {
      $auth['msg'] = self::$errors[0]['text'];
      $auth['errors'] = self::$errors;
    }
    return self::$auth = $auth;
  }

  static function check() {
    self::setAuth();
    return self::$errors ? false : self::$auth ? true : false;
  }

  static function get($param) {
    if (Ngn::isDebug()) {
      $auth = DbModelCore::get('users', 1);
    } else {
      if (!$param) throw new Exception('Use getAll');
      if (!($auth = self::setAuth())) return null;
    }
    return isset($auth[$param]) ? $auth[$param] : null;
  }

  static function getAll() {
    return (($r = self::setAuth())) ? Arr::filterByKeys($r->r, ['id', 'login', 'email']) : false;
  }

}

