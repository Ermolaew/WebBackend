<?php

include ('auth.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  try {
    $stmt = $db->prepare("SELECT application_id, name, phone, email, year, gender, bio FROM application");
    $stmt->execute();
    $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    print ('Error : ' . $e->getMessage());
    exit();
  }
  $messages = array();

  $errors = array();
  $errors['error_id'] = empty ($_COOKIE['error_id']) ? '' : $_COOKIE['error_id'];
  $errors['name'] = !empty ($_COOKIE['name_error']);
  $errors['name2'] = !empty ($_COOKIE['name_error2']);
  $errors['phone'] = !empty ($_COOKIE['phone_error']);
  $errors['phone2'] = !empty ($_COOKIE['phone_error2']);
  $errors['year'] = !empty ($_COOKIE['year_error']);
  $errors['email1'] = !empty ($_COOKIE['email_error1']);
  $errors['email2'] = !empty ($_COOKIE['email_error2']);
  $errors['langs'] = !empty ($_COOKIE['langs_error']);
  $errors['bio'] = !empty ($_COOKIE['bio_error']);

  if ($errors['name']) {
    setcookie('name_error', '', 100000);
    $messages['name'] = '<p class="msg">Заполните поле ФИО</p>';
  } else if ($errors['name2']) {
    setcookie('name_error2', '', 10000);
    $messages['name2'] = '<p class="msg">Имя должно начинаться с заглавной буквы и содержать не более 20 символов</p>';
  }
  if ($errors['email1']) {
    setcookie('email_error1', '', 100000);
    $messages['email1'] = '<p class="msg">Заполните поле email</p>';
  } else if ($errors['email2']) {
    setcookie('email_error2', '', 100000);
    $messages['email2'] = '<p class="msg">Неверно заполнено поле email</p>';
  }
  if ($errors['phone']) {
    setcookie('phone_error', '', 100000);
    $messages['phone'] = '<p class="msg">Заполните поле телефон</p>';
  }
  if ($errors['phone2']) {
    setcookie('phone_error2', '', 10000);
    $messages['phone2'] = '<p class="msg">Неверно заполнено поле телефон</p>';
  }
  if ($errors['year']) {
    setcookie('year_error', '', 100000);
    $messages['year'] = '<p class="msg">Вам должно быть 18 лет или больше</p>';
  }
  if ($errors['langs']) {
    setcookie('langs_error', '', 100000);
    $messages['langs'] = '<p class="msg">Выберите язык программирования</p>';
  }
  if ($errors['bio']) {
    setcookie('bio_error', '', 100000);
    $messages['bio'] = '<p class="msg">Расскажите о себе что-нибудь</p>';
  }
  $_SESSION['token'] = bin2hex(random_bytes(32));
  $_SESSION['login'] = $validUser;

  include ('db.php');
  exit();
} else {

  if (!empty ($_POST['token']) && hash_equals($_POST['token'], $_SESSION['token'])) {
    foreach ($_POST as $key => $value) {

      if (preg_match('/^delete(\d+)$/', $key, $matches)) {
        $app_id = $matches[1];
        setcookie('delete', $app_id, time() + 24 * 60 * 60);
        $stmt = $db->prepare("DELETE FROM application WHERE application_id = ?");
        $stmt->execute([$app_id]);
        $stmt = $db->prepare("DELETE FROM langs WHERE application_id = ?");
        $stmt->execute([$app_id]);
        $stmt = $db->prepare("DELETE FROM logs WHERE application_id = ?");
        $stmt->execute([$app_id]);
      }
      if (preg_match('/^save(\d+)$/', $key, $matches)) {
        $app_id = $matches[1];
        $dates = array();
        $dates['name'] = $_POST['name' . $app_id];
        $dates['phone'] = $_POST['phone' . $app_id];
        $dates['email'] = $_POST['email' . $app_id];
        $dates['year'] = $_POST['year' . $app_id];
        $dates['gender'] = $_POST['gender' . $app_id];
        $langs = $_POST['langs' . $app_id];
        $dates['bio'] = $_POST['bio' . $app_id];

        $name = $dates['name'];
        $phone = $dates['phone'];
        $email = $dates['email'];
        $year = $dates['year'];
        $gender = $dates['gender'];
        $bio = $dates['bio'];

        if (empty ($name)) {
          setcookie('name_error', '1', time() + 24 * 60 * 60);
          $errors = TRUE;
        } else if (!preg_match('/^[A-Z]{1}[a-z]{1,20}$/', $name)) {
          setcookie('name_error2', '1', time() + 24 * 60 * 60);
          $errors = TRUE;
        }
        if (empty ($phone)) {
          setcookie('phone_error', '1', time() + 24 * 60 * 60);
          $errors = TRUE;
        } else if (!preg_match('/^(8|\+7)[-\(]?\d{3}\)?-?\d{3}-?\d{2}-?\d{2}$/', $phone)) {
          setcookie('phone_error2', '2', time() + 24 * 60 * 60);
          $errors = TRUE;
        }
        if (empty ($email)) {
          setcookie('email_error1', '1', time() + 24 * 60 * 60);
          $errors = TRUE;
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          setcookie('email_error2', '1', time() + 24 * 60 * 60);
          $errors = TRUE;
        }
        if (2024 - $year < 18) {
          setcookie('year_error', '1', time() + 30 * 24 * 60 * 60);
          $errors = TRUE;
        }
        if (empty ($langs)) {
          setcookie('langs_error', '1', time() + 24 * 60 * 60);
          $errors = TRUE;
        }
        if (empty ($bio)) {
          setcookie('bio_error', '1', time() + 24 * 60 * 60);
          $errors = TRUE;
        }
        if ($errors) {
          setcookie('error_id', $app_id, time() + 24 * 60 * 60);
          header('Location: index.php');
          exit();
        } else {
          setcookie('name_error', '', 100000);
          setcookie('name_error2', '', 100000);
          setcookie('phone_error', '', 100000);
          setcookie('phone_error2', '', 10000);
          setcookie('email_error1', '', 100000);
          setcookie('year__error', '', 100000);
          setcookie('email_error2', '', 100000);
          setcookie('langs_error', '', 100000);
          setcookie('bio_error', '', 100000);
          setcookie('error_id', '', 100000);
        }
        $stmt = $db->prepare("SELECT name, phone, email, year, gender, bio FROM application WHERE application_id = ?");
        $stmt->execute([$app_id]);
        $old_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT lang FROM langs WHERE application_id = ?");
        $stmt->execute([$app_id]);
        $old_langs = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (array_diff($dates, $old_dates[0])) {
          $stmt = $db->prepare("UPDATE application SET name = ?, phone=?, email = ?, year = ?, gender = ?, bio = ? WHERE application_id = ?");
          $stmt->execute([$dates['name'], $dates['phone'],$dates['email'], $dates['year'], $dates['gender'], $dates['bio'], $app_id]);
        }
        if (array_diff($langs, $old_langs) || count($langs) != count($old_langs)) {
          $stmt = $db->prepare("DELETE FROM langs WHERE application_id = ?");
          $stmt->execute([$app_id]);
          $stmt = $db->prepare("INSERT INTO langs (application_id, lang) VALUES (?, ?)");
          foreach ($langs as $lang) {
            $stmt->execute([$app_id, $lang]);
          }
        }
      }
    }
  } else {
    die ('Ошибка CSRF: недопустимый токен');
  }
  header('Location: index.php');
}
