<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset = "utf-8">
    <link rel="stylesheet" href="style.css">
  </head>
  <body class="main">
    <font id="title">採用応募入力画面</font>
    <form method = "POST" action="process.php" enctype="multipart/form-data"><br>

    <label id="item">名前</label>
    <input id="itemval" type="text" placeholder="名前を入力" name="user" value ="<?php if(!empty($_SESSION["user"])){echo $_SESSION["user"];}?>">  

    <label id="item">名前（カナ）</label>
    <input id="itemval" type="text" placeholder="名前（カナ）を入力" name="user_kana" value ="<?php if(!empty($_SESSION["user_kana"])){echo $_SESSION["user_kana"];}?>">
  
    <label id="item">性別</label>
    <input id="itemval" type="radio" name="gender" value="1" <?php if (isset($_SESSION['gender']) && $_SESSION['gender'] == "1") echo 'checked'; ?>>男性
    <input id="itemval" type="radio" name="gender" value="2" <?php if (isset($_SESSION['gender']) && $_SESSION['gender'] == "2") echo 'checked'; ?>>女性
  
    <label id="item">生年月日</label>
    <input id="itemval" name="birthday" type="date" value="<?php if(!empty($_SESSION["birthday"])){echo $_SESSION["birthday"];}?>" />
  
    <label id="item">最終学歴</label>
    <select id="itemval" name="education">
    <?php
      try {
        $pdo = new PDO("mysql:host=localhost;dbname=localtestdb", "root", "");
        $sql = "SELECT education_name FROM education_mst ORDER BY education_id";
        $education = $pdo->prepare($sql);
        $education -> execute();
        while ($educationRow = $education -> fetch(PDO::FETCH_ASSOC )) {
          if(!empty($_SESSION["education"]) && $_SESSION["education"] == $educationRow['education_name']) {
            echo "<option value = ".$educationRow['education_name']." selected>".$educationRow['education_name']."</option>";
          }
          else {
            echo "<option value = ".$educationRow['education_name'].">".$educationRow['education_name']."</option>";
          }
        }
      } catch (PDOException $e) {
        header("Location: error.php");
        exit;
      }
    ?>
    </select>
  
    <label id="item">自宅住所</label>
    <input id="item" type="text" placeholder="郵便番号" name="postcode" oninput="fetchAddress(this.value)" value ="<?php if(!empty($_SESSION["postcode"])){echo $_SESSION["postcode"];}?>">
    
    <select id="prefecture" name="prefecture">
    <?php
      try {
        $sql = "SELECT prefecture_name FROM prefecture_mst ORDER BY prefecture_id";
        $prefecture = $pdo->prepare($sql);
        $prefecture -> execute();
        while ($prefectureRow = $prefecture -> fetch(PDO::FETCH_ASSOC )) {
          if(!empty($_SESSION["prefecture"]) && $_SESSION["prefecture"] == $prefectureRow['prefecture_name']) {
            echo "<option value = ".$prefectureRow['prefecture_name']." selected>".$prefectureRow['prefecture_name']."</option>";
          }
          else {
            echo "<option value = ".$prefectureRow['prefecture_name'].">".$prefectureRow['prefecture_name']."</option>";
          }
        }
      } catch (PDOException $e) {
        header("Location: error.php");
        exit;
      }
    ?>
    </select>
    <input id="city" type="text" placeholder="市区町村" name="city" value ="<?php if(!empty($_SESSION["city"])){echo $_SESSION["city"];}?>">
  
    <label id="item">メールアドレス</label>
    <input id="itemval" type = "text" placeholder = "メールアドレスを入力" name ="email" value ="<?php if(!empty($_SESSION["email"])){echo $_SESSION["email"];}?>">
  
    <label id="item">希望職種</label>
    <?php
      try {
        $sql = "SELECT job_name FROM job_mst ORDER BY job_id";
        $job = $pdo->prepare($sql);
        $job -> execute();
        while ($jobRow = $job -> fetch(PDO::FETCH_ASSOC )) {
          if(!empty($_SESSION["jobs"]) && strpos(implode("、", $_SESSION["jobs"]) , $jobRow['job_name']) !== false) {
            echo "<input type='checkbox' name='jobs[]' value=".$jobRow['job_name']." checked>".$jobRow['job_name'];
          }
          else {
            echo "<input type='checkbox' name='jobs[]' value=".$jobRow['job_name'].">".$jobRow['job_name'];
          }
        }
        echo "<p id='itemval'></p>";
      } catch (PDOException $e) {
        header("Location: error.php");
        exit;
      }
    ?>

    <label id="item">履歴書</label>
    <input id="itemval" name="userfile" type="file" />
  
    <label id="item">その他要望など</label>
    <textarea id="itemval" cols="50" rows="5" placeholder = "その他要望などを入力" name="etc"  cols="50" rows="5"><?php if(isset($_SESSION['etc'])) print($_SESSION["etc"]);?></textarea>
  
    <label id="item">プライバシーポリシー</label>
    <input id="itemval" type="checkbox" name="privacy_policy[]" value="同意する" <?php if(!empty($_SESSION['privacy_policy']) && in_array("同意する",$_SESSION['privacy_policy'])) echo 'checked'?>> 同意する
  
    <input id="item" type = "submit" name = "btn_confirm"  value = "入力内容の確認" >
    
    </form>
  
    <script>
    function fetchAddress(postcode) {
      if (postcode.length === 8) {
        fetch(`https://api.zipaddress.net/?zipcode=${postcode}`, { mode: 'cors' })
          .then(response => response.json())
          .then(data => {
            if (data.code === 200) {
              document.getElementById('prefecture').value = data.data.pref;
              document.getElementById('city').value = data.data.address;
            }
          })
      }
    }
    </script>
  </body>
</html>