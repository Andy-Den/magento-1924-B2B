/* <!-- AjaxPro --> */

AjaxPro.toolbar = function(){

    var _currentPage, _totalNum, _limit, _url;

    return {
        getTotal: function(){
            return _totalNum;
        },
        setTotal: function(total){
            _totalNum = total;
            return AjaxPro.toolbar;
        },
        getLimit: function(){
            return _limit;
        },
        setLimit: function(limit){
            _limit = limit;
            return AjaxPro.toolbar;
        },
        getPage: function(){
            return _currentPage;
        },
        setPage: function(page){
            _currentPage = page;
            return AjaxPro.toolbar;
        },
        getUrl: function(){
            return _url;
        },
        setUrl: function(url){
            _url = url;
            return AjaxPro.toolbar;
        },
        isEnd: function () {
            if ( _totalNum <= _limit * _currentPage) {
                return true;
            }
            return false;
        },
        request: function() {
            if (AjaxPro.message.visible()) {
                return;
            }
            if (AjaxPro.toolbar.isEnd()) {
                return;
            }

            if ("object" === typeof ajaxlayerednavigation) {
                var params = {};
                window.location.hash.substr(1).split("&").each(function(arg){
                    arg = arg.split('=');
                    if (arg[0] && arg[1]) {
                        params[arg[0]] = arg[1];
                    }
                });
                AjaxPro.request({
                    'url' : _url.replace('.page.', _currentPage + 1),
                    parameters: params
                });
                return;
            }
            AjaxPro.request({
                'url' : _url.replace('.page.', _currentPage + 1)
            });
        },
        incCurrentPage: function() {
            _currentPage++;
        }
    };
}();

Event.observe(window, 'load', function() {
    // Check for possible page without tm/ajaxpro/catalog/category/init.phtml
    // @see /app/code/local/TM/AjaxPro/Model/Observer.php~534, allowedBlockNames
    if (!AjaxPro.toolbar.getTotal()) {
        return;
    }

    if ("scroll"  == AjaxPro.config.get('catalogCategoryView/type')) {

        Event.observe(window, 'scroll', function() {

            var scrollOffsets = document.viewport.getScrollOffsets(),
            dimensions = document.viewport.getDimensions();

            var topElement = $$('.toolbar-bottom').last();
            if (!topElement) {
                return;
            }

            var currentTopPosition = scrollOffsets[1] + dimensions.height,
            elementTopPosition = topElement.offsetTop;

            if (elementTopPosition > currentTopPosition || Ajax.activeRequestCount > 0) {

                return;
            }

            AjaxPro.toolbar.request();
        });

    } else {

        var title = Translator.translate('More Products');
        AjaxPro.toolbar.addButton = function() {

            if ($('ajaxpro-scrolling-button')) {
                return;
            }
            var toolbarBottom = $$('.toolbar-bottom').last();
            if (!toolbarBottom) {
                return;
            }

            toolbarBottom.insert({
                'before': '<a id="ajaxpro-scrolling-button" type="button" title="'+ title +'" class="ajaxpro-scrolling-button">' + title +'<br /><i class="fa fa-chevron-down"></i></a>'
            });

            if (AjaxPro.toolbar.isEnd()) {
                $('ajaxpro-scrolling-button').hide();
            }

            Event.observe($('ajaxpro-scrolling-button'), 'click', AjaxPro.toolbar.request);
            return true;
        };

        AjaxPro.toolbar.addButton();
        AjaxPro.observe('addObservers', AjaxPro.toolbar.addButton);
    }


    AjaxPro.toolbar.appendProductList = function(html) {
        $$('.pager .pages').invoke('hide');
        var el = $('ajaxpro-scrolling-button');
        if (!el) {
            el = $$('.toolbar-bottom').last();
        }
        if (el) {
            el.insert({'before': html.stripScripts()});
            html.extractScripts().map(function(script) {
//                return window.eval.defer(script);
                try {
                    return window.eval.defer(script);
                } catch (err) {
                    console.log(script);
                    console.error(err);
                }
            });

            AjaxPro.toolbar.incCurrentPage();
            if (AjaxPro.toolbar.isEnd() && $('ajaxpro-scrolling-button')) {
                $('ajaxpro-scrolling-button').hide();
            }
        }
        // fix pager amount
        var t = AjaxPro.toolbar;
        count = [t.getLimit() * t.getPage(), t.getTotal()].min();
        $$('.amount').each(function(el){
            el.innerHTML = el.innerHTML.replace(/(\d+)([^\d]*)\d+/, '$1$2' + count);
        });

        //remove last css class
        $$('.products-grid.last').each(function(el){
            el.removeClassName('last');
        });

        //verifica cookie e exibe ou não as imagens no catálogo;
        verifyFlagImgs();
        bindLazy();
        bindQty();
        bindProductAddtocartForm();
        initProlabels();
    };

    var productListEvents = [
        'onComplete:catalog:category:view',
        'onComplete:catalogsearch:result:index',
        'onComplete:attributepages:page:view',
        'onComplete:attributemenu:index:index'
    ];
    productListEvents.each(function(eventName) {
        AjaxPro.observe(eventName, function(e) {
            var r = e.memo.response;
            if (!r.custom.product_list) {
                return false;
            }
            AjaxPro.toolbar.appendProductList(r.custom.product_list);
        });
    });
});
