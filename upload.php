<?php
include("config.php");
include("spam.php");
include("api.php");
class UploadRouter {
    public function __construct() {
        $b = filter_input(INPUT_GET, 'b', FILTER_SANITIZE_STRING); //b is for board
        $p = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING); //p is for post/thread
        $this->handleRequest($b,$p);
    }

    public function handleRequest($b,$p) {
        if(!empty($p) && !empty($b)) {
            $upload = new Upload();
            $upload->newReply($b, $p);
        }
        elseif(empty($p) && empty($b)) {
            $upload = new Upload();
            $upload->newThread($b);
        }
        else {
            die("Could not handle this request");
        }
    }
}
class Upload {
    public static function generateID(): string {
        $bytes = random_bytes(ID_LENGTH);
        $base64 = base64_encode($bytes);
        // Remove non-alphanumeric characters and the padding
        $randomString = substr(str_replace(['+', '/', '='], '', $base64), 0, ID_LENGTH);
        return $randomString;
    }
    public function newThread() {
        global $sql;

        $query = "INSERT INTO threads (id, board, poster, subject, content, magnet) VALUES(?,?,?,?,?,?)";
        $b = filter_var($b, FILTER_SANITIZE_STRING);
        $poster = filter_var($_POST['poster'], FILTER_SANITIZE_STRING);
        $board = filter_var($_POST['board'], FILTER_SANITIZE_STRING);
        $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
        $content = filter_var($_POST['content'], FILTER_SANITIZE_STRING);
        $magnet = filter_var($_POST['magnet'], FILTER_SANITIZE_URL);
        $bot = filter_var($_POST['bot'], FILTER_SANITIZE_STRING);
        $agree = filter_var($_POST['agreement'], FILTER_SANITIZE_STRING);
        
        $captcha_input = filter_var($_POST['captcha'], FILTER_SANITIZE_NUMBER_INT);
        $captcha_answer = filter_var($_POST['captcha_answer'], FILTER_SANITIZE_STRING);
        $csrf = $_POST['csrf_token'];
        
        if($agree !== 'on') {
            die("You have to agree to the TOS");
            
        }
        if(!empty($bot)) {
            die("Botter detected");
        }
        //todo validate the validators and check that the spamcheck actally works

        $spam = new SpamCannon();
        if($spam->spamCheck($content) || $spam->spamCheck($subject)) {
            die("Spam detected."); 
        }
        $valid = new ValidateInput();
        if(!$valid->validCaptcha($captcha_input, $captcha_answer)) {
            die("Captcha incorrect");
        }
        if(!Board::exists($board)) {
            die("Board not found");
        }
        if(!$valid->validCSRFToken($csrf)) {
            die("CSRF invalid");
        }
        if(!$valid->validSubject($subject) || empty($subject)) {
            die("Improper Subject Length");
        }
        if(!$valid->validContent($content) || empty($subject)) {
            die("Improper content Length");
        }
        if(!$valid->validMagnet($magnet) || empty($magnet)) {
            die("Bad magnet link.");
        }
        
        //sql automatically assigns datetime

        if(!($stmt = $sql->prepare($query))) {
            return null;
        }
        $id = $this->generateID();
        if(!$stmt->bind_param("ssssss", $id, $board, $poster, $subject, $content, $magnet)) {
            $stmt->close();
            hachi_api_usage("newThread","bind_param failed", $this->id);
            return null;
        }

        if(!$stmt->execute()) {
            $stmt->close();
            hachi_api_usage("newThread","stmt execution failed", $this->id);
            return null;
        }
        header("location:index.php?b=$board&p=$id"); //Do something different here. This is sort of dumb
        return;
    }

    public function newReply($b, $p) {
        global $sql;

        $query = "INSERT INTO replies (id, board, parent_id, poster, content) VALUES (?,?,?,?,?)";
        
        $b = filter_var($b, FILTER_SANITIZE_STRING);
        $p = filter_var($p, FILTER_SANITIZE_STRING);
        $poster = filter_var($_POST['poster'], FILTER_SANITIZE_STRING);
        $content = filter_var($_POST['content'], FILTER_SANITIZE_STRING);
        $magnet = filter_var($_POST['magnet'], FILTER_SANITIZE_URL);
        $bot = filter_var($_POST['bot'], FILTER_SANITIZE_STRING);
        $agree = filter_var($_POST['agreement'], FILTER_SANITIZE_STRING);
       
        $captcha_input = filter_var($_POST['captcha'], FILTER_SANITIZE_NUMBER_INT);
        $captcha_answer = filter_var($_POST['captcha_answer'], FILTER_SANITIZE_STRING);
        $csrf = $_POST['csrf_token'];
        
        if($agree !== 'on') {
            die("You have to agree to the TOS");
            
        }
        if(!empty($bot)) {
            die("Botter detected");
        }
        if(!Board::exists($b)) {
            die("Board not found");
        }
        if(!Thread::exists($p)) {
            die("Thread not found");
        }
        $spam = new SpamCannon();
        if($spam->spamCheck($content)) {
            die("Spam detected."); 
        }
        $valid = new ValidateInput();
        if(!$valid->validCaptcha($captcha_input, $captcha_answer)) {
            die("Captcha incorrect");
        }
        if(!$valid->validCSRFToken($csrf)) {
            die("CSRF invalid");
        }
        if(!$valid->validContent($content)) {
            die("Improper content Length");
        }

        if(!($stmt = $sql->prepare($query))) {
            return null;
        }
        $id = $p."_".$this->generateID();

        if(!$stmt->bind_param("sssss", $id, $b, $p, $poster, $content)) {
            $stmt->close();
            hachi_api_usage("newReply","bind_param failed", $this->id);
            return null;
        }

        if(!$stmt->execute()) {
            $stmt->close();
            hachi_api_usage("newReply","stmt execution failed", $this->id);
            return null;
        }
        header("location:index.php?b=$b&p=$p");
    }
}

$router = new UploadRouter();