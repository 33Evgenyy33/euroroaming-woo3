// dropzoneWordpressForm is the configuration for the element that has an id attribute
// with the value dropzone-wordpress-form (or dropzoneWordpressForm)
var currentFile = null;
var intro;

Dropzone.options.dropzoneWordpressForm = {
    //acceptedFiles: "image/*", // all image mime types
    acceptedFiles: ".jpg, .png, .pdf, .doc, .docx", // only .jpg files
    // maxFiles: 1,
    uploadMultiple: true,
    maxFilesize: 5, // 5 MB
    parallelUploads: 1,
    addRemoveLinks: true,
    dictRemoveFile: 'Удалить файл',
    dictCancelUpload: 'Отменить загрузку',
    successmultiple: function (file, response) {
        //console.log(response);
        //fileList.push(String(response));
        fileList.push({"serverFileName": response, "fileName": file[0].name});
        //console.log(fileList);
        jQuery(document).ready(function ($) {

            var result = [];
            $.each(fileList, function (k, v) {
                result.push(v.serverFileName)
            });

            CUSTOMER.additional_fields['uploaded_files'] = result.join();
            CART.calculate_totals();
            console.log(result);
            console.log(CUSTOMER);
        });
    },
    init: function () {
        this.on("sending", function (file, xhr, formData) {
            formData.append("name", "value"); // Append all the additional input data of your form here!
        });
    },
    removedfile: function (file) {
        //console.log(file);
        if (!order_is_created) {
            jQuery(document).ready(function ($) {

                if (fileList.length === 1)
                    $('#dropzone-wordpress-form .needsclick').addClass('hide_needsclick');

                swal({
                    title: 'Удалить загруженный файл?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Да, удалить',
                    cancelButtonText: 'Отмена'
                }).then(function () {

                    var data = '';
                    $.each(fileList, function (index, element) {
                        if (element.fileName === file.name) {

                            fileList.splice(index, 1);

                            var jsonString = element.serverFileName.replace('https://euroroaming.ru/wp-content/uploads/passports-from-tourist/', '');

                            data = {
                                action: 'remove_dropzonejs_file',
                                whatever: jsonString
                            };
                            jQuery.post(ajaxurl, data, function (response) {
                                swal(
                                    'Удалено!',
                                    'Выбранный фал был удален.',
                                    'success'
                                );
                                var result = [];
                                $.each(fileList, function (k, v) {
                                    result.push(v.serverFileName)
                                });
                                CUSTOMER.additional_fields['uploaded_files'] = result.join();
                                CART.calculate_totals();
                                console.log(response);
                                console.log(CUSTOMER);
                            });

                            if (fileList.length === 0) {
                                $('#dropzone-wordpress-form .needsclick').removeClass('hide_needsclick');
                            }

                            var _ref;
                            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                        }
                    });

                });

            });
        } else {
            var _ref;
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
        }

    }
    /*removedfile: function (file) {

     return (_ref = file.previewElement) != null ? ref.parentNode.removeChild(file.previewElement) : void 0;

     }*/
};

function startIntro(){
    intro = introJs();
    intro.setOptions({
        steps: [
            {
                intro: "<h3 style='text-align: center;font-weight: 400;    margin-bottom: 30px;'>Добро пожаловать в кабиент продаж!</h3>" +
                "<p style='text-align: center;font-weight: 300;'><span style='text-align: center;font-weight: 500;'><i class='material-icons'>fiber_new</i> Что нового:</span></p>" +
                "<p style='font-weight: 300;'><i class='material-icons'>keyboard_arrow_right</i> Ваша коммисия теперь высчитывается автоматически" +
                "<p style='font-weight: 300;'><i class='material-icons'>keyboard_arrow_right</i> Добавлена возможность загрузить скан загранпаспорта (теперь можно не отправлять на почту)</p>" +
                "<p style='font-weight: 300;'><i class='material-icons'>keyboard_arrow_right</i> Добавлена возможность сохранить создаваемый заказ и загрузить его позднее</p>" +
                "<p style='font-weight: 300;margin-bottom: 20px;'><i class='material-icons'>keyboard_arrow_right</i> В окне <span style='font-weight: 400'>'Информация о клиенте'</span> появилось поле выбора номера продаваемой сим-карты</p>" +
                "<p style='text-align: center;font-weight: 300;'>Чтобы начать ознакомление нажмите кнопку <span style='text-align: center;font-weight: 500;'>'Вперед'</span></p>"
            },
            {
                element: document.querySelector('#wc-pos-register-grids .tbc'),
                intro: "<h3 style='text-align: center;font-weight: 400;'>Блок <strong>Сим-карты</strong></h3>" +
                "<p style='text-align: center;font-weight: 300;'>В данном блоке Вы можете добавлять сим-карты в заказ.</p>" +
                "<p style='text-align: center;font-weight: 300;'>После нажатия на картинку с сим-карой появляется окно, где необходимо выбрать опции сим-карты</p>",
                position: 'auto'
            },
            {
                element: document.querySelector('#bill_screen'),
                intro: "<h3 style='text-align: center;font-weight: 400;'>Блок <strong>Детали заказа</strong></h3>" +
                "<p style='text-align: center;font-weight: 300;'>В данном блоке Вы можете просмотреть информацию о добавленных сим-картах, а также конечную сумму заказа для Вас и для Клиента</p>",
                position: 'right'
            },
            {
                element: document.querySelector('#register_customer_dates'),
                intro: "<h3 style='text-align: center;font-weight: 400;'>Блок <strong>Детали клиента</strong></h3>" +
                "<p style='text-align: center;font-weight: 300;'>В данном блоке Вы можете добавить информацию о клиенте</p>",
                position: 'right'
            },
            {
                element: document.querySelector('#dropzone-wordpress'),
                intro: "<h3 style='text-align: center;font-weight: 400;'>Элемент <strong>Загрузка документов</strong></h3>" +
                "<p style='text-align: center;font-weight: 300;'>В данном блоке Вы можете загрузить загранпаспорт(а) клиента.</p>" +
                "<p style='text-align: center;font-weight: 300;'>Чтобы загрузить документ перетащите его в эту область или нажмите на нее.</p>" +
                "<p style='text-align: center;font-weight: 300;'>Доступные форматы: <strong>jpg, png, pdf, word</strong></p>",
                position: 'right'
            },
            {
                element: document.querySelector('#add_customer'),
                intro: "<h3 style='text-align: center;font-weight: 400;'>Кнопка <strong>Добавить клиента</strong></h3>" +
                "<p style='text-align: center;font-weight: 300;'>После нажатия на кнопку появится всплывающее окно где вы сможете добавить данные о клиенте и выбрать номер продаваемой сим-карты</p>",
                position: 'right'
            },
            {
                element: document.querySelector('#wc-pos-register-buttons div.tbr'),
                intro: "<h3 style='text-align: center;font-weight: 400;'>Блок <strong>Операции с заказом</strong></h3>" +
                "<p style='text-align: center;font-weight: 300;'>В данном блоке Вы можете совершать различные операции с заказом</p>"+
                "<p style='text-align: center;font-weight: 300;'>Чтобы узнать подробнее нажмите <span style='font-weight:400;'>'Вперед'</span></p>",
                position: 'auto'
            },
            {
                element: document.querySelector('.wc_pos_register_void'),
                intro: "<h3 style='text-align: center;font-weight: 400;'>Кнопка <strong>Аннулировать заказ</strong></h3>" +
                "<p style='text-align: center;font-weight: 300;'>После нажатия на кнопку создаваемый заказ обнулится и Вы сможете начать все с чистого листа :)</p>",
                position: 'auto'
            },
            {
                element: document.querySelector('.wc_pos_register_save'),
                intro: "<h3 style='text-align: center;font-weight: 400;'>Кнопка <strong>Сохранить заказ</strong></h3>" +
                "<p style='text-align: center;font-weight: 300;'>После нажатия на кнопку заказ сохранится и кабинет обновится. Вы сможете загрузить заказ позднее и продолжить его заполнять</p>",
                position: 'auto'
            },
            {
                element: document.querySelector('.wc_pos_register_notes'),
                intro: "<h3 style='text-align: center;font-weight: 400;'>Кнопка <strong>Добавить заметку</strong></h3>" +
                "<p style='text-align: center;font-weight: 300;'>После нажати на кнопку появится всплывающее окно где Вы сможете оставить примечание к заказу в произвольной форме. Например: дополнительные данные о клиентах</p>",
                position: 'auto'
            },
            {
                element: document.querySelector('.wc_pos_register_pay'),
                intro: "<h3 style='text-align: center;font-weight: 400;'>Кнопка <strong>Перейти к оплате</strong></h3>" +
                "<p style='text-align: center;font-weight: 300;'>После нажати на кнопку появится всплывающее окно где Вы сможете выбрать способ оплаты и оплатить заказ</p>",
                position: 'auto'
            },
            {
                element: document.querySelector('#wc-pos-registers-edit #retrieve_sales'),
                intro: "<h3 style='text-align: center;font-weight: 400;'>Кнопка <strong>Загрузить заказ</strong></h3>" +
                "<p style='text-align: center;font-weight: 300;'>После нажати на кнопку появится всплывающее окно где Вы сможете просмотреть Ваши заказы и загрузить сохраненные (они отмечаются статусом 'Ожидание оплаты')</p>",
                position: 'auto'
            },
            {
                element: document.querySelector('#wc-pos-registers-edit #full_screen'),
                intro: "<h3 style='text-align: center;font-weight: 400;'>Кнопка <strong>Во весь экран</strong></h3>" +
                "<p style='text-align: center;font-weight: 300;'>Вы можете развернуть кабинет на весь экран, нажав на эту кнопку</p>",
                position: 'auto'
            },
            {
                element: document.querySelector('#wc-pos-registers-edit #close_register'),
                intro: "<h3 style='text-align: center;font-weight: 400;'>Кнопка <strong>Выйти</strong></h3>" +
                "<p style='text-align: center;font-weight: 300;'>Вы можете выйти из кабинета, нажав на эту кнопку</p>",
                position: 'auto'
            },
            {
                intro: "<h3 style='text-align: center;font-weight: 400;    margin-bottom: 30px;'><i class='material-icons'>check_circle</i> Теперь Вы можете смело перейти к продаже</h3>" +
                "<p style='text-align: center;font-weight: 300;'><span style='text-align: center;font-weight: 500;'>Желаем Вам роста продаж!</span></p>",
            },
        ],
        disableInteraction: true,
        prevLabel: 'назад',
        nextLabel: 'вперед',
        skipLabel: 'выйти',
        showProgress: true,
        showStepNumbers:false,
        doneLabel: 'Начать продавать!'
    });

    intro.onexit(function() {
        intro = null;
    });

    intro.start();
}

/*intro.oncomplete(function() {

});*/


jQuery(document).ready(function ($) {

    /*$("#wc-pos-register-grids #grid_layout_cycle").click(function(){
        console.log('stepppsss');
        intro.goToStep(3).start();
    });*/

    /*$(".wc_pos_register_pay").click(function(){
        console.log('stepppsss');
        intro.goToStep(4).start();
    });*/

    $("#start_tour").click(function(){
        startIntro();
    });
});