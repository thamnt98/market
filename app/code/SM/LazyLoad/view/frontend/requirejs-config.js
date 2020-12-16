var config = {
    map: {
        "*": {
            lazyLoad: "SM_LazyLoad/js/lazy.min",
            lazyLoadPlugins: 'SM_LazyLoad/js/lazy.plugins.min',
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
