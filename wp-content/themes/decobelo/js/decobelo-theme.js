var cover = document.getElementById('cover');


/** header */

const headerSwitchers = document.querySelectorAll('#shop-menu > ul > li >a');

headerSwitchers.forEach(function(switcher) {
    switcher.addEventListener('click', function(){
        
    })
})

// ANCHOR header menu 

var menubtns = document.querySelectorAll('#search > a, #menu-product_cat > a, #menu-kolekcje > a');

var menuheight = document.querySelector('#masthead').offsetHeight;

menubtns.forEach(function(el) {
    el.nextElementSibling.style.display = "none";
    el.addEventListener('click', (e)=> {
        e.preventDefault();
        cover.classList.add('active');
        el.nextElementSibling.style.display = "";
        setTimeout(
            function() {
                el.nextElementSibling.classList.add('active');
            }, 250
        )
        setTimeout(
            function() {
                document.body.style.overflowY = "hidden";
                document.body.style.marginTop = menuheight + "px";
            }, 750
        )
        
    })

    el.nextElementSibling.querySelector('.close').addEventListener('click', function() {
        if(window.pageYOffset > menuheight) {
            document.body.style.paddingTop = menuheight + "px";
        }
        el.nextElementSibling.classList.add('active');
        document.body.style.overflowY = "";
        document.body.style.marginTop = "";
        setTimeout(
            function() {
                el.nextElementSibling.style.display = "none";
            }, 
            500
        )
        
    })

    el.nextElementSibling.addEventListener('scroll', function(e) {
        console.log('aa');
        //e.stopPropagation();
    })

})

// ANCHOR search

var searchicon = document.getElementById('search-icon');
var searchinput = document.getElementById('search');

searchicon.addEventListener('click', function(e) {
    e.preventDefault();
    searchinput.style.display = "block";
    searchinput.classList.add('active');
    searchicon.classList.add('active');
    cover.classList.add('active');
});

searchinput.querySelector('.close').addEventListener('click', function(){
    searchinput.style.display = "none";
})



/*** vaildacja formualrz logowania */


let emailVal = document.getElementById('user_login');
let passVal = document.getElementById('user_pass');
let loginSubmit = document.getElementById('wp-submit');

var valid = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

if(loginSubmit) {

    loginSubmit.addEventListener('click', function(e) {
    
        if(emailVal.value == '') {
            e.preventDefault();
            if(emailVal.classList.contains('err') == false) {
                var emailAlert = document.createElement("span");
                emailVal.classList.add('err');
                var text = document.createTextNode('Wprowadź e-mail');
                console.log(emailAlert.classList.contains('active'));
                emailAlert.appendChild(text);
                emailAlert.classList.add('err-msg');
                document.querySelector('p.login-username').appendChild(emailAlert);
            };
        } else {
            emailVal.classList.remove('active');
        }
    
        if(emailVal.value.match(valid) == null) {
            e.preventDefault();
            if(emailVal.classList.contains('err') == false) {
                var emailAlert = document.createElement("span");
                emailVal.classList.add('err');
                var text = document.createTextNode('Sprawdź e-mail');
                console.log(emailAlert.classList.contains('active'));
                emailAlert.appendChild(text);
                emailAlert.classList.add('err-msg');
                document.querySelector('p.login-username').appendChild(emailAlert);
            };
        } else {
            emailVal.classList.remove('active');
        }
    
        if(passVal.value == '') {
            e.preventDefault();
            if(passVal.classList.contains('err') == false) {
                passVal.classList.add('err');
                var passAlert = document.createElement("span");
                passAlert.classList.add('err-msg');
                var text = document.createTextNode('Wprowadź hasło');
                passAlert.appendChild(text);
                document.querySelector('p.login-password').appendChild(passAlert);
            };
        } else {
            passVal.classList.remove('active');
        }
    })
    
    passVal.addEventListener('input', function() {
        passVal.classList.remove('err');
        const errMsg = document.querySelector('.login-password .err-msg');
            if(errMsg) {
                document.querySelector('.login-username .err-msg').textContent = '';    
            }
    })
    
    emailVal.addEventListener('input', function() {
        console.log(emailVal.value.match(valid));
        if(emailVal.value.match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/) !== 'null') {
            emailVal.classList.remove('err');
            const errMsg = document.querySelector('.login-username .err-msg');
            if(errMsg) {
                document.querySelector('.login-username .err-msg').textContent = '';    
            }
        }
    })

}

