$(document).ready(function() {

    const MaxTrickLoaded = 15;
    const MaxCommentLoaded = 5;

    let removeLoader = true;

    let $homepageSection = $('#homepage-section');
    let $trickSection = $('#trick-section');
    let $trickContainer = $('#trick-container');

    if ($trickSection.length > 0 && $('#trick-medias .trick-media').length > 0) {
        if (detectmob()) {
            $('#trick-medias .row').first().append('<div><button id="loadMedia" class="btn btn-info">Charger les images</button></div>');
            $('#trick-medias .row .trick-media').hide();
        
            $('#loadMedia').on('click', function (e) {
                e.preventDefault();
                $('#trick-medias .row .trick-media').slideDown('slow', function () {
                    $('#loadMedia').parent().remove();
                    replaceFooter();
                });
            })
        }
    }
    
    // set homepage-section full
    if ($homepageSection.length > 0) {
        removeLoader = false;
        let client_Height = $(window).height();
        let navbarHeight = $('#mainNavbar').height();
        let $homepageSection_newHeight = client_Height - (navbarHeight+20) - 10;
        $homepageSection.animate({
            height: $homepageSection_newHeight+'px'
        }, 1500, function () {
            if ($('#trick-section').hasClass('hide')) {
                $('#trick-section').fadeIn();
                replaceFooter();
            }
            if ($('#trick-container').hasClass('hide')) {
                $('#trick-container').fadeIn();
                replaceFooter();
            }
        });

        /* Remove the loader, after set height */
        $('.loaded').fadeIn(1500, function() {
            $('.preloader').remove();
            replaceFooter();
        });

        $homepageArrow = $('#homepage-arrow');

        if ($homepageArrow.length > 0) {
            $(window).on('scroll', function () {
                if (isScrolledIntoView($trickContainer)) {
                    $('#homepage-arrow').addClass('r180');
                } else {
                    $('#homepage-arrow').removeClass('r180');
                }
            });
    
            // Gestion en fonction du scroll
            $('#homepage-arrow').on('click', function() {
                let isVisible = isScrolledIntoView($trickContainer);
                if (isVisible) {
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $('#body').offset().top
                    }, 1500);
                    $('#homepage-arrow').removeClass('r180');
                } else {
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $trickContainer.offset().top
                    }, 1500);
                    $('#homepage-arrow').addClass('r180');
                }
            });
        }
    }

    /* Homepage trick container */
    if ($trickContainer.length > 0) {
        let trickContainerLength = $trickContainer.data('length');

        // Load more tricks ?
        if (trickContainerLength >= MaxTrickLoaded) {
            $trickContainer.children().append('<div class="col-12 text-center"><button id="loadMoreTricks" data-toggle="tooltip" title="Charger plus de trick ?" class="btn btn-info">Plus de tricks !</button></div>');
        }

        $trickContainer.on('click', '#loadMoreTricks', function () {
            $('.tooltip').remove();
            trickContainerLength = $trickContainer.data('length')
            $.get({
                url: "/trick/load/" + MaxTrickLoaded + "/" +trickContainerLength,
                success: function (data) {
                    if (data == false) {
                        $('#loadMoreTricks').slideUp('slow', function () {
                            $(this).remove();
                        });
                        return;
                    }
                    $('#loadMoreTricks').parent().before(data);
                    const newLength = $('.trick-bloc').length;

                    if (newLength > MaxTrickLoaded && trickContainerLength == MaxTrickLoaded) {
                        $('#loadMoreTricks').parent().append('<div id="trick-arrow" class="bg-dark"><i class="fa fa-arrow-up fa-2x text-white"></i></div>')
                        $('#trick-arrow').on('click', function () {
                            $([document.documentElement, document.body]).animate({
                                scrollTop: $trickContainer.offset().top
                            }, 1500);
                        })
                    }

                    $trickContainer.data('length', newLength);
                },
                error: function (e) {
                    console.log(e.responseText)
                }
            })
        })
    }

    if (removeLoader) {
        // Loader
        $('.loaded').fadeIn(1000, function() {
            $('.preloader').remove();
            replaceFooter();
        });
    }

    // Enable tooltip
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });


    if ($('#trick-comments .row').data('length') >= MaxCommentLoaded) {
        $('#trick-comments .row').append('<div class="text-center col-12 my-2"><button id="loadMore" class="btn btn-info">Afficher plus de commentaire</button></div>');
    }

    // load more comment 
    $('#loadMore').on('click', function() {
        let nbComments = $('#trick-comments .row .comment-elm').length;
        let key = $('#trick-comments').data('key');
        $.get({
            url: '/trick/'+key+'/comments/'+MaxCommentLoaded+'/'+nbComments,
            success: function (data) {
                if (data == false) {
                    $('#loadMore').slideUp('slow', function() {
                        $('#loadMore').parent().remove();
                    });
                }
                $('#trick-comments #loadMore').parent().before(data);
                nbComments = $('#trick-comments .row .comment-elm').length;
                $('#trick-comments .row').attr('data-length', nbComments);
                replaceFooter();
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
        e.preventDefault();
        $elm = $(this);

        const onConfirm = function() {
            $.get({
                url: '/trick/remove/'+$elm.data('key'),
                success: function (data) {
                    if ($elm.data('toindex') == true) {
                        window.location.href = '/';
                    } else {
                        $('#'+$elm.data('target')).slideUp('slow', function () {
                            $('#'+$elm.data('target')).remove();
                        });
                    }
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
                $this.parent().hide('slow', function () {
                    $this.parent().remove();
                });
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
                $('#iframe-'+key).hide('slow', function () {
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

        const $trickContainer = $('#trick-section');

        console.log($trickContainer);

        if ($trickContainer.length) {
            let trickContainerHeight = $trickContainer.height() + 150;
            console.log('container de trick detected, footer position on y : ' + trickContainerHeight+'px')
            if (trickContainerHeight >= wh) {
                $('footer').css('top', trickContainerHeight+'px');
                $('footer').css('position', 'absolute');
            }


        }

        if (wh == height) {
            $("footer").addClass('footer-fix');
        }
        $('footer').animate({
            'opacity': 1
        }, 750);
    });
}

function detectmob() { 
    if( navigator.userAgent.match(/Android/i)
    || navigator.userAgent.match(/webOS/i)
    || navigator.userAgent.match(/iPhone/i)
    || navigator.userAgent.match(/iPad/i)
    || navigator.userAgent.match(/iPod/i)
    || navigator.userAgent.match(/BlackBerry/i)
    || navigator.userAgent.match(/Windows Phone/i)
    ){
       return true;
     }
    else {
       return false;
     }
   }