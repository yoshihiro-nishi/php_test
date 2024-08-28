<?php
  $pageFlg = 0;//ページ遷移の変数
  
  if(!empty($_POST["btn_confirm"])){//確認画面
  
    // バリデーションチェック
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // フォームデータを取得
      $user = htmlspecialchars($_POST["user"]);
      $user_kana = htmlspecialchars($_POST["user_kana"]);
      //$gender = htmlspecialchars($_POST["gender"]);
      $education = htmlspecialchars($_POST["education"]);
      $postcode = htmlspecialchars($_POST["postcode"]);
      $prefecture = htmlspecialchars($_POST["prefecture"]);
      $city = htmlspecialchars($_POST["city"]);
      $email = htmlspecialchars($_POST["email"]);
      //$jobs[] = htmlspecialchars($_POST["jobs"]);
      $etc = htmlspecialchars($_POST["etc"]);
      //$privacy_policy = htmlspecialchars($_POST["privacy_policy"]);
  
      // 項目名の設定
      $user_name = "名前";
      $user_kana_name = "名前（カナ）";
      //$gender_name = "性別";
      $education_name = "最終学歴";
      $postcode_name = "郵便番号";
      $prefecture_name = "都道府県";
      $city_name = "市区町村";
      $email_name = "メールアドレス";
      //$jobs[] = "希望職種";
      $etc_name = "その他要望など";
      //$privacy_policy = "プライバシーポリシー";
  
      // バリデーション
      $errors = array();
      emptyCheck($user, $user_name, $errors);
      lengthCheck($user,$user_name,60, $errors);
      emptyCheck($user_kana,$user_kana_name, $errors);
      lengthCheck($user_kana,$user_kana_name, 60, $errors);
      kanaCheck($user_kana,$user_kana_name, $errors);


      emailFormatCheck($email, $email_name, $errors);

      if (count($errors) != 0){
        for( $i = 0; $i < count($errors); $i++ ){
        echo $errors[$i];
        echo "<br>";
        }
      }
       else {
          // バリデーションが成功した場合
          $pageFlg = 1;
      }
    }
  }
  if(!empty($_POST["btn_submit"])){//完了画面
      $pageFlg = 2;
   }
  if(!empty($_POST["btn_close"])){//閉じる画面
       
   }
  
  function emptyCheck($val, $valname, &$errors): void
  // 必須チェック
  {
    if (empty($val)) {
      $errors[] = "$valname は必須です。";
    } 
  }
  function lengthCheck($val, $valname, $length, &$errors): void
  // 桁数チェック
  {
    if(mb_strlen ($val) > $length){
      $errors[] = "$valname は $length 桁以下で入力してください。";
    }
  }

  function kanaCheck($val, $valname, &$errors): void
  // カタカナチェック
  {
    if (!preg_match("/\A[ァ-ヿ]+\z/u", $val)) {
      $errors[] = "$valname はカタカナで入力してください。";
    } 
  }
  
  function emailFormatCheck($val, $valname, &$errors): void
  // メールアドレスフォーマットチェック
  {
    if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "$valname の形式が不正です。";
    }
  }
?>

<!DOCTYPE html>
<meta charset = "utf-8">

<head></head>

<body>
<?php if($pageFlg === 0 ) :?>
  <font size='7'>採用応募入力画面</font>
  <form method = "POST" action="index.php">
  <br>
    名前<br>
  <input type="text" placeholder="名前を入力" name="user" value ="<?php if(!empty($_POST["user"])){echo $_POST["user"];}?>">

  <br><br>
    名前（カナ）<br>
  <input type="text" placeholder="名前（カナ）を入力" name="user_kana" value ="<?php if(!empty($_POST["user_kana"])){echo $_POST["user_kana"];}?>">

  <br><br>
    性別<br>
  <input type="radio" name="gender" value="男性">男性<br>
  <input type="radio" name="gender" value="女性">女性

  <br><br>
    生年月日<br>
<!-- 生年月日　カレンダーを実装する-->

  <br><br>
    最終学歴<br>
  <select name="education">
    <?php
      try {
        $pdo = new PDO("mysql:host=localhost;dbname=localtestdb", "root", "");
        $sql = "SELECT education_name FROM education_mst ORDER BY education_id";
        $education = $pdo->prepare($sql);
        $education -> execute();
        while ($educationRow = $education -> fetch(PDO::FETCH_ASSOC )) {
          echo "<option>".$educationRow['education_name']."</option>";
          }
      } catch (PDOException $e) {
        echo "※DBに接続できませんでした。";
      }
    ?>
  </select>
<!-- 最終学歴　DBから取得する-->

  <br><br>
    自宅住所<br>
  <input type="text" placeholder="郵便番号" name="postcode" value ="<?php if(!empty($_POST["postcode"])){echo $_POST["postcode"];}?>">
  <br>
  <select name="prefecture">
    <?php
      try {
        $pdo = new PDO("mysql:host=localhost;dbname=localtestdb", "root", "");
        $sql = "SELECT prefecture_name FROM prefecture_mst ORDER BY prefecture_id";
        $prefecture = $pdo->prepare($sql);
        $prefecture -> execute();
        while ($prefectureRow = $prefecture -> fetch(PDO::FETCH_ASSOC )) {
          echo "<option>".$prefectureRow['prefecture_name']."</option>";
          }
      } catch (PDOException $e) {
        echo "※DBに接続できませんでした。";
      }
    ?>
  </select>
      <!-- 都道府県　DBから取得する-->
  <input type="text" placeholder="市区町村" name="city" value ="<?php if(!empty($_POST["city"])){echo $_POST["city"];}?>">

  <br><br>
    メールアドレス<br>
    <input type = "text" placeholder = "メールアドレスを入力" name ="email" value ="<?php if(!empty($_POST["email"])){echo $_POST["email"];}?>">

  <br><br>
    希望職種<br>
    <?php
      try {
        $pdo = new PDO("mysql:host=localhost;dbname=localtestdb", "root", "");
        $sql = "SELECT job_name FROM job_mst ORDER BY job_id";
        $job = $pdo->prepare($sql);
        $job -> execute();
        while ($jobRow = $job -> fetch(PDO::FETCH_ASSOC )) {
          echo "<input type='checkbox' name='jobs[]' value=".$jobRow['job_name'].">".$jobRow['job_name'];
          echo "<br>";
          }
      } catch (PDOException $e) {
        echo "※DBに接続できませんでした。";
      }
    ?>

  <br>
    履歴書<br>
<!-- 履歴書　ファイルを選択を実装する-->

  <br><br>
    その他要望など<br>
  <textarea cols="50" rows="5" placeholder = "その他要望などを入力" name="etc"  cols="50" rows="5" 
    value ="<?php if(!empty($_POST["etc"])){echo $_POST["etc"];}?>"></textarea>

  <br><br>
    プライバシーポリシー<br>
    <input type="checkbox" name="privacy_policy" value="同意する"> 同意する

  <br><br>
  <input type = "submit" name = "btn_confirm"  value = "入力内容の確認" >
  <br><br><br>
  
  </form>
<?php endif; ?> 

<?php if($pageFlg === 1 ) :?>
  <font size='7'>採用応募確認画面</font>
  <form method = "POST" action="index.php">
  <br><br>
    名前　：　
  <?php echo $_POST["user"] ;?>
  <br><br>
    名前（カナ）　：　
  <?php echo $_POST["user_kana"] ;?> 
  <br><br>
    性別　：　
  <?php echo $_POST["gender"] ;?> 
  <br><br>
    学歴　：　
  <?php echo $_POST["education"] ;?> 
  <br><br>
    自宅住所　：　
  <?php echo $_POST["postcode"] ;?> 
  <br>
  　　　　　　　<?php echo $_POST["prefecture"] ;?><?php echo $_POST["city"] ;?> 
  <br><br>
    メールアドレス　：　
  <?php echo $_POST["email"] ;?> 
  <br><br>
    希望職種　：　
  <?php
    if (isset($_POST['jobs']) && is_array($_POST['jobs'])) {
      echo $job = implode("、", $_POST["jobs"]);
    }
  ;?> 
  <br><br>
  その他要望など　：　
  <?php echo $_POST["etc"] ;?> 
  <pre>

  <input type = "submit" name = "back" value = "修正する">　<input type = "submit" name = "btn_submit" value = "応募する" >
  <br>
  <input type = "hidden" name = "user" value = "<?php echo $_POST["user"] ;?>">
  <input type = "hidden" name = "user_kana" value = "<?php echo $_POST["user_kana"] ;?>">
  <input type = "hidden" name = "gender" value = "<?php echo $_POST["gender"] ;?>">
  <input type = "hidden" name = "education" value = "<?php echo $_POST["education"] ;?>">
  <input type = "hidden" name = "postcode" value = "<?php echo $_POST["postcode"] ;?>">
  <input type = "hidden" name = "prefecture" value = "<?php echo $_POST["prefecture"] ;?>">
  <input type = "hidden" name = "city" value = "<?php echo $_POST["city"] ;?>">
  <input type = "hidden" name = "email" value = "<?php echo $_POST["email"] ;?>">
  <input type = "hidden" name = "jobs" value = "<?php echo $_POST["jobs"] ;?>">
  <input type = "hidden" name = "etc" value = "<?php echo $_POST["etc"] ;?>">
  </form>
<?php endif; ?>


<?php if($pageFlg === 2 ) :?>
  <font size='7'>採用応募完了画面</font>
  <br><br>
    応募が完了しました。
    <br>
    登録メールアドレスに応募完了メールを送信しましたので、
    <br>
    ご確認ください。
  <br><br>
  <input type = "submit" name = "btn_close" value = "閉じる">
<?php endif; ?>

</body>
</html>
