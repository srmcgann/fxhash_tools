<?
  $url = str_replace('+', '%20', 'https://www.fxhash.xyz/u/' . urlencode($_GET['user']));
  $data = file_get_contents($url);
  echo $data ? 1 : 0;
?>
