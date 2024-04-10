<?php

header('Content-Type: text/html; charset=UTF-8');

// =========== GET ===========
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  $messages = array();
  $errors = array();
  $values = array();
  $langs = array();

  // SAVE PARAMETER
  if (!empty($_COOKIE['save'])) {
    setcookie('save', '', 100000);
    $messages[] = $_COOKIE['save'];
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
      длина имени от 2 до 24 сиволов, обязательно начинается с заглавной.</div>';
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

  $user = 'u67358';
  $pass = '5004219';
  $db = new PDO('mysql:host=localhost;dbname=u67358', $user, $pass, array(PDO::ATTR_PERSISTENT => true));

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
}
  catch(PDOException $e){
    setcookie('save', 'Ошибка! Результаты не сохранены.');
    exit();
  }

  header('Location: index.php');
}
