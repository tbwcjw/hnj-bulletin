<?php
include("config.php");
include(ROOT_DIR . "api.php");
include(ROOT_DIR . "pageinator.php");
include(ROOT_DIR . "spam.php");



class SessionManager {
    public function __construct() {
        @session_set_cookie_params([
            'lifetime' => 0, 
            'path' => '/',
            'domain' => HOSTNAME,
            'secure' => false,  
            'httponly' => true,
            'samesite' => 'Strict', 
        ]);
        session_start();

        $this->instantiateSessionDefaults();
        $this->checkBan();
        $this->generateCSRFToken();
        
    }

    public function instantiateSessionDefaults() {
        if (!isset($_SESSION['ip'])) {
            $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
        }
        if(!isset($_SESSION['theme'])) {
            $_SESSION['theme'] = 'default';
        }
        if(!isset($_SESSION['dtf'])) {
            $_SESSION['dtf'] = 'us';
        }
    }
    public function checkBan() {
        $isBanned = User::checkBan($_SESSION['ip'], session_id());
        if ($isBanned) {
            Router::banned(); //We just barrel through here...
            exit;
        }
    }
    private function generateCSRFToken() {
        if(!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }   
    }
    public function getCSRFToken() {
        return $_SESSION['csrf_token'];
    }
}

class Router {
    public function __construct() {
        $b = filter_input(INPUT_GET, 'b', FILTER_SANITIZE_STRING); //b is for board
        $p = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING); //p is for post/thread
        $q = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING); //q is for search query
        $i = filter_input(INPUT_GET, 'i', FILTER_SANITIZE_STRING); //i is for static page
            $t = filter_input(INPUT_GET, 'theme', FILTER_SANITIZE_STRING); //t for theme
            $d = filter_input(INPUT_GET, 'dtf', FILTER_SANITIZE_STRING); //dtf for datetime
            $this->handleRequest($b, $p, $q, $i, $t, $d);
    }
   
    public function handleRequest($b, $p, $q, $i, $t, $dtf) {
        
        if(!(empty($t))) {
            $_SESSION['theme'] = $t;
        }
        if(!(empty($dtf))) {
            $_SESSION['dtf'] = $dtf;
        }
        if(!(empty($i))) {
            switch($i){
                case('n'):
                    $this->newThread();
                    break;
                case('r'):
                    $this->rules();
                    break;
                case('s'):
                    $this->settings();
                    break;
                case('h'):
                    $this->support();
                    break;
            }
        }
        if (!empty($q)) {
            $this->searchResults($q);
        } elseif (!empty($b) && !empty($p)) {
            $this->thread($b, $p);
        } elseif (empty($b) || strlen($b) < 1) {
            $this->home();
        } else {
            $this->board($b);
        }
        exit;
    }

    public static function support() {
        $page = new Pageinator();
        $page->theme = THEMES[$_SESSION['theme']];
        $page->renderPage('support');
        exit;
    }
    public static function banned() {
        $thread = new Pageinator();
        $thread->theme = THEMES[$_SESSION['theme']];
        $thread->renderPage('banned');
        exit;
    }

    public function settings() {
        $page = new Pageinator();
        $page->renderPage('settings', true);
        exit;
    }

    public function newThread() {
        $page = new Pageinator();
        $page->theme = THEMES[$_SESSION['theme']];
        $captcha = new CaptchaEquation();
        $page->captcha = $captcha->image;
        
        $allBoards = Board::getAllBoards();
        $page->boards = $allBoards;
        $page->renderPage('newThread', true);
        exit;
    }
    public function rules() {
        $rules = Rules::getRules();
        if($rules == null) {
            header("location:index.php");
        }
        $page = new Pageinator();
        $page->theme = THEMES[$_SESSION['theme']];
        $page->rules = $rules;
        $page->renderPage('rules');
    }
    
    public function thread($b, $p) {
        $singleBoard = Board::getSingleBoard($b);
        if ($singleBoard == null) {
            header("location:index.php");
        }
        $thread = new Pageinator();
        $thread->theme = THEMES[$_SESSION['theme']];
        $thread->boards = $singleBoard;
        $post = Thread::getSinglePost($b, $p);
        if ($post == null) {
            header("location:index.php");
        }
        $thread->posts = $post;
        $replies = Reply::getReplies($b, $p, REPLIES_PER_PAGE);
        if ($replies == null) {
            $replies = Reply::getErrorReply($b, $p);
        }
        $thread->replies = $replies;

        $captcha = new CaptchaEquation();
        $thread->captcha = $captcha->image;

        $thread->renderPage('thread', true);
    }

    public function searchResults($q, $b = null) {
        if ($b) {
            $results = Search::searchInBoard($b, $q, SEARCH_RESULTS_PER_PAGE);
        } else {
            $results = Search::searchWholeSite($q, SEARCH_RESULTS_PER_PAGE);
        }
        if ($results == null) {
            $results = Search::getErrorSearch();
        }
        $search = new Pageinator();
        $search->theme = THEMES[$_SESSION['theme']];
        $search->results = $results;
        $search->renderPage('search');
    }

    public function board($b) {
        $singleBoard = Board::getSingleBoard($b);
        if ($singleBoard == null) {
            header("location:index.php");
        }
        $board = new Pageinator();
        $board->theme = THEMES[$_SESSION['theme']];
        $board->boards = $singleBoard;
        $posts = Thread::getMostRecent($b, THREAD_MOST_RECENT);
        if ($posts == null) {
            $posts = Thread::getErrorThread($b);
        }
        $board->posts = $posts;
        $board->renderPage('board');
    }

    public function home() {
        $allBoards = Board::getAllBoards();
        $home = new Pageinator();
        $home->lang = $_SESSION['lang'];
        $home->boards = $allBoards;
        $home->renderPage('home');
    }
}

$sessionManager = new SessionManager();
$router = new Router();
