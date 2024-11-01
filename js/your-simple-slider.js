function sliderheight(){
    var slider = jQuery('.flexslider');
    var heightunits = slider.data('height-units');
    var sliderwidth = slider.width();

    slider.each(function() {
        var heightunits = jQuery(this).data('height-units');
        if (heightunits == '%'){
            var sliderheight = jQuery(this).data('slider-height');
            jQuery(this).find('.slides .slider-item > div').css('height', jQuery(this).width() * sliderheight / 100);
        }
                
    });
}

jQuery(document).ready(function() {
    sliderheight();
    new ResizeSensor(jQuery('.flexslider .slides .slider-item'), function(){ 
        sliderheight();
    });
});
