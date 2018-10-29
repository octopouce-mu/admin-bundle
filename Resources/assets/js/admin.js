require('materialize-css');
require('chart.js');


// require jQuery normally
const $ = require('jquery');

// create global $ and jQuery variables
global.$ = global.jQuery = $;
jQuery.expr[':'].regex = function(elem, index, match) {
    var matchParams = match[3].split(','),
        validLabels = /^(data|css):/,
        attr = {
            method: matchParams[0].match(validLabels) ?
                matchParams[0].split(':')[0] : 'attr',
            property: matchParams.shift().replace(validLabels,'')
        },
        regexFlags = 'ig',
        regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
}

$(".hamburger").on('click', function(){
    var sidebarLeft = $('.sidebar-left');
    if(sidebarLeft.hasClass('open')){
        sidebarLeft.removeClass('open');
        $('.main').removeClass('sidebar-open');
        $('body').removeClass('open')
    }else {
        sidebarLeft.addClass('open');
        $('.main ').addClass('sidebar-open');
        $('body').addClass('open')
    }

});

$(".hamburger-menu").on('click', function(){
    var dropdown = $('.dashboard-filter .right');
    if(dropdown.hasClass('open')) {
        dropdown.removeClass('open');
    }
    else{
        dropdown.addClass('open');

    }
});

$(document).ready(function(){
   // Materialize.toast('I am a toast!', 10000);
    setMateralize();

    $('input:regex(name, slug)').each(function(index, value) {
        var id = value.id;
        var target = $(this);
        $('input#'+id.replace('slug', 'name')).bind('keyup change', function() {
            var source = $(this).val();
            if(source.length > 0) {
                var slug = slugify(source);
                target.val(slug);
            }
        });
        $('input#'+id.replace('slug', 'title')).bind('keyup change',function() {
            var source = $(this).val();
            if(source.length > 0) {
                var slug = slugify(source);
                target.val(slug);
            }
        });

    });
});
$('.add-collection-widget').on('click', function() {
   setMateralize();
});

function setMateralize() {
    $('select:not(".select2")').formSelect();

    $('.select2').select2();

    $('select[data-increment=true]').select2({
        tags: true
    });

    $('.tabs').tabs();
    $('.tooltipped').tooltip();
    $('.datepicker').datepicker({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 2, // Creates a dropdown of 15 years to control year,
        today: 'Today',
        clear: 'Clear',
        close: 'Ok',
        format: 'yyyy/mm/dd',
        autoClose: true,
        defaultDate: 'now'
    });
    $('.timepicker').timepicker({
        twelveHour: false, // Use AM/PM or 24-hour format
        donetext: 'OK', // text for done-button
        cleartext: 'Clear', // text for clear-button
        canceltext: 'Cancel', // Text for cancel-button
        defaultTime: 'now',
        autoClose: true
    });

    setEditor();
}

function setEditor() {
    var allEditors = document.querySelectorAll('.editor');
    for (var i = 0; i < allEditors.length; ++i) {
        ClassicEditor.create(allEditors[i], {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'alignment', 'bulletedList', 'numberedList', 'code', '|', 'undo', 'redo' ],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5' }
                ]
            }
        });
    }
}

function slugify(text)
{
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
}
