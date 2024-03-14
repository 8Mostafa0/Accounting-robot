<?PHP
$bot_token = "6938169902:AAF47uv_0GZG_oJPy9fB3WDJLGpwnZb7ahI";
$url = "https://api.telegram.org/bot".$bot_token;

$json_updates = file_get_contents("php://input");
$updates = json_decode($json_updates, true);

if(isset($updates['message'])){
    $chat_id = $updates['message']['chat']['id'];
    $text = $updates['message']['text'];
    send_message($text);
}

//!========= > MAIN FUNCTIONS
    //!=========== SEND MESSAGE
    function send_message($text){
        $url = $GLOBALS['url']."/sendMessage";
        $parameters = ['chat_id'=>$GLOBALS['chat_id'],'text'=>$text];
        $result = send_request($url,$parameters);
        return $result;
    }
    //!========== REQUEST FUNCTION
    function send_request($url,$parameters){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

?>