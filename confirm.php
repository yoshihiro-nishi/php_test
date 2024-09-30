<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset = "utf-8">
    <link rel="stylesheet" href="style.css">
  </head>

  <body class="main">
    <font id="title">採用応募確認画面</font>
    <form method = "POST" action="process.php" enctype="multipart/form-data"><br>

    <label id="confitem">名前</label>
    <label id="confitemval"><?php echo $_SESSION["user"] ;?></label>

    <label id="confitem">名前（カナ）</label>
    <label id="confitemval"><?php echo $_SESSION["user_kana"] ;?></label>

    <label id="confitem">性別</label>
    <label id="confitemval">
    <?php if($_SESSION['gender'] == 1){echo "男性";}
          elseif($_SESSION['gender'] == 2){echo "女性";}?></label>

    <label id="confitem">生年月日</label>
    <label id="confitemval"><?php echo date("Y/m/d", strtotime($_SESSION["birthday"])) ;?></label>

    <label id="confitem">学歴</label>
    <label id="confitemval"><?php echo $_SESSION["education"] ;?></label>

    <label id="confitem">自宅住所</label>
    <label id="confpost"><?php echo $_SESSION["postcode"] ;?></label>
    <label id="confitemval"><?php echo $_SESSION["prefecture"] ;?><?php echo $_SESSION["city"] ;?></label>

    <label id="confitem">メールアドレス</label>
    <label id="confitemval"><?php echo $_SESSION["email"] ;?></label>

    <label id="confitem">希望職種</label>
    <label id="confitemval">
    <?php if (isset($_SESSION['jobs']) && is_array($_SESSION['jobs'])) {
            echo $jobs = implode("、", $_SESSION["jobs"]);};?></label>

    <label id="confitem">履歴書</label>
    <label id="confitemval"><?php print_r($_FILES["userfile"]["name"]);?></label>

    <label id="confitem">その他要望など</label>
    <label id="confitemval"><?php echo $_SESSION["etc"] ;?></label>
  
    <input type = "submit" name = "back" value = "修正する">　<input type = "submit" name = "btn_submit" value = "応募する" >
    </form>
  </body>
</html>