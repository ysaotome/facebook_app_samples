<?php

//Facebook公式SDK(開発セット)を読み込む
//SDKは下記からダウンロードして解凍して設置してください。
//　https://github.com/facebook/facebook-php-sdk/zipball/v3.2.0
require './facebook-php-sdk/src/facebook.php';

//AppIDとAppSecretをFacebook Developer Centerにて取得して下さい。
//　https://developers.facebook.com/apps/
//AppIDとAppSecretを設定してください。
$facebook = new Facebook(array(
    'appId'  => 'APP ID',
    'secret' => 'APP SECRET',
));


//ログイン状態を取得する
$user = $facebook->getUser();

if ($user) {
    
    try {
        //ログインしていたら、自分のアルバム一覧を取得
        $user_albums= $facebook->api("/me/albums");
        //ログインしていたら、自分の所属グループ一覧を取得
        $user_groups= $facebook->api("/me/groups");
    } catch (FacebookApiException $e) {
        //アルバム一覧の取得に失敗 = ログインしていない
        error_log($e);
        $user = null;
    } 
}

if ($user) {
    //ログインしていたら、ログアウトURLを取得。
    $params = array( 'next' => 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'] );
    $logoutUrl = $facebook->getLogoutUrl($params);
    //セッションのクリア
    $facebook->destroySession();

} else {
    //ログインして無いなら、ログインURLを取得。
    $params = array(
      //【大事】ログイン時に同時に取得する権限。下記は「写真」と「グループ」情報へのアクセス許可を取得
      //詳細は　https://developers.facebook.com/docs/authentication/permissions/　参照
      'scope' => 'user_photos, user_groups',
      'redirect_uri' => 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'] ,
    );
    $loginUrl = $facebook->getLoginUrl($params);
}

//HTMLヘッダを表示
echo <<<_HEADER_
<html>
    <head>
        <meta content='text/html; charset=utf-8' http-equiv='content-type'>
    </head>
    <h1>Facebook連携サンプルプログラム２</h1>
    <p><a href="./example2.php.txt">このページのソースコード</a>(文字コード：UTF-8)</p>
_HEADER_;

//==========================================================================
echo '<hr />'."\n";
//ログインボタン、ログアウトボタンを表示
if ($user) {
    echo '<a href="'. $logoutUrl .'">ログアウト</a>'."\n";
} else {
    echo '<div><a href="'. $loginUrl .'">ログイン</a></div>'."\n";
}

//==========================================================================
echo '<hr />'."\n";

//ログインしていたら、取得した情報を表示する
if ($user) {
    echo '<h3>ログインしている人の写真</h3>'."\n";
    echo '<img src="https://graph.facebook.com/'. $user .'/picture">'."\n";

    echo '<h3>ログインしている人のアルバム一覧 (/me/albums)</h3>';
    echo '<pre>'."\n";
    echo print_r($user_albums);
    echo '</pre>'."\n"; 
  
    //個々のアルバム情報を表示
    echo '<h3>ログインしている人のアルバムへのリンク</h3>';
    foreach ($user_albums['data'] as $key_album => $val_album) {
      echo '<h4><a href="'.$val_album['link'].'">'.$val_album['name'].'</a></h4>'."\n";
    }

    //==========================================================================
    echo '<hr />'."\n";

    echo '<h3>ログインしている人の所属グループ一覧 (/me/groups)</h3>';
    echo '<pre>'."\n";
    echo print_r($user_groups);
    echo '</pre>'."\n"; 
  
    //個々のグループ情報を表示
    foreach ($user_groups['data'] as $key_group => $val_group) {
        echo '<h4><a href="https://www.facebook.com/groups/'.$val_group['id'].'">'.$val_group['name'].'</a></h4>'."\n";
    }

    //==========================================================================
    echo '<hr />'."\n";
 
  
} else {
    echo '<strong><em>あなたはまだログインしていません</em></strong>'."\n";
}


//==========================================================================
echo '<hr />'."\n";

echo '<h3>デバッグ用の情報</h3>'."\n";
echo '<pre>'."\n";
echo print_r($_SESSION);
echo '</pre>'."\n";

//==========================================================================
echo '<hr />'."\n";

echo<<<_FOOTER_
</body>
</html>
_FOOTER_;
