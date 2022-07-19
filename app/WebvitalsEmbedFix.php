<?php
namespace App;

class WebvitalsEmbedFix extends BaseController{

    public $wpVersion;
    public $replaceService;
    
    

    public function register(){
        //    
        $this->wpVersion = get_bloginfo( 'version' );
        $this->replaceService = new ReplaceService();
        add_filter( 'the_content', [ $this, 'initReplace' ] );

    }

    public function initReplace($content){
        
        // Twitter filter
        $content = $this->replaceService->twitter($content);

        // Youtube filter
        $content = $this->replaceService->youtube($content);

        // Instagram filter
        $content = $this->replaceService->instagram($content);

          // Tiktok filter
        $content = $this->replaceService->tiktok($content);

        $lazyLoadScript = "<script >
	    var listener = function(){
		window.removeEventListener('mousemove',listener)
		window.removeEventListener('touchstart',listener)
		Object.keys(window._iframeLazyLoad).map(function(e) {
            
                var n, a, i = JSON.parse(window._iframeLazyLoad[e]);

                var r = document.getElementById(e);
                r.innerHTML = (n = i.embed, (a = document.createElement(\"textarea\")).innerHTML = n, a.value);
                for (var o = r.querySelectorAll('script'), d = 0; d < o.length; d++) {
                    var l = document.createElement('script');
                    o[d].src ? l.src = o[d].src : l.innerHTML = o[d].innerHTML, r.appendChild(l)
                }
            });
            
        }
        window.addEventListener(\"mousemove\", listener);
        window.addEventListener(\"touchstart\",listener);
	    </script>";
	    $content.=$lazyLoadScript;
	
        return $content;
    }
}