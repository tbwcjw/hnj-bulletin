<?php
require_once("config.php");


class Board {
    public $link;
    public $title;
    public $description;
    /**
     * Board constructor.
     *
     * @param string $link        The link to the board.
     * @param string $title       The title of the board.
     * @param string $description The description of the board.
     */
    public function __construct($link, $title, $description) {
        $this->link = $link;
        $this->title = $title;
        $this->description = $description;
    }   

    public function getLink() {
        return $this->link;
    }
    public function getTitle() {
        return $this->title;
    }
    public function getDescription() {
        return $this->description;
    }

    public static function exists($id):bool {
        global $sql;
        $query = "SELECT * FROM boards WHERE link=?";

        if(!($stmt = $sql->prepare($query))) {
            return false;
        }
        if(!$stmt->bind_param("s", $id)) {
            $stmt->close();
            hachi_api_usage("Board::exists","bind_param failed", $id);
            return false;
        }
        if(!$stmt->execute()) {
            $stmt->close();
            hachi_api_usage("Board::exists","stmt execution failed", $id);
            return false;
        }

        $result = $stmt->get_result();
        if($result->num_rows < 1) {
            $stmt->close();
            $result->free_result();
            return false;
        }
        $stmt->close();
        $result->free_result();
        return true;
    }
    public static function getSingleBoard($id): Board|null {
        $object = [];
        global $sql;

        $query = "SELECT * FROM boards WHERE link=?";

        if(!($stmt = $sql->prepare($query))) {
            return null;
        }

        if(!$stmt->bind_param("s", $id)) {
            $stmt->close();
            hachi_api_usage("getSingleBoard","bind_param failed", $id);
            return null;
        }

        if(!$stmt->execute()) {
            $stmt->close();
            hachi_api_usage("getSingleBoard","stmt execution failed", $id);
            return null;
        }

        $result = $stmt->get_result();
        if($result->num_rows < 1) {
            $stmt->close();
            hachi_api_usage("getSingleBoard","0 results for ", $id);
            return null;
        }
        $row = $result->fetch_assoc();
        $stmt->close();
        $result->free_result();
        hachi_api_usage("getSingleBoard", "success", $id);
        return new Board($row['link'], $row['title'], $row['description']);
    }

    public static function getAllBoards(): array|bool {
        $objects = [];
        global $sql;

        $query = "SELECT * FROM boards"; 
        $result = $sql->query($query);
        $count = $result->num_rows;
        while($row = $result->fetch_assoc()) {
            $objects[] = new Board($row['link'], $row['title'], $row['description']);
        }
        hachi_api_usage("getAllBoards", "success", "$count rows");
        $sql->close();
        $result->free_result();
        return $objects;
    }
}
class Thread {
    public $id;
    public $board;
    public $poster;
    public $subject;
    public $content;
    public $magnet;
    public $datetime;
    /**
     * Thread constructor.
     *
     * @param int    $id       The unique identifier of the thread.
     * @param string $board    The board where the thread belongs.
     * @param string $poster   The poster of the thread.
     * @param string $subject  The subject of the thread.
     * @param string $content  The content of the thread.
     * @param string $magnet   The magnet link associated with the thread.
     * @param string $datetime The datetime when the thread was created.
     */
    public function __construct($id, $board, $poster, $subject, $content, $magnet, $datetime) {
        $this->id = $id;
        $this->board = $board;
        $this->poster = $poster;
        $this->subject = $subject;
        $this->content = $content;
        $this->magnet = $magnet;
        $this->datetime = $datetime;
    }
    public function getID() {
        return $this->id;
    }
    public function getBoard() {
        return $this->board;
    }
    public function getPoster() {
        return $this->poster;
    }
    public function getSubject() {
        return $this->subject;
    }
    public function getContent() {
        return $this->content;
    }
    public function getMagnet() {
        return $this->magnet;
    }
    public function getDatetime() {
        return $this->datetime;
    }

    public static function exists($id):bool {
        global $sql;
        $query = "SELECT * FROM threads WHERE id=?";

        if(!($stmt = $sql->prepare($query))) {
            return false;
        }
        if(!$stmt->bind_param("s", $id)) {
            $stmt->close();
            hachi_api_usage("Thread::exists","bind_param failed", $id);
            return false;
        }
        if(!$stmt->execute()) {
            $stmt->close();
            hachi_api_usage("Thread::exists","stmt execution failed", $id);
            return false;
        }

        $result = $stmt->get_result();
        if($result->num_rows < 1) {
            $stmt->close();
            $result->free_result();
            return false;
        }
        $stmt->close();
        $result->free_result();
        return true;
    }
    
    public static function getErrorThread($b): array {
        $error = new Thread(EMPTY_BOARD['id'], $b, EMPTY_BOARD['poster'], EMPTY_BOARD['subject'], EMPTY_BOARD['content'], EMPTY_BOARD['magnet'], EMPTY_BOARD['datetime']);
        return array($error);
    }
    public static function getSinglePost($board, $post): Thread|null {
        global $sql;

        $query = "SELECT * FROM threads WHERE board=? AND id=?";

        if(!($stmt = $sql->prepare($query))) {
            return null;
        }
        if(!$stmt->bind_param("ss", $board, $post)) {
            $stmt->close();
            hachi_api_usage("getSinglePost","bind_param failed", $post);
            return null;
        }
        if(!$stmt->execute()) {
            $stmt->close();
            hachi_api_usage("getSinglePost","stmt execution failed", $post);
            return null;
        }

        $result = $stmt->get_result();
        if($result->num_rows < 1) {
            $stmt->close();
            hachi_api_usage("getSinglePost","0 results for ", $post);
            return null;
        }
        $row = $result->fetch_assoc();
        $stmt->close();
        $result->free_result();
        return new Thread($row['id'], $row['board'], $row['poster'], $row['subject'], $row['content'], $row['magnet'], $row['datetime']);
    }

    

    public static function getMostRecent($board, $amount):array|null {
        $objects = [];
        global $sql;

        $query = "SELECT * FROM threads WHERE board=? LIMIT ?";

        if(!($stmt = $sql->prepare($query))) {
            return null;
        }
        if(!$stmt->bind_param("si", $board, $amount)) {
            $stmt->close();
            hachi_api_usage("getMostRecent","bind_param failed", $board);
            return null;
        }
        if(!$stmt->execute()) {
            $stmt->close();
            hachi_api_usage("getMostRecent","stmt execution failed", $board);
            return null;
        }
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        if($num_rows < 1) {
            $stmt->close();
            hachi_api_usage("getMostRecent","0 results for ", $board);
            return null;
        }

        while($row = $result->fetch_assoc()) {
            $objects[] = new Thread($row['id'], $row['board'], $row['poster'], $row['subject'], $row['content'], $row['content'], $row['magnet'], $row['datetime']);
        }
        hachi_api_usage("getMostRecent", "success", "$num_rows rows");
        $sql->close();
        $result->free_result();
        return $objects;
    }

}
class Reply {
    public $id;
    public $board;
    public $parent_id;
    public $poster;
    public $content;
    public $datetime;

    /**
     * Reply constructor.
     *
     * @param int    $id       The unique identifier of the reply.
     * @param string $board    The board where the reply belongs.
     * @param int    $parent_id The identifier of the parent thread.
     * @param string $poster   The poster of the reply.
     * @param string $content  The content of the reply.
     * @param string $datetime The datetime when the reply was created.
     */
    public function __construct($id, $board, $parent_id, $poster, $content, $datetime) {
        $this->id = $id;
        $this->board = $board;
        $this->parent_id = $parent_id;
        $this->poster = $poster;
        $this->content = $content;
        $this->datetime = $datetime;
    }
    public function getID() {
        return $this->id;
    }
    public function getBoard() {
        return $this->board;
    }
    public function getParentID() {
        return $this->parent_id;
    }
    public function getPoster() {
        return $this->poster;
    }
    public function getContent() {
        return $this->content;
    }
    public function getDatetime() {
        return $this->datetime;
    }

    public static function getErrorReply($b, $p) {
        $error = new Reply(EMPTY_REPLIES['id'],$b,$p, EMPTY_REPLIES['poster'],EMPTY_REPLIES['content'],EMPTY_REPLIES['datetime']);
        return array($error);
    }

    public static function getReplies($board, $post, $limit):array|null {
        $objects = [];
        global $sql;

        $query = "SELECT * FROM replies WHERE board=? AND parent_id=? LIMIT ?";

        if(!($stmt = $sql->prepare($query))) {
            return null;
        }
        if(!$stmt->bind_param("ssi", $board, $post, $limit)) {
            $stmt->close();
            hachi_api_usage("getReplies","bind_param failed", $post);
            return null;
        }
        if(!$stmt->execute()) {
            $stmt->close();
            hachi_api_usage("getReplies","stmt execution failed", $post);
            return null;
        }
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        if($num_rows < 1) {
            $stmt->close();
            hachi_api_usage("getReplies","0 results for ", $post);
            return null;
        }

        while($row = $result->fetch_assoc()) {
            $objects[] = new Reply($row['id'], $row['board'], $row['parent_id'], $row['poster'], $row['content'], $row['datetime']);
        }
        hachi_api_usage("getReplies", "success", "$num_rows rows");
        $sql->close();
        $result->free_result();
        return $objects;

    }
}
class Search {
    public $id;
    public $board;
    public $poster;
    public $subject;
    public $content;
    public $datetime;

    public $confidence;

     /**
     * Search constructor.
     *
     * @param int    $id        The unique identifier of the search result.
     * @param string $board     The board where the search result was found.
     * @param string $poster    The poster of the search result.
     * @param string $subject   The subject of the search result.
     * @param string $content   The content of the search result.
     * @param string $datetime  The datetime when the search result was created.
     * @param int    $confidence The confidence score of the search result.
     */
    public function __construct($id, $board, $poster, $subject, $content, $datetime, $confidence) {
        $this->id = $id;
        $this->board = $board;
        $this->poster = $poster;
        $this->subject = $subject;
        $this->content = $content;
        $this->datetime = $datetime;
        $this->confidence = $confidence;

    }
    public function getID() {
        return $this->id;
    }
    public function getBoard() {
        return $this->board;
    }
    public function getPoster() {
        return $this->poster;
    }
    public function getSubject() {
        return $this->subject;
    }
    public function getContent() {
        return $this->content;
    }
    public function getDatetime() {
        return $this->datetime;
    }
    public function getConfidence() {
        return $this->confidence;
    }
    
    public static function getErrorSearch() {
        $error = new Search(EMPTY_SEARCH_RESULT['id'],EMPTY_SEARCH_RESULT['board'],EMPTY_SEARCH_RESULT['poster'],EMPTY_SEARCH_RESULT['subject'],EMPTY_SEARCH_RESULT['content'],EMPTY_SEARCH_RESULT['datetime'],EMPTY_SEARCH_RESULT['confidence']);
        return array($error);
    }
    public static function searchInBoard($b, $q, $limit): array|null {
        $objects = [];
        global $sql;
        $st = microtime(true);
        $b = filter_var($b, FILTER_SANITIZE_STRING);
        $parts = explode(" ", (filter_var((string)$q, FILTER_SANITIZE_STRING)));
        $query = "SELECT * FROM threads WHERE board='$b' AND (";
        foreach($parts as $part) {
            $query .= "subject LIKE '%$part%' OR content LIKE '%$part%' OR subject='$part' OR content='$part' OR ";
        }
        $query = rtrim($query, "OR ");
        $query .= (") LIMIT $limit");

        $result= $sql->query($query);
        if(!$result) {
            return null;
        }
        $num_rows = $result->num_rows;
        while($row = $result->fetch_assoc()) {
            $subject = $row['subject'];
            $content = $row['content'];
            $confidence = 0;
            foreach($parts as $part) {
                $termCount = substr_count(strtolower($content), strtolower($part));
                $confidence += $termCount;
                $termCount = substr_count(strtolower($subject), strtolower($part));
                $confidence += $termCount;
            }
            
            $objects[] = new Search($row['id'], $row['board'], $row['poster'], $row['subject'], $row['content'], $row['datetime'], $confidence);
        }
        usort($objects, function ($a, $b) {
            return $b->getConfidence() - $a->getConfidence();
        });
        
        
        return $objects;
    }
    public static function searchWholeSite($q, $limit) {
        $objects = [];
        global $sql;
        
        $parts = explode(" ", (filter_var((string)$q, FILTER_SANITIZE_STRING)));
        $query = "SELECT * FROM threads WHERE (";
        foreach($parts as $part) {
            $query .= "subject LIKE '%$part%' OR content LIKE '%$part%' OR subject='$part' OR content='$part' OR ";
        }
        $query = rtrim($query, "OR "); //Strip trailing 'OR'
        $query .= (") LIMIT $limit");

        $result= $sql->query($query);
        if(!$result) {
            return null;
        }
        $num_rows = $result->num_rows;
        while($row = $result->fetch_assoc()) {
            $subject = $row['subject'];
            $content = $row['content'];
            $confidence = 0;
            foreach($parts as $part) {
                $termCount = substr_count(strtolower($content), strtolower($part));
                $confidence += $termCount;
                $termCount = substr_count(strtolower($subject), strtolower($part));
                $confidence += $termCount;
            }
            
            $objects[] = new Search($row['id'], $row['board'], $row['poster'], $row['subject'], $row['content'], $row['datetime'], $confidence);
        }
        usort($objects, function ($a, $b) {
            return $b->getConfidence() - $a->getConfidence();
        });
        return $objects;
    }
}
class User {
    public $session_id;
    public $ip;
    /**
     * User constructor.
     *
     * @param string $session_id The user's session identifier.
     * @param string $ip         The user's IP address.
     */
    public function __construct($session_id, $ip) {
        $this->session_id = $session_id;
        $this->ip = $ip;
    }

    function getSessionID() {
        return $this->session_id;
    }
    function getIP() {
        return $this->ip;
    }

    public static function checkBan($ip, $sid): bool {
        global $sql;

        $query = "SELECT num FROM banned WHERE ip=? OR session_id=? LIMIT 1";

        if(!($stmt = $sql->prepare($query))) {
            return false;
        }

        if(!$stmt->bind_param("ss", $ip, $sid)) {
            $stmt->close();
            hachi_api_usage("checkBan","bind_param failed", $ip);
            return false;
        }

        if(!$stmt->execute()) {
            $stmt->close();
            hachi_api_usage("checkBan","stmt execution failed", $ip);
            return false;
        }

        $result = $stmt->get_result();
        if($result->num_rows < 1) {
            $stmt->close();
            hachi_api_usage("checkBan","0 results for ", $ip);
            return false;
        }
        $row = $result->fetch_assoc();
        $stmt->close();
        $result->free_result();
        hachi_api_usage("checkBan", "banned user. checked success", $ip);
        return true;

    } 
}
class Rules {
    public $id;
    public $board;
    public $title;
    public $content;

     /**
     * Rules constructor.
     *
     * @param int    $id       The unique identifier of the rules.
     * @param string $board    The board to which the rules apply.
     * @param string $title    The title of the rules.
     * @param string $content  The content of the rules.
     */
    public function __construct($id, $board, $title, $content) {
        $this->id = $id;
        $this->board = $board;
        $this->title = $title;
        $this->content = $content;
    }

    public function getID() {
        return $this->id;
    }
    public function getBoard() {
        return $this->board;
    }
    public function getTitle() {
        return $this->title;
    }
    public function getContent() {
        return $this->content;
    }

    public static function getRules() {
        $objects = [];
        global $sql;

        $query = "SELECT * FROM rules ORDER BY board ASC";
        $result = $sql->query($query);
        $count = $result->num_rows;
        while($row = $result->fetch_assoc()) {
            $objects[] = new Rules($row['num'], $row['board'], $row['title'], $row['content']);
        }
        hachi_api_usage("getAllRules", "success", "$count rows");
        $sql->close();
        $result->free_result();
        return $objects;
    }
}