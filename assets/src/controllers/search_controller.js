import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['search_input','categories']
    static values = {
        // slides: Array,
    }

    connect()
    {
        super.connect();
        let el = this.element;
        // if (this.hasSlideshowTarget) {
        //     // this.slideshowTarget.innerHTML = "…"
        //     // this.slideshowTarget.innerHTML = 'test';
        // } else {
        //     console.error('missing slideshowTarget');
        // }
        console.warn("hello from " + this.identifier);


    }

    changed(el) {
        console.warn(el);
        this.categoriesTarget.innerHTML = "Show!  switch the class or whatever";
    }
        // ...
}
