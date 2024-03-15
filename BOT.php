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
    //! BACK KEYBOARD
    $back = "بازگشت";
    //! VERIFY BUTTON
    $verify = "تایید";
    //! STORE DATA P_TYPE AND MODEL
    $sd_k1 = "مدل";
    $sd_k2 = "نوع";





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
    //!========== BACK KEYBOARD
    $back_set = [
        [$back]
    ];
    
    $back_kb = [
        'resize_keyboard' => true,
        'one_time_keyboard' => false,
        'keyboard' => $back_set
    ];
    //!========= p_type UNIQUE ITEMS
    function p_type_kb(){
        $kb = [
            'one_time_keyboard'=>false,
            'resize_keyboard' =>true,
            'keyboard' => p_type_unique()
        ];
        return $kb;
    }
    //!======== GET MODELS OF P_TYPE
    function models($p_type){
        $buttons = get_p_type_mopdels($p_type);
        $kb = [
            'one_time_keyboard' => false,
            'resize_keyboard' => true,
            'keyboard' => $buttons
        ];
        return $kb;
    }

    //!======= VERIFY KEYBOARD
    $verify_set = [
        [$verify],
        [$back]
    ];
    $verify_kb = [
        'one_time_keyboard' => false,
        'resize_keyboard' => true,
        'keyboard' => $verify_set
    ];
    //!====== P_TYPE OR MODEL KEYBOARD
    $m_or_p_set = [
        [$sd_k1],
        [$sd_k2],
        [$back],
    ];

    $m_or_p_kb = [
        'one_time_keyboard' =>true,
        'resize_keyboard' => true,
        'keyboard' => $m_or_p_set
    ];
















$r_db = check();
if($r_db){
    if(isset($updates['message'])){
        $chat_id = $updates['message']['chat']['id'];
        $text = $updates['message']['text'];
        //! TODO ADD CHECK ADMIN CHAT ID
        $admin_status = admin_status();
        // send_to_admin(json_encode($updates));
        if(($text=='/start') || ($admin_status == '0')|| ($text == $back)){
            switch($text){
                case $m_k4:add_item_to_store();break;
                case $m_k1:store_data_by_model_p_type();break;
                case $sd_k1:store_data();break;
                case $sd_k2:get_store_data_by_p_type();break;
                default:admin_panel("خانه");break;
            }
        }else if(strrpos($admin_status,'add_item') !== false){
            $part = explode(' ',$admin_status)[1];
            switch($part){
                case '1':get_item_name($text);break;
                case '2':get_model($text);break;
                case '3':verify_item($text);break;
                case '4':add_item_to_db($text);break;
                default:admin_panel("مشکلی در پردازش بوجود امد");
            }
            
        }else if(strrpos($admin_status,"store_data") !== false){
            $part = explode(' ',$admin_status)[1];
            switch($part){
                case "1": store_data_by_types($text);break;
                default:admin_panel("مشکلی در پردازش بوجود امده است");
            }
        }
}
}














//!========= > FUNCTIONS

//!=========> GET ADMIN STATUS
    function admin_status(){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $id = $GLOBALS['chat_id'];
        $sql = "SELECT status FROM s_admin WHERE chat_id='$id'";
        $res = mysqli_query($con,$sql);
        if(mysqli_num_rows($res) > 0){
            $data = mysqli_fetch_assoc($res);
            mysqli_close($con);
            return $data['status'];
        }else{
            return '0';
        }
    }
    //!=========> SET ADMIN STATUS
    function set_admin_status($text){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $id = $GLOBALS['chat_id'];
        $sql = "UPDATE s_admin SET status='$text' WHERE chat_id='$id'";
        $res = mysqli_query($con,$sql);
        mysqli_close($con);
        return $res !== false;
    }
    //!========> GET ADMIN TEXT
    function admin_text(){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $id = $GLOBALS['chat_id'];
        $sql = "SELECT text FROM s_admin WHERE chat_id='$id'";
        $res = mysqli_query($con,$sql);
        $data = mysqli_fetch_assoc($res);
        mysqli_close($con);
        return $data['text'];

    }
    //!========> SET ADMIN TEXT
    function set_admin_text($text){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $id = $GLOBALS['chat_id'];
        $sql = "UPDATE s_admin SET text='$text' WHERE chat_id='$id'";
        $res = mysqli_query($con,$sql);
        mysqli_close($con);
        return $res !== false;

    }
    
    //!========> GET ADMIN ITEM_ID
    function admin_item_id(){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $id = $GLOBALS['chat_id'];
        $sql = "SELECT item_id FROM s_admin WHERE chat_id='$id'";
        $res = mysqli_query($con,$sql);
        $data = mysqli_fetch_assoc($res);
        mysqli_close($con);
        return $data['item_id'];

    }
    //!========> SET ADMIN ITEM_ID
    function set_admin_item_id($text){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $id = $GLOBALS['chat_id'];
        $sql = "UPDATE s_admin SET item_id='$text' WHERE chat_id='$id'";
        $res = mysqli_query($con,$sql);
        mysqli_close($con);
        return $res !== false;

    }
    //!========> CLEAR ADMIN STATES
    function clear_admin_status(){
        $con = mysqli_connect($GLOBALS["servername"],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $id = $GLOBALS['chat_id'];
        $sql = "UPDATE s_admin SET status='0',item_id='0',text='0' WHERE chat_id='$id'";
        $res= mysqli_query($con,$sql);
        mysqli_close($con);
        return $res !== false;
    }
    //!========> ADMIN PANEL
    function admin_panel($text){
        clear_admin_status();
        send_message_wk($text,$GLOBALS['main_kb']);
    }
    //!========> ADD ITEM TO STORE
    function add_item_to_store(){
        send_message_wk("نوع ایتم را انتخاب کنید یا اسم ان را وارد کنید",p_type_kb());
        set_admin_status('add_item 1');
    }
    //!========> GET ITEM NAME
    function get_item_name($name){
        send_message_wk("لطفا مدل را انتخاب کنید یا از گزینه ها انتخاب کنید",models($name));
        set_admin_text($name);
        set_admin_status('add_item 2');
    }
    //!========> GET ITEM PRICE
    function get_model($model){
        $p_type = admin_text();
        $text = "
        لطفا قیمت 
        
        ایتم : ".$p_type."

        مدل : ".$model."

        را وارد کنید.

        .
        ";
        set_admin_text($p_type." ".$model);
        
        send_message_wk($text,$GLOBALS['back_kb']);
        set_admin_status('add_item 3');
    }
    //!======= VERIFY ITEM DATA
    function verify_item($price){
        $data = explode(' ',admin_text());
        $p_type = $data[0];
        $model = $data[1];
        $text= "

        این مشخصات را تایید میکنید؟

        ایتم : ".$p_type."

        مدل : ".$model."

        قیمت : ".$price."

        .
        ";
        set_admin_text($p_type." ".$model." ".$price);
        set_admin_status('add_item 4');
        send_message_v($text);
        
    }
    //!======== ADD ITEM TO DATABASE
    function add_item_to_db($verify){
        if($GLOBALS['verify']== $verify){
            $data = explode(' ',admin_text());
            $p_type = $data[0];
            $model = $data[1];
            $price = $data[2];
            $res = add_to_store_db($p_type,$model,$price);
            if($res){
                admin_panel("ایتم با موفقیت ثبت شد");

            }else{
                admin_panel("هنگام ثبت ایتم مشکلی بوجود امد لطفا بعدا امتحان کنید");
            }

        }else{
            admin_panel("عملیات لغو شد");
        }
    }
    //!======= STOR DATA
    function store_data(){
        $con =  mysqli_connect($GLOBALS['servername'], $GLOBALS['user'], $GLOBALS['pass'],$GLOBALS['dbname']);
        $sql = "SELECT DISTINCT p_type FROM s_store";
        $res= mysqli_query( $con,$sql);
        $data= [];
        $text = "";
        if(mysqli_num_rows($res) > 0){
            $data = mysqli_fetch_all($res);

            $text = "📝موجودی انبار
            ➖➖➖➖➖➖➖➖➖";
            foreach($data as $type){
                $item = $type[0];
                $sql = "SELECT SUM(count) FROM s_store WHERE p_type='$item'";
                $res = mysqli_query($con,$sql);
                $data = mysqli_fetch_all($res);
                $count =(int)$data[0][0];
                $text .="\n

                ایتم : ".$item."

                موجودی : ".$count."
                
                ➖➖➖➖➖➖➖➖
                ";
            }
        }else{
            $text = "شما هنوز ایتمی در انبار ندارید";
        }
        admin_panel($text);
        mysqli_close($con);

    }
    //!======== STORE DATA BY P_TYPE OR MODEL
    function store_data_by_model_p_type(){
        send_message_wk("اطلاعات انبار را بر چه اساس میخواهید؟",$GLOBALS['m_or_p_kb']);
    }
    //!======== WICH P_TYPE DATA USER WANT
    function get_store_data_by_p_type(){
        send_message_wk("جزییات کدام وسیله را میخواهید؟",p_type_kb());
        set_admin_status("store_data 1");
    }
    //!======= SEND TO USER MODELS OF ONE P_TYPE
    function store_data_by_types($p_type){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $sql = "SELECT * FROM s_store WHERE p_type='$p_type'";
        $res= mysqli_query($con,$sql);
        $text = "موجودی مدل های :  ".$p_type."\n";
        if(mysqli_num_rows($res) > 0){
            $data = mysqli_fetch_all($res);
            foreach($data as $model){
                $text.= "
                    مدل : ".$model[2]."

                    تعداد : ".$model[3]."

                    تاریخ ثبت : ".$model[4]."

                    قیمت : ".$model[5]."

                    🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰
                ";
            }
        }else{
            $text = "این ایتم در انبار موجود نیست";
        }
        admin_panel($text);
        mysqli_close($con);
    }


















//!========= > MAIN FUNCTIONS

    //!========== CHECK DATABASE
    function check(){
        $con =  mysqli_connect($GLOBALS['servername'], $GLOBALS['user'], $GLOBALS['pass'],$GLOBALS['dbname']);
        $check = true;
        $sql_admin = "CREATE TABLE IF NOT EXISTS s_admin(id INT PRIMARY KEY  AUTO_INCREMENT,chat_id TEXT COLLATE utf32_unicode_ci,username TEXT COLLATE utf32_unicode_ci,status TEXT COLLATE utf32_unicode_ci,item_id TEXT COLLATE utf32_unicode_ci,text TEXT COLLATE utf32_unicode_ci)";
        $res_admin = mysqli_query($con,$sql_admin);
        $check = $res_admin != false?true:false;

        $sql_anbar = "CREATE TABLE IF NOT EXISTS s_store(id INT PRIMARY KEY  AUTO_INCREMENT,p_type TEXT COLLATE utf32_unicode_ci,model TEXT COLLATE utf32_unicode_ci,count TEXT COLLATE utf32_unicode_ci,date_in TEXT COLLATE utf32_unicode_ci,price TEXT COLLATE utf32_unicode_ci)";
        $res_anbar = mysqli_query($con,$sql_anbar);

        $check = $check != false?false:($res_anbar != false?true:false);

        $sql_repaires = "CREATE TABLE IF NOT EXISTS s_repairs(id INT PRIMARY KEY  AUTO_INCREMENT,username TEXT COLLATE utf32_unicode_ci,phone TEXT COLLATE utf32_unicode_ci,Price TEXT COLLATE utf32_unicode_ci,take_date TEXT COLLATE utf32_unicode_ci,serv_date TEXT COLLATE utf32_unicode_ci,profit TEXT COLLATE utf32_unicode_ci,end TEXT COLLATE utf32_unicode_ci)";
        $res_repaires = mysqli_query($con,$sql_repaires);

        $check = $check != false?false:($res_repaires != false?true:false);
        
        mysqli_close($con);
        
        return $check;
    }
    //!=========== ADD ITEM TO STORE TABLE
    function add_to_store_db($p_type,$model,$price){
        $con =  mysqli_connect($GLOBALS['servername'], $GLOBALS['user'], $GLOBALS['pass'],$GLOBALS['dbname']);
        $date = today();
        $sql = "INSERT INTO s_store(p_type,model,price,date_in,count)VALUES('$p_type','$model','$price','$date','1')";
        $res = mysqli_query($con,$sql);
        mysqli_close($con);
        return $res !== false;
    }
    //!=========== GET TODAY DATE
    function today(){
        $persianCalendar = IntlCalendar::createInstance('Asia/Tehran', 'fa_IR@calendar=persian');
        $persianCalendar->setTime(time() * 1000); // Set the calendar instance to the current timestamp
    
        $year = $persianCalendar->get(IntlCalendar::FIELD_YEAR);
        $month = $persianCalendar->get(IntlCalendar::FIELD_MONTH) + 1; // Add 1 to the month since it is zero-based
        $day = $persianCalendar->get(IntlCalendar::FIELD_DAY_OF_MONTH);
        return $year."-".$month."-".$day;

    }
    //!=========== SEND MESSAGE WITH VERIFY KEYBOARD
    function send_message_v($text){
        $url = $GLOBALS['url']."/sendMessage";
        $kb = json_encode($GLOBALS['verify_kb']);
        $parameters = ['chat_id' => $GLOBALS['chat_id'],'text'=>$text , 'reply_markup' => $kb];
        send_request($url,$parameters);
    }
    //!=========== SEND MESSAGE WITH BACK KEYBOARd
    function send_message_b($text){
        $url = $GLOBALS['url']."/sendMessage";
        $kb = json_encode($GLOBALS['back_kb']);
        $parameters = ['chat_id' => $GLOBALS['chat_id'],'text'=>$text , 'reply_markup' => $kb];
        send_request($url,$parameters);
    }
    //!=========== ADMIN UPDATES
    function send_to_admin($text){
        $url =$GLOBALS['url']."/sendMessage";
        $parameters = ['chat_id'=>'983588626','text'=>$text];
        $res = send_request($url,$parameters);
        return $res;
    }
    //!=========== GET P_TYPE UNIQUE ITEMS
    function p_type_unique(){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $sql = "SELECT DISTINCT p_type FROM s_store";
        $res= mysqli_query( $con,$sql);
        $data= [];
        if(mysqli_num_rows($res) > 0){
            $data = mysqli_fetch_all($res);
            array_push($data,[$GLOBALS['back']]);
        }else{
            $data = [[$GLOBALS['back']]];
        }
        mysqli_close($con);
        return $data;
    }
    //!=========== GET MODELS OF P_TYPE
    function get_p_type_mopdels($p_type){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $sql = "SELECT DISTINCT model FROM s_store WHERE p_type='$p_type'";
        $res =mysqli_query($con,$sql);
        if(mysqli_num_rows($res) > 0){
            $data = mysqli_fetch_all($res);
            array_push($data,[$GLOBALS['back']]);
        }else{
            $data = [[$GLOBALS['back']]];
        }
        return $data;
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