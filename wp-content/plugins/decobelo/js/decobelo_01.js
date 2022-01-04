// ANCHOR nip

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


// ANCHOR hold body

jQuery('document').ready(function(){

   var buffer = document.querySelector('#masthead').clientHeight;
   var productBox = document.querySelector('.product-float');
   productBox.style.background = "red";

   if(productBox == null) {
      return;
   }


   const productBoxPos = productBox.getBoundingClientRect();
   const productBoxTop = (productBoxPos.top - (1.5*buffer)) + "px";
   const productBoxLeft = productBoxPos.left + "px";
   

   document.addEventListener('scroll', function() {

      if(productBox) {
         if(window.scrollY > (2*buffer)) {
            productBox.classList.add('hold');
            productBox.style.top = productBoxTop;
            productBox.style.left = productBoxLeft;
            productBox.style.position = "fixed";
         } else {
            productBox.classList.remove('hold');
            productBox.style.position = "static";
         };
      };
   
   });
    

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

});


 
   // lista produktow

window.onload = function() {

   if(document.querySelector('.filters') == null) {
      return;
   };

   var nonce = document.querySelector('.filters').getAttribute('data-nonce');
   var currentType = document.querySelector('.filters').getAttribute('data-current_type');
   var currentTerm = document.querySelector('.filters').getAttribute('data-current_term');

   const query = {
      "firstTerm" : {
         type : currentType,
         value : currentTerm
      },
      "secondTerm": {
         type : "",
         title : 'www',
         values : [],
      },
      "attrs": {}
   };

   var nonce = document.querySelector('.filters').getAttribute('data-nonce');
   

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
            
            query.secondTerm.type = filterType;
            query.secondTerm.title = filterTitle;

            var check = true;

            if(query.secondTerm.values) {
               query.secondTerm.values.forEach(function(value) {
                  if(value.value == filterValue) {
                     check = false;
                  }
               });
            };

            if(filter.classList.contains('selected')) {

               for( var i = 0; i < query.secondTerm.values.length; i++) {

                  if(query.secondTerm.values[i].value == filterValue) {
                     query.secondTerm.values.splice(i, 1); 
                  };
               }

            } else {
               if(check == true) {
                  query.secondTerm.values.push({name : filterName, value : filterValue});
               };
            }

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
           
         // wysylka query

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

                  if(activeArr.length > 0 ) {
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

               } else {
                  console.log('response');
               }

               var availableTerms = JSON.parse(response.available);

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
               

            },

            complete: function() {
               document.querySelector('.products').classList.remove('hide');
            }
         });

      };

   });

};


// dodatkowe eventy do przyciskow filtrow

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

jQuery(document).ready( function() {
    jQuery(".list-btn").click( function(e) {
      e.preventDefault(); 
      product_id = jQuery(this).attr("data-product_id");
      nonce = jQuery(this).attr("data-nonce");
      jQuery.ajax({
          type : "post",
          dataType : "json",
          url : my_ajax.ajax_url,
          data : {action: "list", product_id : product_id, nonce: nonce},
          
          success: function(response) {
             if(response.type == "success") {
               document.querySelector('.list-'+product_id).classList.add('inlist');
               document.querySelector('.list-count').textContent = response.length;
               jQuery('.saved-list').html(response.lista);
             } else {
                console.log(response);
             }
          }
       });
    });
 });

   

   
// lista ulubionych

jQuery(document).ready( function() {
    jQuery(".list-btn").click( function(e) {
      e.preventDefault(); 
      product_id = jQuery(this).attr("data-product_id");
      nonce = jQuery(this).attr("data-nonce");
      jQuery.ajax({
          type : "post",
          dataType : "json",
          url : my_ajax.ajax_url,
          data : {action: "list", product_id : product_id, nonce: nonce},
          
          success: function(response) {
             if(response.type == "success") {
               document.querySelector('.list-'+product_id).classList.add('inlist');
               document.querySelector('.list-count').textContent = response.length;
               jQuery('.saved-list').html(response.lista);
             } else {
                console.log(response);
             }
          }
       });
    });
 });

 jQuery(document).ready( function() {
   jQuery(".remove-from-list").click( function(e) {
      e.preventDefault(); 
      product_id = jQuery(this).attr("data-product_id");
      nonce = jQuery(this).attr("data-nonce");
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
                  document.querySelector('.saved-list').textContent = 'aaa';
               }
            } else {
               console.log(response);
            }
         }
      });
   });
});

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
                  console.log(response);
               }
               var toRemove = document.querySelector('.list' + '-' + response.removed);
               toRemove.classList.remove('inlist');

            }
         });   
      }
});

 const likedAddToCartBtns = document.querySelectorAll('.saved-list a.button');

 likedAddToCartBtns.forEach(function(el){
   el.setAttribute('title', 'Dodaj do koszyka');
 });


// ANCHOR aktywacja covera

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