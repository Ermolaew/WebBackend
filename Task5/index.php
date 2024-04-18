<?php

header('Content-Type: text/html; charset=UTF-8');

$user = 'u67358';
$pass = '5004219';

$db = new PDO('mysql:host=localhost;dbname=u67358', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

// =========== GET ===========
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  $messages = array();
  $errors = array();
  $values = array();
  $langs = array();

  // SAVE PARAMETER
  if (!empty($_COOKIE['save'])) {

    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);

    $messages[] = $_COOKIE['save'];

    if (!empty($_COOKIE['pass'])) {
      $messages['savelogin'] = sprintf('<div>Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.</div>',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['pass']));
    }
  }

  // ERRORS
  $errors['name'] = empty($_COOKIE['name_error']) ? '' : $_COOKIE['name_error'];
  $errors['phone'] = !empty($_COOKIE['phone_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['langs'] = !empty($_COOKIE['langs_error']);
  $errors['bio'] = !empty($_COOKIE['bio_error']);
  $errors['check'] = !empty($_COOKIE['check_error']);

  // name
  if ($errors['name'] == '1') {
    setcookie('name_error', '', 100000);
    $messages[] = '<div>Заполните имя.</div>';
  }
  else if ($errors['name'] == '2') {
      setcookie('name_error', '', 100000);
      $messages[] = '<div>Недопустимые символы. Разрешены только русские и английские буквы,
      длина имени от 2 до 24 символов, обязательно начинается с заглавной.</div>';
  }

  // phone
  if ($errors['phone']) {
    setcookie('phone_error', '', 100000);
    $messages[] = '<div>Заполните телефон.</div>';
  }

  // email
  if ($errors['email']) {
    setcookie('email_error', '', 100000);
    $messages[] = '<div>Заполните почту.</div>';
  }

  // langs
  if ($errors['langs']) {
    setcookie('langs_error', '', 100000);
    $messages[] = '<div>Заполните языки программирования.</div>';
  }

  // bio
  if ($errors['bio']) {
    setcookie('bio_error', '', 100000);
    $messages[] = '<div>Заполните биографию.</div>';
  }

  // checkbox
  if ($errors['check']) {
    setcookie('check_error', '', 100000);
    $messages[] = '<div>Нельзя отправить форму без согласия с контрактом.</div>';
  }

  // VALUES
  $langs['C#'] = 'C#';
  $langs['Java'] = 'Java';
  $langs['Python'] = 'Python';

  $values['name'] = empty($_COOKIE['name_value']) ? '' : $_COOKIE['name_value'];
  $values['phone'] = empty($_COOKIE['phone_value']) ? '' : $_COOKIE['phone_value'];
  $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
  $values['year'] = empty($_COOKIE['year_value']) ? '' : $_COOKIE['year_value'];
  $values['gender'] = empty($_COOKIE['gender_value']) ? 'male' : $_COOKIE['gender_value'];
  $values['bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];

  if (!empty($_COOKIE['langs_value'])) {
      $langs_value = json_decode($_COOKIE['langs_value']);
  }

  $values['langs'] = [];
  if (isset($langs_value) && is_array($langs_value)) {
      foreach ($langs_value as $lang) {
          if (!empty($langs[$lang])) {
              $values['langs'][$lang] = $lang;
          }
      }
  }

  if (!empty($_COOKIE[session_name()]) &&
      session_start() && !empty($_SESSION['login'])) {

    if (!empty($_GET['quit'])) {
      $_SESSION = array();
      if (ini_get("session.use_cookies")) {
          $params = session_get_cookie_params();
          setcookie(session_name(), '', time() - 42000,
              $params["path"], $params["domain"],
              $params["secure"], $params["httponly"]
          );
      }
      foreach($_COOKIE as $key => $value) {
        setcookie($key, '', 100000);
      }
      session_destroy();
      header('Location: ./');
    }

    $messages[] = '<div>Вы вошли с логином '.$_SESSION['login'].'. <a href="./?quit=1">Выйти</a> из аккаунта.</div>';

    try {

      $stmt = $db->prepare("SELECT application_id FROM logs WHERE login = ?");
      $stmt->execute(array($_SESSION['login']));
      $app_id = $stmt->fetchColumn();

      $stmt = $db->prepare("SELECT * FROM application WHERE application_id = ?");
      $stmt->execute(array($app_id));

      $user_data = $stmt->fetch();

      $values['name'] = strip_tags($user_data['name']);
      $values['phone'] = strip_tags($user_data['phone']);
      $values['email'] = strip_tags($user_data['email']);
      $values['year'] = strip_tags($user_data['year']);
      $values['gender'] = strip_tags($user_data['gender']);
      $values['bio'] = strip_tags($user_data['bio']);

      $stmt = $db->prepare("SELECT lang FROM langs WHERE application_id = ?");
      $stmt->execute(array($app_id));

      $user_data = $stmt->fetch();

      $langs_value = explode(", ", $user_data['lang']);

      $values['lang'] = [];
      foreach ($langs_value as $lang) {
        if (!empty($langs[$lang])) {
          $values['lang'][$lang] = $lang;
        }
      }

    } catch(PDOException $e) {
      setcookie('save', 'Ошибка! Результаты не загружены.');
      exit();
    }
  }

  include('form.php');
}
// =========== POST ===========
else {
  // ERRORS
  $errors = FALSE;

  // name
  if (empty($_POST['name'])) {
    setcookie('name_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else if (!preg_match('/^([А-Я]{1}[а-яё]{1,23}|[A-Z]{1}[a-z]{1,23})$/u', $_POST["name"])) {
      setcookie('name_error', '2', time() + 24 * 60 * 60);
      $errors = TRUE;
  }
  else {
    setcookie('name_value', $_POST['name'], time() + 30 * 24 * 60 * 60);
  }

  // phone
  if (empty($_POST['phone'])) {
    setcookie('phone_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('phone_value', $_POST['phone'], time() + 30 * 24 * 60 * 60);
  }

  // email
  if (empty($_POST['email'])) {
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);
  }

  // langs
  $langs = array();

  foreach ($_POST['langs'] as $key => $value) {
      $langs[$key] = $value;
  }

  if (!sizeof($langs)) {
    setcookie('langs_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('langs_value', json_encode($langs), time() + 30 * 24 * 60 * 60);
  }

  // bio
  if (empty($_POST['bio'])) {
    setcookie('bio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {
    setcookie('bio_value', $_POST['bio'], time() + 30 * 24 * 60 * 60);
  }

  // checkbox
  if (empty($_POST['check'])) {
    setcookie('check_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }

  // other
  setcookie('year_value', $_POST['year'], time() + 30 * 24 * 60 * 60);
  setcookie('gender_value', $_POST['gender'], time() + 30 * 24 * 60 * 60);

  if ($errors) {
    header('Location: index.php');
    exit();
  }
  else {
    setcookie('name_error', '', 100000);
    setcookie('phone_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('langs_error', '', 100000);
    setcookie('bio_error', '', 100000);
    setcookie('check_error', '', 100000);
  }

  if (!empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {

    try {
      $stmt = $db->prepare("SELECT application_id FROM logs WHERE login = ?");
      $stmt->execute(array($_SESSION['login']));
      $application_id = $stmt->fetchColumn();

      $stmt = $db->prepare("UPDATE application SET name = ?, phone = ?, email = ?, year = ?, gender = ?, bio = ? WHERE application_id = ?");
      $stmt->execute(array(
        $_POST['name'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['year'],
        $_POST['gender'],
        $_POST['bio'],
        $application_id
      ));
      
      $stmt = $db->prepare("SELECT lang FROM langs WHERE application_id = ?");
      $stmt->execute(array($application_id));
      $lang = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

      $languages = $_POST["langs"];

      if (array_diff($languages, $lang) || array_diff($lang, $languages)) {
        $stmt = $db->prepare("DELETE FROM langs WHERE application_id = ?");
        $stmt->execute(array($application_id));

        $stmt = $db->prepare("INSERT INTO langs SET application_id = ?, lang = ?");
        foreach ($languages as $lang) {
          $stmt->execute(array($application_id, $lang));
        }
      }

    } catch(PDOException $e) {
      setcookie('save', 'Ошибка! Результаты не сохранены.');
      exit();
    }

  }

  else {
    $user_login = uniqid();
    $user_pass = rand(123456, 999999);
    setcookie('login', $user_login);
    setcookie('pass', $user_pass);
    
    try {
      $stmt = $db->prepare("INSERT INTO application SET name = ?, phone = ?, email = ?, year = ?, gender = ?, bio = ?");
      $stmt -> execute(array(
        $_POST['name'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['year'],
        $_POST['gender'],
        $_POST['bio']
      ));

      $application_id = $db->lastInsertId();
      
      foreach ($_POST['langs'] as $lang) {
        $stmt = $db->prepare("INSERT INTO langs (application_id, lang) VALUES (?, ?)");
        $stmt->execute([$application_id, $lang]);
      }

      $stmt = $db->prepare("INSERT INTO logs (application_id, login, pass) VALUES (?, ?, ?)");
      $stmt->execute([$application_id, $user_login, md5($user_pass)]);
      
    }
    catch(PDOException $e){
      setcookie('save', 'Ошибка! Результаты не сохранены.');
      exit();
    }
  }

  setcookie('save', '<div>Спасибо, результаты сохранены!</div>');

  header('Location: index.php');
}
