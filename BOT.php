<?PHP
$bot_token = "6938169902:AAF47uv_0GZG_oJPy9fB3WDJLGpwnZb7ahI";
$url = "https://api.telegram.org/bot".$bot_token;

require 'connections.php';

$json_updates = file_get_contents("php://input");
$updates = json_decode($json_updates, true);

//!========= BUTTONS
    //! MAIN MAIN
    $m_k1 = "موجودی انبار";
    $m_k2 = "فاکتور ماهانه";
    $m_k3 = "ثبت تعمیر";
    $m_k4 = "افزودن به انبار";
//!========= KEYBOARD
    //!========== MAIN KEYBOARD
    $main_k_set = [
        [$m_k1,$m_k2],
        [$m_k3,$m_k4],
    ];
    $main_kb = [
        'one_time_keyboard' =>false,
        'resize_keyboard'   => true,
        'keyboard'          => $main_k_set
    ];



if(isset($updates['message'])){
    $chat_id = $updates['message']['chat']['id'];
    $text = $updates['message']['text'];
    //! TODO ADD CHECK ADMIN CHAT ID
    if(true){
        check();
        send_message_wk($text,$main_kb);

    }
}
//!========= > FUNCTIONS
    //!=========> GET ADMIN STATUS
    function admin_status(){

    }
    //!=========> SET ADMIN STATUS
    function set_admin_status($text){

    }
//!========= > MAIN FUNCTIONS




    //!========== CHECK DATABASE
    function check(){
        $con =  mysqli_connect($GLOBALS['servername'], $GLOBALS['user'], $GLOBALS['pass'],$GLOBALS['dbname']);
        $check = true;
        $sql_admin = "CREATE TABLE IF NOT EXISTS s_admin(id INT PRIMARY KEY,chat_id TEXT,username TEXT,status TEXT,item_id TEXT,text TEXT)";
        $res_admin = mysqli_query($con,$sql_admin);

        $check = $res_admin !== false?true:false;

        $sql_anbar = "CREATE TABLE IF NOT EXISTS s_store(id INT PRIMARY KEY,p_type TEXT,model TEXT,count TEXT,date_in TEXT,price TEXT)";
        $res_anbar = mysqli_query($con,$sql_anbar);

        $check = $res_anbar !== false?true:false;

        $sql_repaires = "CREATE TABLE IF NOT EXISTS s_repairs(id INT PRIMARY KEY,username TEXT,phone TEXT,Price TEXT,take_date TEXT,serv_date TEXT,profit TEXT,end TEXT)";
        $res_repaires = mysqli_query($con,$sql_repaires);

        $check = $res_repaires !== false?true:false;
        
        mysqli_close($con);
        
        return $check;
    }

    //!=========== ADMIN UPDATES
    function send_to_admin($text){
        $url =$GLOBALS['url']."/sendMessage";
        $parameters = ['chat_id'=>$GLOBALS['chat_id'],'text'=>$text];
        $res = send_request($url,$parameters);
        return $res;
    }
    //!=========== SEND MESSAGE WITH KEYBAORD
    function send_message_wk($text,$keyboard){
        $url= $GLOBALS['url']."/sendMessage";
        $kb = json_encode($keyboard);
        $parameters = ['chat_id' => $GLOBALS['chat_id'],'text'=>$text,'reply_markup'=>$kb];
        $result = send_request($url,$parameters);
        return $result;
    }
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