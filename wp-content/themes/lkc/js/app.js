function handlePrintBtnClick(btn, stylesheetUrl){
    var $this = btn,
        $id = $this.data('div'),
        $css = new String ('<link href="'+stylesheetUrl+'" rel="stylesheet" type="text/css">'),
        $content = document.getElementById($id),
        $html = '<html><head>'+$css+'</head><body>'+$content.innerHTML+'</body></html>';

    var WinPrint = window.open('', '', 'letf=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
    WinPrint.document.write($html);
    WinPrint.document.close();
    WinPrint.focus();
    WinPrint.print();
    // WinPrint.close();

    return false;
}

jQuery(document).ready(function(){

    jQuery('.main-nav').supersubs({ 
        minWidth:    13.6,   // minimum width of sub-menus in em units 
        maxWidth:    27,   // maximum width of sub-menus in em units 
        extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
                           // due to slight rounding differences and font-family 
    }).superfish({
        delay: 200,
        //autoArrows: false,
        dropShadows: false ,
        disableHI: true            
    });

/*    jQuery('.main-nav').superfish({
        delay: 400,
        autoArrows: false,
        dropShadows: false
    }); */

    var sideShadow = jQuery('.side-shadow');
    sideShadow.css('height', jQuery(window).height());

    jQuery(window).bind('resize', function(){  
        sideShadow.css('height', jQuery(window).height());      
    })

    //jQuery("a[href$='.jpg'],a[href$='.png'],a[href$='.gif'],a[href$='.bmp']").colorbox({
    jQuery("a[href$='.jpg']:not(.paprasta-nuoroda),a[href$='.png']:not(.paprasta-nuoroda),a[href$='.gif']:not('.paprasta-nuoroda'),a[href$='.bmp']:not('.paprasta-nuoroda')").each(function(){
        var thiss = jQuery(this);

        thiss.colorbox({
            title: function(){
                if(thiss.data('desc')){
                    return thiss.data('desc');
                } else {
                    return thiss.attr('title');
                }         
            },
            current: '{current}/{total}',
            scalePhotos:true,
            maxWidth:'95%',
            maxHeight:'95%',
            loop: false,
            onComplete: function(){
                if(jQuery('#cboxTitleInner').length < 1){
                    jQuery('#cboxTitle').wrapInner('<div id="cboxTitleInner" />');
                }

                var titleHeight = jQuery('#cboxTitle').height(); 
                    
                if (titleHeight > 20){ 
                    jQuery('#cboxTitleInner').hide();
                    // jQuery('#cboxTitle').animate({opacity: 0},0);
                    var newCboxContentHeight = jQuery('#cboxLoadedContent').height() + titleHeight - 12;

                    jQuery.colorbox.resize({
                        innerHeight: newCboxContentHeight
                    }); 
                } 
                jQuery('#cboxTitleInner').fadeIn();
            },
            onClosed: function(){
                jQuery('#cboxTitleInner').fadeOut();
            }
        });
    })



    /* START home cycle */

    //init slider
    jQuery('.cycle-first-row').anythingSlider({
        showMultiple : 10, //any number, but enought to fill screen
        resizeContents: false,
        buildNavigation: false, 
        buildStartStop: false,
        appendForwardTo: jQuery('.home-cycles-wrap'),
        hashTags: false,
        onInitialized: function(){
            markBubleSlide();
            bindHsCycleAnimation();
        },
        onSlideBegin: function(){
            jQuery('.cycle-first-row .hs-cycle-item.bubles-slide').removeClass('bubles-slide');
            unbindHsCycleAnimation();
        },
        onSlideComplete: function(){
            markBubleSlide();
            bindHsCycleAnimation();
        }
        //startPanel: 0,
       // changeBy     : 2
    });

    jQuery('.cycle-second-row').anythingSlider({
        showMultiple : 10,
        resizeContents: false,
        buildNavigation: false, 
        buildStartStop: false,
        appendBackTo: jQuery('.home-cycles-wrap'),
        hashTags: false
    });


    //overlay caption animation
    //slides are too big that two slides could fit in a small screen. 
    //Need to change overlay size if it gets cut in small monitors
    var hsItemHeight = jQuery('.hs-cycle-item').outerHeight(),
        hsItemWidth = jQuery('.hs-cycle-item').outerWidth();
    //jQuery('.hs-cycle-item-overlay').css('bottom', '-'+hsItemHeight+'px'); hidden with css

    function bindHsCycleAnimation(){
        jQuery('.hs-cycle-item').bind({
            'mouseenter.hs': function(){
                var thiss = jQuery(this),
                    overlay = thiss.find('.hs-cycle-item-overlay');
                
                //do not show overlay animation
                if(!thiss.hasClass('bubles-slide')){
                    elToViewport(overlay);
                    showCaption(thiss);
                }
            },
            'mouseleave.hs': function(){
                var thiss = jQuery(this);
                if(!thiss.hasClass('bubles-slide')){
                    hideCaption(thiss);
                }
            }
        });
    }

    function unbindHsCycleAnimation(){
        jQuery('.hs-cycle-item').unbind('mouseenter.hs').unbind('mouseleave.hs');
    }

    function showCaption(el){
        el.find('.hs-cycle-item-overlay').stop().animate({
            bottom: 0
        }, 400); 
    }

    function hideCaption(el){
        var overlay = el.find('.hs-cycle-item-overlay');
        overlay.stop().animate({
            bottom: '-'+hsItemHeight
        }, 400, function(){
            overlay.css('left',0);
            overlay.css({
                'left': 0,
                'width': '100%'
            })
        });  
    }

    function elToViewport(el){
   
        var scrollLeft = (document.documentElement.scrollLeft || document.body.scrollLeft),
            elW = jQuery(el).outerWidth(),
            elOffsetLeft = jQuery(el).offset().left,
            elOffsetRight = elOffsetLeft + elW,
            winW = ( window.innerWidth && window.innerWidth < jQuery(window).width() ) ? window.innerWidth : jQuery(window).width(),
            cssLeft,
            cssWidth;

        //1. take care of left side
        //if el is scrolled to left less than window edge, means we are not in viewport, need apply bigger css left property   
        //but do nothing if less than 50% of el is visible
        if( elOffsetLeft < scrollLeft && Math.abs(elOffsetLeft) < elW/2 ) {
            cssLeft = scrollLeft - elOffsetLeft,
            cssWidth = Math.round(elW + elOffsetLeft);                
            el.css({
                'left': cssLeft+'px',
                'width': cssWidth
            })

        //2. take care of right side if left side is in viewport (can't be both out of viewport)
        //but do nothing if less than 50% of el is visible
        } else if( elOffsetRight > scrollLeft + winW && Math.abs(scrollLeft + winW - elOffsetRight) < elW/2 ){
            cssWidth = Math.round(winW + scrollLeft - elOffsetLeft);     
            el.css({
                'width': cssWidth
            })
        }

    }

    //adding class to slide which is under bubles
    function markBubleSlide(){
        var bublesAreaOffset = jQuery('.area-to-animate-home-bubles').offset();   

        //add bubles-slide class and bind mouse event
        jQuery('.cycle-first-row .hs-cycle-item').each(function(){
            var hsCycleItemOffset = jQuery(this).offset();
            if( (Math.abs(hsCycleItemOffset.left-bublesAreaOffset.left) < 10) && (jQuery('.home-buble-1').length > 0 || jQuery('.home-buble-2').length > 0) ){
                jQuery(this).addClass('bubles-slide');                
            }
        });    
        //initBublesAnimation();   
    }

    //eof cycle

    //hotkeys for disabled
    jQuery(document).bind('keydown', 'Alt+i', function(evt){ window.location.href = jsVars.siteUrl; return false; });
    jQuery(document).bind('keydown', 'Alt+h', function(evt){ window.location.href = jQuery('.mini-nav-tree').attr('href'); return false; });
    jQuery(document).bind('keydown', 'Alt+a', function(evt){ window.location.href = jsVars.eventsUrl; return false; });
    jQuery(document).bind('keydown', 'Alt+b', function(evt){ window.location.href = jsVars.newsUrl; return false; });
    jQuery(document).bind('keydown', 'Alt+n', function(evt){ window.location.href = jsVars.keysListUrl; return false; });
    jQuery(document).bind('keydown', 'Alt+s', function(evt){ jQuery('#header .search-form-text').focus(); return false;});
    jQuery(document).bind('keydown', 'Alt+l', function(evt){ window.location.href = jQuery('.mini-nav-en').attr('href'); return false});
    jQuery(document).bind('keydown', 'Alt+m', function(evt){window.location.href = jQuery('.mini-nav-email').attr('href'); return false});

    jQuery('#movie-frame').bind('contextmenu',function() { return false; });

    jQuery('textarea[placeholder], input[placeholder]').simplePlaceholder({placeholderClass: "placeholder"});

    jQuery(function() {
        jQuery( '.select2' ).select2({width: 'resolve'});
    });

});