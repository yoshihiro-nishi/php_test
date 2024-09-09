<!DOCTYPE html>
<meta charset = "utf-8">

<head></head>

<body>
  
  <font size='7'>採用応募入力画面</font>
  <form method = "POST" action="process.php" enctype="multipart/form-data">
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
  <br>
    履歴書<br>
    <input name="userfile" type="file" />

  <br><br>
    その他要望など<br>
  <textarea cols="50" rows="5" placeholder = "その他要望などを入力" name="etc"  cols="50" rows="5"><?php if(isset($_POST['etc'])) print($_SESSION["etc"]);?></textarea>

  <br><br>
    プライバシーポリシー<br>
    <input type="checkbox" name="privacy_policy[]" value="同意する" <?php if(isset($_POST['privacy_policy'])&&in_array("同意する",$_POST['privacy_policy'])) echo 'checked'?>> 同意する

  <br><br>
  <input type = "submit" name = "btn_confirm"  value = "入力内容の確認" >
  <br><br><br>
  
  </form>

</body>
</html>