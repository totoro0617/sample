<?php
    //ファイルの読み込み
    require_once "db_connect.php";
    require_once "functions.php";
    //セッション開始
    session_start();

    // セッション変数 $_SESSION["loggedin"]を確認。ログイン済だったらウェルカムページへリダイレクト
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        header("location: main.php");
        exit;
    }

    //POSTされてきたデータを格納する変数の定義と初期化
    $datas = [
        'name'  => '',
        'email' => '',
        'password'  => '',
        'confirm_password'  => ''
    ];
    $login_err = "";

    //GET通信だった場合はセッション変数にトークンを追加
    if($_SERVER['REQUEST_METHOD'] != 'POST'){
        setToken();
    }

    //POST通信だった場合はログイン処理を開始
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        ////CSRF対策
        checkToken();

        // POSTされてきたデータを変数に格納
        foreach($datas as $key => $value) {
            if($value = filter_input(INPUT_POST, $key, FILTER_DEFAULT)) {
                $datas[$key] = $value;
            }
        }

        // バリデーション
        $errors = validation($datas,false);
        if(empty($errors)){
            //ユーザーネームから該当するユーザー情報を取得
            $sql = "SELECT id,name,email,password FROM users WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue('email',$datas['email'],PDO::PARAM_INT);
            $stmt->execute();

            //ユーザー情報があれば変数に格納
            if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                //パスワードがあっているか確認
                if (password_verify($datas['password'],$row['password'])) {
                    //セッションIDをふりなおす
                    session_regenerate_id(true);
                    //セッション変数にログイン情報を格納
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $row['id'];
                    $_SESSION["name"] =  $row['name'];
                    $_SESSION["email"] = $row['email'];
                    // $_SESSION["password"] = $row['password'];
                    //ウェルカムページへリダイレクト
                    header("location:main.php");
                    exit();
                } else {
                    $login_err = 'Invalid username or password.';
                }
            }else {
                $login_err = 'Invalid username or password.';
            }
        }
    }
?>
<!doctype html>
<html lang="ja" >
  <head>
    <meta charset="UTF-8">
    <title>ログイン画面</title>
    <link rel="canonical" href="https://getbootstrap.jp/docs/5.3/examples/sign-in/">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="signin.css" rel="stylesheet">
    <script src="./main.js"></script>
  </head>
  <body  class="text-center" >
    <a id="skippy" class="sr-only sr-only-focusable" href="#content">
      <div class="container">
        <span class="skiplink-text">Skip to main content</span>
      </div>
    </a>
    <!-- <form class="form-signin" method=”post" action="<?php //echo $_SERVER ['SCRIPT_NAME']; ?>"> -->
    <form action="<?php echo $_SERVER ['SCRIPT_NAME']; ?>" method="post" class="form-signin">
        <?php 
            if(!empty($login_err)){
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }        
        ?>
      <!-- <form class="form-signin" action=“./main.html"> -->
      <img class="mb-4" src=".\favicon.ico" alt="" width="72" height="72">
      <h1 class="h3 mb-3 font-weight-normal">ログインしてください</h1>
      <p class="h3 mb-3 font-weight-normal">初めての方は新規登録を押してください</p>

      <label for="inputEmail" class="sr-only">Email address</label>
      <!-- <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address"<?php //echo (!empty(h($errors['email']))) ? 'is-invalid' : ''; ?>" value="<?php //echo h($datas['email']); ?>" required autofocus> -->
      <input type="email" name="email" id="inputEmail" class="form-control
                    <?php // echo (!empty(h($errors['email']))) ? 'is-invalid' : ''; ?>"
                    value="<?php echo h($datas['email']);?>" placeholder="Email address" required autofocus>
      <span class="invalid-feedback"><?php echo h($errors['email']); ?></span>
      <!-- <input type="name" name="name" id="inputName" class="form-control" placeholder="Full name" <?php //echo (!empty(h($errors['name']))) ? 'is-invalid' : ''; ?>" value="<?php //echo h($datas['name']); ?>" required> -->
      <!-- <span class="invalid-feedback"><?php //echo h($errors['name']); ?></span> -->

      <!-- <label for="inputPassword" class="sr-only">Password</label> -->
      <!-- <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" <?php //echo (!empty(h($errors['password']))) ? 'is-invalid' : ''; ?>" value="<?php //echo h($datas['password']); ?>" required> -->
      <input type="password" name="password" id="inputPassword" class="form-control <?php //echo (!empty(h($errors['password']))) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['password']); ?>" placeholder="Password" required>
      <span class="invalid-feedback"><?php echo h($errors['password']); ?></span>

      <!-- <span class="invalid-feedback"><?php //echo h($errors['password']); ?></span> -->
      <input type="checkbox" id="showPassword" onchange="togglePasswordVisibility()" />
      <label for="password-check" onclick="">パスワードを表示する</label>
      
      <!-- <button class="btn btn-lg btn-primary btn-block" type="submit" formaction="main.php">ログイン</button> -->
      <input type="submit" class="btn btn-lg btn-primary btn-block" value="Login">

      <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">

      <button class="btn btn-lg btn-primary btn-block" type="button" onclick="location.href='./resister.php'">新規登録</button>
      
      <p class="mt-5 mb-3 text-muted">&copy; 2024-</p>
      <!-- <button class="btn btn-lg btn-primary btn-block" type="submit" onclick="location.href='./main.html'">ログイン</button> -->
    </form>
    <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></scrip> -->
    <!-- <script> -->
    <!-- window.jQuery || document.write('<script src="/docs/4.3/assets/js/vendor/jquery-slim.min.js"><\/script>') -->
    <!-- </script> -->
    <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script><script src="/docs/4.3/assets/js/vendor/anchor.min.js"></script> -->
    <!-- <script src="/docs/4.3/assets/js/vendor/clipboard.min.js"></script> -->
    <!-- <script src="/docs/4.3/assets/js/vendor/bs-custom-file-input.min.js"></script> -->
    <!-- <script src="/docs/4.3/assets/js/src/application.js"></script> -->
    <!-- <script src="/docs/4.3/assets/js/src/search.js"></script> -->
    <!-- <script src="/docs/4.3/assets/js/src/ie-emulation-modes-warning.js"></script> -->
  </body>
</html>