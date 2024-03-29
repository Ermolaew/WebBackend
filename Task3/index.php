<?php

header('Content-Type: text/html; charset=UTF-8');

// =========== GET ===========
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  if (!empty($_GET['save'])) {
    print('<div class="alert alert-success col-sm-12 col-md-3 mt-3 mx-auto">Спасибо, результаты сохранены!</div>');
  }

  include('form.php');

  exit();
}

// =========== POST ===========

// Errors
$errors = FALSE;

// name
if (empty($_POST['name'])) {
  print('Заполните имя.<br/>');
  $errors = TRUE;
}


//phone
if (empty($_POST['phone'])) {
  print('Заполните телефон.<br/>');
  $errors = TRUE;
}

// email
if (empty($_POST['email'])) {
  print('Заполните почту.<br/>');
  $errors = TRUE;
}

// langs
if (empty($_POST['langs'])) {
  print('Выберите языки программирования.<br/>');
  $errors = TRUE;
}

// bio
if (empty($_POST['bio'])) {
  print('Заполните биографию.<br/>');
  $errors = TRUE;
}

// checkbox
if (empty($_POST['check'])) {
  print('Нельзя отправить форму без согласия с контрактом.<br/>');
  $errors = TRUE;
}

// exit if errors
if ($errors) {
  exit();
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
  print('Ошибка: ' . $e->getMessage());
  exit();
}

header('Location: ?save=1');
