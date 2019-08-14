// Create a new object for custom validation of a custom field.
var formLoadedController = Marionette.Object.extend( {
    
    initialize: function() {
      this.listenTo( nfRadio.channel( 'form' ), 'render:view', this.doCustomInitialization );
    },
  
    doCustomInitialization: function( view ) {
        var formModel = view.model; // formModel will be a Backbone model with all of our form data.
        var formID = formModel.get( 'id' ); // We can use .get( 'setting' ) get get any of our form settings.

        // Loop through filetrip_options array and initialize every single uploader
         jQuery.each(formModel.get('loadedFields'),function(index,field_obj){
             if(field_obj.type=='filetrip'){
                 initialize_filetrip_uploader(field_obj.filtrip_shortcode,formModel.id)
             }
             
         })


       
    },
  
  });
  
  // On Document Ready...
  jQuery( document ).ready( function( $ ) {
      // Instantiate our custom controller, defined above.
      new formLoadedController();
  });

// Function to initialize uploaders
function initialize_filetrip_uploader(uploader_id,form_id)
{


    var fieldValue = [], id = '#multi-'+uploader_id+'-'+form_id,getObject={};

    successUpload = function(uploadedItem){
        fieldValue.push(uploadedItem.attid);
        jQuery('.ninja_filetrip_'+form_id).val(fieldValue.toString());
    }

    deleted = function(deletedItem){
        var deletedIndex = deletedItem.fileIndx;
        fieldValue.splice(deletedIndex,1);
    
        jQuery('.ninja_filetrip_'+form_id).val(fieldValue.toString());
    }

    // Dynamically pass the uploader ID as option index
    jQuery.each(filetrip_options, function(index,field_obj){
        if(index == uploader_id){
            getObject = JSON.parse(field_obj);
        }

    })

    setObject={
        successfulUpload:successUpload,
        fileDeleted:deleted
    };

    options=jQuery.extend({},getObject,setObject);




    jQuery(id).arfaly(options);
}