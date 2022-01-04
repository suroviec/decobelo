
// lista produktow

document.querySelector('.filters').addEventListener('click', function(e) {
   e.preventDefault();
   console.log(e.target);
});


window.onload = function() {

   const query = {
      "secondTerm": {
         type : "",
         values : [],
      },
      "attrs": {}
   };

   const filters = document.querySelectorAll('.filter');

   var nonce = document.querySelector('.filters').getAttribute('data-nonce');

   filters.forEach(filter => {
      
      filter.addEventListener('click',(e) => {

         e.preventDefault(); 

         filterTitle = filter.parentElement.parentElement.getAttribute('data-title');
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

            // NOTE Usuwanie z listy filtrow

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

         // TODO Dokonczyc usuwanie z filtrow dla atrybutow

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
         
         // TODO Dodac domyslny tax query
         
         console.log(query);

         jQuery.ajax({
            type : "post",
            dataType : "json",
            url : my_ajax.ajax_url,
            data : {action: "send_products", nonce: nonce, query: query},

            beforeSend: function () {
               //console.log('aaa')
            },
            
            success: function(response) {

               if(response.type == "success") {

                  console.log(JSON.parse(response.active));
                  
                  document.querySelector('.products').innerHTML = response.products;

                  var filters = document.querySelectorAll('a.filter');

                  var activeArr = JSON.parse(response.active);
                  
                  var activeDiv = document.querySelector('.active-filters');

                  // render aktywnych filtrow

                  activeArr.forEach(active => {
                     var btn = document.createElement('a');
                     btn.classList.add('filter', 'selected');
                     btn.setAttribute('href','aaa');
                     
                     var name;

                     if(!active.term_name) {
                        name = active.term_value;
                     } else {
                        name = active.term_name;
                     }

                     btn.textContent = active.tax_name + ": " + name;
                     btn.setAttribute('data-type', active.term_slug);
                     btn.setAttribute('data-value', active.term_value);

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

                  console.log(activeArr);




               } else {
                  console.log('response');
               }
            },

            complete: function() {
               //console.log('bbb');
            }
         
         });

         

      });
   });   





}



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
            }
         });   
      }
});

 const likedAddToCartBtns = document.querySelectorAll('.saved-list a.button');

 likedAddToCartBtns.forEach(function(el){
   el.setAttribute('title', 'Dodaj do koszyka');
 });
