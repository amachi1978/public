<?php
define('CONSUMER_KEY'   , '');
define('CONSUMER_SECRET', '');

/**
 * リクエストトークンの取得
 *
 * @param $CallBackURL
 * @return void
 */
function getRequestToken($CallBackURL){
    // **************************
    // * リクエストする情報をセット *
    // **************************
    $oauth = array(
        // 認証後に戻ってくる URL
        'oauth_callback' => $CallBackURL,
        // 登録したアプリの Consumer key
        'oauth_consumer_key' => CONSUMER_KEY,
        // ランダムな文字列
        'oauth_nonce' => md5(uniqid(rand(), true)),
        // ここは以下の値で固定らしい
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_timestamp' => time(),
        'oauth_version' => '1.0',
    );
    // *********************
    // * リクエストするURL *
    // *********************
    $url = "https://api.twitter.com/oauth/request_token";

    // ********************************************************************************
    // * シグネチャを生成するためにベースとなる文字列を生成する                                *
    // * 今回は分かりやすくするため一つ一つやってるけど、配列でループさせて生成したほうがいいかも    *
    // * パラメータは順番がキーの部分で昇順になってないといけないっぽい                         *
    // ********************************************************************************
    $base = 'POST&';
    $base .= rawurlencode($url) . '&';
    $base .= 'oauth_callback' . rawurlencode('=' . rawurlencode($oauth['oauth_callback']) . '&');
    $base .= 'oauth_consumer_key' . rawurlencode('=' . rawurlencode(CONSUMER_KEY) . '&');
    $base .= 'oauth_nonce' . rawurlencode('=' . rawurlencode($oauth['oauth_nonce']) . '&');
    $base .= 'oauth_signature_method' . rawurlencode('=' . rawurlencode('HMAC-SHA1') . '&');
    $base .= 'oauth_timestamp' . rawurlencode('=' . rawurlencode($oauth['oauth_timestamp']) .'&');
    $base .= 'oauth_version' . rawurlencode('=' . rawurlencode('1.0'));

    // **********************************************************************************
    // * ハッシュ値を生成するのに使う鍵、リクエストトークンの場合は Consumer secret に & つけたもの *
    // **********************************************************************************
    $key = rawurlencode(CONSUMER_SECRET) . '&';

    // ************************************************
    // * シグネチャ生成、ハッシュには sha1 を使用する *
    // ************************************************
    $oauth['oauth_signature'] = base64_encode(hash_hmac('sha1', $base, $key, true));

    // ******************
    // * キー=値の形にする *
    // ******************
    $values = array();
    foreach ($oauth as $key => $value) {
        $values[] = "{$key}=\"" . rawurlencode($value) . "\"";
    }

    // ***********
    // * ヘッダー *
    // ***********
    $headers = array(
        "POST /oauth/request_token HTTP/1.1",
        "Host: api.twitter.com",
        "User-Agent: MyApp",
        "Accept: */*",
        // カンマ区切りでセットする
        "Authorization: OAuth ".implode(',', $values)
    );

    // *************
    // * リクエスト *
    // *************
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);       // サーバのHTTPS証明書を信頼確認を無視
    curl_setopt($ch, CURLOPT_HEADER, false);               // ヘッダ初期化
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        // HTTPヘッダセット
    curl_setopt($ch, CURLOPT_POST, true);                  // POST通信を行う
    curl_setopt($ch, CURLOPT_POSTFIELDS, "");
    $ret = curl_exec($ch);
    if ($ret === false) {
        var_dump(curl_error($ch));
        die;
    }

    // ************
    // * 終了処理 *
    // ************
    curl_close($ch);
    $tokens = array();
    $ret = explode('&', $ret);
    foreach ($ret as $key => $value) {
        $value = explode('=', $value);
        $tokens[$value[0]] = $value[1];
    }

    // ************************
    // * 認証ページにリダイレクト *
    // ************************
    header('Location:https://api.twitter.com/oauth/authenticate?oauth_token=' . $tokens['oauth_token']);
    die();
}

/**
 * アクセストークンの取得
 *
 * @param $oauthToken
 * @param $oauthVerifier
 * @return $TwitterID
 */
function getAccessToken($oauthToken, $oauthVerifier)
{
    // **************************
    // * リクエストする情報をセット *
    // **************************
    $oauth = array(
        'oauth_consumer_key' => CONSUMER_KEY,
        'oauth_nonce' => md5(uniqid(rand(), true)),
        'oauth_token' => $oauthToken,
        'oauth_verifier' => $oauthVerifier,
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_timestamp' => time(),
        'oauth_version' => '1.0',
    );

    // *****************************
    // * アクセストークン取得のURL *
    // *****************************
    $url = "https://api.twitter.com/oauth/access_token";

    // *************************************************
    // * シグネチャを生成するためにベースとなる文字列を生成する *
    // *************************************************
    $base = 'POST&';
    $base .= rawurlencode($url) . '&';
    $base .= 'oauth_consumer_key' . rawurlencode('=' . rawurlencode(CONSUMER_KEY) . '&');
    $base .= 'oauth_nonce' . rawurlencode('=' . rawurlencode($oauth['oauth_nonce']) . '&');
    $base .= 'oauth_signature_method' . rawurlencode('='.rawurlencode('HMAC-SHA1') . '&');
    $base .= 'oauth_timestamp' . rawurlencode('=' . rawurlencode($oauth['oauth_timestamp']) . '&');
    $base .= 'oauth_token' . rawurlencode('=' . rawurlencode($oauthToken) . '&');
    $base .= 'oauth_verifier' . rawurlencode('=' . rawurlencode($oauthVerifier) . '&');
    $base .= 'oauth_version' . rawurlencode('=' . rawurlencode('1.0'));

    // ***************************************************************************
    // * consumerSecret だけでなく、リダイレクト前に取得した token_secret を後ろに繋げる *
    // ***************************************************************************
    $key = rawurlencode(CONSUMER_KEY) . '&' . rawurlencode($_SESSION['request_token']['oauth_token_secret']);
    $oauth['oauth_signature'] = base64_encode(hash_hmac('sha1', $base, $key, true));

    // ******************
    // * キー=値の形にする *
    // ******************
    $values = array();
        foreach($oauth as $key=>$value) {
        $values[] = "{$key}=\"".rawurlencode($value)."\"";
    }

    // ***********
    // * ヘッダー *
    // ***********
    $headers = array(
        "POST /oauth/access_token HTTP/1.1",
        "Host: api.twitter.com",
        "User-Agent: MyApp",
        "Accept: */*",
        "Authorization: OAuth ".implode(',', $values),
    );

    // *************
    // * リクエスト *
    // *************
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "");
    $ret = curl_exec($ch);
    if ($ret === false) {
        var_dump(curl_error($ch));
    }

    // ************
    // * 終了処理 *
    // ************
    curl_close($ch);
    $tokens = array();
    $ret = explode('&', $ret);
    foreach ($ret as $key => $value) {
        $value = explode('=', $value);
        $tokens[$value[0]] = $value[1];
    }

    // *******************
    // * ツイッターIDを取得 *
    // *******************
    $TwitterID = $tokens['screen_name'];
    if($TwitterID == ""){
        return false;
    }else{
        return $TwitterID;
    }
}
?>
