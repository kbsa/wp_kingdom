<?php

namespace UMC\service;

use UMC\sql\FrontSql;
use UMC\model\Count;
use UMC\service\OptionService;

class VerifyFrontService {

    public $frontSql;
    public $optionService;

    public function __construct() {
        $this->frontSql = new FrontSql();
        $this->optionService = new OptionService();
    }

    public function countHtmlShortCodes() {
        $option = $this->optionService->get();
        $countContent = $this->frontSql->countShortCodeContent($option);
        $countExcerpt = $this->frontSql->countShortCodeExcerpt($option);
        $count = new Count();
        $count->size = max($countContent, $countExcerpt);
        return $count;
    }

    public function getHtmlShortCodes($page = 0, $size = 20) {
        $option = $this->optionService->get();
        $idsWithContent = $this->frontSql->getShortCodeContent($page, $size, $option);
        $idsWithExcerpt = $this->frontSql->getShortCodeExcerpt($page, $size, $option);
        $htmls = array();
        $htmls = $this->convertIdToHtmlShortCodes($idsWithContent, $htmls);
        $htmls = $this->convertIdToHtmlShortCodes($idsWithExcerpt, $htmls, 'excerpt');
        return $htmls;
    }

    private function convertIdToHtmlShortCodes(array $ids, array $htlms, $row = 'content') {
        if (empty($ids) || count($ids) < 1) {
            return $htlms;
        }

        //https://codex.wordpress.org/Function_Reference/get_shortcode_regex
        $pattern = get_shortcode_regex();
        $special = array("\n", "\t");
        foreach ($ids as $id) {
            $post = get_post($id);

            switch ($row) {
                case 'excerpt':
                    $text = $post->post_excerpt;

                    break;
                case 'content':
                default:
                    $text = $post->post_content;
                    break;
            }

            if (preg_match_all('/' . $pattern . '/s', $text, $matches) && array_key_exists(2, $matches)
            ) {
                unset($post);

                $htlm = '';

                foreach ($matches[0] as $shortCode) {
                    $htlm .= do_shortcode($shortCode);
                }

                if (!empty($htlm)) {
                    $htlm = str_replace($special, "", $htlm);
                    $htlms[] = $htlm;
                }
            }
        }
        
        return $htlms;
    }

}
