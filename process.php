<?php
session_start();

$btn = new BtnClass();
$btn->btnCheck();

class BtnClass
{
 /**
  * ボタン処理
  *
  */
  public function btnCheck() : void
  {
    $sessionAction = new SessionActionClass();
    $validator = new ValidatorClass();

    // 確認ボタン押下
    if(!empty($_POST["btn_confirm"])) {
      $sessionAction->clearSessionAllData();
      $sessionAction->setSessionAllData();
      
      if($validator->outputError()) {
        // 履歴書ファイルをアップロードする
        $userfileUpPass = "C:\\testfile\\". date('YmdHis') . "_" .$_SESSION["file"]["name"];
        move_uploaded_file( $_SESSION["file"]["tmp_name"], $userfileUpPass ); // 「C:\testfile\日付_ファイル名」で保存する
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
      if($validator->outputError()) {
        // 応募情報登録
        $insDataClass = new InsDataClass();
        $return = $insDataClass->insData();
    
        if ($return) {
          require_once('send.php');
          
        }
        else {
          echo "<font color='red'>DB登録時にエラーが発生しました。</font><br>";
          exit;
        }
      }
    }

    // 閉じるボタン押下
    else if(!empty($_POST["btn_close"])) {
      echo "<script type = 'text/javascript' > window.close(); </script>";
      $sessionAction->clearSessionAllData();

      require_once('index.php');
    }

    else {
      echo "<font color='red'>不正なページ操作が行われました。</font><br>";
      exit;
    }
  }
}

class SessionActionClass
{
 /**
  * 全項目のセッションデータ設定
  *
  */
  public function setSessionAllData() : void
  {
    $this->setSessionData("user");
    $this->setSessionData("user_kana");
    $this->setSessionData("gender");
    $this->setSessionData("birthday");
    $this->setSessionData("education");
    $this->setSessionData("postcode");
    $this->setSessionData("prefecture");
    $this->setSessionData("city");
    $this->setSessionData("email");
    $this->setSessionData("jobs");
    $this->setSessionData("etc");
    $this->setSessionData("privacy_policy");
    $_SESSION["file"] = $_FILES["userfile"];
  }
  
 /**
  * セッションデータの設定、エスケープ
  *
  */
  public function setSessionData($valname) : void
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
  
 /**
  * 全項目のセッションデータクリア
  *
  */
  public function clearSessionAllData() : void
  {
    $this->clearSessionData("user");
    $this->clearSessionData("user_kana");
    $this->clearSessionData("gender");
    $this->clearSessionData("birthday");
    $this->clearSessionData("education");
    $this->clearSessionData("postcode");
    $this->clearSessionData("prefecture");
    $this->clearSessionData("city");
    $this->clearSessionData("email");
    $this->clearSessionData("jobs");
    $this->clearSessionData("etc");
    $this->clearSessionData("privacy_policy");
    $this->clearSessionData("file");
  }

 /**
  * セッションデータのクリア
  *
  */
  public function clearSessionData($valname) : void
  {
    $_SESSION[$valname] = null;
  }
}

class ValidatorClass
{
  private $errors = [];

 /**
  * エラーメッセージ取得
  *
  * @return array  エラーメッセージ
  */
  public function getResult()
  {
    return $this->errors;
  }

 /**
  * エラー可否
  *
  * @return bool エラー可否
  */
  public function hasError()
  {
      return count($this->getResult()) > 0;
  }

 /**
  * エラー出力
  *
  * @return bool エラー出力可否
  */
  public function outputError()
  {
    $this->validateCheck();
    
    // エラーがある場合、赤文字でエラーを表示
    if ($this->hasError()) {
      for( $i = 0; $i < count($this->getResult()); $i++ ) {
        echo "<font color='red'>{$this->getResult()[$i]}</font><br>";
      }
      require_once('index.php');
      return false;
    }
    return true;
  } 

 /**
  * チェック処理
  *
  */
  public function validateCheck()
  {
    $user_name           = "名前";
    $user                = $_SESSION["user"];
    $this->checkEmpty($user, $user_name);
    $this->checkLength($user, $user_name, 60);
   
    $user_kana_name      = "名前（カナ）";
    $user_kana           = $_SESSION["user_kana"];
    $this->checkEmpty($user_kana, $user_kana_name);
    $this->checkLength($user_kana, $user_kana_name, 60);
    $this->checkKana($user_kana, $user_kana_name);

    $gender_name         = "性別";
    $gender              = $_SESSION["gender"];
    $this->checkGender($gender, $gender_name);
    
    $birthday_name       = "生年月日";
    $birthday            = $_SESSION["birthday"];
    $this->checkBirthday($birthday, $birthday_name);
    
    $education_name      = "最終学歴";
    $education           = $_SESSION["education"];
    $this->checkTblData($education, $education_name, "education_mst", "education_name");
    
    $postcode_name       = "郵便番号";
    $postcode            = mb_convert_kana($_SESSION["postcode"], 'a', 'UTF-8'); // 全角の場合、半角に変換
    $this->checkPostcode($postcode,$postcode_name);
    
    $prefecture_name     = "都道府県";
    $prefecture          = $_SESSION["prefecture"];
    $this->checkEmpty($prefecture, $prefecture_name);
    $this->checkTblData($prefecture, $prefecture_name, "prefecture_mst", "prefecture_name");
    
    $city_name           = "市区町村";
    $city                = $_SESSION["city"];
    $this->checkEmpty($city, $city_name);
    $this->checkLength($city,$city_name, 100);
    
    $email_name          = "メールアドレス";
    $email               = $_SESSION["email"];
    $this->checkEmpty($email, $email_name);
    $this->checkLength($email,$email_name, 100);
    $this->checkMailFormat($email, $email_name);
        
    $job_name            = "希望職種";
    $job                 = $_SESSION["jobs"];
    $this->checkJob($job, $job_name);
        
    
    $userfile_name       = "履歴書";
    $userfile            = $_SESSION["file"];
    $this->checkFile($userfile, $userfile_name);
    
    $etc_name            = "その他要望など";
    $etc                 = $_SESSION["etc"];
    $this->checkLength($etc,$etc_name, 2000);

    $privacy_policy_name = "プライバシーポリシー";

    if (!isset($_SESSION['privacy_policy'])) {
      $this->errors[]    = "$privacy_policy_name は同意しないと応募できません。";
    }
  }

 /**
  * 必須チェック
  *
  */
  public function checkEmpty($val, $valname)
  {
    if (empty($val)) {
        $this->errors[] = "$valname は必須です。";
    }
  }

 /**
  * 桁数チェック
  *
  */
  public function checkLength($val, $valname, $length)
  {
    if(mb_strlen ($val) > $length) {
      $this->errors[] = "$valname は $length 桁以下で入力してください。";
    }
  }

 /**
  * カタカナチェック
  *
  */
  public function checkKana($val, $valname)
  {
    if (!empty($val)) {
      if (!preg_match("/\A[ァ-ヿ]+\z/u", $val)) {
        $this->errors[] = "$valname はカタカナで入力してください。";
      }
    }
  }

 /**
  * 性別チェック
  *
  */
  public function checkGender($val, $valname)
  {
    if(isset($val)) {
      if(!($val == 1 or $val == 2)) {
        $this->errors[] = "$valname の形式が不正です。";
      }
    } else {
      $this->errors[]   = "$valname は必須です。";
    }
  }

 /**
  * メールアドレスフォーマットチェック
  *
  */
  public function checkMailFormat($val, $valname)
  {
    if (!empty($val)) {
      if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
        $this->errors[]   = "$valname の形式が不正です。";
      }
      for( $i = 0; $i < strlen($val); $i++ ) {
        if(ctype_upper(mb_substr($val,$i,1))) {
          $this->errors[] = "$valname は小文字で入力してください。";
          break;
        }
      }
    }
  }

 /**
  * 生年月日チェック
  *
  */
  public function checkBirthday($val, $valname)
  {
    $this->checkEmpty($val, $valname);

    if(!empty($val)) {
      $this->checkDateFormat($val, $valname);
      $this->checkExistDate($val, $valname);
    }
  }

 /**
  * 日付フォーマットチェック
  *
  */
  public function checkDateFormat($val, $valname)
  {
    if(preg_match('/\A[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}\z/', $val) == false) {
      $this->errors[] = "$valname の形式が不正です。";
    }
  }

 /**
  * 日付存在チェック
  *
  */
  public function checkExistDate($val, $valname)
  {
    list($year, $month, $day) = explode('-', $val);

    if(checkdate($month, $day, $year) == false) {
      $this->errors[] = "$valname の日付は存在しません。";
    }
  }

 /**
  * 郵便番号チェック
  *
  */
  public function checkPostcode($val, $valname)
  {
    $this->checkEmpty($val, $valname);
    
    if(!empty($val)) {
      $this->checkLength($val,$valname, 8);
  
      // 郵便番号フォーマットチェック
      if (!preg_match("/\A\d{3}[-]\d{4}\z/", $val)) {
        $this->errors[] = "$valname の書式に誤りがあります。";
      }
  
      // 郵便番号存在チェック
      $errFlg = false;
      $readCsvClass = new ReadCsvClass();
      $PostcodeList = $readCsvClass->readPostcodeCsv();
  
      // 配列の値を繰り返し処理で表示する
      foreach($PostcodeList as $value){
        // 1行のデータをコンマで分割する
        $data = explode(',', $value);

        if(strpos($data[2], str_replace("-","",$val)) !== false) {
          $errFlg = true;
          break;
        }
      }

      if ($errFlg == false) {
        $this->errors[] = "存在しない$valname が設定されています。";
      }
    }
  }

 /**
  * ファイル項目チェック
  *
  */
  public function checkFile($file, $file_name)
  {
    if(strcmp($file['size'], 0) == 0) {
      $this->errors[]   = "$file_name は必須です。";
    }
    else {
      // ファイル形式チェック
      if (!(str_ends_with($file['name'], ".pdf") or
            str_ends_with($file['name'], ".PDF") or
            str_ends_with($file['name'], ".docx") or
            str_ends_with($file['name'], ".DOCX"))) {
              $this->errors[] = "$file_name はpdf、もしくはdocxの形式でアップロードしてください。";
      }
  
      // ファイルサイズチェック
      else if ($file['size'] > 1048576) {
        $this->errors[] = "$file_name のファイルサイズは1MB以下でアップロードしてください。";
      }
    }
  }

 /**
  * 職種チェック
  *
  */
  public function checkJob($val, $valname)
  {
    if (isset($val)) {
      for( $i = 0; $i < count($val); $i++ ) {
        $this->checkTblData($val[$i], $valname, "job_mst", "job_name");
      }
    } else {
      $this->errors[] = "$valname は必須です。";
    }
  }

 /**
  * マスタデータの存在チェック
  *
  */
  public function checkTblData($val, $valname, $tblname, $column)
  {
    $pdo  = new PDO("mysql:host=localhost;dbname=localtestdb", "root", "");
    $sql  = "SELECT COUNT(*) AS cnt FROM $tblname WHERE $column = ?";
    $stmt = $pdo->prepare($sql);
    $stmt -> bindValue(1, $val, PDO::PARAM_STR);
    $stmt -> execute();
    $row  = $stmt->fetch(PDO::FETCH_ASSOC );

    if($row['cnt'] == 0) {
      $this->errors[]   = "存在しない $valname が設定されています。";
    }
  }
}

class ReadCsvClass
{
 /**
  * 郵便番号CSV読み込み処理
  *
  * @return array CSV配列
  */
  public function readPostcodeCsv()
  {
    // CSVファイルを読み込む
    return file('./KEN_ALL.CSV');
  }
}

class InsDataClass
{
 /**
  * 応募情報TBL登録処理
  *
  * @return bool 登録成功／登録失敗
  */
  public function insData()
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
    $statement      = $db->prepare(
      "INSERT INTO oubo_info
        (`username`,`username_kana`,`gender`,`birthday`,`education`,`postcode`,`prefecture`,`city`,`email`,`job`,`userfile`,`etc`)
        VALUES
        (?,?,?,?,?,?,?,?,?,?,?,?);"
    );

    if (!$statement) {
        die($db->error);
    }

    $statement->bind_param("ssisssssssss" 
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

    return $statement->execute();
  }
}