<?

include('/usr/local/www/apache22/data/elves.ru/compare/settings.php');
require_once('/usr/local/www/apache22/data/elves.ru/admin/pdf_id_functions.php');

echo $header;

if (empty($_POST) AND empty($_GET)):
 form_view();
 loaded_pdf();
endif;

if (isset($_GET['load'])):
 load();
endif;

if (isset($_GET['del'])):
 del();
endif;


echo $foother;
?>
