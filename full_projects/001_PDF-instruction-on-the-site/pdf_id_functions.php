include('/usr/local/www/apache22/data/elves.ru/compare/translit.php');

function form_view() {
 global $new_pdf;
 echo $new_pdf;
}

function loaded_pdf() { // Функция отображения уже загруженных PDF-инструкций

 global $table_foother, $table_header;

 echo $table_header;
 
 $baners_sql = mysql_query("SELECT * FROM `oc_pdf` ORDER BY `oc_pdf`.`id` DESC;");
 while($baners_data = mysql_fetch_array($baners_sql)):

  $product_data = mysql_fetch_array(mysql_query("SELECT * FROM oc_product WHERE product_id = {$baners_data['product_id']};"));
  
  $product_name = $product_data['model'];
  $product_id = chpu_id($baners_data['product_id']);
  $file = $baners_data['file'];
  $filesize = round((filesize('/usr/local/www/apache22/data/elves.ru/pdf/'.$baners_data['file']))/(1024*1024),3);

  echo <<<NND
  <tr>
   <td style="background: #ffffcc; width: 540px; padding:2px; font-size:15px; font-family: Calibri, Arial;">
    <center><a href="$product_id">$product_name</a><br/>
    <a href="http://elves.ru/pdf/$file"><i>Скачать PDF - $filesize мб</i></a></center>
   </td>
   <td style="background: #ffffcc; width: 70px; padding:2px; font-size:15px; font-family: Calibri, Arial;">
    <a href="pdf_id.php?del=$file">Удалить</a>
   </td>
  </tr>
NND;
 endwhile;

 echo $table_foother;

 return true;
}



function validate() { // Функция проверки загружаемого файла
 global $product_id;

 if (isset($_POST['product_id']) AND $_POST['product_id']!=""):

  $product_id = (int)$_POST['product_id'];
  $check_product_id = mysql_fetch_array(mysql_query('SELECT * FROM oc_product WHERE product_id="'.$product_id.'";'));

  if (empty($check_product_id)):

   echo "Введен ID несуществующего товара ((( Повторим <a href='pdf_id.php'>ввод</a>?<br/>";
   return false;

  endif;

 else:

  echo "Не указан ID товара ((( Повторим <a href='pdf_id.php'>ввод</a>?<br/>";
  return false;

 endif;

 if(!is_uploaded_file($_FILES["uploadfile"]["tmp_name"])):
  
  echo "Ошибка какая-то, файл не записался в директорию... Может размер больше 50 Мб?.. Попробуем <a href='pdf_id.php'>еще раз</a> загрузить?<br/>";
  return false;
 
 else:

  if (end(explode(".", $_FILES["uploadfile"]["name"]))!='pdf'):

  echo "Ошибка какая-то... Может это не PDF-файл?.. Попробуем <a href='pdf_id.php'>еще раз</a> загрузить?<br/>";
  return false;

  endif;

 endif;

 return true;

}

function load() { // Функция загрузки файла после проверки
 
 global $product_id;

 if(!validate()):
  return false;
 endif;

 $product_data = mysql_fetch_array(mysql_query('SELECT * FROM `oc_product` WHERE product_id='.$product_id.';'));

 $product_name = translit($product_data['model']);

 if (move_uploaded_file($_FILES["uploadfile"]["tmp_name"], "/usr/local/www/apache22/data/elves.ru/pdf/".$product_name.'.pdf')):

  mysql_query("INSERT INTO `oc_pdf`(`product_id`, `file`) VALUES ('".$product_id."','".$product_name.".pdf');");

  echo '<meta http-equiv="refresh" content="3; url=http://elves.ru/admin/pdf_id.php">
        <center><h2>PDF-инструкция успешно добавлена</h2></center>';

 else:

   echo 'Ошибка какая-то, файл не записался в директорию...';

 endif;

}


function del() { // Функция удаления загруженного ранее файла
 
 if (!isset($_GET['del']) OR $_GET['del']==''):
  echo 'Нечего удалять';
  return false;
 endif;

 mysql_query("DELETE FROM oc_pdf WHERE file='{$_GET['del']}';");
 unlink("/usr/local/www/apache22/data/elves.ru/pdf/".$_GET['del']);

 echo '<meta http-equiv="refresh" content="3; url=http://elves.ru/admin/pdf_id.php">
        <center><h2>PDF-инструкция успешно удалена</h2></center>';

 return true;

}

function pdf_view() { // Функция отображения ссылки на инструкцию на сайте

  $product_id = (int)$_GET['product_id'];
  $check_product_id = mysql_fetch_array(mysql_query('SELECT * FROM oc_pdf WHERE product_id="'.$product_id.'";'));

  if (empty($check_product_id['product_id'])):
   return false;
  else:
   
   echo <<<HHH
<div style="float:right; padding:5px; margin:5px; border:1px #215321 solid; background:#e0f2d9; width: 200px; font: 12px Arial; color: #215321;">
<table border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td width="40px;">
   <center><a href="http://elves.ru/pdf/" style="font-size:10px;"><img src="http://elves.ru/pdf/pdf.png" /><br/>
   11,3мб</a></center>
  </td>
  <td style="text-align:right;">
   <p>Скачать инструкцию для:<br/> <a href="http://elves.ru/pdf/" style="font-size:10px;">Очиститель воздуха Panasonic F-VXD50R-S-сер.</a></p>
  </td>
 </tr>
</table>
</div><br/><br/>
HHH;
  endif;

  return true;

}


$header = <<<EOA
<html>
<head>
 <title>PDF инструкции</title>
</head>
<body>
<style>
 body {background: #ffc;}
 a:link	{text-decoration: none;	color: #04449a; }
 a:visited	{text-decoration: none;	color: #336699; }
 a:active	{text-decoration: underline;color: #ff0000; }
 a:hover	{text-decoration: underline; color: #ff0000; }
</style>
 <center><span style="font-family: Cambria, Georgia; font-size: 32px; color:brown; font-weight:bold;">Загрузка pdf-инструкций на сайт</span></center>
 <br/>
EOA;

$foother = <<<EOB
</body>
</html>
EOB;

$new_pdf = <<<EOC
<center>
 <table cellpadding="0" cellspacing="5" style="border: 1px green solid; background: white; width:400px;">
  <tr>
   <td style="background: white;">
     <img src="img/add.png" style="margin-bottom:-5px;"> <span style="font-family: Calibri, Arial; font-size: 26px; color:green; font-weight:bold;">Новая инструкция</span><br/><br/>
     <form action="http://elves.ru/admin/pdf_id.php?load" method="POST" enctype="multipart/form-data">
	 <div style="text-align:right;">
	  <span style="font-family: Calibri, Arial; font-size: 20px;">Инструкция:</span> <input type="file" name="uploadfile" style="width:250px; border: 1px black solid;"><br/>
	  <span style="font-family: Calibri, Arial; font-size: 20px;">ID товара:</span> <input type="text" name="product_id" style="width:250px; border: 1px black solid;"><br/>
	 </div>
	 <br/>
	 <center>
	  <input type="submit" style="width:150px; border:1px black solid;" value="Добавить">
	 </center>
	 </form>
   </td>
  </tr>
 </table>
</center>
<br/>
EOC;

$table_header = <<<EOD
<center>
 <table cellpadding="0" cellspacing="5" style="border: 1px green solid; background: white; width:610px;">
  <tr>
   <td style="background: #caddf7; width: 540px; padding:2px; font-size:15px; font-family: Calibri, Arial;">
    <center>Pdf-инструкция</center>
   </td>
   <td style="background: #caddf7; width: 70px; padding:2px; font-size:15px; font-family: Calibri, Arial;">
    Удалить
   </td>
  </tr>
EOD;

$table_foother = <<<EOE
 </table>
</center>
</body>
</html>
EOE;


?>
