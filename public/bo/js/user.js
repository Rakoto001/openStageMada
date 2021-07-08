$(document).ready(function(){

    /*Liste des utilisateurs*/
    /* https://datatables.net/examples/api/row_details.html */
    if($('#user-list-datatable').length > 0){
        var userTableElement = '#user-list-datatable' 
        var $dataTable = $(userTableElement)
        $dataTable.dataTable({
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
           ajax:{
               "url" : $dataTable.attr('ajax-url'),
               "type" : "POST",
               "data" : function( data ){
                   data.page = $dataTable.attr('page'),
                   data.lisType = $('#user-list-datatable').val();
                   columnList = new Array(),
                
                $(userTableElement+ 'thead th tr').each(function(){
                    var classLabel = $(this).attr('class');

                    if( classLabel === 'sorting_asc' || classLabel === 'sorting_desc'){
                        column = $(this).index(),
                        orderBy = classLabel.replace("sorting_", "");

                        obj = {"column": column , "orderBy": orderBy};
                        columnList.push(obj);
                    }
                });
                data.columns = columnList;
               }

           },

            "drawCallback": function( settings ) {
               /*delete annonce*/
               deleteUser();
                
            }
        });
    }

    function deleteUser(){
        $('.delete-user').unbind('click').bind('click',function(){

            var ajaxUrl = $(this).attr('ajax-url');
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
                        
                        $('#user-avatar').remove() ;
                        // element.parent().remove() ;
                    }
                });
                
                //confirmDeleteAction :
            });
         

            
        });
    }

    //***SUPPRESSION avatar */
    // if( $('.btn-delete-avatar').length>0 ){
    //     $('.btn-delete-avatar').unbind('click').bind('click', function(){
    //         var urldelete = $(this).attr('ajax-url-delete-avatar');
    //                var id = $(this).attr('id-avatar');

    //         $.magnificPopup.open({
    //             items:{ src: '#modalFileConfirmDelete'},
    //             type:'inline'
    //         }, 0);


    //     $('#modalFileConfirmDelete .btn-modal-confirm-action').unbind('click').bind('click', function(){

    //         $.post({
    //             url : urldelete,
    //             data : {'id' : id},
    //             dataType: 'json',
    //             success: function(result){
    //                 new PNotify({
    //                     title: 'Notification',
    //                     type: 'success',
    //                     text: "Suppression succès",
    //                     animation: "fade",
    //                     delay: 6000,
    //                 });

    //                 $('#user-'+id+'').remove();

    //             }
    //         })

    //     });

    //     });
    // }



  /*Suppression avatar*/
  if($('.btn-delete-avatar').length > 0){
    $('.btn-delete-avatar').unbind('click').bind('click', function(){
        var ajaxUrl = $(this).attr('ajax-url-delete-avatar') ;
        var id = $(this).attr('data-id') ;
        
        $.magnificPopup.open({
            items: {src: '#modalFileConfirmDelete'},type: 'inline'
        }, 0);
        
        /* Gestion de confirmation de suppression*/
        $('#modalFileConfirmDelete .btn-modal-confirm-action').unbind('click').bind('click', function(){
            $.ajax({
                type: 'POST',
                url: ajaxUrl,
                dataType: 'json',
                data: {"id" : id},
/*
                success: function(result) {
                    new PNotify({
                        title: 'Notification',
                        type: 'success',
                        text: "Suppression succès",
                        animation: "fade",
                        delay: 6000,
                    });
                    
                    //Suppression de la ligne à supprimer
                    $('#user-avatar').remove() ;
                }
                */
            });
            $('#user-avatar').remove() ;

        });
        
    }) ;
}

});