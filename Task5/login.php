<?php

header('Content-Type: text/html; charset=UTF-8');

session_start();

if (!empty($_SESSION['login'])) {
  header('Location: ./');
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <title>Задание 5</title>
</head>
<body>
  <div class="container">
    <div class="row align-items-center justify-content-center">
      <div class="col-sm-12 col-md-6">
        <form method="POST" class="bg-light p-3 mt-5">
          <div class="form-group">
            <label for="login-input">Логин</label>
            <input type="text" class="form-control" id="login-input" name="login" placeholder="Ваш логин">
          </div>
          <div class="form-group">
            <label for="pass-input">Пароль</label>
            <input type="password" class="form-control" id="pass-input" name="pass" placeholder="Ваш пароль">
          </div>
          <button type="submit" class="btn btn-primary">Войти</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
<?php
}
else {

  $login = strip_tags($_POST['login']);
  $pass =  md5($_POST['pass']);

  $db_user = 'u67358';
  $db_pass = '5004219'; 

  $db = new PDO('mysql:host=localhost;dbname=u67358', $db_user, $db_pass, array(PDO::ATTR_PERSISTENT => true));

  try {

    $stmt = $db->prepare("SELECT * FROM logs WHERE login = ?");
    $stmt->execute(array($login));
    $user_data = $stmt->fetch();

    if ($pass == $user_data['pass']) {
      $_SESSION['login'] = $login;
    }
    else {
      echo "Неверные данные, повторите попытку.";
      exit();
    }

  }
  catch(PDOException $e) {
    echo 'Ошибка: ' . $e->getMessage();
    exit();
  }

  header('Location: ./');
}
