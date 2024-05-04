<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="./main.css">
</head>

<body>

    <form action="" method="POST">

        <table>
            <h1>Страница администратора</h1>
            <div class = "langStat">
                <h4 class="langProg">Статистика по ЯП:</h4>
                <?php


                $stmt = $db->prepare("SELECT count(application_id) from langs where lang = 'C#';");
                $stmt->execute();
                $Cs = $stmt->fetchColumn();
                $stmt = $db->prepare("SELECT count(application_id) from langs where lang = 'Java';");
                $stmt->execute();
                $Java = $stmt->fetchColumn();
                $stmt = $db->prepare("SELECT count(application_id) from langs where lang = 'Python';");
                $stmt->execute();
                $Python = $stmt->fetchColumn();             
                
                echo "C#: ";
                echo (empty ($Cs) ? '0' : $Cs) . "</br>";
                echo "Java: ";
                echo (empty ($Java) ? '0' : $Java) . "</br>";
                echo "Python: ";
                echo (empty ($Python) ? '0' : $Python) . "</br>";
            

                echo '<div class="msgbox">';
                if (!empty ($messages)) {
                    foreach ($messages as $message) {
                        print ($message);
                    }
                }
                echo '</div>';
                ?>
            </div>
            <tr>
                <th>id</th>
                <th>Имя</th>
                <th>Телефон</th>
                <th>email</th>
                <th>Год</th>
                <th>Пол</th>
                <th>ЯП</th>
                <th>Биография</th>
                <th>Изменить</th>
            </tr>
            <?php
            foreach ($values as $value) {
                echo '<tr>
                            <td style="font-weight: 700;">';
                print ($value['application_id']);
                echo '</td>
                            <td class="name">
                                <input name="name' . $value['application_id'] . '" value="';
                print (htmlspecialchars(strip_tags($value['name'])));
                echo '">
                            </td>
                            <td class="phone">
                            <input  name="phone' . $value['application_id'] . '" value="';
                print (htmlspecialchars(strip_tags($value['phone'])));
                echo '">
                            </td>
                            <td class="email">
                                <input  name="email' . $value['application_id'] . '" value="';
                print (htmlspecialchars(strip_tags($value['email'])));
                echo '">
                            </td>
                             <td class="year">
                                <select class="year" name="year' . $value['application_id'] . '">';
                for ($i = 2024; $i >= 1922; $i--) {
                    if ($i == $value['year']) {
                        printf('<option selected value="%d">%d </option>', $i, $i);
                    } else {
                        printf('<option value="%d">%d </option>', $i, $i);
                    }
                }
                echo '</select>
                            </td>
                            <td> 
                                <div >
                                    <input type="radio" id="radioMale' . $value['application_id'] . '" name="gender' . $value['application_id'] . '" value="male" ';
                if ($value['gender'] == 'male')
                    echo 'checked';
                echo '>
                                    <label for="radioMale' . $value['application_id'] . '">Мужчина</label>
                                </div>
                                <div >
                                    <input type="radio" id="radioFemale' . $value['application_id'] . '" name="gender' . $value['application_id'] . '" value="female" ';
                if ($value['gender'] == 'female')
                    echo 'checked';
                echo '>
                                    <label for="radioFemale' . $value['application_id'] . '">Женщина</label>
                                </div>
                            </td>
                            ';
                $stmt = $db->prepare("SELECT lang FROM langs WHERE application_id = ?");
                $stmt->execute([$value['application_id']]);
                $langs = $stmt->fetchAll(PDO::FETCH_COLUMN);
                echo '<td class="langs">
                                <div>
                                    <input type="checkbox" id="Cs' . $value['application_id'] . '" name="langs' . $value['application_id'] . '[]" value="C#"' . (in_array('C#', $langs) ? ' checked' : '') . '>
                     
                                    <label for="Cs' . $value['application_id'] . '">C#</label>
                                </div>
                                <div >
                                    <input type="checkbox" id="Java' . $value['application_id'] . '" name="langs' . $value['application_id'] . '[]" value="Java"' . (in_array('Java', $langs) ? ' checked' : '') . '>
                            
                                    <label for="Java' . $value['application_id'] . '">Java</label>
                                </div>
                                <div >
                                    <input type="checkbox" id="Python' . $value['application_id'] . '" name="langs' . $value['application_id'] . '[]" value="Python"' . (in_array('Python', $langs) ? ' checked' : '') . '>
                         
                                    <label for="Python' . $value['application_id'] . '">Python</label>
                                </div>
                            </td>
                            <td class="bio">
                                <textarea  name="bio' . $value['application_id'] . '" id="" cols="15" rows="4" maxlength="128">';
                print htmlspecialchars(strip_tags($value['bio']));
                echo '</textarea>
                            </td>
                            <td >
                            <div class="change">
                       
                                <div class="column-item button_save">
                                    <input name="save' . $value['application_id'] . '" type="submit" value="save' . $value['application_id'] . '"/>
                                </div>
                              
                                <div class="column-item button_delete">
                                    <input name="delete' . $value['application_id'] . '" type="submit" value="delete' . $value['application_id'] . '"/>
                                </div>
                            </div>
                            </td>


                        </tr>';
            }
            ?>
        </table>
        <input type="hidden" name="token" value="<?= $_SESSION['token']; ?>" />
    </form>
</body>

</html>