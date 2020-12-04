var nav = document.querySelector("nav");
nav.innerHTML = "<div id='navrow'>\
                 <a class='logo' href='index.php'><img class='logo' src='logo.svg'></img></a>\
                 <input id='mobilesearch' placeholder='Search by title'></input>\
                 <label for='expand'>☰</label>\
                 </div>\
                 <input id='expand' class='hidden' type='checkbox'>\
                 <div id='links'>\
                 <a href='index.php'>Home</a>\
                 <a id='hot'>Hot</a>\
                 <input id='search' placeholder='Search by title'></input>\
                 <div id='navAccount' class='ml-auto'>\
                 </div>\
                 </div>";
var navAccount = nav.querySelector("#navAccount");
var body = document.querySelector('body');
if (body.dataset.loggedin == 'true') {
   navAccount.innerHTML = "<a href='logout.php'>LogOut</a>";
} else {
   navAccount.innerHTML = "<a href='login.php'>LogIn</a>\
                           <a href='register.php'>Register</a>";
}
