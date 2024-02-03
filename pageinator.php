<?php
class Pageinator {
    protected $template_dir = "templates/";
    protected $cache_dir = "cache/";
    protected $vars = array();

    public $theme;
    public $themes;
    public $dt_format;
    public $dt_formats;

    public function __construct($template_dir = null, $cache_dir = null) {
        if ($template_dir !== null) {
            $this->template_dir = $template_dir;
        }
        if ($cache_dir !== null) {
            $this->cache_dir = $cache_dir;
        }

        // Initialize theme and date-time format based on global variables (assuming session variables are set)
        $this->theme = THEMES[$_SESSION['theme']] ?? 'default';
        $this->dt_format = locale_datetimes[$_SESSION['dtf']] ?? 'Y-m-d H:i:s';

        // Define theme and date-time format options based on global variables
        $this->themes = THEMES;
        $this->dt_formats = locale_datetimes;
        $this->remCache();
    }

    public function renderPage($page, $nocache = false) {       //nocache = true for pages with captchas
        $templateFile = $this->template_dir . $page . ".php";
        $headerFile = $this->template_dir . "header.php";
        $this->pageTitle = $page;
        if (file_exists($templateFile) && file_exists($headerFile)) {
            if(CACHE_ENABLED && !$nocache) {
                $fname = md5($templateFile.serialize($_GET).$this->lang.$this->dt_format);
                $cached = $this->getCache($fname);
                if($cached !== false) {
                include $headerFile;
                include $cached;
            } else {
                include $headerFile;
                $this->setCache($headerFile, $templateFile);
            }
            } else {
                include $headerFile;
                include $templateFile;
            }
        } else {
            echo "Template file not found.";
        }
    }

    public function minify_data($data) {
        $search = array(
            '/\>[^\S ]+/s',  // Remove whitespaces after tags
            '/[^\S ]+\</s',  // Remove whitespaces before tags
            '/(\s)+/s'       // Remove multiple consecutive spaces
        );
    
        $replace = array(
            '>',
            '<',
            '\\1'
        );
    
        $minified_html = preg_replace($search, $replace, $data);
    
        return $minified_html;
    }

    public function remCache() //FUTURE: we can remove this and make a cronjob 1/hour instead. For testing purposes only (too expensive)
    {
        $size = 0;

        foreach (glob(rtrim(CACHE_DIR, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->remCache($each);
        }
        if($size>CACHE_DIR_SIZE_LIMIT) {
            foreach (glob(rtrim(CACHE_DIR, '/').'/*', GLOB_NOSORT) as $each) {
                unlink($each);
            }
        }

    }

    public function setCache($headerFile, $templateFile) {
        $date = date(locale_datetimes['us'], time());
        $expires = date(locale_datetimes['us'], time()+CACHE_TTL);
        ob_start();
        echo "<!-- CACHED @ $date -->";
        echo "<!-- Expiry @ $expires -->";
        include $templateFile;
        $cached = $this->minify_data(ob_get_contents());
        $fname = md5($templateFile.serialize($_GET).$this->lang.$this->dt_format);
        file_put_contents(CACHE_DIR . "/$fname.phtml", $cached);
        return $cached;
    }   
    public function getCache($fname) {
        $include = CACHE_DIR . "/" .$fname . ".phtml";
        if(!file_exists($include)) {
            return false;
        }
        if(filemtime($include) > time() + CACHE_TTL) {
            return false;
        }
        return $include;
    }

    public function __set($key, $value) {
        $this->vars[$key] = $value;
    }

    public function __get($key) {
        return $this->vars[$key] ?? null;
    }
}