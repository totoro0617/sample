<?php
    //ファイルの読み込み
    require_once "db_connect.php";
    require_once "functions.php";
    //セッション開始
    session_start();
    // セッション変数 $_SESSION["loggedin"]を確認。ログイン済だったらウェルカムページへリダイレクト
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: welcome.php");
        exit;
    }
?>
<!doctype html>
<html lang="ja" >
  <head>
    <meta charset="UTF-8">
    <title>メイン画面</title>
    <link rel="canonical" href="https://getbootstrap.jp/docs/5.3/examples/sign-in/">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="signin.css" rel="stylesheet">
  </head>
  <body  class="text-center" >
    <a id="skippy" class="sr-only sr-only-focusable" href="#content">
  <div class="container">
    <span class="skiplink-text">Skip to main content</span>
  </div>
</a>

  <form class="form-signin">
    <img src=".\favicon.ico" alt="" width="72" height="72">
    <h1 class="h3 mb-3 font-weight-normal">あなたの登録情報</h1>
    <label for="inputEmail" class="sr-only">Email</label>
    

    <p>名前：<?php echo htmlspecialchars($_SESSION["name"]); ?></p>
    <p>Mail：<?php echo htmlspecialchars($_SESSION["email"]); ?></p>

    <!-- <label for="inputEmail" class="sr-only">Email address</label> -->
    <!-- <input type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus> -->
    <!-- <label for="inputPassword" class="sr-only">Password</label> -->
    <!-- <input type="password" id="inputPassword" class="form-control" placeholder="Password" required> -->
    <button class="btn btn-lg btn-primary btn-block" type="button" onclick="location.href='./logout.php'">ログアウト</button>
    <!-- <button class="btn btn-lg btn-primary btn-block" type="button" onclick=history.back()>戻る</button> -->
    <!-- <p>
        <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
    </p> -->
    <p class="mt-5 mb-3 text-muted">&copy; 2024-</p>
  </form>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script>
    window.jQuery || document.write('<script src="/docs/4.3/assets/js/vendor/jquery-slim.min.js"><\/script>')
  </script>
</body>
</html>