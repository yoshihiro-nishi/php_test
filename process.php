<?php
session_start();

$btn = new BtnClass();
$btn -> btnCheck();
class BtnClass
{
  function btnCheck() {

    //確認ボタン押下
    if(!empty($_POST["btn_confirm"])) {
    
      $sessionAction = new SessionAction();
      $sessionAction -> setSessionData("user");
      $sessionAction -> setSessionData("user_kana");
      $sessionAction -> setSessionData("gender");
      $sessionAction -> setSessionData("birthday");
      $sessionAction -> setSessionData("education");
      $sessionAction -> setSessionData("postcode");
      $sessionAction -> setSessionData("prefecture");
      $sessionAction -> setSessionData("city");
      $sessionAction -> setSessionData("email");
      $sessionAction -> setSessionData("jobs");
      $sessionAction -> setSessionData("etc");
      $sessionAction -> setSessionData("privacy_policy");
      $_SESSION["file"] = $_FILES["userfile"];
      
      // バリデーションチェック
      $errors = array();
      $validate = new Validate();
      $validate -> validateCheck($errors);
      
      if (count($errors) != 0) {
        // エラーがある場合、赤文字でエラーを表示
        for( $i = 0; $i < count($errors); $i++ ) {
          echo "<font color='red'>$errors[$i]</font>";
          echo "<br>";
        }
        require_once('index.php');
      }
      else {
        // エラーがない場合
          $pageFlg = 1;
      
        // 履歴書ファイルをアップロードする
        $userfileUpPass = "C:\\testfile\\". date('YmdHis') . "_" .$_SESSION["file"]["name"];
        move_uploaded_file( $_SESSION["file"]["tmp_name"], $userfileUpPass ); // 「C:\testfile\名前_日付_ファイル名」に保存する
        $_SESSION['userfileUpPass'] = $userfileUpPass;
  
        require_once('confirm.php');
      }
    }
  
    //修正するボタン押下
    if(!empty($_POST["back"])) {
      // アップロードした履歴書ファイルを削除する
      unlink($_SESSION['userfileUpPass']);
      require_once('index.php');
    }
  
    //応募するボタン押下
    if(!empty($_POST["btn_submit"])) {
  
  
      $return = insData();
  
      if ($return) {
        require_once('send.php');
        
      }
      else {
        // DB登録時にエラーが出た場合
      }
    }
  
    //閉じるボタン押下
    if(!empty($_POST["btn_close"])) {
      echo "<script type='text/javascript'>window.close();</script>";
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

      require_once('index.php');
    }
  }
}

class SessionAction {
  function setSessionData($valname) {
    if(!empty($_POST[$valname]) && !is_array($_POST[$valname])) {
      $_SESSION[$valname] = htmlspecialchars($_POST[$valname], ENT_QUOTES, 'UTF-8');
    }
    else if(!empty($_POST[$valname])) {
      $vals = array();
      for( $i = 0; $i < count($_POST[$valname]); $i++ ) {
        $vals[] = htmlspecialchars($_POST[$valname][$i], ENT_QUOTES, 'UTF-8');
      }
      $_SESSION[$valname] = $vals;
    }
    else {
      $_SESSION[$valname] = "";
    }
  }
  function clearSessionData($valname) {
    $_SESSION[$valname] = "";
  }
}
class validate
{
  function validateCheck(&$errors) : void
  // バリデーションチェック
  {
    $user_name = "名前";
    $user = $_SESSION["user"];
    $errors = $this -> checkEmpty($user, $user_name, $errors);
    $errors = $this -> checkLength($user,$user_name, 60, $errors);

    $user_kana_name = "名前（カナ）";
    $user_kana = $_SESSION["user_kana"];
    $errors = $this -> checkEmpty($user_kana,$user_kana_name, $errors);
    $errors = $this -> checkLength($user_kana,$user_kana_name, 60, $errors);
    $errors = $this -> checkKana($user_kana,$user_kana_name, $errors);

    $gender_name = "性別";
    // 性別必須チェック
    if(isset($_SESSION['gender'])) {
      $gender = $_SESSION["gender"];
      // 性別フォーマットチェック
      if(!($gender == 1 or $gender == 2)) {
        $errors[] = "$gender_name の形式が不正です。";
      }
    } else {
      $errors[] = "$gender_name は必須です。";
    }

    $birthday_name = "生年月日";
    $birthday = $_SESSION["birthday"];
    $errors = $this -> checkEmpty($birthday, $birthday_name, $errors);
    if(!empty($_SESSION['birthday'])) {
      $errors = $this -> checkDateFormat($birthday, $birthday_name, $errors);
      $errors = $this -> checkExistDate($birthday, $birthday_name, $errors);
    }
    
    $education_name = "最終学歴";
    $education = $_SESSION["education"];
    $errors = $this -> checkTblData($education, $education_name, "education_mst", "education_name", $errors);

    $postcode_name = "郵便番号";
    $postcode = mb_convert_kana($_SESSION["postcode"], 'a', 'UTF-8'); // 全角の場合、半角に変換
    $errors = $this -> checkEmpty($postcode, $postcode_name, $errors);
    $errors = $this -> checkLength($postcode,$postcode_name, 8, $errors);
    // 郵便番号フォーマットチェック
    if (!preg_match("/\A\d{3}[-]\d{4}\z/", $postcode)) {
      $errors[] = "$postcode_name の書式に誤りがあります。";
    }

    $prefecture_name = "都道府県";
    $prefecture = $_SESSION["prefecture"];
    $errors = $this -> checkEmpty($prefecture, $prefecture_name, $errors);
    $errors = $this -> checkTblData($prefecture, $prefecture_name, "prefecture_mst", "prefecture_name", $errors);

    $city_name = "市区町村";
    $city = $_SESSION["city"];
    $errors = $this -> checkEmpty($city, $city_name, $errors);
    $errors = $this -> checkLength($city,$city_name, 100, $errors);

    $email_name = "メールアドレス";
    $email = $_SESSION["email"];
    $errors = $this -> checkEmpty($email, $email_name, $errors);
    $errors = $this -> checkLength($email,$email_name, 100, $errors);
    $errors = $this -> checkMailFormat($email, $email_name, $errors);
    
    $job_name = "希望職種";
    if (isset($_SESSION['jobs'])) {
      $jobs = array();
      $jobs = $_SESSION["jobs"];
      for( $i = 0; $i < count($jobs); $i++ ) {
        $errors = $this -> checkTblData($jobs[$i], $job_name, "job_mst", "job_name", $errors);
      }
    } else {
      $errors[] = "$job_name は必須です。";
    }

    $userfile_name = "履歴書";
    $userfile = $_FILES["userfile"];
    $errors = $this -> checkFile($userfile, $userfile_name, $errors);

    $etc_name = "その他要望など";
    $etc = $_SESSION["etc"];
    $errors = $this -> checkLength($etc,$etc_name, 2000, $errors);

    $privacy_policy_name = "プライバシーポリシー";
    if (!isset($_SESSION['privacy_policy'])) {
      $errors[] = "$privacy_policy_name は同意しないと応募できません。";
    }
  }

  function checkEmpty($val, $valname, $errors) : array
  // 必須チェック
  {
    if (empty($val)) {
      $errors[] = "$valname は必須です。";
    }
    return $errors;
  }
  function checkLength($val, $valname, $length, $errors) : array
  // 桁数チェック
  {
    if(mb_strlen ($val) > $length) {
      $errors[] = "$valname は $length 桁以下で入力してください。";
    }
    return $errors;
  }

  function checkKana($val, $valname, &$errors) : array
  // カタカナチェック
  {
    if (!preg_match("/\A[ァ-ヿ]+\z/u", $val)) {
      $errors[] = "$valname はカタカナで入力してください。";
    } 
    return $errors;
  }
  
  function checkMailFormat($val, $valname, &$errors) : array
  // メールアドレスフォーマットチェック
  {
    if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "$valname の形式が不正です。";
    }
    return $errors;
  }
  
  function checkDateFormat($val, $valname, &$errors) : array
  // 日付フォーマットチェック
  {
    if(preg_match('/\A[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}\z/', $val) == false) {
      $errors[] = "$valname の形式が不正です。";
    }
    return $errors;
  }
  
  function checkExistDate($val, $valname, &$errors) : array
  // 日付存在チェック
  {
    list($year, $month, $day) = explode('-', $val);

    if(checkdate($month, $day, $year) == false) {
      $errors[] = "$valname の日付は存在しません。";
    }
    return $errors;
  }
  
  function checkFile($file, $file_name, &$errors) : array
  // ファイルチェック
  {
    // 必須チェック
    $errors = $this -> checkEmpty($file["name"], $file_name, $errors);
    
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
    return $errors;
  }

  function checkTblData($val, $valname, $tblname, $column, $errors) : array
  // DBの存在チェック
  {
    $pdo = new PDO("mysql:host=localhost;dbname=localtestdb", "root", "");
    $sql = "SELECT COUNT(*) AS cnt FROM $tblname WHERE $column = '$val'";
    $education = $pdo -> prepare($sql);
    $education -> execute();
    $educationRow = $education -> fetch(PDO::FETCH_ASSOC );
    if($educationRow['cnt'] == 0) {
      $errors[] = "存在しない $valname が設定されています。";
    }
    return $errors;
  }
}
  function insData()
  {
    // 登録情報の設定
    $user = $_SESSION["user"];
    $user_kana = $_SESSION["user_kana"];
    $gender = $_SESSION["gender"];
    $birthday = $_SESSION["birthday"];
    $education = $_SESSION["education"];
    $postcode = str_replace("-","",$_SESSION["postcode"]);
    $prefecture = $_SESSION["prefecture"];
    $city = $_SESSION["city"];
    $email = $_SESSION["email"];
    $job = implode("、", $_SESSION["jobs"]);
    $etc = $_SESSION["etc"];
    $userfileUpPass = $_SESSION['userfileUpPass'];

    // DB登録
    $db = new mysqli("localhost", "root", "", "localtestdb");
    $statement = $db -> prepare(
      "INSERT INTO oubo_info
        (`username`,`username_kana`,`gender`,`birthday`,`education`,`postcode`,`prefecture`,`city`,`email`,`job`,`userfile`,`etc`)
        VALUES
        (?,?,?,?,?,?,?,?,?,?,?,?);"
    );
    if (!$statement) {
        die($db -> error);
    }
    $statement -> bind_param("ssisssssssss", $user, $user_kana, $gender, $birthday, $education, $postcode, $prefecture, $city, $email, $job, $userfileUpPass, $etc); 

    return $statement -> execute();
  }
