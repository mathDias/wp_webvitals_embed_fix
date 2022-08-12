<?php
namespace App;

class ReplaceService {

    public $classicEditor = false;
    private $replaceRegex = [
        "tiktok" => [
            "classic" => [
                "regex" => '/<script[^>]*src\s*=\s*"?https?:\/\/[^\s"\/]*tiktok.com\/embed.js(?:\/[^\s"]*)?"?[^>]*>.*?<\/script>/i',
                "container" => 'span',
            ],
            "blocks" => [
                "regex" => '/<script[^>]*src\s*=\s*"?https?:\/\/[^\s"\/]*tiktok.com\/embed.js(?:\/[^\s"]*)?"?[^>]*>.*?<\/script>/i',
                "container" => 'div',
            ], 
        ],
        "twitter" => [
            "classic" => [
                "regex" => '/<script[^>]*src\s*=\s*"?https?:\/\/[^\s"\/]*platform.twitter.com(?:\/[^\s"]*)?"?[^>]*>.*?<\/script>/i',
                "container" => 'span',
            ],
            "blocks" => [
                "regex" => '/<figure class="wp-block-embed is-type-rich is-provider-twitter(.*?)<\/figure>/i',
                "container" => 'div',
            ], 
        ],
        "youtube" => [
            "classic" => [
                "regex" => '/<iframe[^>]*src\s*=\s*"?https?:\/\/[^\s"\/]*youtube.com(?:\/[^\s"]*)?"?[^>]*>.*?<\/iframe>/i',
                "container" => 'span',
            ],
            "blocks" => [
                "regex" => '/<iframe[^>]*src\s*=\s*"?https?:\/\/[^\s"\/]*youtube.com(?:\/[^\s"]*)?"?[^>]*>.*?<\/iframe>/i',
                "container" => 'div',
            ], 
        ],
        
        "spotify" => [
            "classic" => [
                "regex" => '/<iframe[^>]*src\s*=\s*"?https?:\/\/[^\s"\/]*open.spotify.com\/embed(?:\/[^\s"]*)?"?[^>]*>.*?<\/iframe>/i',
                "container" => 'span',
            ],
            "blocks" => [
                "regex" => '/<iframe[^>]*src\s*=\s*"?https?:\/\/[^\s"\/]*open.spotify.com\/embed(?:\/[^\s"]*)?"?[^>]*>.*?<\/iframe>/i',
                "container" => 'div',
            ], 
        ],
        "instagram" => [
            "classic" => [
                "regex" => '/<script[^>]*src\s*=\s*"?https?:\/\/[^\s"\/]*instagram.com\/embed.js(?:\/[^\s"]*)?"?[^>]*>.*?<\/script>/i',
                "container" => 'span',
            ],
            "blocks" => [
                "regex" => '/<script[^>]*src\s*=\s*"?https?:\/\/[^\s"\/]*instagram.com\/embed.js(?:\/[^\s"]*)?"?[^>]*>.*?<\/script>/i',
                "container" => 'div',
            ], 
        ],
    ];


    function __construct()
    {
        
        $this->classicEditor = !$this->isActive();
        //var_dump($this->classicEditor);die();
    }


    function isActive() {
        // Gutenberg plugin is installed and activated.
        $gutenberg = ! ( false === has_filter( 'replace_editor', 'gutenberg_init' ) );
    
        // Block editor since 5.0.
        $block_editor = version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' );
    
        if ( ! $gutenberg && ! $block_editor ) {
            return false;
        }
    
        if ( $this->isClassicEditorPluginActive() ) {
            $editor_option       = get_option( 'classic-editor-replace' );
            $block_editor_active = array( 'no-replace', 'block' );
    
            return in_array( $editor_option, $block_editor_active, true );
        }
    
        return true;
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

    public function tiktok ($content){
        $match = false;
        $replacedContent = $content;
        $regex = $this->classicEditor ? $this->replaceRegex['tiktok']['classic']["regex"] : $this->replaceRegex['tiktok']['blocks']["regex"];
        $container = $this->classicEditor ? $this->replaceRegex['tiktok']['classic']["container"] : $this->replaceRegex['tiktok']['blocks']["container"];
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

    
    public function spotify ($content){
        $match = false;
        $replacedContent = $content;
        $regex = $this->classicEditor ? $this->replaceRegex['spotify']['classic']["regex"] : $this->replaceRegex['spotify']['blocks']["regex"];
        $container = $this->classicEditor ? $this->replaceRegex['spotify']['classic']["container"] : $this->replaceRegex['spotify']['blocks']["container"];
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