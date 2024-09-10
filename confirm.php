<!DOCTYPE html>
<meta charset = "utf-8">

<head></head>

<body>
  <font size='7'>採用応募確認画面</font>
  <form method = "POST" action="process.php" enctype="multipart/form-data">
  <br><br>
    名前　：　
  <?php echo $_SESSION["user"] ;?>
  <br><br>
    名前（カナ）　：　
  <?php echo $_SESSION["user_kana"] ;?> 
  <br><br>
    性別　：　
  <?php 
    if($_SESSION['gender'] == 1){echo "男性";}
    elseif($_SESSION['gender'] == 2){echo "女性";}
  ?>
  <br><br>
    生年月日　：　
  <?php echo date("Y/m/d", strtotime($_SESSION["birthday"])) ;?> 
  <br><br>
    学歴　：　
  <?php echo $_SESSION["education"] ;?> 
  <br><br>
    自宅住所　：　
  <?php echo $_SESSION["postcode"] ;?> 
  <br>
  　　　　　　　<?php echo $_SESSION["prefecture"] ;?><?php echo $_SESSION["city"] ;?> 
  <br><br>
    メールアドレス　：　
  <?php echo $_SESSION["email"] ;?> 
  <br><br>
    希望職種　：　
  <?php
    if (isset($_SESSION['jobs']) && is_array($_SESSION['jobs'])) {
      echo $jobs = implode("、", $_SESSION["jobs"]);
    }
  ;?> 
  <br><br>
    履歴書　：　
  <?php print_r($_FILES["userfile"]["name"]);?> 
  <br><br>
  その他要望など　：　
  <?php echo $_SESSION["etc"] ;?> 
  <pre>

  <input type = "submit" name = "back" value = "修正する">　<input type = "submit" name = "btn_submit" value = "応募する" >
  <br>
  <input type = "hidden" name = "user" value = "<?php echo $_SESSION["user"] ;?>">
  <input type = "hidden" name = "user_kana" value = "<?php echo $_SESSION["user_kana"] ;?>">
  <input type = "hidden" name = "gender" value = "<?php echo $_SESSION["gender"] ;?>">
  <input type = "hidden" name = "birthday" value = "<?php echo $_SESSION["birthday"] ;?>">
  <input type = "hidden" name = "education" value = "<?php echo $_SESSION["education"] ;?>">
  <input type = "hidden" name = "postcode" value = "<?php echo $_SESSION["postcode"] ;?>">
  <input type = "hidden" name = "prefecture" value = "<?php echo $_SESSION["prefecture"] ;?>">
  <input type = "hidden" name = "city" value = "<?php echo $_SESSION["city"] ;?>">
  <input type = "hidden" name = "email" value = "<?php echo $_SESSION["email"] ;?>">
  <input type = "hidden" name = "job" value = "<?php  if (isset($_SESSION['jobs']) && is_array($_SESSION['jobs'])) {
      echo implode("、", $_SESSION["jobs"]);
    } ;?>">
  <input type = "hidden" name = "etc" value = "<?php echo $_SESSION["etc"] ;?>">
  </form>
</body>
</html>