<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <title>Задание 3</title>
</head>
<body>
  <div class="container py-3">
    <div class="row justify-content-center align-items-center">
      <div class="col-sm-12 col-md-6">
        <form id="form" action="" method="POST">

          <!-- Имя -->
          <div class="form-group">
            <label for="name-input">Имя</label>
            <input id="name-input" class="form-control" type="text" name="name" placeholder="Ваше имя"/>
          </div>

          <!--Телефон-->
          <div class="form-group">
            <label for="phone-input">Телефон</label>
            <input id="phone-input" class="form-control" type="text" name="phone" placeholder="Ваш телефон" />
          </div>

          <!-- Email -->
          <div class="form-group">
            <label for="email-input">Email</label>
            <input id="email-input" class="form-control" type="email" name="email" placeholder="Ваш Email" />
          </div>

          <!-- Год рождения -->
          <div class="form-group">
            <label for="year-select">Год рождения</label>
            <select id="year-select" class="form-control" name="year">
              <?php
              for ($i = 2024; $i > 1900; $i--) {
                print('<option value="'.$i.'">'.$i.'</option>');
              }
              ?>
            </select>
          </div>

          <!-- Пол -->
          <div class="form-group">
            <label>Пол</label>
            <div class="control">
              <!-- Мужской -->
              <div class="custom-control custom-radio custom-control-inline">
                <input id="gender-radio-1" type="radio" name="gender" class="custom-control-input" value="male" checked>
                <label class="custom-control-label" for="gender-radio-1">Мужской</label>
              </div>
              <!-- Женский -->
              <div class="custom-control custom-radio custom-control-inline">
                <input id="gender-radio-2" type="radio" name="gender" class="custom-control-input" value="female">
                <label class="custom-control-label" for="gender-radio-2">Женский</label>
              </div>
            </div>
          </div>

          <!-- Любимый ЯП -->
          <div class="form-group">
            <label for="powers-select">Любимый язык программиррования</label>
            <select id="powers-select" class="form-control" name="langs[]" multiple size="3">
              <option value="C#">C#</option>
              <option value="Java">Java</option>
              <option value="Python">Python</option>
            </select>
          </div>

          <!-- Биография -->
          <div class="form-group">
            <label for="bio-textarea">Биография</label>
            <textarea id="bio-textarea" name="bio" class="form-control" placeholder="Напишите немного о себе..."></textarea>
          </div>

          <!-- Чекбокс -->
          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input id="ok-checkbox" type="checkbox" class="custom-control-input" name="check">
              <label class="custom-control-label" for="ok-checkbox"> С контрактом ознакомлен(а).</label>
            </div>
          </div>

          <!-- Кнопка -->
          <div class="form-group">
            <div class="control">
              <button name="send" type="submit" class="btn btn-primary" value="ok">Сохранить</button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</body>
</html>
