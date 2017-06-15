$(document).ready(function () {

    // АУДИО: создаем объект
    var objAudio = new audio();

    // АУДИО: инициализируем
    objAudio.init();


});

// ЛОГИКА: вся логика для аудио
function audio() {

    // ТЕГ <АУДИО>: находим наше аудио
    var audio = document.getElementById('audio');

    // КНОПКА: вкл/выкл
    var btn = '#sound-btn';

    // ТРИГГЕР: на главной аудио нужно, на следующем шаге - нет.
    // Кликают этот элемент, для следующего шага. Мы же по нему выключаем аудио.
    var trigger = '.a-text-want-hidden';

    // КОНСТРУКТОР: - Он тут главный!
    this.init = function () {
        this.go(); // ЗАПУСК:
        this.onOff(); // ВКЛ/ВЫКЛ:
        this.loop(); // ПО КРУГУ: зацикливание аудио
    };

    // (1) ЗАПУСК:
    this.go = function() {

        /**
         * Важно!
         * Чтобы принимать GET параметр, нужно подключать файл 'location.js'
         * который содержит необходимый метод в объекте 'Hash'.
         */

        // Если GET параметр 'sound=0', то дальше не продолжаем
        if (Hash.get().sound === '0') return false;

        // ЗАПУСК: стартуем аудио
        audio.play();

    };

    // (2) ПО КРУГУ:
    this.loop = function() {

        // (2.1) ПО КРУГУ: когда аудио закончиться, то
        audio.addEventListener('ended', function() {

            // ОБНУЛЯЕМ: "перематываем" аудио в начало
            this.currentTime = 0;

            // ЗАПУСК: стартуем по новой аудио
            this.play();

        }, false);

    };

    // (3) ВКЛ/ВЫКЛ
    this.onOff = function () {

        // (3.1) Если GET параметр 'sound=0', то
        if (Hash.get().sound === '0') {

            // Кнопку меняем на ВЫКЛ
            $(btn).addClass('sound-btn__off');

        }

        // (3.2) Слушаем клик на кнопке "ВКЛ/ВЫКЛ"
        $(btn).click(function() {

            // Добавляем/удаляем CSS класс внешнего вида кнопки
            $(this).toggleClass('sound-btn__off');

            // Если на ПАУЗЕ, то
            if (audio.paused) {

                // ЗАПУСК: стартуем аудио
                audio.play(); // Запускаем

            } else {

                // ПАУЗА: останавливаем аудио
                audio.pause();
            }

        });

        // (3.3) Слушаем клик на ТРИГГЕР
        $(trigger).click(function() {

            // Кнопку меняем на ВЫКЛ
            $(btn).addClass('sound-btn__off');

            // ПАУЗА: останавливаем аудио
            audio.pause();

        });

    }

}
