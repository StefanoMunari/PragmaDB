( function() {
	var is_webkit = navigator.userAgent.toLowerCase().indexOf( 'webkit' ) > -1,
	    is_opera  = navigator.userAgent.toLowerCase().indexOf( 'opera' )  > -1,
	    is_ie     = navigator.userAgent.toLowerCase().indexOf( 'msie' )   > -1;

	if ( ( is_webkit || is_opera || is_ie ) && 'undefined' !== typeof( document.getElementById ) ) {
		var eventMethod = ( window.addEventListener ) ? 'addEventListener' : 'attachEvent';
		window[ eventMethod ]( 'hashchange', function() {
			var element = document.getElementById( location.hash.substring( 1 ) );

			if ( element ) {
				if ( ! /^(?:a|select|input|button|textarea)$/i.test( element.tagName ) )
					element.tabIndex = -1;

				element.focus();
			}
		}, false );
	}
})();
window.onload = function (){
    var backTop = document.getElementById('backTop'),
        backTime = null,
        flag = true;
    backTop.onclick = function(){
    	backtop();
    	return false;
    }
    window.onscroll = function (){
        var scrTop = document.documentElement.scrollTop || document.body.scrollTop;
        if (scrTop > 153) {
            backTop.style.display = 'block';
        }else{
            backTop.style.display = 'none';
        }
        if (!!window.ActiveXObject && !window.XMLHttpRequest){ //for IE6
            var t = document.documentElement.clientHeight - backTop.offsetHeight;
            backTop.style.top = scrTop + t;
        }
        if (!flag) {
            clearTimeout(backTime);
        };
        flag = false;
    };
};

function backtop() {
    var myScroll = document.documentElement.scrollTop || document.body.scrollTop;
    var speed = Math.floor(-myScroll/5);
    backTime = setTimeout( backtop, 20 );
    if ( myScroll == 0 ) {
        clearTimeout(backTime);
    };
    document.documentElement.scrollTop = document.body.scrollTop = myScroll + speed;
    flag = true;
}