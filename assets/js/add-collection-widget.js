var $addTagLink = $('<div class="text-right"><a href="#" class="add_tag_link btn btn-info">Ajouter un média</a></div>');

$(document).ready(function() {
    // Get the ul that holds the collection of tags
    var $collectionHolder = $('ul.collections');

    // add the "add a tag" anchor and li to the tags ul
    var $newLinkLi = $('<li class="list-group-item"></li>').append($addTagLink);
    $collectionHolder.append($newLinkLi);
    
    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);
    
    $('div.text-right .add_tag_link').on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        var $newLinkLi = $(this).parent().parent().parent().append($newLinkLi);
        $collectionHolder = $(this).parent().parent().parent();
        
        // add a new tag form (see code block below)
        addTagForm($collectionHolder, $newLinkLi, $(this));
    });
    
    
});

function addTagForm($collectionHolder, $newLinkLi, $elm) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');
    
    // get the new index
    var index = $collectionHolder.data('index');
    
    // Replace '$$name$$' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);
    
    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);
    
    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<li class="list-group-item"></li>').append(newForm);
    
    $newLinkLi.before($newFormLi);

    var type = $collectionHolder.attr('id');
    var type = type.split('-');

    var targetId = 'trick_'+type[0]+'_'+index;

    // also add a remove button, just for this example
    $('#' + targetId + ' div.form-group label').first().append('<a href="#" class="remove-tag ml-2"><i class="fa fa-trash"></i></a>');
    
    
    // handle the removal, just for this example
    $('.remove-tag').click(function(e) {
        e.preventDefault();
        
        $(this).parent().parent().parent().parent().remove();
        
        return false;
    });
}