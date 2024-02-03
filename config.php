<?php
declare(strict_types=1);
// 蜂 の 磁石                                                        //
// Hachi no Jishaku                                                 //tbwcjw
// 蜂: Hachi: Bee                                                   //2023-2024
// 磁石: Jishaku: Magnet (maybe... idk how to speak japan)          //~^_^~

//Personalization
define("SITE_TITLE",                                                //Your site title
"Hachi no Jishaku");
define("SITE_DESC_64",                                              //Your site short description (64 characters EXACT)
"Hachi no Jishaku is a text based bulletin board");
define("SITE_DESC_255",                                             //Your site long description (255 characters EXACT) 
"Hachi no Jishaku: Text based bulletin boards");
define("SITE_KEYWORDS",                                             //SEO Keywords
"forum,board,bbs");


define("REPLIES_PER_PAGE", 25);                                     //How many replies to show per page?
define("THREAD_MOST_RECENT", 25);                                   //How many threads to show on the board frontpage?
define("SEARCH_RESULTS_PER_PAGE", 25);
define("TRUNCATE_RESULT_CONTENT", 200);                              //Truncate search result "preview" content and 
                                                                    //board homepage "preview" content to this length
define("READONLY_BOARDS", array("hnj"));                            //Which boards are reserved for internal posting only?

//Error Messages
define("EMPTY_BOARD", array("id" => 404,
                            "poster" => "ErrorBot",
                            "subject" => "Nothing here!",
                            "content" => "Nothing to see here yet... Maybe create a post to start the conversation?",
                            "magnet" => "",
                            "datetime" => "The start of the universe"));
define("EMPTY_REPLIES", array("id" => 404,
                              "poster" => "ConversationBot",
                              "content" => "Nobody has replied to this post yet... Start the conversation!",
                              "datetime" => "The end, and the beginning of time"));
define("EMPTY_SEARCH_RESULT", array("id" => null,
                                    "board" => 404,
                                    "poster" => "SearchBot",
                                    "subject" => "Nothing here!",
                                    "content" => "We couldn't find any results for your search terms. Perhaps refactor your query and try again?",
                                    "datetime" => "Right now, forever ago",
                                    "confidence" => 100));
//Database and Connections
define("SQL_USERNAME", "username");
define("SQL_PASSWORD", "password");
define("SQL_HOST", "localhost");
define("SQL_DB", "yourdbname");
define("SQL_DB_TABLE_BOARDS", "boards");
define("HOSTNAME", gethostname());

//Flood Prevention
define("SERVER_LOAD_PROT", true);                                   //Enable or disable server load protection
define("SERVER_SPIKE_LIMIT", "5.00");                               //If the server avgs this in one minute it will 503
define("SERVER_5MIN_LIMIT", "3.50");                                //If the server avgs this in 5 minutes it will 503
define("SERVER_15MIN_LIMIT", "3.00");                               //If the server avgs this in 15 minutes it will 503


extract($_POST,EXTR_SKIP);                                          //Don't change this unless you know what you're doing
extract($_GET,EXTR_SKIP);                                           //Or this
extract($_COOKIE,EXTR_SKIP);                                        //Or this

define("ROOT_DIR", $_SERVER['DOCUMENT_ROOT'] . "/");                //Don't change this unless you know what you're doing
define("LOG_DIR", "log");

define("ASSETS_DIR", "../assets" . "/");                           //Static site assets such as CSS, Icons and Banners 

define("FONT_DIR", "assets/fonts" . "/");
define("ICON_DIR", ASSETS_DIR . "icons" . "");
define("CSS_DIR", ASSETS_DIR . "css" . "/");                        //CSS Elements
define("MAIN_STYLE", CSS_DIR . "haj.css");                          //Main defines actual site element styling

//Client Customization Options
define("THEMES", array("default" => CSS_DIR . "default.css",
                       "night" => CSS_DIR . "night.css",
                       "orange" => CSS_DIR . "orange.css"));
define("locale_datetimes", array("us" => 'M-d-Y H:i:s A',           
                                  "uk" => 'D-m-Y H:i:s',
                                  "iso6801" => 'Y-m-d\TH:i:sP',
                                  "rfc2822" => 'r',
                                  "human" => 'F j, Y, g:i a'));

define("CACHE_ENABLED", true);                          
define("CACHE_TTL", 600);
define("CACHE_DIR", ROOT_DIR . "cache/");
define("CACHE_DIR_SIZE_LIMIT", 1024000000);                         //in Bytes. > Limit will empty the directory.

//Logging Options
define("ERROR_LOG", ROOT_DIR . LOG_DIR . '/php.log');               //Ensure these files have the correct permissions
define("EXCEPTION_LOG", ROOT_DIR . LOG_DIR .'/exception.log');      //Don't rely on the chmod's below...
define("FLOOD_LOG", ROOT_DIR . LOG_DIR . '/flood.log');             //
define("API_USAGE_LOG", ROOT_DIR . LOG_DIR . '/api_usage.log');     //
define("SPAM_LOG", ROOT_DIR . LOG_DIR . '/spam.log');               //

//chmod (ROOT_DIR, 0755);                                             //0755 for testing purposes only. NOT for prod!
//chmod(ROOT_DIR . LOG_DIR, 0755);                                    //
//chmod(CACHE_DIR, 0755);                                             //

define("DEBUG_MODE", false);                                        //Not for prod. Displays errors in browser
define("WRITE_API_LOG", true);                                      //Override Debug Mode to keep logging API calls

ini_set('log_errors', 1);                                           //Turn off only if you're flying dark
ini_set('error_log', ERROR_LOG);                                    //Don't change this unless you know what you're doing

//SpamIonCannon
define("CAPTCHA_PNG_QUALITY", 9);                                   //0 (least compression) to 9 (most compression)
define("CAPTCHA_HASH_COST", 4);                                     //4 (least expensive) 31 (most expensive)
define("SPAM_KEYWORDS_LIST", ROOT_DIR . "data/keywords.list");      //Location of keywords.list
define("SPAM_THRESHOLD", 0.5);                                      //0.0 to 1.0 Keywords Matched / input length

//Post Guidelines
define("SUBJECT_LENGTH", array("min" => 3,
                               "max" => 128));
define("CONTENT_LENGTH", array("min" => 8,
                               "max" => 11510));    
define("ID_LENGTH", 4);                                             //The more amount of characters, the less the chance
                                                                    //of collisions. WIP. Higher entropy algo coming soon. 
                                                                    //Default: 4 (a-z<case-insensitive 0-9) 36^4 = 1,679,616                              
//Internal Localization
mb_internal_encoding("8bit");                                       //
date_default_timezone_set('UTC');                                   //UTC is the standard


if(DEBUG_MODE) {
    error_reporting(6135);                                          //Default is 6135 (All errors PHP collects together)
    ini_set('display_errors', 1);       
}


function hachi_exception(Throwable $exception) {                    

    $dtnow = date(locale_datetimes['us']) . ' (' . date_default_timezone_get() . ')';
    $error = "$dtnow >> Uncaught Exception: " . $exception->getMessage() . ". In file " .$exception->getFile(). ". Line:" . $exception->getLine() . "<< EOL";
    if(!file_exists("error_log")) {
        file_put_contents(EXCEPTION_LOG, "", FILE_APPEND);
    }
    file_put_contents(EXCEPTION_LOG, $error . "\n", FILE_APPEND);
    if(DEBUG_MODE) {
        echo "<div class='exception'>".$error . "</div>";
    }
}
function hachi_error($errno, $errstr, $errfile, $errline) {
    $dtnow = date(locale_datetimes['us']) . ' (' . date_default_timezone_get() . ')';
    $error = "$dtnow >> Error ($errno) '$errstr' on $errfile. Line: $errline << EOL";
        if(!file_exists(ERROR_LOG)) {
            file_put_contents(ERROR_LOG, "", FILE_APPEND);
        }
    
    file_put_contents(ERROR_LOG, $error . "\n", FILE_APPEND);
    if(DEBUG_MODE) {
        echo "<div class='error'>".$error."</div>";
    }
}
function hachi_flood($load) {
    $dtnow = date(locale_datetimes['us']) . ' (' . date_default_timezone_get() . ')';
    $m1 = $load[0]; $m5 = $load[1]; $m15 = $load[2];
    $error = "$dtnow >> Server Load Over Limit: 1 Minute: $m1 5 Minutes: $m5 15 Minutes: $m15 << EOL";
    if(!file_exists(FLOOD_LOG)) {
        file_put_contents(FLOOD_LOG, "", FILE_APPEND);
    }
    
    file_put_contents(FLOOD_LOG, $error . "\n", FILE_APPEND);
    if(DEBUG_MODE) {
        echo "<div class='flood'>".$error."</flood>";
    }
    header('HTTP/1.1 503 Too busy, try again later');
    die('Server too busy. Please try again later.');
}

function hachi_spam_log($confidence, $spam_count, $input_length) {
    $dtnow = date(locale_datetimes['us']) . ' (' . date_default_timezone_get() . ')';
    $error = "$dtnow >> Spam Blocked: ($confidence/1.0 confidence) $spam_count spam keywords detected in $input_length word string << EOL";
    if(!file_exists(SPAM_LOG)) {
        file_put_contents(SPAM_LOG, "", FILE_APPEND);
    }
    file_put_contents(SPAM_LOG, $error. '\n', FILE_APPEND);
}
function hachi_api_usage($function, $message, $elements) {
    if(!DEBUG_MODE && !WRITE_API_LOG) {
        return;
    }
    $dtnow = date(locale_datetimes['us']) . ' (' . date_default_timezone_get() . ')';
    $line = "$dtnow >> API CALL: $function '$message' with $elements << EOL";
    if(!file_exists(API_USAGE_LOG)) {
        file_put_contents(API_USAGE_LOG, "", FILE_APPEND);
    }
    if(DEBUG_MODE) {
        echo "<div class='api'>".$line."</div>";
    }
    
    file_put_contents(API_USAGE_LOG, $line . "\n", FILE_APPEND);
}

set_error_handler('hachi_error', 6135);                             //You can change this if you have an external error/
set_exception_handler('hachi_exception');                           //Exception handler. May break stuff.

if(SERVER_LOAD_PROT) {
    $server_load = sys_getloadavg();
    if($server_load != NULL) {
        if($server_load[0] > (float)SERVER_SPIKE_LIMIT ||
        $server_load[1] > (float)SERVER_5MIN_LIMIT  ||
        $server_load[2] > (float)SERVER_15MIN_LIMIT) {
            hachi_flood($server_load);
        }
    }
}

try {
    global $sql;
    $sql = mysqli_connect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD, SQL_DB);
} catch(Exception $e) { die("Database is down for maintenance or is unavailable. Please try again later."); }

//orphan functions
//i honestly didnt know where else to stick this
function truncate($string,$length=TRUNCATE_RESULT_CONTENT,$append="&hellip;") {
    $string = substr($string,0,$length).$append;
    return $string;
  }