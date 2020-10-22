var config = {
    map: {
        '*': {
            configurable: 'Magento_ConfigurableProduct/js/configurable'
        }
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Wizkunde_ConfigurableBundle/js/swatch-renderer-mixin': true
            }
        }
    }
};
