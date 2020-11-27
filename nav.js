var nav = document.getElementsByTagName('nav')[0];
nav.innerHTML = "<a id='logo' href='index.php'>Logo</a>\
                 <a href='index.php'>Home</a>\
                 <a id='hot'>Hot</a>\
                 <input id='search'></input>\
                 <div id='navAccount'>\
                 </div>";
var navAccount = document.getElementById("navAccount");
if (nav.dataset.loggedin == 'true') {
   navAccount.innerHTML = "<a href='logout.php'>Log Out</a>";
} else {
   navAccount.innerHTML = "<a href='login.php'>Log In</a>\
                           <a href='register.php'>Register</a>";
}