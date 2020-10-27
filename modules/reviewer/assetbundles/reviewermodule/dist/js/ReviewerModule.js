/**
 * reviewer module for Craft CMS
 *
 * reviewer JS
 *
 * @author    Eugen Juhos
 * @copyright Copyright (c) 2019 Eugen Juhos
 * @link      eugen@juhos.sk
 * @package   ReviewerModule
 * @since     1.0.0
 */
$(document).ready(function () {
    var stepSlider  = $('.slider');
    var sliders     = [];
    $.each(stepSlider, function (k,slider) {
        var questionKey = $(slider).data('question');

        var sliderContainer = $('#'+questionKey)[0];
        noUiSlider.create(sliderContainer, {

            start: [5],
            step: 1,
            range: {
                'min': [1],
                'max': [10]
            },
            pips: {
                mode: 'steps',
                density: 10,

            }
        });

        sliderContainer.noUiSlider.on('update', function (values, handle) {
            var input   = $('input[name="questions['+questionKey+']"]');
            var inputValue = values[handle];
            $(input).val(inputValue);

            var commentContainer = $(input).parents('fieldset').find('.comment');
            if (inputValue < 7){
                $(commentContainer).addClass('comment--show');
            } else{
                $(commentContainer).removeClass('comment--show');
            }
        });

        sliders[$(slider).data('question')] = sliderContainer;

    });


    var radioBtn    = $('input[type="radio"]');



    radioBtn.on('click',function (e) {
        console.log($(this).hasClass('chainable'));
        var chainKey        = $(this).data('chain');
        var toChain         = $('.question__block').filter(function (block) {
            if ($(this).data('question') === chainKey){
                return block;
            }
        }).first();


        if ($(this).data('chainparent') < 1){
            $(toChain).hide();
        } else{
            $(toChain).show();
        }
        // console.log('chainKey',chainKey);
        // console.log('toChain',toChain);

    });

});
