<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/AdminLTE.min.js"></script>
<script src="../assets/js/slimScroll/jquery.slimscroll.min.js"></script>
<script src="../assets/js/toastr/toastr.min.js"></script>
<script src="../assets/js/lets-dev.min.js"></script>
<script>
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    (function ($) {
        $.fn.markdown.messages['fr'] = {
            'Bold': "Gras",
            'Italic': "Italique",
            'Heading': "Titre",
            'URL/Link': "Insérer un lien HTTP",
            'Image': "Insérer une image",
            'List': "Liste à puces",
            'Preview': "Voir",
            'strong text': "texte important",
            'emphasized text': "texte souligné",
            'heading text': "texte d'entête",
            'enter link description here': "entrez la description du lien ici",
            'Insert Hyperlink': "Insérez le lien hypertexte",
            'enter image description here': "entrez la description de l'image ici",
            'Insert Image Hyperlink': "Insérez le lien hypertexte de l'image",
            'enter image title here': "entrez le titre de l'image ici",
            'list text here': "texte à puce ici"
        };
    }(jQuery))
</script>