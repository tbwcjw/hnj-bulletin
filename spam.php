<?php
session_start();
class ValidateInput {
    public static function validMagnet($input):bool { 
        $pattern = '/^magnet:\?xt=urn:[a-z0-9]+:[a-z0-9]{32}/i';
        $input = urldecode($input);
        if (preg_match($pattern, $input)) {
            return true;
        } 
        return false;
    }
    public static function validSubject($input):bool {
        if(strlen($input) > SUBJECT_LENGTH['max'] || strlen($input) < SUBJECT_LENGTH['min']) {
            return false;
        }
        return true;
    }
    public static function validContent($input):bool {
        if(strlen($input) > CONTENT_LENGTH['max'] || strlen($input) < CONTENT_LENGTH['min']) {
            return false;
        }
        return true;
    }
    public function validCSRFToken($token):bool|null {
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    public function validCaptcha($input, $form_hash) {
        return password_verify($input, $form_hash);
    }
}

class SpamCannon {
    private $keywords;
    private $threshold;

    public function __construct() {
        $this->keywords = $this->getKeywords(SPAM_KEYWORDS_LIST);
        $this->threshold = SPAM_THRESHOLD;
    }

    private function getKeywords($l): array|null {
        $keywords = file_get_contents($l);
        if(!file_exists($l)) {
            return null;
        }
        return explode(" ", $keywords);
    }
    public function spamCheck($input):bool {
        $input_words = explode(" ", $input);
        $input_length = count($input_words);

        $spam_count = 0;
        foreach($input_words as $word) {
            foreach ($this->keywords as $keyword) {
                if($word == $keyword) {
                    if(strlen($keyword) <= 3) { //ignore keywords <= 3 letters as these are most usually "the, as and but" etc.
                        continue;
                    } else {
                        $spam_count++;
                    }
                    
                }
            }
        }
        (float)$confidence_score = ($spam_count / $input_length)/100.0;
        if($confidence_score >= SPAM_THRESHOLD) {
            hachi_spam_log($confidence_score, $spam_count, $input_length);
            echo $confidence_score;
            exit;
            return true;   //spam detected!
        }
        return false; //Spam threshold not met. Not spam
    }
}
//csrf is in SessionManager
class CaptchaEquation {
    public $equation;
    public $width;
    public $height;
    public $image;
    public $answer;
    
    public function __construct($width = 50, $height = 21) {
        $this->equation = $this->generateEquation();
        $this->width = $width;
        $this->height = $height;
        $this->image = $this->generateImage();
        $this->answer = $this->getAnswer();
    }

    private static function generateEquation() {
        $numbers = "123456789";
        $operators = "+-*";
        $equals = "=";

        $first_number = (int)$numbers[rand(0,strlen($numbers) - 1)];
        $operator = $operators[rand(0,strlen($operators) - 1)];
        $second_number = (int)$numbers[rand(0,strlen($numbers) - 1)];

        switch($operator) {
            case("+"):
                $answer = $first_number + $second_number;
            break;
            case("-"):
                $answer = $first_number - $second_number;
            break;
            case("*"):
                $answer = $first_number * $second_number;
            break;
        }
        $equation = $first_number . $operator . $second_number . $equals;
        $answer = ceil($answer);
        return array('equation'=>$equation, 'answer'=>$answer);
    }

    private function generateImage() {
        $image = @imagecreate($this->width, $this->height)
            or die("Cannot Initialize new GD image stream");
        $background_color = imagecolorallocate($image, 255,255,255);
        $text_color = imagecolorallocate($image, 0, 0, 0);

        //adds noise (optional)
        for ($i = 0; $i < 50; $i++) {
            imagesetpixel($image, rand(0, $this->width), rand(0, $this->height), $text_color);
        }

        imagestring($image, 24, rand(0,$this->width/2), 0, $this->equation['equation'], $text_color);
        
        ob_start();
        imagepng($image, null, 9);
        $idata = ob_get_contents();
        ob_end_clean();
        return "data:image/png;base64,".base64_encode($idata);
    }
    private function getAnswer() {
        $answer_hashed = password_hash($this->equation['answer'], PASSWORD_DEFAULT, ["cost" => CAPTCHA_HASH_COST]);
        $_SESSION['captcha_answer'] = $answer_hashed;
        return $answer_hashed;
    }
}