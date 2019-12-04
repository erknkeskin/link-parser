<?php

require 'simple_html_dom/simple_html_dom.php';

class LinkParser
{

    private $template;
    private $html;

    function __construct($template)
    {
        $this->template = $template;
    }

    /**
     * debug for array or object test
     */

    function debug($p, $exit = true)
    {
        echo "<pre>";
        print_r($p);
        echo "</pre>";

        if ($exit) exit;
    }

    /**
     * url check
     */

    function url_exists($url = NULL)
    {
        if ($url == NULL) return false;

        $headers = @get_headers($url);

        if ($headers && strpos($headers[0], '200')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * parse document content url to change links
     */

    function parse($check_domain = false)
    {

        // open file
        $content = file_get_contents($this->template);
        $this->html = $content;

        // select all url
        $re = '/((https?:\/\/)?([-\\w]+\\.[-\\w\\.]+)+\\w(:\\d+)?(\/([-\\w\/_\\.]*(\\?\\S+)?)?)*)/';

        preg_match_all($re, $this->html, $matches, PREG_SET_ORDER, 0);

        $allUrls = sizeof($matches) > 0 ? $matches : null;

        if ($allUrls !== null) {

            // if exists a tag delete in all urls
            $templateContent = file_get_html($this->template);

            $linkTags = $templateContent->find('a');

            foreach ($linkTags as $elem) {
                foreach ($allUrls as $key => $url) {
                    if (isset($url[0])) {
                        if ($url[0] == $elem->href) {
                            unset($allUrls[$key]);
                        }
                    }
                }
            }

            sort($allUrls);


            // check domain
            if ($check_domain === true) {
                for ($i = 0; $i < sizeof($allUrls); $i++) {

                    if (!$this->url_exists($allUrls[$i])) {
                        unset($allUrls[$i]);
                    }
                }
                sort($allUrls);
            }

            // new array for all urls
            $newUrls = [];
            for ($y = 0; $y < sizeof($allUrls); $y++) {
                $newUrls[] = $allUrls[$y][0];
            }

            //all urls ready for replace
            $newLinks = [];
            for ($x = 0; $x < sizeof($allUrls); $x++) {
                $newLinks[] = '<a href="' . $newUrls[$x] . '">' . $newUrls[$x] . '</a>';
            }

            // all arrays unique
            $newUrls = array_unique($newUrls, SORT_REGULAR);
            $newLinks = array_unique($newLinks, SORT_REGULAR);

            sort($newUrls);
            sort($newLinks);

            /*$this->debug($newUrls, false);
            $this->debug($newLinks, false);*/

            // change url to links change in document
            $this->html = str_replace($newUrls, $newLinks, $this->html);
            echo $this->html;
        }
    }
}
