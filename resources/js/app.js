const hideInitialLoader = () => {
    const loader = document.getElementById('app-loader');

    if (!loader) {
        return;
    }

    loader.classList.remove('is-active');
};


/*
|--------------------------------------------------------------------------
| Initial Page Load
|--------------------------------------------------------------------------
*/

const initInitialLoader = () => {

    const loader =
        document.getElementById('app-loader');

    if (!loader) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Make sure loader starts visible
    |--------------------------------------------------------------------------
    */

    loader.classList.add('is-active');


    /*
    |--------------------------------------------------------------------------
    | Hide after complete page load
    |--------------------------------------------------------------------------
    */

    if (document.readyState === 'complete') {

        setTimeout(() => {
            hideInitialLoader();
        }, 800);

    } else {

        window.addEventListener(
            'load',
            () => {

                setTimeout(() => {
                    hideInitialLoader();
                }, 800);

            },
            { once: true }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Safety fallback
    |--------------------------------------------------------------------------
    */

    setTimeout(() => {
        hideInitialLoader();
    }, 4000);
};


/*
|--------------------------------------------------------------------------
| Start
|--------------------------------------------------------------------------
*/

if (document.readyState === 'loading') {

    document.addEventListener(
        'DOMContentLoaded',
        initInitialLoader,
        { once: true }
    );

} else {

    initInitialLoader();

}