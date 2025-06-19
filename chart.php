<?php
// エラー表示設定 (開発時のみONにすることを推奨)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// data.json ファイルパス
$file = __DIR__ . '/data.json';
$data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

// データがない場合のメッセージ
if (empty($data)) {
    echo "<h1>アンケート結果</h1><p>まだデータがありません。</p><a href='index.html'>アンケートに戻る</a>";
    exit;
}

// --- 各項目の集計 ---

// 1. 性別の集計
$gender_counts = [];
foreach ($data as $entry) {
    $gender = $entry['gender'] ?? '未回答'; // ?? は null合体演算子。未定義の場合のデフォルト値
    if (!isset($gender_counts[$gender])) {
        $gender_counts[$gender] = 0;
    }
    $gender_counts[$gender]++;
}

// 2. 年齢層別の集計 (例: 10代, 20代, 30代...)
$age_ranges = [
    '~19歳' => 0, '20-29歳' => 0, '30-39歳' => 0, '40-49歳' => 0,
    '50-59歳' => 0, '60歳~' => 0, '未回答' => 0
];
foreach ($data as $entry) {
    $age = $entry['age'] ?? null;
    if ($age === null || !is_numeric($age)) {
        $age_ranges['未回答']++;
    } elseif ($age <= 19) {
        $age_ranges['~19歳']++;
    } elseif ($age >= 20 && $age <= 29) {
        $age_ranges['20-29歳']++;
    } elseif ($age >= 30 && $age <= 39) {
        $age_ranges['30-39歳']++;
    } elseif ($age >= 40 && $age <= 49) {
        $age_ranges['40-49歳']++;
    } elseif ($age >= 50 && $age <= 59) {
        $age_ranges['50-59歳']++;
    } else {
        $age_ranges['60歳~']++;
    }
}

// 3. 居住地の集計
$prefecture_counts = [];
foreach ($data as $entry) {
    $prefecture = $entry['prefecture'] ?? '未回答';
    $prefecture = trim($prefecture); // 余計な空白を削除
    if (empty($prefecture)) { // 空文字の場合も「未回答」とする
        $prefecture = '未回答';
    }
    if (!isset($prefecture_counts[$prefecture])) {
        $prefecture_counts[$prefecture] = 0;
    }
    $prefecture_counts[$prefecture]++;
}
// 未回答が多すぎる場合は表示しないなどの調整も可能

// 4. 希望の職種の集計 (既存のもの)
$job_counts = [];
foreach ($data as $entry) {
  $job = $entry['job'] ?? '未回答';
  if (!isset($job_counts[$job])) {
    $job_counts[$job] = 0;
  }
  $job_counts[$job]++;
}

// 5. 週に働ける日数の集計
$days_counts = [];
foreach ($data as $entry) {
    $days = $entry['days'] ?? '未回答';
    if (!isset($days_counts[$days])) {
        $days_counts[$days] = 0;
    }
    $days_counts[$days]++;
}
ksort($days_counts, SORT_NUMERIC); // 数字順にソート

// 6. 働ける曜日の集計 (複数選択)
$weekday_counts = [
    '月' => 0, '火' => 0, '水' => 0, '木' => 0, '金' => 0, '土' => 0, '日' => 0
];
foreach ($data as $entry) {
    if (isset($entry['weekday']) && is_array($entry['weekday'])) {
        foreach ($entry['weekday'] as $day) {
            if (isset($weekday_counts[$day])) { // 定義済みの曜日のみカウント
                $weekday_counts[$day]++;
            }
        }
    }
}
// 未回答の概念をどうするかは要検討。ここでは選択肢がなかった場合はカウントされない。

// 7. 働ける時間帯の集計
$time_counts = [];
foreach ($data as $entry) {
    $time = $entry['time'] ?? '未回答';
    if (!isset($time_counts[$time])) {
        $time_counts[$time] = 0;
    }
    $time_counts[$time]++;
}

// 8. 就職活動で重視している点の集計 (複数選択)
$priority_counts = [
    '給与' => 0, '勤務地' => 0, '企業文化' => 0, '成長性' => 0, '安定性' => 0, '福利厚生' => 0
];
foreach ($data as $entry) {
    if (isset($entry['priority']) && is_array($entry['priority'])) {
        foreach ($entry['priority'] as $prio) {
            if (isset($priority_counts[$prio])) {
                $priority_counts[$prio]++;
            }
        }
    }
}

// 9. 経験のある職種の集計 (複数選択)
$experience_counts = [
    'エンジニア' => 0, 'デザイナー' => 0, '営業' => 0, 'マーケティング' => 0, '事務' => 0
];
foreach ($data as $entry) {
    if (isset($entry['experience']) && is_array($entry['experience'])) {
        foreach ($entry['experience'] as $exp) {
            if (isset($experience_counts[$exp])) {
                $experience_counts[$exp]++;
            }
        }
    }
}

// 10. 最終学歴の集計
$education_counts = [];
foreach ($data as $entry) {
    $education = $entry['education'] ?? '未回答';
    if (!isset($education_counts[$education])) {
        $education_counts[$education] = 0;
    }
    $education_counts[$education]++;
}

// 11. 学部系統の集計
$major_field_counts = [];
foreach ($data as $entry) {
    $major_field = $entry['major_field'] ?? '未回答';
    if (!isset($major_field_counts[$major_field])) {
        $major_field_counts[$major_field] = 0;
    }
    $major_field_counts[$major_field]++;
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>アンケート結果</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #333; }
    h2 { margin-top: 40px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
    .chart-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        margin-top: 20px;
    }
    .chart-item {
        border: 1px solid #eee;
        padding: 10px;
        box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
        background-color: #fff;
    }
    .chart-title {
        text-align: center;
        font-weight: bold;
        margin-bottom: 10px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
  </style>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script>
    google.charts.load('current', {packages: ['corechart', 'bar']}); // barパッケージも追加
    google.charts.setOnLoadCallback(function() {
        drawJobChart();
        drawGenderChart();
        drawAgeChart();
        drawPrefectureChart();
        drawDaysChart();
        drawWeekdayChart();
        drawTimeChart();
        drawPriorityChart();
        drawExperienceChart();
        drawEducationChart();
        drawMajorFieldChart();
    });

    // 1. 希望の職種の割合 (円グラフ)
    function drawJobChart() {
      const data = google.visualization.arrayToDataTable([
        ['職種', '人数'],
        <?php foreach ($job_counts as $job => $count) { echo "['" . addslashes($job) . "', $count],"; } ?>
      ]);
      const options = { title: '希望する職種の割合' };
      const chart = new google.visualization.PieChart(document.getElementById('job_chart_div'));
      chart.draw(data, options);
    }

    // 2. 性別の割合 (円グラフ)
    function drawGenderChart() {
      const data = google.visualization.arrayToDataTable([
        ['性別', '人数'],
        <?php foreach ($gender_counts as $gender => $count) { echo "['" . addslashes($gender) . "', $count],"; } ?>
      ]);
      const options = { title: '性別の割合' };
      const chart = new google.visualization.PieChart(document.getElementById('gender_chart_div'));
      chart.draw(data, options);
    }

    // 3. 年齢層別の集計 (棒グラフ)
    function drawAgeChart() {
      const data = google.visualization.arrayToDataTable([
        ['年齢層', '人数'],
        <?php foreach ($age_ranges as $range => $count) { echo "['" . addslashes($range) . "', $count],"; } ?>
      ]);
      const options = {
        title: '年齢層別の人数',
        hAxis: { title: '人数' },
        vAxis: { title: '年齢層' },
        legend: { position: 'none' }
      };
      const chart = new google.visualization.BarChart(document.getElementById('age_chart_div'));
      chart.draw(data, options);
    }

    // 4. 居住地の集計 (棒グラフ)
    function drawPrefectureChart() {
      const data = google.visualization.arrayToDataTable([
        ['都道府県', '人数'],
        <?php foreach ($prefecture_counts as $pref => $count) { echo "['" . addslashes($pref) . "', $count],"; } ?>
      ]);
      const options = {
        title: '居住地（都道府県）別の人数',
        hAxis: { title: '人数' },
        vAxis: { title: '都道府県' },
        legend: { position: 'none' }
      };
      const chart = new google.visualization.BarChart(document.getElementById('prefecture_chart_div'));
      chart.draw(data, options);
    }

    // 5. 週に働ける日数 (棒グラフ)
    function drawDaysChart() {
      const data = google.visualization.arrayToDataTable([
        ['日数', '人数'],
        <?php foreach ($days_counts as $days => $count) { echo "['" . addslashes($days) . "日', $count],"; } ?>
      ]);
      const options = {
        title: '週に働ける日数',
        hAxis: { title: '人数' },
        vAxis: { title: '日数' },
        legend: { position: 'none' }
      };
      const chart = new google.visualization.BarChart(document.getElementById('days_chart_div'));
      chart.draw(data, options);
    }

    // 6. 働ける曜日 (棒グラフ)
    function drawWeekdayChart() {
      const data = google.visualization.arrayToDataTable([
        ['曜日', '回答数'],
        <?php foreach ($weekday_counts as $day => $count) { echo "['" . addslashes($day) . "', $count],"; } ?>
      ]);
      const options = {
        title: '働ける曜日（複数選択）',
        hAxis: { title: '回答数' },
        vAxis: { title: '曜日' },
        legend: { position: 'none' }
      };
      const chart = new google.visualization.BarChart(document.getElementById('weekday_chart_div'));
      chart.draw(data, options);
    }

    // 7. 働ける時間帯 (円グラフ)
    function drawTimeChart() {
      const data = google.visualization.arrayToDataTable([
        ['時間帯', '人数'],
        <?php foreach ($time_counts as $time => $count) { echo "['" . addslashes($time) . "', $count],"; } ?>
      ]);
      const options = { title: '働ける時間帯' };
      const chart = new google.visualization.PieChart(document.getElementById('time_chart_div'));
      chart.draw(data, options);
    }

    // 8. 就職活動で重視している点 (棒グラフ)
    function drawPriorityChart() {
      const data = google.visualization.arrayToDataTable([
        ['重視点', '回答数'],
        <?php foreach ($priority_counts as $prio => $count) { echo "['" . addslashes($prio) . "', $count],"; } ?>
      ]);
      const options = {
        title: '就職活動で重視している点（複数選択）',
        hAxis: { title: '回答数' },
        vAxis: { title: '重視点' },
        legend: { position: 'none' }
      };
      const chart = new google.visualization.BarChart(document.getElementById('priority_chart_div'));
      chart.draw(data, options);
    }

    // 9. 経験のある職種 (棒グラフ)
    function drawExperienceChart() {
      const data = google.visualization.arrayToDataTable([
        ['職種', '回答数'],
        <?php foreach ($experience_counts as $exp => $count) { echo "['" . addslashes($exp) . "', $count],"; } ?>
      ]);
      const options = {
        title: '経験のある職種（複数選択）',
        hAxis: { title: '回答数' },
        vAxis: { title: '職種' },
        legend: { position: 'none' }
      };
      const chart = new google.visualization.BarChart(document.getElementById('experience_chart_div'));
      chart.draw(data, options);
    }

    // 10. 最終学歴の集計 (円グラフ)
    function drawEducationChart() {
      const data = google.visualization.arrayToDataTable([
        ['学歴', '人数'],
        <?php foreach ($education_counts as $edu => $count) { echo "['" . addslashes($edu) . "', $count],"; } ?>
      ]);
      const options = { title: '最終学歴の割合' };
      const chart = new google.visualization.PieChart(document.getElementById('education_chart_div'));
      chart.draw(data, options);
    }

    // 11. 学部系統の集計 (円グラフ)
    function drawMajorFieldChart() {
      const data = google.visualization.arrayToDataTable([
        ['学部系統', '人数'],
        <?php foreach ($major_field_counts as $mf => $count) { echo "['" . addslashes($mf) . "', $count],"; } ?>
      ]);
      const options = { title: '学部系統の割合' };
      const chart = new google.visualization.PieChart(document.getElementById('major_field_chart_div'));
      chart.draw(data, options);
    }
  </script>
</head>
<body>
  <h1>アンケート結果</h1>

  <div class="chart-container">
    <div class="chart-item">
      <div class="chart-title">希望する職種の割合</div>
      <div id="job_chart_div" style="width: 500px; height: 300px;"></div>
    </div>
    <div class="chart-item">
      <div class="chart-title">性別の割合</div>
      <div id="gender_chart_div" style="width: 500px; height: 300px;"></div>
    </div>
    <div class="chart-item">
      <div class="chart-title">年齢層別の人数</div>
      <div id="age_chart_div" style="width: 500px; height: 300px;"></div>
    </div>
    <div class="chart-item">
      <div class="chart-title">居住地（都道府県）別の人数</div>
      <div id="prefecture_chart_div" style="width: 500px; height: 300px;"></div>
    </div>
    <div class="chart-item">
      <div class="chart-title">週に働ける日数</div>
      <div id="days_chart_div" style="width: 500px; height: 300px;"></div>
    </div>
    <div class="chart-item">
      <div class="chart-title">働ける曜日（複数選択）</div>
      <div id="weekday_chart_div" style="width: 500px; height: 300px;"></div>
    </div>
    <div class="chart-item">
      <div class="chart-title">働ける時間帯</div>
      <div id="time_chart_div" style="width: 500px; height: 300px;"></div>
    </div>
    <div class="chart-item">
      <div class="chart-title">就職活動で重視している点（複数選択）</div>
      <div id="priority_chart_div" style="width: 500px; height: 300px;"></div>
    </div>
    <div class="chart-item">
      <div class="chart-title">経験のある職種（複数選択）</div>
      <div id="experience_chart_div" style="width: 500px; height: 300px;"></div>
    </div>
    <div class="chart-item">
      <div class="chart-title">最終学歴の割合</div>
      <div id="education_chart_div" style="width: 500px; height: 300px;"></div>
    </div>
    <div class="chart-item">
      <div class="chart-title">学部系統の割合</div>
      <div id="major_field_chart_div" style="width: 500px; height: 300px;"></div>
    </div>
  </div>

  <hr>
  <h2>全ての回答一覧</h2>
  <table>
    <thead>
      <tr>
        <th>氏名</th>
        <th>性別</th>
        <th>年齢</th>
        <th>居住地</th>
        <th>希望職種</th>
        <th>学歴</th>
        <th>学部</th>
        <th>週日数</th>
        <th>曜日</th>
        <th>時間帯</th>
        <th>重視点</th>
        <th>経験職種</th>
        <th>その他の希望</th>
        <th>回答日時</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($data as $entry): ?>
      <tr>
        <td><?php echo htmlspecialchars($entry['name'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($entry['gender'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($entry['age'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($entry['prefecture'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($entry['job'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($entry['education'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($entry['major_field'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($entry['days'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars(implode(', ', $entry['weekday'] ?? [])); ?></td>
        <td><?php echo htmlspecialchars($entry['time'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars(implode(', ', $entry['priority'] ?? [])); ?></td>
        <td><?php echo htmlspecialchars(implode(', ', $entry['experience'] ?? [])); ?></td>
        <td><?php echo nl2br(htmlspecialchars($entry['free_text'] ?? '')); ?></td>
        <td><?php echo htmlspecialchars($entry['created_at'] ?? ''); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>
</html>