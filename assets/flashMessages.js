
/*
 * script to fade out all success & error flash messages populated by PHP setFlash()
*/
var flashError = document.getElementById('flash-error');
var flashSuccess = document.getElementById('flash-success');

function fadeOut(fadeScaler) {
    if (!this instanceof Element) return false;
    var element = this;   
    var opacity = 1;
    //    opacity = "0";
   // var opacity = this.style.opacity = 1;

    animate = setInterval(function() {
        opacity += fadeScaler;

        if (opacity <= -1) {
            clearInterval(animate);
        }
        element.style.opacity = opacity;
       },
       5000);
};

if(flashError) {fadeOut.apply(flashError, [-1]); };
if (flashSuccess) {fadeOut.apply(flashSuccess, [-1]); };