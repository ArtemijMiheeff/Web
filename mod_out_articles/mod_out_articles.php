<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_out_articles
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Приветствуем текущего пользователя
$user = JText::_(JFactory::getUser());
print "<p> Привет ".$user."! </p>";

// Получаем значения параметров модуля
$hint = (int) $params->get('show_author', '30');
//$color = (string) $params->get('active_row');

// Генерируем стили для раздела style:
//<style>
//  tr:hover {color: цвет;}
//  table {border: 1px;}
//</style>
//$rowStyle = '<style> .r1:hover {background-color: '.$color.';}'.'.t1 {border: 1px;}</style>';

// Добавляем стили на страницу:
//echo $rowStyle; 

//Получаем данные из БД:
// Берём ссылку на объект базы данных:
$db =& JFactory::getDBO();
// Подготовка имен: заключаем название таблицы в 
// кавычки нужного вида: для MySQL это апострофы “`”
$tableContent  = $db->quoteName('#__content');
$tableUsers = $db->quoteName('#__users');
echo "$tableContent:".$tableContent."; $tableUsers: ".$tableUsers."<br>";
// Строим SQL:
// Create a new query object.
$sql = $db->getQuery(true);
/*
$sql = "SELECT a.`title` , a.`alias` , b.`name` 
        FROM  $tableContent AS a, $tableUsers AS b
        WHERE a.`created_by` = b.`id`"; 
echo $sql;
*/

/*
SELECT a.`title` , a.`alias` , b.`username` , b.`name` 
FROM  `test2_content` AS a
INNER JOIN  `test2_users` AS b ON a.`created_by` = b.`id` 

или без кавычек
SELECT a.title,a.alias,b.username,b.name 
FROM `#__content` AS `a` 
INNER JOIN `#__users` AS `b` ON (`a`.`created_by` = `b`.`id`)
*/

/*
 SELECT * 
 FROM `sfu83_usergroups` 
 INNER JOIN (`sfu83_users` INNER JOIN `sfu83_user_usergroup_map` ON `sfu83_users`.id = `sfu83_user_usergroup_map`.user_id) ON `sfu83_user_usergroup_map`.group_id = `sfu83_usergroups`.id;
 */

 /*
$sql->select(array('a.name', 'a.lastvisitDate', 'a.usergroup.title', 'a.block'))
    ->from($db->quoteName('#__usergroups', 'a'))
    ->join('INNER', ->from($db->quoteName('#__users')) ->join('INNER', $db->quoteName('#__user_usergroup_map', 'uum') . ' ON (' . $db->quoteName('#__users.id') . ' = ' . $db->quoteName('uum.user_id')')'  . ' ON (' . $db->quoteName('uum.group_id') . ' = ' . $db->quoteName('a.id') . ')'));

 */

 $sql = "SELECT * \n"

    . "FROM `#__usergroups` \n"

    . "INNER JOIN (`#__users` INNER JOIN `#__user_usergroup_map` ON `#__users`.id = `#__user_usergroup_map`.user_id) ON `#__user_usergroup_map`.group_id = `#__usergroups`.id;";

echo "<br> sql=".$sql;

// Предварительно устанавливаем текст запроса 
$db->setQuery($sql);
?>





<div class="container mt-4 mb-4">
    <form id="myForm" action="index.php" method="post">
        <div class="form-group">
            <div class = "row">
                <div class="col-md-2">
                    <label for="colorRow" class="form-label">Цвет:</label>
                </div>
                <div class="col-md-4">
                    <input type="color" class="form-control form-control-lg" id="colorRow" name="favcolor" value="#FFD700"/>
                </div>
            </div>
            <div class = "row">
                <div class="col-md-2">
                    <label for="nameRow" class="form-label">Группа:</label>
                </div>
                <div class="col-md-4">
                    <input type=text class="form-control " id="group" name="group_name">
                </div>
            </div>
        </div>
            <!--<input type="submit" name="send" value="OK"/>-->
        <div class="d-flex justify-content-center">
            <button type="submit" name="send" class="btn btn-dark btn-sm">Отправить форму</button>
        </div>
        
    </form>
</div>

<script>
    document.getElementById("myForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Предотвращаем стандартное поведение отправки формы
    
    // Получаем значения из формы
    var colorRow = document.getElementById('colorRow').value;
    var numGr = document.getElementById('group').value;

    var tableRows = document.querySelectorAll('table tbody tr');

    tableRows.forEach(function(row) {
        var cells = row.querySelectorAll('td');
        cells.forEach(function(cell) {
            var selectedCell = cell.textContent;
            if(selectedCell === numGr)
            {
                row.style.backgroundColor = colorRow;
                
            }
        });
    });
});
</script>







<?php
// Выполняем запрос и анализируем результат 
if ($db->execute()) {
 // Запрос выполнился успешно. Получаем кол-во 
    // задействованных в запросе строк.
    $RowCount = $db->getAffectedRows();
    // Выводим сообщение
    //echo JText::sprintf  ('<p>На сайте %u статей.</p>',  $RowCount);
 
 // Если нужно, выводим подсказку к статье:
 if ($hint) {
   echo "<style>
         td > .hint {
              position: absolute;
              display: none;
            }
            td:hover > .hint {
              display: block;
              background-color: lightgreen;
              border: 3px outset;
     border-radius: 7px;
            }
   </style>";
 }
 
 //Выводим таблицу статей:
 $table="<thead><tr><th>№<th>Имя<th>Дата последнего входа <th> Группа <th>Заблокирован</tr></thead>";
 $table=$table."<tbody>";
 // получаем данные в виде ассоциативного списка
 $data = $db->loadAssocList(); 
 $i = 1;
 $hintContent="";
 $article = array ();
    
    foreach ($data as $row) 
    {   

      
    $date = JTEXT::_(date('l', strtotime($row['lastvisitDate'])));
    $format = (int)$params->get('data_type');
    //echo $date;
    //echo "_________"; 
    if ($format === 1){
      $date = date(JTEXT::_(date('l'))."/".'d/'.JTEXT::_(date('F')).'/Y', strtotime($row['lastvisitDate']));
      //echo $date;
    }
        
    else if($format === 2){
      $date = date(JTEXT::_(date('F')).'/'.JTEXT::_(date('l')).'/'.'d'.'/Y', strtotime($row['lastvisitDate']));
      //echo $date;
    }
    else
    {
      $date = $row['lastvisitDate'];
    }
        
            
        
       if ($hint) {
      $data="Автор: <br>Логин - ".$row['username']."<br>Имя   - ".$row['name'];
   $hintContent="<div class=\"hint\">".$data."</div>";
    }

        
        
   if ($row['block']==0){
   $color = 'green';
   $rowStyle = '<style> .r2:hover {background-color: '.$color.';}'. '.t1 {border: 2px black solid;}'. 'td, th {border: 2px black solid}</style>';
   echo $rowStyle; 
 //echo $row['block'];
 if ($row['group_id']==$_REQUEST['group_name'])
 {    
    $table=$table."<tr class='r3'><td>".$i."<td>".$row['name']."<td>".$date."<td>".$row['group_id']."<td>".$row['block']."<td>";
 }
 else
 {
    $table=$table."<tr class='r2'><td>".$i."<td>".$row['name']."<td>".$date."<td>".$row['group_id']."<td>".$row['block'].$hintContent."<td>";
 }

    $i=$i+1;
   //echo 'red';
    }
   else {
   $color = 'red';
   
   $rowStyle = '<style> .r1:hover {background-color: '.$color.';}'. '.t1 {border: 2px black solid;}'. 'td, th {border: 2px black solid}</style>';
   //echo 'green';
   echo $rowStyle; 
   if ($row['group_id']==$_REQUEST['group_name'])
   { 
    $table=$table."<tr class='r3'><td>".$i."<td>".$row['name']."<td>".$date."<td>".$row['group_id']."<td>".$row['block']."<td>";
   }
   else
   {
    $table=$table."<tr class='r1'><td>".$i."<td>".$row['name']."<td>".$date."<td>".$row['group_id']."<td>".$row['block'].$hintContent."<td>";
   }
    $i=$i+1;
    }
        
        
    

    };
 $table=$table."</tbody>";
 
 echo "<table class='t1'>".$table."</table>"; 
 
   } else {
    // Неудача (например, ошибка в синтаксисе SQL)
    ECHO "<p>Ошибка при работе с БД.</p>";
}

// if(isset($_REQUEST['group_name']))
// {
//     foreach ($data as $row) 
//     {
//         if($row['group_id'] == $_REQUEST['group_name'])
//         {
//             $rowStylee =	'<style> 
//                 .r3 {
//                     background-color: ' . $_REQUEST['favcolor'] . ';
//                 }
//             </style>';
//         }
//     }
// }
// echo $rowStylee;

?>

