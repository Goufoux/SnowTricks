$(document).ready(function() {

    // Loader
    $('.loaded').fadeIn(1500, function() {
        $('.preloader').remove();
        replaceFooter();
    });

    // Enable tooltip
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    // set homepage-section full 
    let $homepageSection = $('#homepage-section');

    if ($homepageSection.length > 0) {
        let $homepageSection_Height = $homepageSection.height();
        let client_Height = $(window).height();
        let navbarHeight = $('#mainNavbar').height();
        let $homepageSection_newHeight = client_Height - (navbarHeight+20) - 10;
        $homepageSection.css('height', $homepageSection_newHeight+'px');

        $homepageArrow = $('#homepage-arrow');

        if ($homepageArrow.length == 0) {
            return;
        }

        $(window).on('scroll', function () {
            if (isScrolledIntoView($('#trick-section'))) {
                $('#homepage-arrow').addClass('r180');
            } else {
                $('#homepage-arrow').removeClass('r180');
            }
        })

        if (isScrolledIntoView($('#trick-section'))) {
            $('#homepage-arrow').addClass('r180');
        }

        // Gestion en fonction du scroll
        $('#homepage-arrow').on('click', function() {
            let isVisible = isScrolledIntoView($('#trick-section'));
            // this.console.log('trick section est visible : ' + isVisible)
            if (isVisible) {
                // $('#homepage-arrow').attr('href', '#body');
                $([document.documentElement, document.body]).animate({
                    scrollTop: $('#body').offset().top
                }, 1500);
                $('#homepage-arrow').removeClass('r180');
            } else {
                $([document.documentElement, document.body]).animate({
                    scrollTop: $('#trick-section').offset().top
                }, 1500);
                $('#homepage-arrow').addClass('r180');
            }
        })
    }

    // load more comment 
    $('#loadMore').on('click', function() {
        let nbComments = $('#trick-comments .row .comment-elm').length;
        let key = $('#trick-comments').data('key');
        $.get({
            url: '/trick/'+key+'/comments/'+5+'/'+nbComments,
            success: function (data) {
                if (data.length == 0) {
                    $('#loadMore').slideUp('slow', function() {
                        $('#loadMore').remove();
                    });
                }
                $('#trick-comments #loadMore').before(data);
                nbComments = $('#trick-comments .row .comment-elm').length;
                $('#trick-comments .row').attr('data-length', nbComments);
            },
            error: function (e) {
                console.log(e.responseText);
            }
        })
    })

    // Preload img 
    $("form").on("change", ".custom-file-input", function () {
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

    // remove trick 
    $('.delete-trick').on('click', function(e) {
        
        $elm = $(this);

        const onConfirm = function() {
            $.get({
                url: '/trick/remove/'+$elm.data('key'),
                success: function (data) {
                    $('#'+$elm.data('target')).slideUp('slow', function () {
                        $('#'+$elm.data('target')).remove();
                    });
                },
                error: function (error) {
                    console.log('Erreur');
                } 
            })
        };
        
        confirmDialog('Confirmation requise', 'Êtes-vous sûr de vouloir supprimer ce trick ?', onConfirm);
    });

    $('.delete-trick-redirect').on('click', function(e) {
        e.preventDefault();
        let link = $(this).attr('href');
        const onConfirm = function() {
            window.location.href = link;
        }
        confirmDialog('Confirmation requise', 'Êtes-vous sûr de vouloir supprimer ce trick ?', onConfirm);
    })
    
    // remove an image
    $('.delete-image').on('click', function() {
        var $this = $(this);
        var key = $this.data('key');
        var elm = $this.data('elm');
        var type = $this.data('type');
        $.get({
            url: "/"+elm+"/"+type+"/remove/"+key,
            success: function (data) {
                $this.fadeOut();
            },
            error: function(e) {
                console.log("error", e);
            }
        });
    });

    // remove video
    $('.delete-video').on('click', function() {
        var $this = $(this);
        var key = $this.data('key');
        $.get({
            url: "/trick/video/remove/"+key,
            success: function (data) {
                $('#iframe-'+key).fadeOut('slow', function () {
                    $('#iframe-'+key).remove();
                    if ($('.video-bloc .bloc-iframe').length <= 0) {
                        $('.video-bloc').append($('<div class="alert alert-info"><h5 class="alert-heading">Aucune vidéo</h5></div>'));
                    }
                });
            },
            error: function (e) {
                console.log('error', e.responseJSON);
            }
        });
    });
});

function confirmDialog(title, message, onSuccess) {
    $.confirm({
        title: title,
        theme: 'dark',
        type: 'red',
        useBootstrap: true,
        bootstrapClasses: {
            container: 'container',
            containerfluid: 'container-fluid',
            row: 'row'
        },
        content: message,
        buttons: {
            supprimer: {
                btnClass: 'btn-danger',
                action: onSuccess
            },
            annuler: {
                btnClass: 'btn-info'
            }
        }
    })
}

function isScrolledIntoView(elem)
{
    var docViewTop = $(window).scrollTop();
    var docViewBottom = docViewTop + $(window).height();

    var elemTop = $(elem).offset().top;
    var elemBottom = elemTop + $(elem).height();

    return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
}

function replaceFooter() {
    $(function() {
        var body = document.body,
            html = document.documentElement;
        var wh = window.innerHeight;

        var height = Math.max( body.scrollHeight, body.offsetHeight, 
                       html.clientHeight, html.scrollHeight, html.offsetHeight );

        if (wh == height) {
            $("footer").addClass('footer-fix');
        }
        $('footer').animate({
            'opacity': 1
        }, 750);
    });
}