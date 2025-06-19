<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>就職活動アンケート</title>
  <style>
    /* 全体的なスタイル */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f7f6;
      margin: 0;
      padding: 20px;
      display: flex;
      flex-direction: column;
      align-items: center; /* 中央寄せ */
      color: #333;
    }

    h1 {
      color: #2c3e50;
      text-align: center;
      margin-bottom: 30px;
      border-bottom: 2px solid #3498db;
      padding-bottom: 10px;
      width: 100%;
      max-width: 600px;
    }

    form {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 600px;
      box-sizing: border-box; /* paddingを含めて幅を計算 */
    }

    /* 各質問グループのスタイル */
    .form-group {
      margin-bottom: 20px;
      padding: 15px;
      background-color: #fdfdfd;
      border: 1px solid #e0e0e0;
      border-radius: 6px;
    }

    .form-group label {
      display: block; /* ラベルをブロック要素にして、下に要素を配置 */
      margin-bottom: 8px;
      font-weight: bold;
      color: #555;
    }

    /* テキスト入力、数値入力、セレクトボックスのスタイル */
    input[type="text"],
    input[type="number"],
    select,
    textarea {
      width: calc(100% - 20px); /* paddingを考慮 */
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
      box-sizing: border-box;
    }

    textarea {
      resize: vertical; /* 縦方向のみリサイズ可能 */
      min-height: 80px;
    }

    /* ラジオボタン、チェックボックスのスタイル */
    .radio-group label,
    .checkbox-group label {
      display: inline-block; /* 横並びにする */
      margin-right: 15px;
      margin-bottom: 5px;
      font-weight: normal; /* 親のラベルのboldを打ち消す */
    }

    .radio-group input[type="radio"],
    .checkbox-group input[type="checkbox"] {
      margin-right: 5px;
    }

    /* 必須項目ラベル */
    .required::after {
      content: " *";
      color: #e74c3c;
      font-weight: normal;
      margin-left: 3px;
    }

    /* 送信ボタンのスタイル */
    input[type="submit"] {
      background-color: #3498db;
      color: white;
      padding: 12px 25px;
      border: none;
      border-radius: 5px;
      font-size: 1.1rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
      display: block; /* ブロック要素にして中央寄せ可能に */
      margin: 30px auto 0; /* 上に余白、左右は自動で中央寄せ */
      width: auto; /* 幅をコンテンツに合わせる */
    }

    input[type="submit"]:hover {
      background-color: #2980b9;
    }

    /* レスポンシブ対応 (任意) */
    @media (max-width: 768px) {
      form {
        padding: 20px;
      }
      input[type="text"],
      input[type="number"],
      select,
      textarea {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <h1>就職活動アンケート</h1>
  <form action="submit.php" method="post">

    <div class="form-group">
      <label for="name" class="required">氏名：</label>
      <input type="text" id="name" name="name" required>
    </div>

    <div class="form-group">
      <label class="required">性別：</label>
      <div class="radio-group">
        <label><input type="radio" name="gender" value="男性" required>男性</label>
        <label><input type="radio" name="gender" value="女性">女性</label>
        <label><input type="radio" name="gender" value="その他">その他</label>
      </div>
    </div>

    <div class="form-group">
      <label for="age" class="required">年齢：</label>
      <input type="number" id="age" name="age" min="15" max="100" required>
    </div>

    <div class="form-group">
      <label for="prefecture">現在の居住地（都道府県）：</label>
      <input type="text" id="prefecture" name="prefecture" placeholder="例：東京都">
    </div>

    <div class="form-group">
      <label for="job">希望の職種：</label>
      <select id="job" name="job">
        <option value="エンジニア">エンジニア</option>
        <option value="デザイナー">デザイナー</option>
        <option value="営業">営業</option>
        <option value="マーケティング">マーケティング</option>
        <option value="事務">事務</option>
      </select>
    </div>

    <div class="form-group">
      <label>希望の条件：</label>
      <div style="margin-bottom: 10px;">
        <label for="days">週に働ける日数：</label>
        <input type="number" id="days" name="days" min="1" max="7">日
      </div>
      <div style="margin-bottom: 10px;">
        <label>働ける曜日：</label>
        <div class="checkbox-group">
          <label><input type="checkbox" name="weekday[]" value="月">月</label>
          <label><input type="checkbox" name="weekday[]" value="火">火</label>
          <label><input type="checkbox" name="weekday[]" value="水">水</label>
          <label><input type="checkbox" name="weekday[]" value="木">木</label>
          <label><input type="checkbox" name="weekday[]" value="金">金</label>
          <label><input type="checkbox" name="weekday[]" value="土">土</label>
          <label><input type="checkbox" name="weekday[]" value="日">日</label>
        </div>
      </div>
      <div>
        <label for="time">働ける時間帯：</label>
        <select id="time" name="time">
          <option value="朝">朝</option>
          <option value="昼">昼</option>
          <option value="夕方">夕方</option>
          <option value="夜">夜</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label>就職活動で重視している点（複数選択可）：</label>
      <div class="checkbox-group">
        <label><input type="checkbox" name="priority[]" value="給与">給与</label>
        <label><input type="checkbox" name="priority[]" value="勤務地">勤務地</label>
        <label><input type="checkbox" name="priority[]" value="企業文化">企業文化</label>
        <label><input type="checkbox" name="priority[]" value="成長性">成長性</label>
        <label><input type="checkbox" name="priority[]" value="安定性">安定性</label>
        <label><input type="checkbox" name="priority[]" value="福利厚生">福利厚生</label>
      </div>
    </div>

    <div class="form-group">
      <label>経験のある職種（複数選択可）：</label>
      <div class="checkbox-group">
        <label><input type="checkbox" name="experience[]" value="エンジニア">エンジニア</label>
        <label><input type="checkbox" name="experience[]" value="デザイナー">デザイナー</label>
        <label><input type="checkbox" name="experience[]" value="営業">営業</label>
        <label><input type="checkbox" name="experience[]" value="マーケティング">マーケティング</label>
        <label><input type="checkbox" name="experience[]" value="事務">事務</label>
      </div>
    </div>

    <div class="form-group">
      <label for="education">最終学歴：</label>
      <select id="education" name="education">
        <option value="大学卒">大学卒</option>
        <option value="大学院卒">大学院卒</option>
        <option value="専門学校卒">専門学校卒</option>
        <option value="その他">その他</option>
      </select>
    </div>

    <div class="form-group">
      <label for="major_field">学部系統：</label>
      <select id="major_field" name="major_field">
        <option value="文系">文系</option>
        <option value="理系">理系</option>
        <option value="情報系">情報系</option>
        <option value="その他">その他</option>
      </select>
    </div>

    <div class="form-group">
      <label for="free_text">その他の希望（任意）：</label>
      <textarea id="free_text" name="free_text" rows="5" cols="50"></textarea>
    </div>

    <input type="submit" value="送信">

  </form>
</body>
</html>