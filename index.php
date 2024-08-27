<?php
$pageFlg = 0;//ページ遷移の変数

if(!empty($_POST["btn_confirm"])){//確認画面
 $pageFlg = 1;
}
if(!empty($_POST["btn_submit"])){//完了画面
    $pageFlg = 2;
 }
if(!empty($_POST["btn_close"])){//閉じる画面
     
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
    名前
  <br>
  <input type="text" placeholder="名前を入力" name="name" value ="<?php if(!empty($_POST["name"])){echo $_POST["name"];}?>">

  <br><br>
    名前（カナ）
  <br>
  <input type="text" placeholder="名前（カナ）を入力" name="name_kana" value ="<?php if(!empty($_POST["name_kana"])){echo $_POST["name_kana"];}?>">

  <br><br>
    性別
  <br>
  <input type="radio" name="gender_id" value="男性">男性<br>
  <input type="radio" name="gender_id" value="女性">女性

  <br><br>
    生年月日
  <br>
<!-- 生年月日　カレンダーを実装する-->

  <br><br>
    最終学歴
  <br><select name="gakureki">
        <option value="大卒">大卒</option>
        <option value="院卒">院卒</option>
        <option value="option3">etc..</option>
      </select>
<!-- 最終学歴　DBから取得する-->

  <br><br>
    自宅住所
  <br>
  <input type="text" placeholder="郵便番号" name="postcode" value ="<?php if(!empty($_POST["postcode"])){echo $_POST["postcode"];}?>">
  <br><select name="prefecture_id">
        <option value="北海道">北海道</option>
        <option value="青森県">青森県</option>
        <option value="option3">etc..</option>
      </select>
      <!-- 都道府県　DBから取得する-->
  <input type="text" placeholder="市区町村" name="city" value ="<?php if(!empty($_POST["city"])){echo $_POST["city"];}?>">

  <br><br>
    メールアドレス
  <br><input type = "text" placeholder = "メールアドレスを入力" name ="email" value ="<?php if(!empty($_POST["email"])){echo $_POST["email"];}?>">

  <br><br>
    希望職種
  <br>
    <input type="checkbox" name="jobs[]" value="総合職"> 総合職
    <input type="checkbox" name="jobs[]" value="事務職"> 事務職
    <input type="checkbox" name="jobs[]" value="技術職"> 技術職
<!-- 希望職種　DBから取得する-->

  <br><br>
    履歴書
  <br>
<!-- 履歴書　ファイルを選択を実装する-->

  <br><br>
    その他要望など
  <br>
  <textarea cols="50" rows="5" placeholder = "その他要望などを入力" name="etc"  cols="50" rows="5" 
    value ="<?php if(!empty($_POST["etc"])){echo $_POST["etc"];}?>"></textarea>

  <br><br>
    プライバシーポリシー
  <br>
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
  <?php echo $_POST["name"] ;?>
  <br><br>
    名前（カナ）　：　
  <?php echo $_POST["name_kana"] ;?> 
  <br><br>
    性別　：　
  <?php echo $_POST["gender_id"] ;?> 
  <br><br>
    学歴　：　
  <?php echo $_POST["gakureki"] ;?> 
  <br><br>
    自宅住所　：　
  <?php echo $_POST["postcode"] ;?> 
  <br>
  　　　　　　　<?php echo $_POST["prefecture_id"] ;?><?php echo $_POST["city"] ;?> 
  <br><br>
    メールアドレス　：　
  <?php echo $_POST["email"] ;?> 
  <br><br>
    希望職種　：　
  <?php
    if (isset($_POST['jobs']) && is_array($_POST['jobs'])) {
      echo $food = implode("、", $_POST["jobs"]);
    }
  ;?> 
  <br><br>
  その他要望など　：　
  <?php echo $_POST["etc"] ;?> 
  <pre>

  <input type = "submit" name = "back" value = "修正する">　<input type = "submit" name = "btn_submit" value = "応募する" >
  <br>
  <input type = "hidden" name = "name" value = "<?php echo $_POST["name"] ;?>">
  <input type = "hidden" name = "name_kana" value = "<?php echo $_POST["name_kana"] ;?>">
  <input type = "hidden" name = "gender_id" value = "<?php echo $_POST["gender_id"] ;?>">
  <input type = "hidden" name = "gakureki" value = "<?php echo $_POST["gakureki"] ;?>">
  <input type = "hidden" name = "postcode" value = "<?php echo $_POST["postcode"] ;?>">
  <input type = "hidden" name = "prefecture_id" value = "<?php echo $_POST["prefecture_id"] ;?>">
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
