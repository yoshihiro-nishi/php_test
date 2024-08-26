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
<!-- 性別　ラジオボタンを実装する-->

  <br><br>
    生年月日
  <br>
<!-- 生年月日　カレンダーを実装する-->

  <br><br>
    最終学歴
  <br>
<!-- 最終学歴　プルダウンを実装する-->

  <br><br>
    自宅住所
  <br>
  <input type="text" placeholder="郵便番号" name="yubin_no" value ="<?php if(!empty($_POST["yubin_no"])){echo $_POST["yubin_no"];}?>">
  <br>
  <!-- 都道府県　プルダウンを実装する-->
  <input type="text" placeholder="市区町村" name="shikuchoson" value ="<?php if(!empty($_POST["shikuchoson"])){echo $_POST["shikuchoson"];}?>">

  <br><br>
    メールアドレス
  <br><input type = "text" placeholder = "メールアドレスを入力" name ="email" value ="<?php if(!empty($_POST["email"])){echo $_POST["email"];}?>">

  <br><br>
    希望職種
  <br>
<!-- 希望職種　チェックボックスを実装する-->

  <br><br>
    履歴書
  <br>
<!-- 履歴書　ファイルを選択を実装する-->

  <br><br>
    その他要望など
  <br>
<!-- その他要望など　テキストエリアを実装する-->

  <br><br>
    プライバシーポリシー
  <br>
<!-- プライバシーポリシー　チェックボックスを実装する-->

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
    メールアドレス　：　
  <?php echo $_POST["email"] ;?> 
  <pre>

  <input type = "submit" name = "back" value = "修正する">　<input type = "submit" name = "btn_submit" value = "応募する" >
  <br>
  <input type = "hidden" name = "name" value = "<?php echo $_POST["name"] ;?>">
  <input type = "hidden" name = "name_kana" value = "<?php echo $_POST["name_kana"] ;?>">
  <input type = "hidden" name = "email" value = "<?php echo $_POST["email"] ;?>">
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
