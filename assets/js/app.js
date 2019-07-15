$(document).ready(function() {

    // Enable tooltip
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    // Preload img 
    $("form").on("change", ".custom-file-input", function () {
        console.log('file changed');
        let $input = $(this);
        let fileName = $input.val().split("\\").pop();
        $input.siblings(".custom-file-label").html(fileName);

        // Afficher un aperçu de l'image sélectionnée
        let files = this.files;
        if (files == undefined || files.length == 0) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            $parent = $input.parent().parent();

            $previewEl = $parent.children('.preview-photo');
            if ($previewEl.length == 0) {
                $parent.append('<img class="preview-photo img-fluid" src="' + e.target.result + '" alt="prévisualisation de l\'image">')
            } else {
                $previewEl.attr('src', e.target.result);
            }
        }
        reader.readAsDataURL(files[0]);
    });
    
    // remove an image
    $('.delete-image').on('click', function() {
        var $this = $(this);
        var user = $this.data('user');
        $.get({
            url: "/user/avatar/remove/"+user,
            success: function (data) {
                $this.fadeOut();
            },
            error: function(e) {
                console.log("error", e);
            }
        })
    })

});