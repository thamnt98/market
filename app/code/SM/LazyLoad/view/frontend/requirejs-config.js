var config = {
    map: {
        "*": {
            lazyLoad: "SM_LazyLoad/js/lazy.min",
            lazyLoadPlugins: 'SM_LazyLoad/js/lazy.plugins.min',
            'fotorama/fotorama': 'SM_LazyLoad/js/fotorama'
        }
    },
    shim: {
        'lazyLoad': {
            'deps': ['jquery']
        },
        'lazyLoadPlugins': {
            'deps': ['jquery', 'lazyLoad']
        }
    }
};
