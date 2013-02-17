<?php
/**
Facebookサンプルプログラム１
**/


//Facebook公式SDK(開発セット)を読み込む
//SDKは下記からダウンロードして解凍して設置してください。
//　https://github.com/facebook/facebook-php-sdk/zipball/v3.2.0
require './facebook-php-sdk/src/facebook.php';

//AppIDとAppSecretをFacebook Developer Centerにて取得して下さい。
//　https://developers.facebook.com/apps/
//AppIDとAppSecretを設定してください。
$facebook = new Facebook(array(
    'appId'  => '431491296930667',
    'secret' => 'a85ced3a0f628cf3e05a10c105534832',
));
//このアプリを公開した人
$yourname = '五月女雄一';

//ログイン状態を取得する
$user = $facebook->getUser();

if ($user) {
    try {
        //ログインしていたら、自分のユーザプロファイルを取得
        $user_profile = $facebook->api('/me');

    } catch (FacebookApiException $e) {
        //ユーザプロファイル取得失敗 = ログインしていない
        error_log($e);
        $user = null;
    }
    
    try {
        //ログインしていたら、自分の友達一覧を取得
        $user_friends = $facebook->api('/me/friends');
    } catch (FacebookApiException $e) {
        //友達一覧取得に失敗 = ログインしていない
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
    $loginUrl = $facebook->getLoginUrl();
}

//ユーザの公開情報の取得を試みる（ログインしなくても取得出来る情報）
//試しに五月女の情報を取得してみる
$ysaotome = $facebook->api('/ysaotome');

//HTMLヘッダを表示
echo <<<_HEADER_
<html>
    <head>
        <meta content='text/html; charset=utf-8' http-equiv='content-type'>
    </head>
    <p><a href="./">&lt; &lt;サンプル一覧</a></p>
    <h1>Facebook連携サンプルプログラム１</h1>
<h2>このアプリを公開した人：<span style="color:blue;">$yourname</span></h2>
    <p><a href="./example1.php.txt">このページのソースコード</a>(文字コード：UTF-8)</p>
    <p>出来る事</p>
    <ul>
        <li>１）ログイン、ログアウト処理</li>
        <li>２）ログインしている人の情報を取得する処理</li>
        <li>３）ログインしている人の友達リストを取得する処理</li>
        <li>４）ログインしてない状態で取得可能な情報を表示する処理</li>
    </ul>
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

//ログインしていたら、ログインしている人の情報を取得する
if ($user) {
    echo '<h3>ログインしている人の写真</h3>'."\n";
    echo '<img src="https://graph.facebook.com/'. $user .'/picture">'."\n";

    echo '<h3>ログインしている人の情報 (/me)</h3>'."\n";
    echo '<pre>'."\n";
    echo print_r($user_profile);
    echo '</pre>'."\n";

    echo '<h3>ログインしている人の友達リスト (/me/friends)</h3>';
    //友達リストからユーザ情報だけ取得
    $user_friends_data = $user_friends['data'];
    echo '<h4>友達の数：'. count($user_friends_data) . ' 人</h4>'."\n";
    $i=0;
    foreach ($user_friends_data as $fkey=>$fvalue) {
        $i++;
        echo '<a href="http://www.facebook.com/profile.php?id='.$fvalue[id].'"><img src="https://graph.facebook.com/' . $fvalue[id] . '/picture" border="0" title="' . $fvalue[name].'"/></a>';
        if ($i % 5 == 0) {
            echo '<br><br>';
        }
    }

} else {
    echo '<strong><em>あなたはまだログインしていません</em></strong>'."\n";
}

//==========================================================================
echo '<hr />'."\n";

echo '<h3>公開情報の取得（ログインしなくても取得出来る情報の例）</h3>'."\n";
echo '<img src="https://graph.facebook.com/ysaotome/picture">'."\n";
echo $ysaotome['name'];


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
