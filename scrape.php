<?
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
function add_hashmark($n){
  return '#' . $n;
}
error_reporting(E_ALL);
  $user = urlencode('NukCZ');//$_GET['user']);
  if($user){
    $url = str_replace('+', '%20', 'https://www.fxhash.xyz/u/' .$user);
    $data = file_get_contents($url);
    if($data){
      $data = file_get_contents("$url/collection");
      $obj_json = substr($data, strpos($data, '"objkts":[{"id"'));
      $obj_json = '{' . substr($obj_json, 0, strpos($obj_json, '"}]}},"')+4);
      $ar=[];
      $json = json_decode($obj_json);
      foreach($json as $key => $val_){
        if($key == 'objkts'){
          for($i = 0; $i<sizeof($val_); ++$i){
            $ar_ = [
                     'id'=>'',
                     'author_name'=>'',
                     'author_avatar'=>'',
                     'author_name'=>'',
                     'tags'=>'',
                     'createdAt'=>'',
                     'time'=>''
                   ];
            foreach($val_[$i] as $key => $val){
              switch($key){
                case 'id': $ar_['id'] = $val; break;
                case 'issuer':
                  $ar_['author_name'] = $val->{'author'}->{'name'};
                  $ar_['author_avatar'] = str_replace('ipfs://', 'https://gateway.fxhash.xyz/ipfs/', $val->{'author'}->{'avatarUri'});
                  break;
                case 'name': $ar_['objkt_name'] = $val; break;
                case 'metadata':
                  $ar_['tags'] = implode(' ', array_map('add_hashmark',  $val->{'tags'}));
                break;
                case 'createdAt':
                  $ar_['createdAt'] = $val;
                  $ar_['time'] = strtotime($val);
                  break;
              }
            }
            $ar[] = $ar_;
          }
        }
      };

      function cmp($a, $b) {
        return strcmp($a['time'], $b['time']);
      }

      usort($ar, "cmp");

      $fp = fopen("./csvs/".$user."_collection.csv", 'w');
      $runonce = false;
      foreach($ar as $key => $val) {
        if(!$runonce){
          foreach($val as $key2 => $value){
            $headers[] = $key2;
          }
        }
        $runonce = true;
      }
      $header_count = sizeof($headers);
      fputs($fp, implode(', ', $headers) . "\n");
      foreach($ar as $key => $val) {
        $vals = [];
        foreach($val as $key2 => $value){
          $vals[] = str_replace(',', ';', $value);
        }
        fputs($fp, ($vals ? implode(', ', $vals) : []) . "\n");
      }
      fclose($fp);
      echo 1;
    } else {
      echo 0;
    }
  } else {
    echo 0;
  }
?>
