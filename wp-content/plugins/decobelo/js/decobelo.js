// ANCHOR state

var query;
var nonce;

jQuery('document').ready(function(){

   if(document.body.classList.contains('archive')){

      // przygotowanie query

      if((document.querySelector('.filters') == null) && (document)) {
         return;
      };
     
      var currentType = document.querySelector('.filters').getAttribute('data-current_type');
      var currentTerm = document.querySelector('.filters').getAttribute('data-current_term');
      var currentSearch = document.querySelector('.filters').getAttribute('data-search');
      var promocje = document.querySelector('.filters').getAttribute('data-promocje');
   
      nonce = document.querySelector('.filters').getAttribute('data-nonce');
      
      query = {
        "firstTerm" : {
           type : currentType,
           value : currentTerm
        },
        "secondTerm": {
           "product_cat" : {
              type : "",
              title : "",
              values : [],
           },
           "kolekcje" : {
              type : "",
              title : "",
              values : [],
           }
        },
        "attrs": {},
        "search" : currentSearch,
        "promocje" : promocje,
        "onsale" : {},
        "orderby" : {},
        "page" : 1
     }; 

     

      // ustawienie punktu startowego historii po wczytaniu strony

      history.pushState({nonce: nonce, query: query}, "title 1", "")

      // dodanie event listenera do zmiany w historii

      window.addEventListener('popstate', (event) => {
         setTimeout(
            function() {
               var data = JSON.parse(JSON.stringify(event.state));
               sendQuery(data.nonce, data.query);
               query = data.query;
            },0
         )
      }); 

      // TODO budowa query z zaznaczonych filtrow

      
      
      selectedFilters = document.querySelectorAll('.filter.selected');

      selectedFilters.forEach(element => {
         
         var type = element.getAttribute('data-type')
         var value = element.getAttribute('data-value')
         var title = element.parentElement.parentElement.getAttribute('data-title');

         // atrybuty //

         if(type.includes('pa_') == true) {
            
            if(!query.attrs[type]) {
               query.attrs[type] = {
                  'title'  : title,
                  'values' : []
               };
            }
            
            query.attrs[type].values.push(value);
            
         }

         // second term //

         if(type.includes('kolekcje') == true) {
            
            if(!query.secondTerm.kolekcje.title) {
               query.secondTerm.kolekcje = {
                  'title'  : title,
                  'type'   : 'kolekcje',
                  'values' : []
               };
            }
            query.secondTerm.kolekcje.values.push(
               {
                  'name'   : element.textContent,
                  'value'  : value
               }
            )
            
         }

         // promocje //

         if(type.includes('onsale') == true) {
            query.onsale = {
               type: 'onsale', 
               title: 'W promocji', 
               value: 'tak'
            }
         }

         // orderby //

         if(type.includes('orderby') == true) {
            query.orderby = {
               title:   'Sortowanie', 
               value:   value, 
               name:    element.textContent, 
               type:    'orderby'
            }
         }


      });

      console.log(query);


   };

});
   




// ANCHOR sticky filters

   if(document.body.classList.contains('archive')) {

      var oldpos = window.pageYOffset;
      var filters = document.querySelector('.lower-filters');
      var filterspos = filters.getBoundingClientRect().top;
      var filtersheight = filters.offsetHeight;
      
      document.addEventListener('scroll', function(){
         
         var newpos = window.pageYOffset;
         
         if(newpos > filterspos) {
            document.body.style.paddingTop = filtersheight + 'px';
            filters.classList.add('sticky');
         } else {
            document.body.style.paddingTop = '';
            filters.classList.remove('sticky');
         }
      });

   } else if (document.body.classList.contains('page-template')) {

      var oldpos = window.pageYOffset;
      var menu = document.querySelector('#masthead');
      var menupos = 0;
      var menuheight = menu.offsetHeight;
      var submenus = document.querySelectorAll('#primary-menu .sub-menu');
      
      menu.classList.remove('sticky');

      document.addEventListener('scroll', function(){

            var cover = document.getElementById('cover');

            var check = cover.classList.contains('active');

            var newpos = window.pageYOffset;

            if(check == false) {

               if(newpos > oldpos) {
                  document.body.style.paddingTop = menuheight + 'px';
                  menu.style.position = 'fixed';
                  menu.classList.add('pre-sticky');
                  menu.classList.remove('sticky');
                  submenus.forEach(function(el) {
                     el.style.top = menuheight + "px";
                  });
               } else if (newpos == 0)  {
                  document.body.style.paddingTop = '';
                  menu.style.position = 'relative';
                  menu.classList.remove('pre-sticky');
                  menu.classList.remove('sticky');
                  submenus.forEach(function(el) {
                     el.style.top = "0px";
                  });
               } else {
                  menu.classList.add('sticky');
                  submenus.forEach(function(el) {
                     el.style.top = '0';
                  });
               }
   
               oldpos = newpos;

            }
         
            

      });

   }


// ANCHOR nip

window.onload = function() {

   if(document.body.classList.contains('woocommerce-checkout')) {
      const invoice = document.querySelector('#invoice_field input');
      const nip = document.querySelector('#nip_field');
   
      if(invoice.checked) {
         nip.classList.add('visible');
      }
   
      invoice.addEventListener('input', ()=> {
         if(invoice.checked) {
            nip.classList.add('visible');
         } else {
            nip.classList.remove('visible');
         }
      })
   
 }

}
 
 
 // ANCHOR hold body

jQuery('document').ready(function(){

   var productBox = document.querySelector('.product-float');

   if(productBox == null) {
      return;
   }

   var buffer = document.querySelector('#masthead').clientHeight;
   var initalpos = document.querySelector('.woocommerce-breadcrumb').offsetHeight + document.querySelector('#masthead').offsetHeight;
   var loadpos = parseInt(document.querySelector('.woocommerce-product-gallery').getBoundingClientRect().top);

   var productBoxPos = productBox.getBoundingClientRect();
   var productBoxTop = (productBoxPos.top - (1.5*buffer)) + "px";
   var productBoxLeft = productBoxPos.left + "px";
   var productBoxBottom = parseInt(productBox.getBoundingClientRect().bottom);
   
   var footerheight = parseInt(document.querySelector('#colophon').offsetHeight);

   
   if(loadpos < initalpos) {

      productBox.classList.add('hold');
      productBox.style.top = '32px';
      productBox.style.left = productBoxLeft;
      productBox.style.position = "fixed";

      document.addEventListener('scroll', function() {
         
         if(window.scrollY < (initalpos - 48)) {
            productBox.style.position = "static";
         } else {
            productBox.classList.add('hold');
            productBox.style.top = '48px';
            productBox.style.bottom = "";
            productBox.style.left = productBoxLeft;
            productBox.style.position = "fixed";
            productBox.style.right = '3rem'; 
         }
      });
      
   } else {

      var d = 0;

      document.addEventListener('scroll', function() {

         var currentline = window.scrollY + productBox.offsetHeight;
         var stopline = document.body.offsetHeight - document.querySelector('#colophon').offsetHeight;

         var a = parseInt(window.scrollY + productBox.offsetHeight + 48);
         var b = parseInt(document.body.offsetHeight - document.querySelector('#colophon').offsetHeight);
         

         if((window.scrollY > (initalpos - 48)) && (a < b)) {

            productBox.classList.add('hold');
            productBox.style.top = '48px';
            productBox.style.bottom = "";
            productBox.style.left = productBoxLeft;
            productBox.style.position = "fixed";
            productBox.style.right = '3rem'; 

         } else if ((window.scrollY > (initalpos - 48)) && (a > b)) {
            
            var footertop = parseInt(document.querySelector('#colophon').getBoundingClientRect().top);
            d = (window.innerHeight - footertop);

            productBox.classList.add('hold');
            productBox.style.top = "";
            productBox.style.bottom = d + "px";
            productBox.style.left = productBoxLeft;
            productBox.style.position = "fixed";
            productBox.style.right = '3rem'; 

         } else {
            
            productBox.classList.remove('hold');
            productBox.style.position = "static";
         };

         var newpos = window.scrollY;

      });

   }

   window.addEventListener('resize', function() {
      productBoxLeft = document.querySelector('.woocommerce-product-gallery').getBoundingClientRect().right + 32 + 'px';
      productBox.style.left = productBoxLeft; 
      productBox.style.right = '3rem'; 
   })

});
 
 
 // TODO usunac title ze zdjec galerii
 
 
 jQuery('document').ready(function(){
 
    const imgs = document.querySelectorAll('.woocommerce-product-gallery img');
 
    imgs.forEach(img => {
       img.title = "";
       if(img.offsetWidth > img.offsetHeight) {
          img.parentElement.parentElement.classList.add('wide-img');
       }
    });
 
    if((imgs.length % 2) !== 0) {
       document.querySelector('.woocommerce-product-gallery div:last-of-type').classList.add('wide-img');
    } 
 
 
 });
 
 
 
 // glightbox
 
 var lightbox;
 
 jQuery('document').ready(function(){

   if(document.body.classList.contains('single-product')) {

      var lightbox = GLightbox({
         selector : '.woocommerce-product-gallery__wrapper > .woocommerce-product-gallery__image > a',
         loop: true,
         preload: false,
         openEffect : 'fade',
         closeEffect : 'fade'
      });
      
      setTimeout(
         function() {
            lightbox.reload();
         }, 500
      );
      
      const variationForm = document.querySelector('.variations_form');
   
      if(variationForm) {
         variationForm.addEventListener('click', function(e){
            setTimeout(
               function() {
                  lightbox.reload();
               }, 200
            );
         });
      }

   } 
});
 
// SECTION lista produktow
 
jQuery('document').ready(function(){

    document.querySelector('.filters').addEventListener('click',function(e){

 
       if(e.target.classList.contains('filter')) {
 
          var filter = e.target;
 
          e.preventDefault(); 
 
          // tworzenie query
 
          var filterTitle;
 
          if(!filter.getAttribute('data-title')) {
             filterTitle = filter.parentElement.parentElement.getAttribute('data-title');
          } else {
             filterTitle = filter.getAttribute('data-title');
          }
 
          filterType = filter.getAttribute('data-type');
          filterValue = filter.getAttribute('data-value');
          filterName = filter.textContent;
 
          if((filterType == 'kolekcje') || (filterType == 'product_cat')) {
                          
             query.secondTerm[filterType].type = filterType;
             query.secondTerm[filterType].title = filterTitle;
 
             var check = true;
 
             if(query.secondTerm[filterType].values) {
                query.secondTerm[filterType].values.forEach(function(value) {
                   if(value.value == filterValue) {
                      check = false;
                   }
                });
             };
 
             if(filter.classList.contains('selected')) {
 
                for( var i = 0; i < query.secondTerm[filterType].values.length; i++) {
 
                   if(query.secondTerm[filterType].values[i].value == filterValue) {
                      query.secondTerm[filterType].values.splice(i, 1); 
                   };
                }
 
             } else {
                if(check == true) {
                   query.secondTerm[filterType].values.push({name : filterName, value : filterValue});
                };
             }
            } else if (filterType == 'onsale') {

               if (filterValue == 'tak') {

                  if(filter.classList.contains('selected')) {
                     query.onsale = {};
                  } else {
                     query.onsale.type = filterType;
                     query.onsale.title = filterTitle;
                     query.onsale.value = filterValue;
                  }

                  
               };
          
            } else if (filterType == 'orderby') {

               query.orderby = {
                  "title"  : filterTitle,
                  "value"  : filterValue,
                  "name"   : filterName,
                  "type"   : "orderby"
               };

            
            } else {
 
             if(filter.classList.contains('selected')) {
 
                for( var i = 0; i < query.attrs[filterType].values.length; i++) {
 
                   if(query.attrs[filterType].values[i] == filterValue) {
                      query.attrs[filterType].values.splice(i, 1); 
                   };
                }
 
             } else {
 
                if(!query.attrs[filterType]) {
                   query.attrs[filterType] = {};
                   query.attrs[filterType].title = filterTitle;
                   query.attrs[filterType].values = [];
                      query.attrs[filterType].values.push(filterValue);
                } else if (query.attrs[filterType].values.includes(filterValue) == false) {
                   query.attrs[filterType].values.push(filterValue);
                }
             }
          } 

         // ANCHOR wysylka query

         query.page = 1;

         var url = '';

         // ANCHOR zmiana url 

         // second term

         for (var tax in query.secondTerm) {

         var taxonomy = query.secondTerm[tax];

         if(taxonomy.type) {

            if(taxonomy.type == 'product_cat') {
               url += "produkty=";
            } else {
               url += taxonomy.type + "=";
            }
            
            
            var taxterms = taxonomy.values;
            for (var key in taxterms) {
               
               url += taxterms[key].value;
               if(taxterms.length > 1) url += ',';
            }
            if(taxterms.length > 1) {
               url = url.slice(0, -1);
            }
         }
         }
         
         // atrybuty

         for (var attr in query.attrs) {

            if (!query.attrs.hasOwnProperty(attr)) continue;

            var values = query.attrs[attr]['values'];

            url += "&";

            url += attr.replace('pa_', '') + "=";

            url += query.attrs[attr]['values'].toString();

            url += "&";

            url = url.slice(0, -1);

         }  

         // promocje

         if(query.onsale.value == "tak") {
            url += "&promocje=tak";
         }

         // sortowanie

         var translateorderby = {
            'price'     : 'cena-rosnaco',
            'price-desc': 'cena-malejaco',
            'date'      : 'on-najnowszych'
         }
          
         if(query.orderby.value) {
            url += "&sortowanie=" + translateorderby[query.orderby.value];
         }
          
         
         //console.log(url);

         //var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + url;
         history.pushState({nonce: nonce, query: query}, "title 1", "?" + url);

         //console.log(query);
         

       };

       //console.log(query);

       sendQuery(nonce, query);
 
    });

    var loadmore = document.querySelector('#load-more');    

    if(loadmore) {

      loadmore.addEventListener('click', function() {
         query.page = query.page + 1;

         //console.log(query);

         jQuery.ajax({
            type : "post",
            dataType : "json",
            url : my_ajax.ajax_url,
            data : {action: "send_products", nonce: nonce, query: query},

            beforeSend: function () {
               document.querySelector('.products').classList.add('hide');
            },
            success: function(response) {
               if(response.type == "success") {
                  document.querySelector('.products li:last-of-type').insertAdjacentHTML('afterend', response.products);
               }  

               if(query.page == response.count) {
                  loadmore.classList.add('hide');
               } else {
                  loadmore.classList.remove('hide');
               }

            },
            complete: function() {
               document.querySelector('.products').classList.remove('hide');
            }
         });

      });
   }

});

// ANCHOR send query

function sendQuery(nonce,query) {

   jQuery.ajax({
      type : "post",
      dataType : "json",
      url : my_ajax.ajax_url,
      data : {action: "send_products", nonce: nonce, query: query},

      beforeSend: function () {
         document.querySelector('.products').classList.add('hide');
      },
      
      success: function(response) {

         if(response.type == "success") {
            
            document.querySelector('.products').innerHTML = response.products;

            var filters = document.querySelectorAll('a.filter');

            var activeArr = JSON.parse(response.active);
            
            var activeDiv = document.querySelector('.active-filters');

            // span Aktywne filtry

            if ((activeArr.length == 1) && (activeArr[0].term_slug == "ordeby")) {
              activeDiv.classList.remove('active');
            } else if(activeArr.length > 0 ) {
              activeDiv.classList.add('active');
            } else {
              activeDiv.classList.remove('active');
            }

            // usuniecie aktywnych filtrow 

            var activeBtns = activeDiv.querySelectorAll('a');
            activeBtns.forEach(button => {
               button.remove();
            });

            // render aktywnych filtrow

            activeArr.forEach(active => {

              if(active.term_slug == "orderby") {
                 return;
              }

               var btn = document.createElement('a');
               btn.classList.add('filter', 'selected');
               
               var name;

               if(!active.term_name) {
                  name = active.term_value;
               } else {
                  name = active.term_name;
               }

               btn.innerHTML = "<b>" + active.tax_name + "</b>: " + name;
               btn.setAttribute('data-type', active.term_slug);
               btn.setAttribute('data-value', active.term_value);
               btn.setAttribute('data-title', active.tax_name);
               btn.setAttribute('title', 'Usuń filtr');

               var append = true;

               var activeBtns = activeDiv.querySelectorAll('a');

               activeBtns.forEach(btn => {
                  if((btn.getAttribute('data-type') == active.term_slug) && (btn.getAttribute('data-value') == active.term_value)) {
                     append = false;
                  }
               });
               
               if(append == true) {
                  activeDiv.appendChild(btn);
               };

               activeBtns.forEach(btn => {
                  if((btn.getAttribute('data-type') == active.term_slug) && (btn.getAttribute('data-value') == active.term_value)) {
                     append = false;
                  }
               });
               // dodanie klasy selcted do aktywnych filtrow
               
               
            });

            filters.forEach(filter => {
               filter.classList.remove('selected');
               activeArr.forEach(active => {
                  if((filter.getAttribute('data-type') === active.term_slug) && (filter.getAttribute('data-value') === active.term_value)) {
                     filter.classList.add('selected');
                  } 
               });
            });

            // loadmore on off

            var loadmore = document.querySelector('#load-more');

            if(loadmore) {
               if(query.page == response.count) {
                  loadmore.classList.add('hide');
               } else {
                  loadmore.classList.remove('hide');
               }
            }

            console.log(query)

         } else {
            console.log('err');
         }

        // STUB wygaszanie niedost opcji

        /**

         var availableTerms = JSON.parse(response.available);

         console.log('availableTerms');
         console.log(availableTerms);

         var lowerBtns = document.querySelectorAll('.lower-filters a');

         lowerBtns.forEach(lowerBtn => {
            var available = false;
            availableTerms.forEach(term => {
               if((term.type == lowerBtn.getAttribute('data-type')) && (term.value == lowerBtn.getAttribute('data-value'))) {
                  available = true;
               };
            });

            var paSelected = false;

            activeArr.forEach(term => {
               if(term.term_slug.search('pa') == 0) {
                  paSelected = true;
               };
            });

           if(available == false) {
              if(paSelected == true) {
                 lowerBtn.classList.add('unavailable');
              };
           } else {
              lowerBtn.classList.remove('unavailable');
           }

         });

         **/  
        
     },
      complete: function() {
         document.querySelector('.products').classList.remove('hide');
      }

   });
}

 // !SECTION
 
 // ANCHOR dodatkowe eventy do przyciskow filtrow
 
 var activeFilters = document.querySelector('.active-filters');
 
 
 if(activeFilters) {
    activeFilters.addEventListener('click', (e)=> {
       var elem = e.target;
       if(elem.classList.contains('filter')) {
          elem.classList.add('hide');
          elem.classList.add('narrow');
       }
    });
 };

 /*

 jQuery(document).ready( function() {
     jQuery(".list-btn").click( function(e) {
       e.preventDefault(); 
       product_id = jQuery(this).attr("data-product_id");
       nonce = jQuery(this).attr("data-nonce");
       user = jQuery(this).attr("data-user");
       jQuery.ajax({
           type : "post",
           dataType : "json",
           url : my_ajax.ajax_url,
           data : {action: "list", product_id : product_id, nonce: nonce, user : user},
           
           success: function(response) {
              if(response.type == "success") {
                document.querySelector('.list-'+product_id).classList.add('inlist');
                document.querySelector('.list-count').textContent = response.length;
                jQuery('.saved-list').html(response.lista);
              } else {
                 console.log('err');
              }
           }
        });
     });
  });
 
    
 */
    
 // lista ulubionych
 
 jQuery(document).ready( function() {

   var productimgs = document.querySelectorAll('.products');

   productimgs.forEach(function(pimg) {
      pimg.addEventListener('click', function(e) {
         
         if(e.target.classList.contains('list-btn') == true) {

            e.preventDefault(); 
            product_id = e.target.getAttribute("data-product_id");
            nonce = e.target.getAttribute("data-nonce");
            user = e.target.getAttribute("data-user");
            jQuery.ajax({
               type : "post",
               dataType : "json",
               url : my_ajax.ajax_url,
               data : {action: "list", product_id : product_id, nonce: nonce, user: user},
               
               success: function(response) {
                  if(response.type == "success") {
                     document.querySelector('.list-'+product_id).classList.add('inlist');
                     document.querySelector('.list-count').textContent = response.length;
                     jQuery('.saved-list').html(response.lista);
                  } else {
                     console.log('err');
                  }
               }
            });

         };
   
      })
   })
});

 jQuery(document).ready(function(){
    
   document.querySelector('.saved-list').addEventListener('click', function(e) {
      
      if(e.target.classList.contains('remove-from-list') == true) {

         e.preventDefault(); 

         product_id = e.target.getAttribute("data-product_id");
         nonce = e.target.getAttribute("data-nonce");
         user = e.target.getAttribute("data-user");
         
         jQuery.ajax({
            type : "post",
            dataType : "json",
            url : my_ajax.ajax_url,
            data : {action: "remove_from_list", product_id : product_id, nonce: nonce, user: user},
            success: function(response) {
               if(response.type == "success") {
                  document.querySelector('.list-prod-'+product_id).classList.add('hide');
                  document.querySelector('.list-'+product_id).classList.remove('inlist');
                  document.querySelector('.list-count').textContent = response.length;
                  console.log(response.user);
                  if(response.length == 0) {
                     document.querySelector('.saved-list').textContent = 'Brak produktów.';
                  }
               } else {
                  console.log('err');
               }
            }
         });

      };
   })

 });


 /**
 window.onload = function() {
 
   document.querySelector('.saved-list').addEventListener('click', function(e){
         var elem = e.target;
         if(elem.classList.contains('remove-from-list')) {
            e.preventDefault(); 
            product_id = jQuery(elem).attr("data-product_id");
            nonce = jQuery(elem).attr("data-nonce");
            jQuery.ajax({
               type : "post",
               dataType : "json",
               url : my_ajax.ajax_url,
               data : {action: "remove_from_list", product_id : product_id, nonce: nonce},
               success: function(response) {
                  if(response.type == "success") {
                     document.querySelector('.list-prod-'+product_id).classList.add('hide');
                     document.querySelector('.list-count').textContent = response.length;
                     if(response.length == 0) {
                        document.querySelector('.saved-list').innerHTML = '<span>Brak zapisanych produktów</span>';
                     }
                  } else {
                     console.log('err');
                  }
                  var toRemove = document.querySelector('.list' + '-' + response.removed);
                  toRemove.classList.remove('inlist');
   
               }
            });   
         }
   });
 }

  */
 
  const likedAddToCartBtns = document.querySelectorAll('.saved-list a.button');
 
  likedAddToCartBtns.forEach(function(el){
    el.setAttribute('title', 'Dodaj do koszyka');
  });
 
 
 // ANCHOR aktywacja covera

 window.onload = function() {
 
   var coverBtns = document.querySelectorAll('.cover-btn');
   var cover = document.querySelector('#cover');
   
   coverBtns.forEach((btn)=>{
      btn.addEventListener('click', (e)=> {
         e.preventDefault();
         btn.nextElementSibling.classList.add('active');
         cover.classList.add('active');
      })
   })
   
   cover.addEventListener('click', ()=> {
      actives = document.querySelectorAll('.active');
      actives.forEach((el)=> {
         el.classList.remove('active');
      });
      cover.classList.remove('white');
   })
   
   var closeBtns = document.querySelectorAll('.close');
   
   closeBtns.forEach((btn)=>{
      btn.addEventListener('click', ()=> {
         actives = document.querySelectorAll('.active');
         actives.forEach((el)=> {
            el.classList.remove('active');
         })
         cover.classList.remove('white');
      })
   })

}
 
 jQuery( document.body ).on( 'updated_checkout', function(e){
    
       var rightInputs = document.querySelectorAll('.right #shipping_method li input');
       var leftInputs = document.querySelectorAll('.left #shipping_method li input');
       let shipping = '';
    
       rightInputs.forEach(function(el) {
          if(el.checked) {
             shipping = el.value;
          }
       });
    
       leftInputs.forEach(function(lel) {
          if(lel.value == shipping) {
             lel.nextElementSibling.classList.add('selected');
          } else {
             lel.nextElementSibling.classList.remove('selected');  
          }
       });
 
       var leftPaymentInputs = document.querySelectorAll('.step-payment li input');
 
       leftPaymentInputs.forEach(function(el){
          if(el.checked) {
             el.nextElementSibling.classList.add('selected');
          } else {
             el.nextElementSibling.classList.remove('selected');
          }
       })
 
       var inputs = document.querySelectorAll('.right #shipping_method input');
       var liElements = document.querySelectorAll('.right #shipping_method li');
       var labels = document.querySelectorAll('.right #shipping_method label');
 
       liElements.forEach(function(el){
          el.style.padding = "0";
          el.addEventListener('click', function(e) {
             e.preventDefault();
          })
       });
 
       inputs.forEach(function(inpt) {
          inpt.style.display = "none";
       });
 
       labels.forEach(function(lab){
          var text = lab.textContent.split(":");
          if(!text[1]) {
             lab.textContent = "0 zł";
          } else {
             lab.textContent = text[1];
          };
 
          lab.style.opaciy = "1";
          
          if(lab.previousElementSibling.checked) {
             lab.style.display = "inline";
          } else {
             lab.style.display = "none";
          }
 
       });
 
 });
 
 jQuery(document).ready(function(){
    if(document.body.classList.contains('.woocommerce-checkout')) {
       var inputs = document.querySelectorAll('.right #shipping_method input');
       var labels = document.querySelectorAll('.right #shipping_method label');
 
       inputs.forEach(function(inpt) {
          inpt.style.display = "none";
       });
    }
 });