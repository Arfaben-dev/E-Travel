/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

import noUiSlider from 'nouislider';
import 'nouislider/dist/nouislider.css';
import axios from "axios";

// slide price
const slider = document.getElementById('price-slider');

if (slider) {
    const min = document.getElementById('min')
    const max = document.getElementById('max')
    const range = noUiSlider.create(slider, {
        start: [min.value || parseInt(slider.dataset.min, 10), max.value || parseInt(slider.dataset.max, 10)],
        step: 10,
        connect: true,
        range: {
            'min': parseInt(slider.dataset.min, 10),
            'max': parseInt(slider.dataset.max, 10),
        }
    })


    range.on('slide', function (values, handle) {

        if (handle === 0) {
            min.value = Math.round(values[0])
        }
        if (handle === 1) {
            max.value = Math.round(values[1])
        }

        console.log(values, handle);

    })
}




// favorite js
function onClickBtnLike(event) {
    event.preventDefault();
    const url = this.href;
    const spancount = this.querySelector('span.js-likes');
    const icone = this.querySelector('i');

    axios.get(url).then(function (response) {
        const likes = response.data.likes;

        spancount.textContent = likes;


          if(icone.classList.contains('icon-bookmark')) {
            icone.classList.replace('icon-bookmark', 'icon-bookmark-empty');
        } else {
            icone.classList.replace('icon-bookmark-empty', 'icon-bookmark');
        }

    })
}

document.querySelectorAll('a.like').forEach(function (link) {
    link.addEventListener('click', onClickBtnLike);
})





