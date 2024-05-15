function vulcanCaptcha(id, images)
{
    let img = document.querySelector('#captcha-' + id + ' img'),
        button = document.querySelector('#captcha-' + id + ' button'),
        input = document.querySelector('#captcha-' + id + ' input[required]'),
        serial =  document.querySelector('#captcha-' + id + ' input[type="hidden"]');

    button.addEventListener('click', function(){
        if(serial.value == (images.length - 1)){
            serial.value = 0;
        }else{
            serial.value = parseInt(serial.value) + 1;
        }
        
        img.setAttribute('src', images[serial.value]);
        input.value = '';
    });
}
