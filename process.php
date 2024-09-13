<?php
session_start();

$btn = new BtnClass();
$btn -> btnCheck();

class ConstClass
{
  const indexPage = "0";
  const confirmPage = "1";
  const sendPage = "2";
}

class BtnClass
{
 /**
  * ボタン処理
  */
  function btnCheck() : void
  {
    // 確認ボタン押下
    if(!empty($_POST["btn_confirm"])) {
    
      $validate = new Validate();
      $validate -> checkFromPage("btn_confirm");
      
      $sessionAction = new SessionAction();
      $sessionAction -> clearSessionAllData();
      $sessionAction -> setSessionAllData();
      
      $errors = $validate -> validateCheck();
      
      if (count($errors) != 0) {
        // エラーがある場合、赤文字でエラーを表示
        for( $i = 0; $i < count($errors); $i++ ) {
          echo "<font color='red'>$errors[$i]</font>";
          echo "<br>";
        }
        require_once('index.php');
      }
      else {
        // 履歴書ファイルをアップロードする
        $userfileUpPass = "C:\\testfile\\". date('YmdHis') . "_" .$_SESSION["file"]["name"];
        move_uploaded_file( $_SESSION["file"]["tmp_name"], $userfileUpPass ); // 「C:\testfile\日付_ファイル名」に保存する
        $_SESSION['userfileUpPass'] = $userfileUpPass;
  
        require_once('confirm.php');
      }
    }

    // 修正するボタン押下
    else if(!empty($_POST["back"])) {
      // アップロードした履歴書ファイルを削除する
      unlink($_SESSION['userfileUpPass']);
      require_once('index.php');
    }

    // 応募するボタン押下
    else if(!empty($_POST["btn_submit"])) {
  
      $validate = new Validate();
      $validate -> checkFromPage("btn_submit");
      $errors   = $validate -> validateCheck();
      
      if (count($errors) != 0) {
        for( $i = 0; $i < count($errors); $i++ ) {
          echo "<font color='red'>$errors[$i]</font>";
          echo "<br>";
        }
        require_once('index.php');
      }
      else {
        // 応募情報登録
        $return = insData();
    
        if ($return) {
          require_once('send.php');
          
        }
        else {
          echo "<font color='red'>DB登録時にエラーが発生しました。</font>";
          echo "<br>";
          exit;
        }
      }
    }

    // 閉じるボタン押下
    else if(!empty($_POST["btn_close"])) {
      echo "<script type = 'text/javascript' > window.close(); </script>";
      $sessionAction = new SessionAction();
      $sessionAction -> clearSessionAllData();

      require_once('index.php');
    }

    else {
      echo "<font color='red'>不正なページ操作が行われました。</font>";
      echo "<br>";
      exit;
    }
  }
}

class SessionAction
{
  // 全項目のセッションデータ設定
  function setSessionAllData() : void
  {
    $sessionAction    = new SessionAction();
    $sessionAction    -> setSessionData("user");
    $sessionAction    -> setSessionData("user_kana");
    $sessionAction    -> setSessionData("gender");
    $sessionAction    -> setSessionData("birthday");
    $sessionAction    -> setSessionData("education");
    $sessionAction    -> setSessionData("postcode");
    $sessionAction    -> setSessionData("prefecture");
    $sessionAction    -> setSessionData("city");
    $sessionAction    -> setSessionData("email");
    $sessionAction    -> setSessionData("jobs");
    $sessionAction    -> setSessionData("etc");
    $sessionAction    -> setSessionData("privacy_policy");
    $_SESSION["file"] = $_FILES["userfile"];
  }
  
  // セッションデータの設定、サニタイズ
  function setSessionData($valname) : void
  {
    if(!empty($_POST[$valname]) && !is_array($_POST[$valname])) {
      $_SESSION[$valname] = htmlspecialchars($_POST[$valname], ENT_QUOTES, 'UTF-8');
    }
    else if(!empty($_POST[$valname]) && isset($_POST[$valname])) {
      $vals = array();
      for( $i = 0; $i < count($_POST[$valname]); $i++ ) {
        $vals[] = htmlspecialchars($_POST[$valname][$i], ENT_QUOTES, 'UTF-8');
      }
      $_SESSION[$valname] = $vals;
    }
    else {
      $_SESSION[$valname] = null;
    }
  }

  // 全項目のセッションデータクリア
  function clearSessionAllData() : void
  {
    $sessionAction = new SessionAction();
    $sessionAction -> clearSessionData("user");
    $sessionAction -> clearSessionData("user_kana");
    $sessionAction -> clearSessionData("gender");
    $sessionAction -> clearSessionData("birthday");
    $sessionAction -> clearSessionData("education");
    $sessionAction -> clearSessionData("postcode");
    $sessionAction -> clearSessionData("prefecture");
    $sessionAction -> clearSessionData("city");
    $sessionAction -> clearSessionData("email");
    $sessionAction -> clearSessionData("jobs");
    $sessionAction -> clearSessionData("etc");
    $sessionAction -> clearSessionData("privacy_policy");
    $sessionAction -> clearSessionData("file");
  }

  // セッションデータのクリア
  function clearSessionData($valname) : void
  {
    $_SESSION[$valname] = null;
  }
}

class validate
{
 /**
  * チェック処理
  *
  * @return array  $errors  エラーメッセージ。
  */
  function validateCheck() : array
  {
    $errors              = array();
   
    $user_name           = "名前";
    $user                = $_SESSION["user"];
    $errors              = $this -> checkEmpty($user, $user_name, $errors);
    $errors              = $this -> checkLength($user, $user_name, 60, $errors);
   
    $user_kana_name      = "名前（カナ）";
    $user_kana           = $_SESSION["user_kana"];
    $errors              = $this -> checkEmpty($user_kana, $user_kana_name, $errors);
    $errors              = $this -> checkLength($user_kana, $user_kana_name, 60, $errors);
    $errors              = $this -> checkKana($user_kana, $user_kana_name, $errors);

    $gender_name         = "性別";
    $gender              = $_SESSION["gender"];
    $errors              = $this -> checkGender($gender, $gender_name, $errors);
    
    $birthday_name       = "生年月日";
    $birthday            = $_SESSION["birthday"];
    $errors              = $this -> checkBirthday($birthday, $birthday_name, $errors);
    
    $education_name      = "最終学歴";
    $education           = $_SESSION["education"];
    $errors              = $this -> checkTblData($education, $education_name, "education_mst", "education_name", $errors);
    
    $postcode_name       = "郵便番号";
    $postcode            = mb_convert_kana($_SESSION["postcode"], 'a', 'UTF-8'); // 全角の場合、半角に変換
    $errors              = $this -> checkPostcode($postcode,$postcode_name, $errors);
    
    $prefecture_name     = "都道府県";
    $prefecture          = $_SESSION["prefecture"];
    $errors              = $this -> checkEmpty($prefecture, $prefecture_name, $errors);
    $errors              = $this -> checkTblData($prefecture, $prefecture_name, "prefecture_mst", "prefecture_name", $errors);
    
    $city_name           = "市区町村";
    $city                = $_SESSION["city"];
    $errors              = $this -> checkEmpty($city, $city_name, $errors);
    $errors              = $this -> checkLength($city,$city_name, 100, $errors);
    
    $email_name          = "メールアドレス";
    $email               = $_SESSION["email"];
    $errors              = $this -> checkEmpty($email, $email_name, $errors);
    $errors              = $this -> checkLength($email,$email_name, 100, $errors);
    $errors              = $this -> checkMailFormat($email, $email_name, $errors);
        
    $job_name            = "希望職種";
    $job                 = $_SESSION["jobs"];
    $errors              = $this -> checkJob($job, $job_name, $errors);
        
    
    $userfile_name       = "履歴書";
    $userfile            = $_SESSION["file"];
    $errors              = $this -> checkFile($userfile, $userfile_name, $errors);
    
    $etc_name            = "その他要望など";
    $etc                 = $_SESSION["etc"];
    $errors              = $this -> checkLength($etc,$etc_name, 2000, $errors);

    $privacy_policy_name = "プライバシーポリシー";

    if (!isset($_SESSION['privacy_policy'])) {
      $errors[]          = "$privacy_policy_name は同意しないと応募できません。";
    }

    return $errors;
  }

  // 必須チェック
  function checkEmpty($val, $valname, $errors) : array
  {
    if (empty($val)) {
      $errors[] = "$valname は必須です。";
    }
    return $errors;
  }

  // 桁数チェック
  function checkLength($val, $valname, $length, $errors) : array
  {
    if(mb_strlen ($val) > $length) {
      $errors[] = "$valname は $length 桁以下で入力してください。";
    }
    return $errors;
  }

  // カタカナチェック
  function checkKana($val, $valname, $errors) : array
  {
    if (!empty($val)) {
      if (!preg_match("/\A[ァ-ヿ]+\z/u", $val)) {
        $errors[] = "$valname はカタカナで入力してください。";
      }
    }
    return $errors;
  }

  // 性別チェック
  function checkGender($val, $valname, $errors) : array
  {
    if(isset($val)) {
      if(!($val == 1 or $val == 2)) {
        $errors[] = "$valname の形式が不正です。";
      }
    } else {
      $errors[]   = "$valname は必須です。";
    }
    return $errors;
  }
  
  // メールアドレスフォーマットチェック
  function checkMailFormat($val, $valname, $errors) : array
  {
    if (!empty($val)) {
      if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
        $errors[]   = "$valname の形式が不正です。";
      }
      for( $i = 0; $i < strlen($val); $i++ ) {
        if(ctype_upper(mb_substr($val,$i,1))) {
          $errors[] = "$valname は小文字で入力してください。";
          break;
        }
      }
    }
    
    return $errors;
  }

  // 生年月日チェック
  function checkBirthday($val, $valname, $errors) : array
  {
    $errors   = $this -> checkEmpty($val, $valname, $errors);

    if(!empty($val)) {
      $errors = $this -> checkDateFormat($val, $valname, $errors);
      $errors = $this -> checkExistDate($val, $valname, $errors);
    }    
    return $errors;
  }
  
  // 日付フォーマットチェック
  function checkDateFormat($val, $valname, $errors) : array
  {
    if(preg_match('/\A[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}\z/', $val) == false) {
      $errors[] = "$valname の形式が不正です。";
    }
    return $errors;
  }
  
  // 日付存在チェック
  function checkExistDate($val, $valname, $errors) : array
  {
    list($year, $month, $day) = explode('-', $val);

    if(checkdate($month, $day, $year) == false) {
      $errors[] = "$valname の日付は存在しません。";
    }
    return $errors;
  }
  
  // 郵便番号チェック
  function checkPostcode($val, $valname, $errors) : array
  {
    $errors       = $this -> checkEmpty($val, $valname, $errors);
    
    if(!empty($val)) {
      $errors     = $this -> checkLength($val,$valname, 8, $errors);
  
      // 郵便番号フォーマットチェック
      if (!preg_match("/\A\d{3}[-]\d{4}\z/", $val)) {
        $errors[] = "$valname の書式に誤りがあります。";
      }
  
      // 郵便番号存在チェック
      $url        = "https://api.zipaddress.net/?zipcode={$val}";
      $response   = file_get_contents($url);
      $data       = json_decode($response, true);
      
      if ($data['code'] !== 200) {
        $errors[] = "存在しない$valname が設定されています。";
      }
    }

    return $errors;
  }
  
  // ファイル項目チェック
  function checkFile($file, $file_name, $errors) : array
  {
    if(strcmp($file['size'], 0) == 0) {
      $errors[]   = "$file_name は必須です。";
    }
    else {
      // ファイル形式チェック
      if (!(str_ends_with($file['name'], ".pdf") or
            str_ends_with($file['name'], ".PDF") or
            str_ends_with($file['name'], ".docx") or
            str_ends_with($file['name'], ".DOCX"))) {
        $errors[] = "$file_name はpdf、もしくはdocxの形式でアップロードしてください。";
      }
  
      // ファイルサイズチェック
      else if ($file['size'] > 1048576) {
        $errors[] = "$file_name のファイルサイズは1MB以下でアップロードしてください。";
      }
    }
    return $errors;
  }

  // 職種チェック
  function checkJob($val, $valname, $errors) : array
  {
    if (isset($val)) {
      for( $i = 0; $i < count($val); $i++ ) {
        $errors = $this -> checkTblData($val[$i], $valname, "job_mst", "job_name", $errors);
      }
    } else {
      $errors[] = "$valname は必須です。";
    }
    return $errors;
  }

  // マスタデータの存在チェック
  function checkTblData($val, $valname, $tblname, $column, $errors) : array
  {
    $pdo          = new PDO("mysql:host=localhost;dbname=localtestdb", "root", "");
    $sql          = "SELECT COUNT(*) AS cnt FROM $tblname WHERE $column = '$val'";
    $education    = $pdo -> prepare($sql);
    $education    -> execute();
    $educationRow = $education -> fetch(PDO::FETCH_ASSOC );

    if($educationRow['cnt'] == 0) {
      $errors[]   = "存在しない $valname が設定されています。";
    }
    return $errors;
  }

  // 遷移元画面チェック
  function checkFromPage($pagename) : void
  {
    $const  = 'ConstClass';
    $errFlg = 0;
    if ($pagename == "btn_confirm") {
      if(strcmp($_POST["page"], $const::indexPage) != 0) {
        $errFlg = 1;
      }
    }
    if ($pagename == "btn_submit") {
      if(strcmp($_POST["page"], $const::confirmPage) != 0) {
        $errFlg = 1;
      }
    }
    if($errFlg == 1) {
      echo "<font color='red'>不正なページ操作が行われました。</font>";
      echo "<br>";
      exit;
    }
  }
}

  // 応募情報TBL登録処理
  function insData()
  {
    // 登録情報の設定
    $user           = $_SESSION["user"];
    $user_kana      = $_SESSION["user_kana"];
    $gender         = $_SESSION["gender"];
    $birthday       = $_SESSION["birthday"];
    $education      = $_SESSION["education"];
    $postcode       = str_replace("-","",$_SESSION["postcode"]);
    $prefecture     = $_SESSION["prefecture"];
    $city           = $_SESSION["city"];
    $email          = $_SESSION["email"];
    $job            = implode("、", $_SESSION["jobs"]);
    $etc            = $_SESSION["etc"];
    $userfileUpPass = $_SESSION['userfileUpPass'];

    // DB登録
    $db             = new mysqli("localhost", "root", "", "localtestdb");
    $statement      = $db -> prepare(
      "INSERT INTO oubo_info
        (`username`,`username_kana`,`gender`,`birthday`,`education`,`postcode`,`prefecture`,`city`,`email`,`job`,`userfile`,`etc`)
        VALUES
        (?,?,?,?,?,?,?,?,?,?,?,?);"
    );

    if (!$statement) {
        die($db -> error);
    }

    $statement -> bind_param("ssisssssssss" 
                           , $user
                           , $user_kana
                           , $gender
                           , $birthday
                           , $education
                           , $postcode
                           , $prefecture
                           , $city
                           , $email
                           , $job
                           , $userfileUpPass
                           , $etc
                        );

    return $statement -> execute();
  }