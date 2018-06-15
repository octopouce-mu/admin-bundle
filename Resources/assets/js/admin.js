require('materialize-css');
// require jQuery normally
const $ = require('jquery');

// create global $ and jQuery variables
global.$ = global.jQuery = $;


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
    $('select').formSelect();
    $('.tooltipped').tooltip();
    $('.datepicker').datepicker({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 15, // Creates a dropdown of 15 years to control year,
        today: 'Today',
        clear: 'Clear',
        close: 'Ok',
        format: 'yyyy/mm/dd'
    });
    $('.timepicker').timepicker({
        twelvehour: false, // Use AM/PM or 24-hour format
        donetext: 'OK', // text for done-button
        cleartext: 'Clear', // text for clear-button
        canceltext: 'Cancel', // Text for cancel-button
        format: 'HH:i'
    });
});


