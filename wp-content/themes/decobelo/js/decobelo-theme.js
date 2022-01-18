// ANCHOR hide img title on hover

jQuery('document').ready(function(){

    var pimg = document.querySelector('.wp-post-image');
    var title = pimg.getAttribute('title');

    pimg.addEventListener('mouseenter', function() {
        pimg.setAttribute('title', '');
    })

    pimg.addEventListener('mouseleave', function() {
        pimg.setAttribute('title', title);
    })

});

var cover = document.getElementById('cover');

function hideactives() {
    actives = document.querySelectorAll('.active');
    actives.forEach((el)=> {
        if(el.classList.contains('active-filters') == false) {
            el.classList.remove('active');
        }
    });
}

/** header */

const headerSwitchers = document.querySelectorAll('#shop-menu > ul > li >a');

headerSwitchers.forEach(function(switcher) {
    switcher.addEventListener('click', function(){
        cover.classList.add('active');
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
        //console.log('aa');
        //e.stopPropagation();
    })

})

// ANCHOR search

var searchicons = document.querySelectorAll('#search-icon, #search-icon-mobile');
var searchinput = document.getElementById('search');

searchicons.forEach(
    function(searchicon) {
        searchicon.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('.dgwt-wcas-suggestions-wrapp').classList.remove('hide');
            searchinput.style.display = "block";
            document.getElementById('dgwt-wcas-search-input-1').focus();
            searchinput.classList.add('active');
            searchicon.classList.add('active');
            cover.classList.add('active');
        });
    });





searchinput.querySelector('.close').addEventListener('click', function(){
    searchinput.style.display = "none";
})


// ANCHOR filtry


if(document.body.classList.contains('archive') && (window.innerWidth < 883)) {

    let filterswitch = document.querySelector('#filter-switch');
    let filterslist = document.querySelector('.filters-list');
    let showproducts = document.querySelector('#show-products');
    let catbtns = document.querySelector('#categorybtns');

    filterswitch.addEventListener('click', function() {
        cover.classList.add('active');
        setTimeout(() => {
            filterslist.classList.add('active');    
        }, 100);
        setTimeout(() => {
            window.scrollTo(0,0);
            document.body.style.overflowY = 'hidden';
        }, 500);
    });

    showproducts.addEventListener('click', function() {
        hideactives();
        document.body.style.overflowY = 'scroll';
    });



}



/*** vaildacja formularz logowania */


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
                //console.log(emailAlert.classList.contains('active'));
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
                //console.log(emailAlert.classList.contains('active'));
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
        //console.log(emailVal.value.match(valid));
        if(emailVal.value.match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/) !== 'null') {
            emailVal.classList.remove('err');
            const errMsg = document.querySelector('.login-username .err-msg');
            if(errMsg) {
                document.querySelector('.login-username .err-msg').textContent = '';    
            }
        }
    })

}



//  ANCHOR mobile

if(window.innerWidth < 883) {

    var btns = document.querySelectorAll('#mobile-menu a');

    function mobilemenu(btn,menu) {

        var cover = document.querySelector('#cover');

        btn.addEventListener('click', function(){

            closeactives();

            btns.forEach(function(el){
                if(el !== btn) {
                    el.classList.remove('selected');
                }
            })

            btn.classList.toggle('selected');

            if(btn.classList.contains('selected') == true) {
                document.body.style.overflowY = "hidden";
            } else {
                document.body.style.overflowY = "";
            }
            
            if(btn.classList.contains('selected') == true) {
                menu.style.display = "";
                setTimeout(() => {
                    menu.classList.add('active');    
                }, 200);
            } else {
                menu.classList.remove('active');    
                setTimeout(() => {
                    cover.classList.remove('active');    
                }, 200);
                setTimeout(() => {
                    menu.style.display = "none";
                }, 500);
            }
        });    
    }

    function justcover(btn,menu) {

        var cover = document.querySelector('#cover');

        btn.addEventListener('click', function(){

            closeactivemenus();

            btns.forEach(function(el){
                if(el !== btn) {
                    el.classList.remove('selected');
                }
            })

            btn.classList.toggle('selected');

            if(btn.classList.contains('selected') == true) {
                document.body.style.overflowY = "hidden";
            } else {
                document.body.style.overflowY = "";
            }
        });    
    }


    // kategorie

    let prodmenuswitch = document.querySelector('#menu-switcher');
    let prodmenu = document.querySelector('#menu-product_cat .sub-menu');

    mobilemenu(prodmenuswitch,prodmenu);

    // lista 

    let listmenuswitch = document.querySelector('#list-icon');
    let listmenu = document.querySelector('#list-btn .submenu');

    mobilemenu(listmenuswitch,listmenu);

    // koszyk

    let cartmenuswitch = document.querySelector('#cart-icon');
    let cartmenu = document.querySelector('#cart-btn .submenu');

    mobilemenu(cartmenuswitch,cartmenu);


    // search 

    let searchswitch = document.querySelector('#search-icon-mobile');
    let searchmenu = document.querySelector('#search');

    searchswitch.addEventListener('click', function(){
        if(searchswitch.classList.contains('selected') == true) {
            document.querySelector('.dgwt-wcas-suggestions-wrapp').classList.add('hide');
            setTimeout(() => {
                searchmenu.classList.remove('active');
            }, 200);
            setTimeout(() => {
                cover.classList.remove('active');    
                document.getElementById('dgwt-wcas-search-input-1').value = "";
            }, 200);
        }
    })

    justcover(searchswitch);

    // ----

    function closeactives() {

        var menus = [prodmenu, listmenu, cartmenu, searchmenu];

        menus.forEach(function(menu){
            menu.classList.remove('active');
        })
    }

    function closeactivemenus() {

        var menus = [prodmenu, listmenu, cartmenu];

        menus.forEach(function(menu){
            menu.classList.remove('active');
        })
    }

    

}