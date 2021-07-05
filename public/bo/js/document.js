$(document).ready(function(){
    
    
    if($('.add-file').length > 0){
        /*Gestion ajout upload fichier*/
        $('.add-file').unbind('click').bind('click', function(){
            $('.file-clone').clone().appendTo("#file-content");
            $( ".file-clone" ).not( ":nth-child(2n)" ).removeClass('hidden') ;
            
            /*Gestion suppression upload fichier*/
            $('.remove-file').unbind('click').bind('click', function(){
                $(this).parent().remove() ;
            });
        });
        
    }
    
    /*Liste de document*/
    /* https://datatables.net/examples/api/row_details.html */
    if($('#document-list-datatable').length > 0){
        var tableElement = '#document-list-datatable';
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
                    data.listType = $("#document-list-type").val() ;
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
                /*Suppression de document*/
                deleteDocument() ;
                
                /*Gestion changement de droit de document pour un utilateur*/
                changeDocumentPermission() ;

                bindEvent();
                
                /*Affichage des fichiers*/
            /*    showFiles() ; */
                
            }
        });
    }
    
    /*Gestion de tree*/
    if($('#document-list-tree').length > 0){
        var ajaxUrl = $('#document-list-tree').attr('ajax-url') ;
        
        $('#document-list-tree').jstree({
            'core' : {
                    'themes' : {
                            'responsive': true
                    },
                    'check_callback' : true,
                    'data' : {
                            'url' : function (node) {
                              return ajaxUrl;
                            },
                            'data' : function (node) {
                              return { 'parent' : node.id };
                            }
                    },
                    
            },
            'types' : {
                    'default' : {
                            'icon' : 'fa fa-folder'
                    },
                    'file' : {
                            'icon' : 'fa fa-file'
                    }
            },
            'plugins': ['types']
        });
    }
    
    /*Gestion d'affichage du fichier dans le bibliothèque*/
    if($('.jstree-anchor').length > 0){
        
        $('#document-list-tree').on("select_node.jstree", function (e, data) {
            //alert("node_id: " + data.node.id); 
            
            var id = data.node.id ;
            
            var url = $('#'+id+' > a.jstree-anchor').attr('href') ;
            
            if(url !== '#'){
                window.open( url,'_blank' );
            }
        });
        
    }
    
    /*Gestion suppression de fichier*/
    if($('.btn-delete-file').length > 0){
        $('.btn-delete-file').unbind('click').bind('click', function(){
            var file = $(this).attr('file') ;
            
            var element = $(this) ;
            $.magnificPopup.open({
                items: {src: '#modalFileConfirmDelete'},type: 'inline'
            }, 0);
            
            /* Gestion de confirmation de suppression*/
            $('#modalFileConfirmDelete .btn-modal-confirm-action').unbind('click').bind('click', function(){
                var ajaxUrl = $('#document-file').attr('ajax-url') ;
                $.ajax({
                    type: 'POST',
                    url: ajaxUrl,
                    dataType: 'json',
                    data: {"file" : file},

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
            });
        });
    }
    
    /**
     * Add sub child row to show file
     * @param {type} d
     * @returns {String}
     */
    function showFiles(){
        $('.btn-show-files').unbind('click').bind('click', function(){
            $(this).addClass('toogle-files') ;
            var document = $(this).attr('document') ;
            var user = $('#document-list-datatable').attr('user') ;
            var parent = $(this).parent().parent() ;
            if($('.files-'+document+'').length === 0){
                
                $.ajax({
                    type: 'POST',
                    url: $('#document-list-datatable').attr('ajax-url-file-document'),
                    dataType: 'html',
                    cache: false,
                    data: {"document" : document, "user" : user},
                    success: function(result) {
                        
                        $(result).insertAfter(parent);
                        
                        $('.toogle-files').unbind('click').bind('click', function(){
                            var document = $(this).attr('document') ;
                            $('.files-'+document+'').toggle() ;
                        });
                        
                        /*CHange fiel permission*/
                        changeFilePermission() ;
                    }
                });
            }
        });
        
    }
    
    /**
     * Change file permission
     * @returns {undefined}
     */
    function changeFilePermission(){
        $('.chbox-file').unbind('click').bind('click', function(){
            var file = $(this).attr('file') ;
            var action = $(this).attr('action') ;
            var user = $('#document-list-datatable').attr('user') ;
            var ajaxUrl = $('#document-list-datatable').attr('ajax-url-file-permission') ;
            
            var isCheck = 0 ;
            if($(this).is(":checked")){
                var isCheck = 1 ;
            }else{
                var isCheck = 0 ;
            }
            
            //Si on clique sur download + check true, on check à true aussi la lecture
            if(action == 'download' && isCheck == 1){
                if($("#chbox-file-read-"+file+"").is(":checked")){
                    /**/
                }else{
                   $("#chbox-file-read-"+file+"").trigger('click') ;
                }
            }
            
            //Si on clique sur read + check false, on check à false aussi le téléchargement
            if(action == 'read' && isCheck == 0){
                if($("#chbox-file-download-"+file+"").is(":checked")){
                    $("#chbox-file-download-"+file+"").trigger('click') ;
                }else{
                   /**/
                }
            }
            
            $.ajax({
                type: 'POST',
                url: ajaxUrl,
                dataType: 'json',
                data: {"user" : user, "file" : file, "action" : action , "value" : isCheck},

                success: function(result) {
                    new PNotify({
                        title: 'Notification',
                        type: 'success',
                        text: "Enregistrement succès",
                        animation: "fade",
                        delay: 6000,
                    });

                }
            });
        });
    }
    
    /**
     * Delete document
     * @returns {undefined}
     */
    function deleteDocument(){
        $('.btn-delete').unbind('click').bind('click', function(){
            
            var ajaxUrl = $(this).attr('ajax-url') ;
            var id = $(this).attr('data-id') ;
            var element = $(this) ;
            $.magnificPopup.open({
                items: {src: '#modalDocumentConfirmDelete'},type: 'inline'
            }, 0);
            
            /* Gestion de confirmation de suppression*/
            $('#modalDocumentConfirmDelete .btn-modal-confirm-action').unbind('click').bind('click', function(){
                $.ajax({
                    type: 'POST',
                    url: ajaxUrl,
                    dataType: 'json',
                    data: {"id" : id},

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
            });
            
        });
    }
    
    /**
     * Change document permission
     * @returns {undefined}
     */
    function changeDocumentPermission(){
        $('.chbox-document').unbind('click').bind('click', function(){
            var document = $(this).attr('document') ;
            var action = $(this).attr('action') ;
            var user = $('#document-list-datatable').attr('user') ;
            var ajaxUrl = $('#document-list-datatable').attr('ajax-url-permission') ;
            
            var isCheck = 0 ;
            if($(this).is(":checked")){
                var isCheck = 1 ;
            }else{
                var isCheck = 0 ;
            }
            
            //Si on clique sur download + check true, on check à true aussi la lecture
            if(action == 'download' && isCheck == 1){
                if($("#chbox-document-read-"+document+"").is(":checked")){
                    /**/
                }else{
                   $("#chbox-document-read-"+document+"").trigger('click') ;
                }
            }
            
            //Si on clique sur read + check false, on check à false aussi le téléchargement
            if(action == 'read' && isCheck == 0){
                if($("#chbox-document-download-"+document+"").is(":checked")){
                    $("#chbox-document-download-"+document+"").trigger('click') ;
                }else{
                   /**/
                }
            }
            
            $.ajax({
                type: 'POST',
                url: ajaxUrl,
                dataType: 'json',
                data: {"user" : user, "document" : document, "action" : action , "value" : isCheck},

                success: function(result) {
                    new PNotify({
                        title: 'Notification',
                        type: 'success',
                        text: "Enregistrement succès",
                        animation: "fade",
                        delay: 6000,
                    });

                }
            });
        });
    }
    
    /**
     * Add table in data
     * @returns {undefined}
     */
    function bindEvent(){
        $(".document-list-type").unbind('change').bind('change', function(){
            //var value = $(this).val() ;
            $('#document-list-datatable').DataTable().ajax.reload();     
        });

    }


    
}) ;
    
 