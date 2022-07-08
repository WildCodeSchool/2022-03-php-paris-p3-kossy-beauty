/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

//require('bootstrap');

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;


var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})

/**
 * Events for click and mouseleave for the small nav
 */
const triggerTabList = document.querySelectorAll('#myTab li button')

triggerTabList.forEach(triggerEl => {
    const tabTrigger = new bootstrap.Tab(triggerEl)

    triggerEl.addEventListener('click', event => {
        event.preventDefault()
        tabTrigger.show()
    })

    triggerEl.addEventListener('shown.bs.tab', function (event) {
        // event.target // newly activated tab
        // event.relatedTarget // previous active tab
        const dataBsTarget = event.target.dataset.bsTarget
        const smallNavId = dataBsTarget.slice(1);
        const smallNav = document.querySelector('#' + smallNavId)
        document.querySelector('body').addEventListener('click', event => {
            if (event.target.classList.contains('nav-link') || event.target.classList.contains('smallnav')) {
                // Do nothing
            } else {
                this.classList.remove('active')
                triggerEl.ariaSelected = 'false'
                triggerEl.tabIndex = '-1'
                smallNav.classList.remove('active', 'show')
            }
        })
    })
})
