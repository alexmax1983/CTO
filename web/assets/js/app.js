$(document).ready(function(){
    // edit news by id
    $(".edit-news").click(function () {
        var id = $(this).attr("itemid");
        var url = "/news/"+id+"/edit";
        $(location).attr('href',url);
    });
    // delete news by id
    $(".delete-news").click(function () {
        var id = $(this).attr("itemid");
        $("#modalDelete").modal({backdrop: true});
        $("#modalDelete h4.modal-title").text("Удаление новости");
        var getTitle = $(this).parent().prev().find("h5").html();
        $("#modalDelete .modal-body").html(getTitle);
        $(".btn-delete-news").click(function () {
            var url = "/news/"+id+"/delete";
            $(location).attr('href',url);
        })
    });
    // create news
    $(".create-news").click(function(){
        var url = "/news/create";
        $(location).attr('href',url);
    });
    // delete author
    $(".delete-author").click(function(){
        var id = $(this).attr("itemid");
        $("#modalDeleteAuthor").modal({backdrop: true});
        $("#modalDeleteAuthor h4.modal-title").text("Удаление Автора");
        var getTitle = $(this).parent().prev().prev().html();
        $("#modalDeleteAuthor .modal-body").html(getTitle);
        $(".btn-delete-author").click(function () {
            var url = "/author/"+id+"/delete";
            $(location).attr('href',url);
        })
    });
    // edit author
    $(".edit-author").click(function(){
        var id = $(this).attr("itemid");
        var url = "/author/"+id+"/edit";
        $(location).attr('href',url);
    });
    // create author
    $(".create-author").click(function(){
        var url = "/author/create";
        $(location).attr('href',url);
    });
});