<?php
  //ページ遷移の変数
  $pageFlg = 0;
  
  //確認ボタン押下
  if(!empty($_POST["btn_confirm"])){
  
    // バリデーションチェック
    $errors = array();
    validateCheck($errors);

    if (count($errors) != 0){
      // エラーがある場合、赤文字でエラーを表示
      for( $i = 0; $i < count($errors); $i++ ){
        echo "<font color='red'>$errors[$i]</font>";
        echo "<br>";
      }
    }
    else {
      // エラーがない場合
        $pageFlg = 1;
    }
    
  }
  
  //応募するボタン押下
  if(!empty($_POST["btn_submit"])){


    $return = insData();

    if ($return) {
      $pageFlg = 2;
      // DB登録完了時

      // 自動メールを送信する。
    

    }
    else {
      // DB登録時にエラーが出た場合
      echo $db->error;
    }

  }

  //閉じるボタン押下
  if(!empty($_POST["btn_close"])){
    echo "<script type='text/javascript'>window.close();</script>";
  }
  
  function validateCheck(&$errors): void
  // バリデーションチェック
  {
    $user_name = "名前";
    $user = $_POST["user"];
    checkEmpty($user, $user_name, $errors);
    checkLength($user,$user_name, 60, $errors);

    $user_kana_name = "名前（カナ）";
    $user_kana = $_POST["user_kana"];
    checkEmpty($user_kana,$user_kana_name, $errors);
    checkLength($user_kana,$user_kana_name, 60, $errors);
    checkKana($user_kana,$user_kana_name, $errors);
 
    $gender_name = "性別";
    // 性別必須チェック
    if(isset($_POST['gender'])) {
      $gender = $_POST["gender"];
      // 性別フォーマットチェック
      if(!($gender == 1 or $gender == 2)) {
        $errors[] = "$gender_name の形式が不正です。";
      }
    } else {
      $errors[] = "$gender_name は必須です。";
    }

    $birthday_name = "生年月日";
    $birthday = $_POST["birthday"];
    checkEmpty($birthday, $birthday_name, $errors);
    if(!empty($_POST['birthday'])) {
      checkDateFormat($birthday, $birthday_name, $errors);
      checkExistDate($birthday, $birthday_name, $errors);
    }
    
    $education_name = "最終学歴";
    $education = $_POST["education"];
    checkTblData($education, $education_name, "education_mst", "education_name", $errors);

    $postcode_name = "郵便番号";
    $postcode = mb_convert_kana($_POST["postcode"], 'a', 'UTF-8'); // 全角の場合、半角に変換
    checkEmpty($postcode, $postcode_name, $errors);
    checkLength($postcode,$postcode_name, 8, $errors);
    // 郵便番号フォーマットチェック
    if (!preg_match("/\A\d{3}[-]\d{4}\z/", $postcode)) {
      $errors[] = "$postcode_name の書式に誤りがあります。";
    }

    $prefecture_name = "都道府県";
    $prefecture = $_POST["prefecture"];
    checkEmpty($prefecture, $prefecture_name, $errors);
    checkTblData($prefecture, $prefecture_name, "prefecture_mst", "prefecture_name", $errors);

    $city_name = "市区町村";
    $city = $_POST["city"];
    checkEmpty($city, $city_name, $errors);
    checkLength($city,$city_name, 100, $errors);

    $email_name = "メールアドレス";
    $email = $_POST["email"];
    checkEmpty($email, $email_name, $errors);
    checkLength($email,$email_name, 100, $errors);
    checkMailFormat($email, $email_name, $errors);
    
    $job_name = "希望職種";
    if (isset($_POST['jobs'])) {
      $jobs = array();
      $jobs = $_POST["jobs"];
      for( $i = 0; $i < count($jobs); $i++ ){
        checkTblData($jobs[$i], $job_name, "job_mst", "job_name", $errors);
      }
    } else {
      $errors[] = "$job_name は必須です。";
    }

    $userfile_name = "履歴書";
    $userfile = $_FILES["userfile"];
    checkFile($userfile, $userfile_name, $errors);

    $etc_name = "その他要望など";
    $etc = $_POST["etc"];
    checkLength($etc,$etc_name, 2000, $errors);

    $privacy_policy_name = "プライバシーポリシー";
    if (!isset($_POST['privacy_policy'])) {
      $errors[] = "$privacy_policy_name は同意しないと応募できません。";
    }
    
    // 履歴書ファイルをアップロードする
    $upfile_name = $userfile['name']; // ファイル名
    move_uploaded_file( $userfile['tmp_name'], 'C:\testfile\\' . $upfile_name); // 「C:\testfile\ファイル名」に保存する
  }

  function checkEmpty($val, $valname, &$errors): void
  // 必須チェック
  {
    if (empty($val)) {
      $errors[] = "$valname は必須です。";
    } 
  }
  function checkLength($val, $valname, $length, &$errors): void
  // 桁数チェック
  {
    if(mb_strlen ($val) > $length){
      $errors[] = "$valname は $length 桁以下で入力してください。";
    }
  }

  function checkKana($val, $valname, &$errors): void
  // カタカナチェック
  {
    if (!preg_match("/\A[ァ-ヿ]+\z/u", $val)) {
      $errors[] = "$valname はカタカナで入力してください。";
    } 
  }
  
  function checkMailFormat($val, $valname, &$errors): void
  // メールアドレスフォーマットチェック
  {
    if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "$valname の形式が不正です。";
    }
  }
  
  function checkDateFormat($val, $valname, &$errors): void
  // 日付フォーマットチェック
  {
    if(preg_match('/\A[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}\z/', $val) == false){
      $errors[] = "$valname の形式が不正です。";
    }
  }
  
  function checkExistDate($val, $valname, &$errors): void
  // 日付存在チェック
  {
    list($year, $month, $day) = explode('-', $val);

    if(checkdate($month, $day, $year) == false){
      $errors[] = "$valname の日付は存在しません。";
    }
  }
  
  function checkFile($file, $file_name, &$errors): void
  // ファイルチェック
  {
    // 必須チェック
    checkEmpty($file["name"], $file_name, $errors);
    
    // ファイル形式チェック
    if (!(str_ends_with($file["name"], ".pdf") or
          str_ends_with($file["name"], ".PDF") or
          str_ends_with($file["name"], ".docx") or
          str_ends_with($file["name"], ".DOCX"))) {
      $errors[] = "$file_name のファイルの形式が不正です。";
    }

    // ファイルサイズチェック
    if ($file["size"] > 1048576) {
      $errors[] = "$file_name のファイルサイズは1MB以下でアップロードしてください。";
    }
  }

  function checkTblData($val, $valname, $tblname, $column, &$errors)
  // DBの存在チェック
  {
    $pdo = new PDO("mysql:host=localhost;dbname=localtestdb", "root", "");
    $sql = "SELECT COUNT(*) AS cnt FROM $tblname WHERE $column = '$val'";
    $education = $pdo->prepare($sql);
    $education -> execute();
    $educationRow = $education -> fetch(PDO::FETCH_ASSOC );
    if($educationRow['cnt'] == 0) {
      $errors[] = "存在しない $valname が設定されています。";
    }
  }

  function insData()
  // DBの存在チェック
  {
    // 登録情報の設定
    $user = $_POST["user"];
    $user_kana = $_POST["user_kana"];
    $gender = $_POST["gender"];
    $birthday = $_POST["birthday"];
    $education = $_POST["education"];
    $postcode = str_replace("-","",$_POST["postcode"]);
    $prefecture = $_POST["prefecture"];
    $city = $_POST["city"];
    $email = $_POST["email"];
    $job = $_POST["job"];
    $userfile = $_POST["userfile"];
    $etc = $_POST["etc"];

    // DB登録
    $db = new mysqli("localhost", "root", "", "localtestdb");
    $statement = $db->prepare(
      "INSERT INTO oubo_info
        (`username`,`username_kana`,`gender`,`birthday`,`education`,`postcode`,`prefecture`,`city`,`email`,`job`,`userfile`,`etc`)
        VALUES
        (?,?,?,?,?,?,?,?,?,?,?,?);"
    );
    if (!$statement) {
        die($db->error);
    }
    $statement->bind_param("ssisssssssss", $user, $user_kana, $gender, $birthday, $education, $postcode, $prefecture, $city, $email, $job, $userfile, $etc); 
    //$statement->bind_param("sib", $oubo_id, $user, $user_kana, $gender, $birthday, $education, $postcode, $prefecture, $city, $email, $job, $userfile, $etc); 

    return $statement->execute();
  }
?>

<!DOCTYPE html>
<meta charset = "utf-8">

<head></head>

<body>
<?php if($pageFlg === 0 ) :?>
  <font size='7'>採用応募入力画面</font>
  <form method = "POST" action="index.php" enctype="multipart/form-data">
  <br>
    名前<br>
  <input type="text" placeholder="名前を入力" name="user" value ="<?php if(!empty($_POST["user"])){echo $_POST["user"];}?>">

  <br><br>
    名前（カナ）<br>
  <input type="text" placeholder="名前（カナ）を入力" name="user_kana" value ="<?php if(!empty($_POST["user_kana"])){echo $_POST["user_kana"];}?>">

  <br><br>
    性別<br>
  <input type="radio" name="gender" value="1" <?php if (isset($_POST['gender']) && $_POST['gender'] == "1") echo 'checked'; ?>>男性<br>
  <input type="radio" name="gender" value="2" <?php if (isset($_POST['gender']) && $_POST['gender'] == "2") echo 'checked'; ?>>女性

  <br><br>
    生年月日<br>
<input name="birthday" type="date" value="<?php if(!empty($_POST["birthday"])){echo $_POST["birthday"];}?>" />

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
    <input name="userfile" type="file" />

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
  <form method = "POST" action="index.php" enctype="multipart/form-data">
  <br><br>
    名前　：　
  <?php echo $_POST["user"] ;?>
  <br><br>
    名前（カナ）　：　
  <?php echo $_POST["user_kana"] ;?> 
  <br><br>
    性別　：　
  <?php 
    if($_POST['gender'] == 1){echo "男性";}
    elseif($_POST['gender'] == 2){echo "女性";}
  ?>
  <br><br>
    生年月日　：　
  <?php echo date("Y/m/d", strtotime($_POST["birthday"])) ;?> 
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
      echo $jobs = implode("、", $_POST["jobs"]);
    }
  ;?> 
  <br><br>
    履歴書　：　
  <?php print_r($_FILES["userfile"]["name"]);?> 
  <br><br>
  その他要望など　：　
  <?php echo $_POST["etc"] ;?> 
  <pre>

  <input type = "submit" name = "back" value = "修正する">　<input type = "submit" name = "btn_submit" value = "応募する" >
  <br>
  <input type = "hidden" name = "user" value = "<?php echo $_POST["user"] ;?>">
  <input type = "hidden" name = "user_kana" value = "<?php echo $_POST["user_kana"] ;?>">
  <input type = "hidden" name = "gender" value = "<?php echo $_POST["gender"] ;?>">
  <input type = "hidden" name = "birthday" value = "<?php echo $_POST["birthday"] ;?>">
  <input type = "hidden" name = "education" value = "<?php echo $_POST["education"] ;?>">
  <input type = "hidden" name = "postcode" value = "<?php echo $_POST["postcode"] ;?>">
  <input type = "hidden" name = "prefecture" value = "<?php echo $_POST["prefecture"] ;?>">
  <input type = "hidden" name = "city" value = "<?php echo $_POST["city"] ;?>">
  <input type = "hidden" name = "email" value = "<?php echo $_POST["email"] ;?>">
  <input type = "hidden" name = "job" value = "<?php  if (isset($_POST['jobs']) && is_array($_POST['jobs'])) {
      echo implode("、", $_POST["jobs"]);
    } ;?>">
  <input type = "hidden" name = "userfile" value = "<?php echo $_FILES["userfile"] ;?>">
  <input type = "hidden" name = "etc" value = "<?php echo $_POST["etc"] ;?>">
  </form>
<?php endif; ?>


<?php if($pageFlg === 2 ) :?>
  <font size='7'>採用応募完了画面</font>
  <form method = "POST" action="index.php">
  <br><br>
    応募が完了しました。
    <br>
    登録メールアドレスに応募完了メールを送信しましたので、
    <br>
    ご確認ください。
  <br><br>
  <input type = "submit" name = "btn_close" value = "閉じる">
  </form>
<?php endif; ?>

</body>
</html>
