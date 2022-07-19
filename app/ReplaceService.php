<?php
namespace App;

class ReplaceService {

    public $classicEditor = false;
    private $replaceRegex = [
        "twitter" => [
            "classic" => [
                "regex" => '/<script async src="https:\/\/platform.twitter.com\/widgets.js" charset="utf-8"><\/script>/s',
                "container" => 'span',
            ],
            "blocks" => [
                "regex" => '/<figure class="wp-block-embed is-type-rich is-provider-twitter(.*?)<\/figure>/s',
                "container" => 'div',
            ], 
        ],
        "youtube" => [
            "classic" => [
                "regex" => '/<iframe (.*?) src="https:\/\/www.youtube.com\/embed\/(.*?)<\/iframe>/s',
                "container" => 'span',
            ],
            "blocks" => [
                "regex" => '/<iframe (.*?) src="https:\/\/www.youtube.com\/embed\/(.*?)<\/iframe>/s',
                "container" => 'div',
            ], 
        ],
        "instagram" => [
            "classic" => [
                "regex" => '/<script (.*?) src="(.*?)instagram.com\/embed.js"(.*?)><\/script>/s',
                "container" => 'span',
            ],
            "blocks" => [
                "regex" => '/<script(.*?)src="(.*?)instagram.com\/embed.js"(.*?)><\/script>/s',
                "container" => 'div',
            ], 
        ],
    ];

    function __construct()
    {
        $this->classicEditor = $this->isClassicEditorPluginActive();
    }

    /**
     * Check if Classic Editor plugin is active.
     *
     * @return bool
    */

    function isClassicEditorPluginActive() {
        if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
            return true;
        }

        return false;
    }

    function str_replace_first($search, $replace, $subject) {
        $pos = strpos($subject, $search);
        if ($pos !== false) {
            return substr_replace($subject, $replace, $pos, strlen($search));
        }
        return $subject;
    }

    public function twitter ($content){
        $match = false;
        $replacedContent = $content;
        $regex = $this->classicEditor ? $this->replaceRegex['twitter']['classic']["regex"] : $this->replaceRegex['twitter']['blocks']["regex"];
        $container = $this->classicEditor ? $this->replaceRegex['twitter']['classic']["container"] : $this->replaceRegex['twitter']['blocks']["container"];
            preg_match_all( $regex , $content, $match);
            
            foreach($match[0] as $key => $matchContent){
                $code = addslashes( wp_json_encode( [ 'embed' => htmlentities( $matchContent ) ] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                $randomId =  uniqid();
                $replacedContent = $this->str_replace_first($match[0][$key], '<'.$container.' id="'.$randomId.'"></'.$container.'><script>
                window._iframeLazyLoad = window._iframeLazyLoad ||  {}
                window._iframeLazyLoad["'.$randomId.'"] = `'.$code.'`;
            </script>', $replacedContent);
            }
        return $replacedContent;
    }   

    public function youtube ($content){
        $match = false;
        $replacedContent = $content;
        $regex = $this->classicEditor ? $this->replaceRegex['youtube']['classic']["regex"] : $this->replaceRegex['youtube']['blocks']["regex"];
        $container = $this->classicEditor ? $this->replaceRegex['youtube']['classic']["container"] : $this->replaceRegex['youtube']['blocks']["container"];
            preg_match_all( $regex , $content, $match);
            foreach($match[0] as $key => $matchContent){
                $code = addslashes( wp_json_encode( [ 'embed' => htmlentities( $matchContent ) ] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                $randomId =  uniqid();
                $replacedContent = $this->str_replace_first($match[0][$key], '<'.$container.' id="'.$randomId.'"></'.$container.'><script>
                window._iframeLazyLoad = window._iframeLazyLoad ||  {}
                window._iframeLazyLoad["'.$randomId.'"] = `'.$code.'`;
            </script>', $replacedContent);
            }
        return $replacedContent;
    }

    public function instagram ($content){
        $match = false;
        $replacedContent = $content;
        $regex = $this->classicEditor ? $this->replaceRegex['instagram']['classic']["regex"] : $this->replaceRegex['instagram']['blocks']["regex"];
        $container = $this->classicEditor ? $this->replaceRegex['instagram']['classic']["container"] : $this->replaceRegex['instagram']['blocks']["container"];
            preg_match_all( $regex , $content, $match);
            foreach($match[0] as $key => $matchContent){
                $code = addslashes( wp_json_encode( [ 'embed' => htmlentities( $matchContent ) ] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                $randomId =  uniqid();
                $replacedContent = $this->str_replace_first($match[0][$key], '<'.$container.' id="'.$randomId.'"></'.$container.'><script>
                window._iframeLazyLoad = window._iframeLazyLoad ||  {}
                window._iframeLazyLoad["'.$randomId.'"] = `'.$code.'`;
            </script>', $replacedContent);
            }
        return $replacedContent;
    }
}