$(document).ready(function(){

    
    /*Liste des annonces*/
    /* https://datatables.net/examples/api/row_details.html */
    if($('#category-list-datatable').length > 0){
        var tableElement = '#category-list-datatable';
        var $table = $(tableElement);
        $table.dataTable({
            "pageLength": 10,
            "processing": true, //Feature control the processing indicator.
            "bServerSide": true, //Feature control DataTables' server-side processing mode.
            "deferRender": true, //Feature control DataTables' server-side processing mode.
            "order": [[ 0, "desc" ]], //Initial no order.
            "bFilter": true,
            "bpaging": true,
            oLanguage: {
                "sProcessing": "traitement...",
                "oPaginate": {
                    "sPrevious": "Précédent", // This is the link to the previous page
                    "sNext": "Suivant", // This is the link to the next page
                },
                "sSearch": "Filtrer: ",
                "sLengthMenu": "Afficher _MENU_ enregistrement(s) / page",
                "sInfo": "Voir _TOTAL_ de _PAGE_ pour _PAGES_ entrées",
                "sEmptyTable": "Aucun résultat",
                "sZeroRecords": "Aucun résultat"
            },
            ajax: {
                "url": $table.attr('ajax-url'),
                "type": "POST",

                "data": function ( data ) {
                    data.page = $table.attr('page');
                    data.user = $table.attr('user');
                    data.listType = $("#category-list-datatable").val() ;
                    columsList = new Array() ;
                    $(tableElement+' thead tr th').each(function(){

                        var classLabel = $(this).attr('class') ;
                        if(classLabel === 'sorting_asc' || classLabel === 'sorting_desc'){
                            column = $(this).index();
                            orderBy = classLabel.replace("sorting_", "") ;

                            obj = {"column" : column, "orderBy":orderBy};
                            columsList.push(obj) ;
                        }

                    }) ;

                    data.columns = columsList ;
                    
                    
                }
            },
            "drawCallback": function( settings ) {
               /*delete annonce*/
               deleteCategory();
                
            }
        });
    }
    
    
    function deleteCategory(){
        $('.delete-category').unbind('click').bind('click',function(){

            var ajaxUrl = $(this).attr('url-action');
            var id = $(this).attr('data-id');
            var element = $(this)

            $.magnificPopup.open({
                items: {src: '#modalFileConfirmDelete'},type: 'inline'
            }, 0);
            
            //modals confirm delete annonce
            $('#modalFileConfirmDelete .btn-modal-confirm-action').unbind('click').bind('click', function(){
            //alert('confirm ok');

                $.ajax({
                    type: 'POST',
                    url: ajaxUrl,
                    dataType:'json',
                    data: {"id": id},

                    success: function(result) {
                        new PNotify({
                            title: 'Notification',
                            type: 'success',
                            text: "Suppression succès",
                            animation: "fade",
                            delay: 6000,
                        });
                        
                        element.parent().parent().remove() ;
                    }
                });
                
                //confirmDeleteAction :
            });
         

            
        });
           
       }
      
    //}



});