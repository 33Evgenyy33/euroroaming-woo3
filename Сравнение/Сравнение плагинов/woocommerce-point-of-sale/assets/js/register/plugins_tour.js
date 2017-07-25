jQuery(document).ready(function($) {

    var enjoyhint_instance = new EnjoyHint({});

    // config
    var enjoyhint_script_steps = [
        {
            '#wc-pos-register-grids' : 'Click the "New" button to start creating your project'
        }
    ];

    // set script config
    enjoyhint_instance.set(enjoyhint_script_steps);

    // run Enjoyhint script
    enjoyhint_instance.run();


});

