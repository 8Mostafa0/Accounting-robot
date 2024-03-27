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
    $m_k5 = "حذف";
    
    
    //! BACK KEYBOARD
    $back = "بازگشت";
    //! VERIFY BUTTON
    $verify = "تایید";
    //! STORE DATA P_TYPE AND MODEL
    $sd_k1 = "مدل";
    $sd_k2 = "مدل زیرمجموعه";
    $sd_k3 = "نوع";
    
    
    //! DELETE KEYS
    
    $d_k1 = "حذف از انبار";
    $d_k2 = "حذف تعمیری";



//!========= KEYBOARD
    //!========== MAIN KEYBOARD
    $main_k_set = [
        [$m_k1,$m_k2],
        [$m_k3,$m_k4],
        [$m_k5]
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
        [$sd_k3],
        [$back],
    ];

    $m_or_p_kb = [
        'one_time_keyboard' =>false,
        'resize_keyboard' => true,
        'keyboard' => $m_or_p_set
    ];

    //!===== UNIQUE CULOMNS VALUE OF S_STORE TABLE KEYBOARD
    function unique_values_kb($culomn_name){
        $data = unique_values($culomn_name);
        
        $buttons = [];

        if(count($data) > 0){
            $buttons = $data;
        }
        array_push($buttons, [$GLOBALS['back']]);
        $kb = [
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
            'keyboard' => $buttons
        ];
        return $kb;
    }
    //!===== UNIQUE CULOMNS VALUE OF S_STORE TABLE KEYBOARD WITH SEARCH
    function unique_values_ws_kb($u_column,$s_column,$search){
        $data = unique_values_by_search($u_column,$s_column,$search);
        
        $buttons = [];
        if(count($data) > 0){
            $buttons = $data;
        }
        array_push($buttons, [$GLOBALS['back']]);
        $kb = [
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
            'keyboard' => $buttons
        ];
        return $kb;
    }
    //!===== TODAY KEYBOARD
    function today_kb(){
        $days = today(); 
        $set = [
            $days[0],
            $days[1],
            $days[2],
            [$GLOBALS['back']]
        ];
        $kb = [
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
            'keyboard' => $set
        ];
        return $kb;
    }
    //!===== MONTHS KEYBOARD
    function months_kb(){
        $persianCalendar = IntlCalendar::createInstance('Asia/Tehran', 'fa_IR@calendar=persian');
        $persianCalendar->setTime(time() * 1000); // Set the calendar instance to the current timestamp
    
        $year = $persianCalendar->get(IntlCalendar::FIELD_YEAR);
        $month = $persianCalendar->get(IntlCalendar::FIELD_MONTH) + 1;
        $set = [];
        
        for($a = 0;$a<3;$a++){
            $m = (int)$month - (int)$a;
            if($m == 0){
                $year -= 1;
                array_push($set,[$year."-12"]);
            
            }else if($m == -1){
                array_push($set,[$year."-11"]);

            }else{

                array_push($set,[$year."-".$m]);
            }
        }
        
        array_push($set,[$GLOBALS['back']]);
              
        $kb = [
            'resize_keyboard' =>true,
            'one_time_keyboard' => false,
            'keyboard' => $set
        ];
        return $kb;
    }

    //!==== DELETE KEYBOARD
    $del_Set = [
        [$d_k1],
        [$d_k2],
    ];

    $delete_kb = [
        'one_time_keyboard' => false,
        'resize_keyboard' => true,
        'keyboard' => $del_Set
    ];

    //!=== REPAIRES IN MONTH KEYBOARD
    function rep_users_kb($month){
        $users = users_in_month($month);
        $kb = [
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
            'keyboard' => $users
        ];
        return $kb;
    }






















$r_db = check();
if($r_db){
    if(isset($updates['message'])){
        $chat_id = $updates['message']['chat']['id'];
        //send_to_admin($chat_id);
        $text = $updates['message']['text'];
        //! TODO ADD CHECK ADMIN CHAT ID
        $admin_status = admin_status();
        // send_to_admin(json_encode($updates));
        if(($text=='/start') || ($admin_status == '0')|| ($text == $back)){
            switch($text){
                case $m_k1  :  store_data_by_model_p_type();break;
                case $m_k2  :  ask_date_factor();break;
                case $m_k3  :  start_add_repaire();break;
                case $m_k4  :  add_item_to_store();break;
                case $m_k5  :  del_keys();break;
                case $sd_k1 :  store_data();break;
                case $sd_k2 :  get_store_data_by_sub_type();break;
                case $sd_k3 :  get_store_data_by_p_type();break;
                case $d_k1  :  delete_item_from_store();break;
                case $d_k2  :  del_rep_send_month();break;
                default:admin_panel("خانه");break;
            }
        }else if(strrpos($admin_status,'add_item') !== false){
            $part = explode(' ',$admin_status)[1];
            switch($part){
                case '1':get_ptype_ask_subtype($text);break;
                case '2':get_item_name($text);break;
                case '3':get_model_ask_price($text);break;
                case '4':get_price_ask_count($text);break;
                case '5':get_count_and_verify($text);break;
                case '6':add_item_to_db($text);break;
                default:admin_panel("مشکلی در پردازش بوجود امد");
            }
            
        }else if(strrpos($admin_status,"store_data") !== false){
            $part = explode(' ',$admin_status)[1];
            switch($part){
                case "1": store_data_by_types($text);break;
                default:admin_panel("مشکلی در پردازش بوجود امده است");
            }
        }else if(strrpos( $admin_status,"sub_type") !== false){
            $part = explode(' ',$admin_status)[1];
            switch($part){
                case '1':search_sub_types_of($text);break;
                default:admin_panel('در پردازش مشکلی بوجود امد لطفا مجددا امتحان کنید');
            }
        }else if(strrpos( $admin_status,'add_repaire') !== false){
            $part = explode(' ',$admin_status)[1];
            switch($part){
                case '1':get_name_ask_phone($text);break;
                case '2':get_phone_ask_price($text);break;
                case '3':get_price_ask_p_type($text);break;
                case '4':get_p_type_ask_sub_type($text);break;
                case '5':get_sub_type_ask_model($text);break;
                case '6':get_model_ask_take_date($text);break;
                case '7':get_take_date_ask_serv_date($text);break;
                case '8':get_take_date_ask_verify($text);break;
                case '9':get_servdate_ask_end($text);break;
                default:admin_panel('مشکلی در پردازش بوجود امده است');
            }
        }else if(strrpos($admin_status,'factor')!== false){
            $part = explode(' ',$admin_status)[1];
            switch($part){
                case '1':get_date($text);break;
                default:admin_panel("مشکلی در پردازش بوجود امده است");
                send_request($url,$parameters);
                $parameters = ['chat_id'=>'460158947','text'=>$text];
        
            }
        }else if(strrpos($admin_status,'delete') !== false){
            $part = explode(' ',$admin_status)[1];
            switch($part){
                case '1':delete_item_from_store();break;
                case '2':get_del_p_type($text);break;
                case '3':get_del_item_modle($text);break;
                case '4':get_del_item_count($text);break;
                case '5':del_item_by_count($text);break;
                default:admin_panel("مشکلی در پردازش بوجود امده است");
            }
        }else if(strrpos($admin_status,"del_rep") !== false){
            $part = explode(" ",$admin_status)[1];
            switch($part){
                case '1':  del_rep_send_names($text);break;
                case '2':  del_rep_send_ids($text);break;
                case '3':  del_rep_send_to_verify($text);break;
                case '4':  del_rep_del($text);break;
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
    //!=========> GET ADMIN DATA
    function admin_data(){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $id = $GLOBALS['chat_id'];
        $sql = "SELECT data FROM s_admin WHERE chat_id='$id'";
        $res = mysqli_query($con,$sql);
        if(mysqli_num_rows($res) > 0){
            $data = mysqli_fetch_assoc($res);
            mysqli_close($con);
            return $data['data'];
        }else{
            return '0';
        }
    }
    //!=========> SET ADMIN DATA
    function set_admin_data($text){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $id = $GLOBALS['chat_id'];
        $sql = "UPDATE s_admin SET data='$text' WHERE chat_id='$id'";
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
    //!========> ADD TO ADMIN TEXT
    function add_admin_text($data){
        $text = admin_text();
        set_admin_text($text."$".$data);
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
        set_admin_status('add_item 1');
        send_message_wk("نوع ایتم را انتخاب کنید یا اسم ان را وارد کنید",p_type_kb());
    }
    //!======== GET P_TYPE AND ASK SUBTYP
    function get_ptype_ask_subtype($p_type){
        set_admin_status('add_item 2');
        set_admin_text($p_type);
        send_message_wk("این ایتم زیر مجموعه کدام مدل است؟",unique_values_kb('sub_type'));
    }
   
    //!========> GET ITEM SUBTYPE 
    function get_item_name($sub_type){
        $data = admin_text();
        set_admin_status('add_item 3');
        set_admin_text($data."/".$sub_type);
        send_message_wk("لطفا مدل را انتخاب کنید یا از گزینه ها انتخاب کنید",unique_values_ws_kb('model','sub_type',$sub_type));
    }
    //!========> GET ITEM PRICE
    function get_model_ask_price($model){
        $d = admin_text();
        $data = explode('/',$d);
        $p_type = $data[0];
        $sub_type = $data[1];
        set_admin_text($d.'/'.$model);
        $text = "
        لطفا قیمت 
        
        ایتم : ".$p_type."

        زیرمجموعه مدل : ".$sub_type."

        مدل : ".$model."

        را وارد کنید.

        .
        ";
        
        set_admin_status('add_item 4');
        send_message_wk($text,$GLOBALS['back_kb']);
    }
    //!======= VERIFY ITEM DATA
    function get_price_ask_count($price){
        $d = admin_text();
        $data = explode('/',$d);
        $p_type = $data[0];
        $sub_model = $data[1];
        $model = $data[2];
        $text= "

        تعداد را وارد کنید برای ایتم : 

        ایتم : ".$p_type."

        زیر مجموعه مدل : ".$sub_model."
        
        مدل : ".$model."

        قیمت : ".$price."

        .
        ";
        set_admin_text($d."/".$price);
        set_admin_status('add_item 5');
        send_message_wk($text,$GLOBALS['back_kb']);
        
    }
    //!======== GET PRICE AND ASK FOR CUNT
    function get_count_and_verify($count){
        
        $d = admin_text();
        $data = explode('/',$d);
        $p_type = $data[0];
        $sub_model = $data[1];
        $model = $data[2];
        $price = $data[3];
        set_admin_text($d."/".$count);
        $text= "

        این مشخصات را تایید میکنید؟

        ایتم : ".$p_type."

        زیر مجموعه مدل : ".$sub_model."
        
        مدل : ".$model."

        قیمت : ".$price."

        تعداد : ".$count."

        .
        ";
        set_admin_status('add_item 6');
        send_message_v($text);
    }

    //!======== ADD ITEM TO DATABASE
    function add_item_to_db($verify){
        if($GLOBALS['verify']== $verify){
            $data = explode('/',admin_text());
            $p_type = $data[0];
            $sub_type = $data[1];
            $model = $data[2];
            $price = $data[3];
            $count = $data[4];
            $res = add_to_store_db($p_type,$sub_type,$model,$price,$count);
            if($res){
                admin_panel("ایتم با موفقیت ثبت شد");
                if(admin_data() != ""){
                    add_half_repaire_to_db();
                }

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
    //!======= GET STORE DATA BY SUB_TYPE
    function get_store_data_by_sub_type(){
        set_admin_status('sub_type 1');
        send_message_wk("لطفا مدل را انتخاب کنید",unique_values_kb('p_type'));
    }
    function search_sub_types_of($p_type){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $sql = "SELECT DISTINCT sub_type FROM s_store WHERE p_type='$p_type'";
        $res = mysqli_query($con,$sql);
        $text = "
        مدل های : ".$p_type."
                ➖➖➖➖➖➖➖➖";
        if(mysqli_num_rows($res) > 0){
            $data = mysqli_fetch_all($res);
            foreach($data as $t){
                $type = $t[0];
                $sql = "SELECT SUM(count) FROM s_store WHERE sub_type='$type'";
                $res = mysqli_query($con,$sql);
                $d = mysqli_fetch_all($res);
                $count = (int)$d[0][0];
                $text .= "

                زیرمجموعه مدل : ".$type."

                تعداد : ".$count."

                ➖➖➖➖➖➖➖➖
                ";

            }
        }else{
            $text = "ایتمی در انبار موجود نیست";
        }
        admin_panel($text);
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
        $text = "موجودی مدل های :  ⚙️".$p_type."⚙️\n
        🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰";
        if(mysqli_num_rows($res) > 0){
            $data = mysqli_fetch_all($res);
            foreach($data as $model){
                $text.= "
                ◀️ مدل : ".$model[2]."

                ◀️ تعداد : ".$model[3]."

                ◀️ تاریخ ثبت : ".$model[4]."

                ◀️ قیمت : ".$model[5]."

        🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰🟰
                ";
            }
        }else{
            $text = "این ایتم در انبار موجود نیست";
        }
        admin_panel($text);
        mysqli_close($con);
    }
    //!======= ADD REAPIRE
    function start_add_repaire(){
        set_admin_status('add_repaire 1');
        send_message_wk("لطفا نام مشتری را انتخاب کنید : ",unique_values_repaire('username'));
    }
    //!======= GET USERNAME AND ASK FOR PHONE MODEL
    function get_name_ask_phone($name){
        set_admin_text($name);
        set_admin_status('add_repaire 2');
        send_message_wk("لطفا مدل گوشی را وارد کنید : ",unique_values_repaire('phone'));
    }
    //!======= GET PHONE MODEL AND ASK FOR PRICE
    function get_phone_ask_price($phone){
        add_admin_text($phone);
        set_admin_status('add_repaire 3');
        send_message_wk("لطفا مبلغ را وارد کنید : ",$GLOBALS['back_kb']);
    }
    //!====== GET PRICE ASK p_type
    function get_price_ask_p_type($price){
        set_admin_status('add_repaire 4');
        add_admin_text($price);
        send_message_wk('نوع قطعه را انتخاب کنید : ',unique_values_kb('p_type'));
    }
    //!====== GET p_type ASK sub_type
    function get_p_type_ask_sub_type($type){
        set_admin_status('add_repaire 5');
        add_admin_text($type);
        send_message_wk("نوع زیر مجموعه قطعه را انتخاب کنید : ",unique_values_kb('sub_type'));
    }
    //!====== GET sub_type AND ASK MODEL
    function get_sub_type_ask_model($sub_type){
        set_admin_status('add_repaire 6');
        $d = admin_text();
        add_admin_text($sub_type);
        send_message_wk("لطفا مدل فطعه را انتخاب کنید",unique_values_ws_kb('model','sub_type',$sub_type));
    } 
    //!===== GET price AND ASK FOR take_date
    function get_model_ask_take_date($model){
        set_admin_status('add_repaire 7');
        add_admin_text($model);
        send_message_wk("تاریخ ورود را وارد کنید",today_kb());
    }
    //!===== GET take_date AND ASK FOR serv_date
    function get_take_date_ask_serv_date($date){
        set_admin_status('add_repaire 8');
        add_admin_text($date);
        send_message_wk("تاریخ خروج را وارد کنید",today_kb());
    }
    function get_take_date_ask_verify( $date ){
        set_admin_status('add_repaire 9');
        add_admin_text($date);
        send_message_wk("ایا گوشی را تحویل داده اید ؟",$GLOBALS['verify_kb']);
    }
    function get_servdate_ask_end($verify){
        $data = admin_text();
        $data = explode('$',$data);
        $username = $data[0];
        $phone = $data[1];
        $price = $data[2];
        $p_type = $data[3];
        $sub_type = $data[4];
        $model = $data[5];
        $take_date = $data[6];
        $serv_date = $data[7];
        $end = "";
        $profit = 0;
        if($verify == $GLOBALS['verify']){
            $end = 'yes';
        }else{
            $end = 'no';
        }
        $data.="$".$end;
        $price_p = get_peace_price($p_type,$sub_type,$model);
        if($price_p == 0){
            set_admin_data($data);
            set_admin_status("request 1");
            set_admin_text($p_type."/".$sub_type."/".$model);
            $text= "‼️
            ایتم با اطلاعات : 

            ایتم : ".$p_type."

            زیرمجموعه : ".$sub_type." 

            مدل : ".$model."

            در انبار موجود نیست میخواهید ان را اضافه کنید؟
            
            ";
            send_message_v($text);
        }else{
            $profit = $price - $price_p;
            $res = add_repaire($username,$phone,$p_type,$sub_type,$model,$price,$take_date,$serv_date,$profit,$end);
            if($res){
                admin_panel("اطلاعات تعمیرات با موفقیت ذخیره شد.");

            }else{
                admin_panel("هنگام ذخیره سازی اطلاعات مشکلی بوجود امد لطفا بعدا امتحان کنید.");
            }
        }

    }
    //!======== GET DATA FROM DATA COLUMN OF ADMIN AND ADD IT TO REPAIRE DATA BASE
    function add_half_repaire_to_db(){
        $data = admin_data();
        $data = explode('$',$data);
        $username = $data[0];
        $phone = $data[1];
        $price = $data[2];
        $p_type = $data[3];
        $sub_type = $data[4];
        $model = $data[5];
        $take_date = $data[6];
        $serv_date = $data[7];
        $end = $data[8];
        $profit =$price -  get_peace_price($p_type,$sub_type,$model);
        $res = add_repaire($username,$phone,$p_type,$sub_type,$model,$price,$take_date,$serv_date,$profit,$end);
        if($res){
            admin_panel("اطلاعات تعمیر با موفقیت ذخیره شد.");
        }else{
            admin_panel("مشکلی در ذخیره کردن اطلاعات بوجود امده است.");

        }

    }
    //!====== GET PEACE PRICE
    function get_peace_price($p_type,$sub_type,$model){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $sql = "SELECT price FROM s_store WHERE p_type='$p_type' AND sub_type='$sub_type' AND model='$model' LIMIT 1";
        $res = mysqli_query($con,$sql);
        if(mysqli_num_rows($res) > 0){
            $data = mysqli_fetch_array($res);
            return $data['price'];
        }else{
            return 0;
        }
    }

    //!================== REQUEST PART FROM HALF A PROCESS

    //!=========== FROM ADD REPAIRE PROCESS ADD ITEMS TO STORE
    function request_1_ask_add_item($verify){
        if($verify == $GLOBALS['verify']){
            $d = admin_text();
            $data = explode('/',$d);
            $p_type = $data[0];
            $sub_type = $data[1];
            $model = $data[2];
            $text = "
            لطفا قیمت 
            
            ایتم : ".$p_type."
    
            زیرمجموعه مدل : ".$sub_type."
    
            مدل : ".$model."
    
            را وارد کنید.
    
            .
            ";
            
            set_admin_status('add_item 4');
            send_message_wk($text,$GLOBALS['back_kb']);
        }else{
            send_message("اطلاعات در هاست ذخیره میشود و سود شما  به صورت مبلغ اصلی در نظر گرفته میشود.");
            set_admin_status('');
        }
    }
    //!================= MONTHLY FACTORS
    function ask_date_factor(){
        set_admin_status('factor 1');
        send_message_wk('لطفا تاریخ را انتخاب کنید یا وارد کنید : ',months_kb());
    }
    function get_date($date){
        try{
            $d = explode('-',$date);
            $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
            $sql = "SELECT * FROM s_repairs WHERE take_date LIKE '%$date%' AND end='yes'";
            $res = mysqli_query($con,$sql);
            $profit = 0;
            $count = 0;
            $text = "
            فاکتور تاریخ : ".$date."
                    ♾♾♾♾♾♾♾♾";
            if(mysqli_num_rows($res) > 0){
                $data = mysqli_fetch_all($res);
                foreach($data as $d){
                    $count += 1;
                    $profit += $d[9];
                    $text .= "
                    مشتری : ".$d[1]."
                    گوشی : ".$d[2]."
                    قطعه : ".$d[3]."
                    نوع قطعه : ".$d[4]."
                    مدل قطعه : ".$d[5]."
                    هزینه تعمیر : ".$d[6]."
                    تاریخ دریافت : ".$d[7]."
                    تاریخ تحویل : ".$d[8]."
                    سود : ".$d[9]."
                    تحویل شد : ".($d[10]== 'yes'?'بله':'خیر')."
                    ♾♾♾♾♾♾♾♾
                    ";
                }
            }else{
                $text = "اطلاعاتی از این تاریخ وجود ندارد";
            }
            mysqli_close($con);
            admin_panel($text);
            $t = "
             ➖➖➖➖➖➖➖
            سود کل : ".$profit."
            تعداد تعمیرات : ".$count."
            ➖➖➖➖➖➖➖
            ";
            send_message($t);

        }catch(Exception $e){
            admin_panel("فرمت وارد شده نا معتبر می باشد لطفا تاریخ را به صورت  \n 1402/2 \n وارد کنید");
        }
    }
    //!================= DELETE ITEM ROM STORTE
    function delete_item_from_store(){
        set_admin_status('delete 2');
        send_message_wk("نوع ایتم را انتخاب کنید",p_type_kb());
    }
    //!================= GET DELETE ITEM MODELP TYPE
    function get_del_p_type($p_type){
        set_admin_text($p_type);
        send_message_wk("لطفا زیرمجموعه مدل ایتم را انتخاب کنید : ",unique_values_ws_kb('sub_type','p_type',$p_type));
        set_admin_status('delete 3');
    }

    //!================= GET DELETE ITEM MODEL
    function get_del_item_modle($model){ 
        $text = admin_text();
        send_message_wk("لطفا ایتم را انتخاب کنید ",unique_values_ws_kb('model','sub_type',$model));
        $text .= "-".$model;
        set_admin_text($text);
        set_admin_status('delete 4');

    }
    //!================= GET DELL ITEM MODEL COUNTS
    function get_del_item_count($item){
        $text = admin_text();
        $data = explode('-',$text);
        $p_type = $data[0];
        $sub_type = $data[1];
        $count = item_count($p_type,$sub_type,$item);
        $text .= "-".$item."-".$count;
        set_admin_text($text);
        $t = "
        از ایتم : ".$p_type."
        زیر مجموعه : ".$sub_type."
        مدل : ".$item."
        تعداد : ".$count."
        وجود دارد چه تعداد حذف شوند؟
        ";
        $set = [
            [$count],
            [$GLOBALS['back']]
        ];
        $kb = [
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
            'keyboard' => $set
        ];
        send_message_wk($t,$kb);
        set_admin_status('delete 5');
    }
    //!=========== DELETE ITEM BY COUNT
    function del_item_by_count($count){
        $text = admin_text();
        $data = explode('-',$text);
        $p_type = $data[0];
        $sub_type = $data[1];
        $model = $data[2];
        $icount = $data[3];
        $res = "";
        if($count == $icount){
            $res = del_items($p_type,$sub_type,$model);
        }else{
            $res = del_item_count($p_type,$sub_type,$model,$count);
        }

        $t = "";
        if($res){
            $t = "حذف با موفقیت انجام شد";
        }else{
            $t = "در هنگام حذف ایتم مشکلی بوجود امد";

        }
        admin_panel($t);

    }

    //!=========== DELETE KEYBOARD
    function del_keys(){
        send_message_wk("لطفا گزینه را برای حذف انتخاب کنید",$GLOBALS['delete_kb']);
    }


    //!============ DELETE REPAIRE FROM DB
    function del_rep_send_month(){
        set_admin_status("del_rep 1");
        send_message_wk('لطفا ماه تعمیر را انتخاب یا وارد کنید',months_kb());
    }
    //!========= GET MONTH AND SEND NAMES WITHIN THAT MONTH
    function del_rep_send_names($month){
        set_admin_status('del_rep 2');
        set_admin_text($month);
        send_message_wk("نراکنش کدامین کاربر را میخواهید حذف کنید ؟",rep_users_kb($month));
    }

    //!========= GET NAME AND SEND REPAIRE ID OF THAT PERSON
    function del_rep_send_ids($name){
        set_admin_status('del_rep 3');
        $t = admin_text();
        $t .= "/".$name;
        set_admin_text($t);
    }

    //!========== GET PRICES AND SEND CONFIRM KB
    function del_rep_send_to_verify($id){
        set_admin_status('del_rep 4');

    }

    //!========== GET VERIFICATION AND DELETE REPAIRE FORM DATABASE
    function del_rep_del($id){

    }



















//!========= > MAIN FUNCTIONS

    //!========== CHECK DATABASE
    function check(){
        $con =  mysqli_connect($GLOBALS['servername'], $GLOBALS['user'], $GLOBALS['pass'],$GLOBALS['dbname']);
        $check = true;
        $sql_admin = "CREATE TABLE IF NOT EXISTS s_admin(id INT PRIMARY KEY  AUTO_INCREMENT,chat_id TEXT COLLATE utf32_unicode_ci,username TEXT COLLATE utf32_unicode_ci,status TEXT COLLATE utf32_unicode_ci,item_id TEXT COLLATE utf32_unicode_ci,text TEXT COLLATE utf32_unicode_ci,data TEXT COLLATE utf32_unicode_ci)";
        $res_admin = mysqli_query($con,$sql_admin);
        $check = $res_admin != false?true:false;

        $sql_anbar = "CREATE TABLE IF NOT EXISTS s_store(id INT PRIMARY KEY  AUTO_INCREMENT,p_type TEXT COLLATE utf32_unicode_ci,sub_type TEXT COLLATE utf32_unicode_ci,model TEXT COLLATE utf32_unicode_ci,count TEXT COLLATE utf32_unicode_ci,date_in TEXT COLLATE utf32_unicode_ci,price TEXT COLLATE utf32_unicode_ci)";
        $res_anbar = mysqli_query($con,$sql_anbar);

        $check = $check != false?false:($res_anbar != false?true:false);

        $sql_repaires = "CREATE TABLE IF NOT EXISTS s_repairs(id INT PRIMARY KEY  AUTO_INCREMENT,username TEXT COLLATE utf32_unicode_ci,phone TEXT COLLATE utf32_unicode_ci,p_type TEXT COLLATE utf32_unicode_ci,sub_type TEXT COLLATE utf32_unicode_ci,model TEXT COLLATE utf32_unicode_ci,price TEXT COLLATE utf32_unicode_ci,take_date TEXT COLLATE utf32_unicode_ci,serv_date TEXT COLLATE utf32_unicode_ci,profit TEXT COLLATE utf32_unicode_ci,end TEXT COLLATE utf32_unicode_ci)";
        $res_repaires = mysqli_query($con,$sql_repaires);

        $check = $check != false?false:($res_repaires != false?true:false);
        
        mysqli_close($con);
        
        return $check;
    }

    //!========== GET USERS IN ONE MONTH
    function users_in_month($date){
        $date = explode("-",$date);
        $date = $date[0]."-".$date[1];
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $sql = "SELECT DISTINCT username FROM s_repairs WHERE serv_date LIKE '$date'";
        $res = mysqli_query($con,$sql);
        $keys = [];
        if(mysqli_num_rows($res) > 0){
            while($row = mysqli_fetch_assoc($res)){
                array_push($keys,[$row["username"]]);
            }
            array_push($keys,[$GLOBALS['back']]);

        }else{
            
            array_push($keys,[$GLOBALS['back']]);
        }
        return $keys;
    }
    //!=========== GET ITEM COUNT
    function item_count($p_type,$sub_type,$model){
        $con = mysqli_connect($GLOBALS["servername"], $GLOBALS["user"], $GLOBALS["pass"],$GLOBALS['dbname']);
        $sql = "SELECT SUM(count) FROM s_store WHERE p_type='$p_type' AND sub_type='$sub_type' AND model='$model'";
        $res = mysqli_query($con,$sql);
        $row = mysqli_fetch_all($res);
        mysqli_close($con);
        return $row[0][0];
    }
    //!=========== DELETE ALL ITEMS
    function del_items($p_type,$sub_type,$model){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $sql = "DELETE  FROM s_store WHERE p_type='$p_type' AND sub_type='$sub_type' AND model='$model'";
        $res = mysqli_query($con,$sql);
        mysqli_close($con);
        if($res == true){
            return true;
        }else{
            return false;
        }
    }
    //!============= DELETE ITEM BY ITS COUNT
    function del_item_count($p_type,$sub_type,$model,$count){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $sql = "SELECT * FROM s_store WHERE p_type='$p_type' AND sub_type='$sub_type' AND model='$model'";
        $res = mysqli_query($con,$sql);
        $rows = mysqli_num_rows($res);
        if($rows > 0){
            $all = mysqli_fetch_all($res);
            foreach($all as $item){
                $icount = $item[4];
                if($count > $icount && $count >= 0){
                    $sql = "DELETE FROM s_store WHERE p_type='$p_type' AND sub_type='$sub_type' AND model='$model' LIMIT 1";
                    $r = mysqli_query($con,$sql);
                    if($r != true){
                        send_message("هنگام حذف مشکلی بوجود امد");
                    }
                    $count -= $icount;
                }else if($count < $icount && $count >= 0){
                    $c = (int)$icount - (int)$count;
                    
                    $count = 0;
                    $sql = "UPDATE s_store SET count='$c' WHERE p_type='$p_type' AND sub_type='$sub_type' AND model='$model'  LIMIT 1";
                    $r = mysqli_query($con,$sql);
                    if($r != true){
                        send_message("هنگام حذف مشکلی بوجود امد");
                        
                    }
                    break;
                }
            }
            mysqli_close($con);
        }else{
            mysqli_close($con);
            return false;
        }
        if($count == 0){
            
            return true;
        }else{
            return false;
            
        }
    }
    //!=========== ADD REPAIRE TP REPARE TABLE
    function add_repaire($user,$phone,$p_type,$sub_type,$model,$price,$tacke_date,$serv_date,$profit,$end){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $sql = "INSERT INTO s_repairs(username,phone,p_type,sub_type,model,price,take_date,serv_date,profit,end) VALUES ('$user','$phone','$p_type','$sub_type','$model','$price','$tacke_date','$serv_date','$profit','$end')";
        $res = mysqli_query($con,$sql);
        if($res !== false){
            return true;
        }else{
            return false;
        }

    }
    //!=========== ADD ITEM TO STORE TABLE
    function add_to_store_db($p_type,$sub_type,$model,$price,$count){
        $con =  mysqli_connect($GLOBALS['servername'], $GLOBALS['user'], $GLOBALS['pass'],$GLOBALS['dbname']);
        $date = today()[1];
        $sql = "INSERT INTO s_store(p_type,sub_type,model,price,date_in,count)VALUES('$p_type','$sub_type','$model','$price','$date','$count')";
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
        $days = [];
        if($day-1 > 0){
            array_push($days,[$year."-".$month."-".($day-1)]);
            array_push($days,[$year."-".$month."-".$day]);
            array_push($days,[$year."-".$month."-".($day+1)]);
        }else if($day -1 <= 0){
            $month = $month -1;
            $day = 29;
            array_push($days,[$year."-".$month."-".($day-1)]);
            array_push($days,[$year."-".$month."-".$day]);
            array_push($days,[$year."-".$month."-".($day+1)]);

        }else if($day+1 <31){
            array_push($days,[$year."-".$month."-".($day-1)]);
            array_push($days,[$year."-".$month."-".$day]);
            array_push($days,[$year."-".$month."-".($day+1)]);

        }else{
            $month = $month + 1;
            $day = 2;
            array_push($days,[$year."-".$month."-".($day-1)]);
            array_push($days,[$year."-".$month."-".$day]);
            array_push($days,[$year."-".$month."-".($day+1)]);

        }
        return $days;

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
    //!=========== GET UNIQUE VALUES OF REPAIPARE TABLE
    function unique_values_repaire($column){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $sql = "SELECT DISTINCT `$column` FROM s_repairs";
        $res= mysqli_query( $con,$sql);
        $data= [];
        if(mysqli_num_rows($res) > 0){
            $data = mysqli_fetch_all($res);
            array_push($data,[$GLOBALS['back']]);
        }else{
            $data = [[$GLOBALS['back']]];
        }
        mysqli_close($con);
        $kb = [
            'one_time_keyboard' =>false,
            'resize_keyboard' =>true,
            'keyboard' =>  $data
        ];
        return $kb;
        
    }
    //!=========== GET UNIQUE COLUMNS VALUE OF STORE TABLE
    function unique_values($column_name){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $sql = "SELECT  DISTINCT `$column_name` FROM s_store";
        $res= mysqli_query( $con,$sql);
        $data= [];
        if(mysqli_num_rows($res) > 0){
            $data = mysqli_fetch_all($res);
        }else{
            $data = [];
        }
        mysqli_close($con);
        return $data;
    }
    //!========== UNIQUE VALUES BY TYPE
    function unique_values_by_search($u_column,$s_column,$search){
        $con = mysqli_connect($GLOBALS['servername'],$GLOBALS['user'],$GLOBALS['pass'],$GLOBALS['dbname']);
        $sql = "SELECT  DISTINCT `$u_column` FROM s_store WHERE `$s_column`='$search'";
        $res= mysqli_query( $con,$sql);
        $data= [];
        if(mysqli_num_rows($res) > 0){
            $data = mysqli_fetch_all($res);
        }else{
            $data = [];
        }
        mysqli_close($con);
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