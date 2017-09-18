var id =-1;
$(document).ready(function(){
    $('form').on('submit',function(){
        $($('.relations:hidden')[0]).remove();
    });

    function delrel(){
        var id_element_to_remove = $(this).attr('id');
        $('#div_relations').children('[id="'+id_element_to_remove+'"]').remove();
    }
    id = $($('.relations:hidden')[0]).attr('id');
    $('.relation_delete').on('click',delrel);

    $('.relation_add').on('click',function(){
        var hidden_el = $('.relations:hidden')[0];
        var oldID = $(hidden_el).attr('id');
        var newID =id-1;
        id = newID;
        var newDiv = $(hidden_el).clone(true)[0];
        changId(newDiv,oldID,newID);
        massChangID($(newDiv).children(),oldID,newID);
        $($(newDiv).find('.relation_delete')[0]).on('click',delrel);
        $(this).after(newDiv);
        $(newDiv).show();
        reinitCkeditor();
    });

    var ckeditor_reinit_elm =[];

    function changId(element,idOld,idNew){
        if($(element).attr('id')!= undefined) {
            var id = $(element).attr('id');
            var newID =  id.replace(idOld, idNew);
            $(element).attr('id', newID);
        }
        if($(element).attr('name') != undefined){
            var name= $(element).attr('name');
            var newName = name.replace(idOld,idNew);
            $(element).attr('name',newName);
            // console.log(element);
        }
        if($(element).attr('for') != undefined){
            var oldfor= $(element).attr('for');
            var newFor = oldfor.replace(idOld,idNew);
            $(element).attr('for',newFor);
        }
        if($(element).attr('href') != undefined){
            var oldfor= $(element).attr('href');
            var newFor = oldfor.replace(idOld,idNew);
            $(element).attr('href',newFor);
        }
        if($(element).attr('value') === idOld){
            $(element).val(idNew);
        }
        if($(element).hasClass('cke_browser_webkit')){
            var parent = element.parentElement;
            var cur= $(parent).children('textarea')[0];
            $(element).remove();
            ckeditor_reinit_elm.push($(cur).attr('id'));
        }
    }

    function reinitCkeditor(){
        $(ckeditor_reinit_elm).each(function(index,el){
            var editor = CKEDITOR.instances[el];
            // console.log(el);
            if(editor){
                editor.destroy(true);
            }
            CKEDITOR.replace(el);
        });
        ckeditor_reinit_elm=[];
    }

    function massChangID(arrElements,idOld,idNew){
        // console.log(arrElements);
        $(arrElements).each(function(){
            changId(this,idOld,idNew);
            if($(this).children().length>0)
            {
                massChangID($(this).children(),idOld,idNew);
            }
            else{
                changId($(this).children(),idOld,idNew);
            }
        });
    }

});
