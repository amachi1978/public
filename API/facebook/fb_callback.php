<?php
    require_once '../../API/API_Facebook.php';
    // ************
    // * データ取得 *
    // ************
    $code = $_REQUEST['code'];
    $token_url = 'https://graph.facebook.com/oauth/access_token?client_id=' . FB_APP_ID . '&client_secret=' . FB_APP_SECRET . '&redirect_uri=' . urlencode(FB_CALLBACK) . '&code=' . $code;

    // 短命のアクセストークンを取得
    $data = json_decode(file_get_contents($token_url));
    $access_token = $data->access_token;

    // アクセストークンからユーザ情報を取得
    $token_url2 = 'https://graph.facebook.com/me?access_token='.$access_token;
    $user = json_decode(file_get_contents($token_url2));

    // Facebookの「user_id」「name(表示名)」を取得
    $FacebookID = $user->id;
    $FacebookName = $user->name;
    if ($FacebookID == "") {
        // ログイン失敗
    } else {
        // ログインID成功
    }
}
?>