<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

//var_dump($_POST);
//if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//    echo "POST送信されていません";
//    exit;
//}

// データ受け取り
$data = [
  'name' => $_POST['name'],
  'gender' => $_POST['gender'],
  'age' => $_POST['age'],
  'prefecture' => $_POST['prefecture'],
  'job' => $_POST['job'],
  'education' => $_POST['education'],
  'major_field' => $_POST['major_field'],
  'days' => $_POST['days'],
  'weekday' => isset($_POST['weekday']) ? $_POST['weekday'] : [],
  'time' => $_POST['time'],
  'priority' => isset($_POST['priority']) ? $_POST['priority'] : [],
  'experience' => isset($_POST['experience']) ? $_POST['experience'] : [],
  'free_text' => $_POST['free_text'],
  'created_at' => date('Y-m-d H:i:s')
];

// 既存のJSONデータを読み込み
$file = __DIR__ . '/data.json';
$jsonData = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

// データ追加
$jsonData[] = $data;

// 保存
file_put_contents($file, json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

// 完了メッセージ
echo "アンケートを送信しました。<a href='chart.php'>結果を見る</a>";
?>