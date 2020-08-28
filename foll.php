<?php
date_default_timezone_set("Asia/Jakarta");
require("./ModulKu.php");
$m = new ModulKu();
if(isset($argv[1])){
    $username = $argv[1];
}else{
    die("# PLEASE PROVIDE TRUTH USERNAME!".PHP_EOL);
}
function headers(){
    $headers = array();
    $headers[] = 'Connection: keep-alive';
    $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.135 Safari/537.36';
    $headers[] = 'Content-Type: application/x-www-form-urlencoded;charset=UTF-8';
    $headers[] = 'Accept: */*';
    $headers[] = 'Origin: https://instarlike.com';
    $headers[] = 'Sec-Fetch-Site: same-origin';
    $headers[] = 'Sec-Fetch-Mode: cors';
    $headers[] = 'Sec-Fetch-Dest: empty';
    $headers[] = 'Referer: https://instarlike.com/auth';
    $headers[] = 'Accept-Language: en-US,en;q=0.9,id;q=0.8';
    $headers[] = 'Cookie: session_id=1598607553170';
    return $headers;
}
function search_user($q){
    global $m;
    $headers = headers();
    $curl = $m->curl("https://instarlike.com/api/instaSearch", "is_web=true&is_vip=true&sessionID=1598607553170&timestamp=03AGdBq247tH5vRDJwWL6_B6coB5Pe8etznoFbiBYiB_9WI1bddm3D_Z8T5Q9zLJhdwYgjc35iyCwLAfNNnFjDbTwx-dWLZBb4EZJnxaZpAmn6DP6fyenItmuik0jCLpVHG-fahELWQ9TkkSJcU9kqaBxNtmLofRuT9HsC-b6tRyY4ULVyb5VE1bBXXkTXA9WligPKWiRVX0hTAlj9EjQ1qaDrFY1QfFvOWB87wwjG1h0LLSPhnSlgwvvSqv1hrCcvuvQuuAPaAY-qVK4L72mbX9LQif7vkTHN7mpjzMM085KY8QnpsR34xlqQtJWp_GLEm23wYysgJh_iVLZyUQ1PCAYU6cGx9wEOtjKdRTScmoZZ3vW2pbWafkPet2lVk4EBeajp7eztHQ1Q&login=".$q."&secretFlag=1", $headers);
    $json = json_decode($curl[1], true);
    $user_lists = array();
    if($json['status'] == "OK"){
        foreach($json['user']['users'] as $usr){
            $user_lists[] = array(
                "user_id"   => $usr['pk'],
                "username"  => $usr['username']
            );
        }
        return $user_lists;
    }else{
        return "~ failed to retrieving user lists".PHP_EOL;
    }
}

function get_info($username){
    global $m;
    $curl = $m->curl("https://www.instagram.com/".$username."/?__a=1");
    $json = json_decode($curl[1], true);
    if(preg_match('/logging_page_id/i', $curl[1])){
        return array(
            "user_id"   => $json['graphql']['user']['id'],
            "username"  => $json['graphql']['user']['id'],
            "profile_pic"   => $json['graphql']['user']['profile_pic_url'],
            "followers_count" => $json['graphql']['user']['edge_followed_by']['count']
        );
    }else{
        return "~ failed to get user info".PHP_EOL;
    }
}

function tembak_corry(array $post){
    global $m;
    $tembak = $m->curl("http://54.169.227.74:6969/v2", http_build_query($post));
    if(preg_match('/"status":true/i', $tembak[1])){
        return true;
    }else{
        return false;
    }
}
// print_r(get_info($username));
while(true){
    echo "[".date("d-m-Y H:i:s")."] ~ Retrieving user info from ".$username."..".PHP_EOL;
    $info_user = get_info($username);
    echo "[".date("d-m-Y H:i:s")."] ~ Followers now : ".$info_user["followers_count"]."..".PHP_EOL;
    echo "[".date("d-m-Y H:i:s")."] ~ Generating user lists...".PHP_EOL;
    $search_user = search_user($m->generateRandomString(rand(2,3)));
    foreach($search_user as $usr){
        $ar = array(
            "username1" => $usr['username'],
            "id1"   => $usr['user_id'],
            "id2"   => $info_user['user_id'],
            "profile_pic"   => $info_user['profile_pic'],
            "count" => $info_user['followers_count']
        );
        $tembak = tembak_corry($ar);
        if($tembak){
            echo " > [".date("d-m-Y H:i:s")."] [ ".$usr['username']." ] ~ STATE : SUCCESS".PHP_EOL;
        }else{
            echo " > [".date("d-m-Y H:i:s")."] [ ".$usr['username']." ] ~ STATE : FAILED".PHP_EOL;
        }
        sleep(5);
    }
    echo PHP_EOL;
}
?>
