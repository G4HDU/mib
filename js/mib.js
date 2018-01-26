




$("#myMibModal").on("hide.bs.modal", function(e) {
//$("#addressbookPage").show();
      // $("#bodyModal").hide();
    //  $('#myMibModal').show(); 
      // $("#bodyModal").text("");  
   
});
$("#myMibModal").on("show.bs.modal", function(e) {
    $("#ajaxSpinner").show();
  //  $("#addressbookPage").hide();
     //  $("#bodyModal").text("");  
      // $("#bodyModal").hide();
      // $('#addrLoader').show(); 
   //    <img src="images/loader.gif" id="aload"/>
   
});
$( document ).ready(function() {
   // var myStylesLocation = "/e107_plugins/mib/css/mib.css";

    $(".mibRow").on('click', function(event){
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();
        var editURL=$(this).attr('href');
       // var editID = $(this).attr('class').replace('mibRow editID', ''); // get the id of the reord
        var ajaxIt=editURL.replace('ajax=false','ajax=true')
        $('#myMibModal').modal('toggle');
        $("#modalContent").hide(0);
        $("#ajaxSpinner").show(0);
        console.log(ajaxIt);
        $("#modalContent").load(ajaxIt,function(){
            $("#ajaxSpinner").fadeOut(700,function(){
                $("#modalContent").fadeIn(1100);
                });  // end ajax spinner    
       });  // end modalContent load
    });  // end addressbookrow click
});  // end document ready