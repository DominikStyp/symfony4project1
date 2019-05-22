/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');
var $ = require('jquery');
window.jQuery = $;
window.$ = $;

//startbootstrap-clean-blog
function loadStartBootstrapCleanBlog() {
    // css
    require('../../node_modules/startbootstrap-clean-blog/vendor/fontawesome-free/css/all.min.css');
    require('../../node_modules/startbootstrap-clean-blog/vendor/bootstrap/css/bootstrap.min.css');
    require('../../node_modules/startbootstrap-clean-blog/css/clean-blog.css');
    //js
    require('../../node_modules/startbootstrap-clean-blog/js/clean-blog.js');
    require('../../node_modules/startbootstrap-clean-blog/vendor/bootstrap/js/bootstrap.bundle.min.js');
}
loadStartBootstrapCleanBlog();


console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
