<?php
//ファイルの読み込み
require_once "db_connect.php";
require_once "functions.php";

//セッションの開始
session_start();

//POSTされてきたデータを格納する変数の定義と初期化
$datas = [
    'name'  => '',
    'email' => '',
    'password'  => '',
    'confirm_password'  => ''
];
$resister_err = '';

//GET通信だった場合はセッション変数にトークンを追加
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    setToken();
}
//POST通信だった場合はDBへの新規登録処理を開始
if($_SERVER["REQUEST_METHOD"] == "POST"){
    //CSRF対策
    checkToken();

    // POSTされてきたデータを変数に格納
    foreach($datas as $key => $value) {
        if($value = filter_input(INPUT_POST, $key, FILTER_DEFAULT)) {
            $datas[$key] = $value;
        }else{$resister_err = 'データ格納出来ませんでした';}
    }

    // バリデーション
    $errors = validation($datas);

    //データベースの中に同一ユーザー名が存在していないか確認
    if(empty($errors['name'])){
        $sql = "SELECT email FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('email',$datas['email'],PDO::PARAM_INT);
        $stmt->execute();

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $errors['email'] = 'This email is already taken.';
        }
    }else{
      $resister_err = '同一のemailが存在しています。';
    }
    //エラーがなかったらDBへの新規登録を実行
    if(empty($errors)){ //echo 'DBへ新規登録';
        $params = [
            'id' =>null,
            'name'=>$datas['name'],
            'email'=>$datas['email'],
            'password'=>password_hash($datas['password'], PASSWORD_DEFAULT),
            // 'created_at'=>null
        ];

        $count = 0;
        $columns = '';
        $values = '';
        foreach (array_keys($params) as $key) {
            if($count > 0){
                $columns .= ',';
                $values .= ',';
            }
            $columns .= $key;
            $values .= ':'.$key;
            $count++;
        }

        $pdo->beginTransaction();//トランザクション処理
        try {
            $sql = 'insert into users ('.$columns .')values('.$values.')';
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $pdo->commit();
            header("location: login.php");
            exit;
        } catch (PDOException $e) {
            echo 'ERROR: Could not register.';
            $pdo->rollBack();
        }
    }else{
      $resister_err = 'DBに登録が出来ませんでした。';
    }
}else{
  // echo 'DBへ新規登録処理が出来ませんでした';
}
?>

<!Doctype html>
<html lang="ja" >
  <head>
    <meta charset="UTF-8">
    <title>新規登録画面</title>
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
    <form action="<?php echo $_SERVER ['SCRIPT_NAME']; ?>" method="post" class="form-signin">
    <?php 
            if(!empty($resister_err)){
                echo '<div class="alert alert-danger">' . $resister_err . '</div>';
            }
    ?>
  <!-- <form class="form-signin" action=“./main.html"> -->
    <img class="mb-4" src=".\favicon.ico" alt="" width="72" height="72">
    <h1 class="h3 mb-3 font-weight-normal">新規登録</h1>

    <input type="name" name="name" id="inputName" class="form-control" placeholder="Full name" <?php //echo (!empty(h($errors['name']))) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['name']); ?>" required>
    <span class="invalid-feedback"><?php echo h($errors['name']); ?></span>

    <!-- <label for="inputEmail" class="sr-only">Email address</label> -->
    <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" <?php //echo (!empty(h($errors['email']))) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['email']); ?>" required autofocus>
    <span class="invalid-feedback"><?php echo h($errors['email']); ?></span>

    <!-- <label for="inputPassword" class="sr-only">Password</label> -->
    <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" <?php //echo (!empty(h($errors['password']))) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['password']); ?>" required>
    <span class="invalid-feedback"><?php echo h($errors['password']); ?></span>

    <!-- <label>Confirm Password</label> -->
    <input type="password" name="confirm_password" id="inputPassword2" class="form-control" placeholder="confirm_password" <?php //echo (!empty(h($errors['confirm_password']))) ? 'is-invalid' : ''; ?>" value="<?php echo h($datas['confirm_password']); ?>" required>
    <span class="invalid-feedback"><?php echo h($errors['confirm_password']); ?></span>


    <input type="checkbox" id="showPassword" onchange="togglePasswordVisibility()" />
    <!-- <input type="checkbox" id="showPassword" onchange="togglePasswordVisibility2()" /> -->
    <label for="password-check" onclick="">パスワードを表示する</label>
    
    <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
    
    <input type="submit" class="btn btn-lg btn-primary btn-block" value="新規登録">
    <!-- <button class="btn btn-lg btn-primary btn-block" type="submit">新規登録</button> -->
    <button class="btn btn-lg btn-primary btn-block" type="button" onclick=history.back()>戻る</button>

    <p class="mt-5 mb-3 text-muted">&copy; 2024-</p>
    <!-- <button class="btn btn-lg btn-primary btn-block" type="submit" onclick="location.href='./main.html'">ログイン</button> -->
  </form>
    <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
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