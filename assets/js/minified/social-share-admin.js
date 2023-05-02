document.addEventListener('DOMContentLoaded', function() {
    // Dohvaćanje elemenata za povlačenje i ispuštanje
    var mediasContainer = document.querySelector('.social-media-options');

    if (mediasContainer && typeof dragula !== 'undefined') {
        // Inicijalizacija Dragula biblioteke
        var drake = dragula([mediasContainer]);

        // Praćenje promjene pozicije elemenata
        drake.on('drop', function () {
        // Ažuriranje pozicija skrivenih polja
        var mediaElements = document.querySelectorAll('.social-media-option');
            mediaElements.forEach(function (mediaElement, index) {

                var inputElement = mediaElement.querySelector('input.position');
                inputElement.value = index;
            });
        });
    }
});
