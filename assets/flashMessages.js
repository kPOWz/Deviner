
/*
 * script to fade out all success & error flash messages populated from the server with PHP setFlash()
 *  or from the client with FLASH.setContent()
*/
//  
var FLASH = function(){
    //private
    var fadeOut = function(fadeScaler) {
        if (!this instanceof Element) return false;
            var element = this;   
            var opacity = 1;
            animate = setInterval(function() {
                opacity += fadeScaler;

                if (opacity <= -1) {
                    clearInterval(animate);
                    element.className += " hide";           
                }
                element.style.opacity = opacity;
               },
               5000);
    };
    var createFlash = function(){
        return $('<div></div>').addClass('alert');
    };
    this.createFlashSuccess = function(){
        this.flashSuccess = (this.flashSuccess || createFlash()
            .addClass('alert-success')
            .attr('id', 'flash-success')
            ).removeClass('hide').css('opacity', 1);
        fadeOut.apply(this.flashSuccess[0], [-1]);
    };
    this.createFlashFailure = function(){
        this.flashError = (this.flashError || createFlash()
            .addClass('alert-danger')
            .attr('id', 'flash-error')
            ).removeClass('hide').css('opacity', 1);
        fadeOut.apply(this.flashError[0], [-1]);
    };

    if(this.flashError){ fadeOut.apply(this.flashError, [-1]); };
    if(this.flashSuccess){ fadeOut.apply(this.flashSuccess, [-1]); };
}

FLASH.prototype = (function () {
    return {
        setContent: function(content, success){
            success = success || true;
            if(success) {
                this.createFlashSuccess();
                $('main').prepend((this.flashSuccess).text(content));
            
            }
            else {
                this.createFlashError();
                $('main').prepend((this.flashError).text(content));
            }
        }
    };
}());


FLASH.prototype.flashError = document.getElementById('flash-error');
FLASH.prototype.flashSuccess = document.getElementById('flash-success');
new FLASH();