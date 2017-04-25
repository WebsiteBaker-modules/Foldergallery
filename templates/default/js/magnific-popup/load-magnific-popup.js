jQuery(function($) {
    $('.magnificPopup li').magnificPopup({
      delegate: 'a', // child items selector, by clicking on it popup will open
      type: 'image',
      gallery:
              {
                enabled: true,
                navigateByImgClick: true,
                preload: [0,1] // Will preload 0 - before current, and 1 after the current image
            },
      image: {
                tError: '<a href="%url%">Image #%curr%</a> could not be loaded.',
                titleSrc: function(item) {
                    var caption = item.el.attr('alt');
                    var url      = window.location.href;
                    var imageUrl = item.el.attr('href');
                    var facebookURL ='http://www.facebook.com/sharer.php?s= 100&amp;p[title]='+caption+'&amp;p[url]='+url+'&amp;p[images][0]='+imageUrl+'&amp;p[summary]='+caption+'';
                    var twitterURL = 'http://twitter.com/share?url='+imageUrl+'&text='+caption+'&count=horiztonal';
                    return '<ul class="share-buttons">' +
                        '<li><a href="'+facebookURL+'" onclick="window.open(\''+facebookURL+'\',\'newWindow\',\'width=670, height=350\'); return false;" ><img src="https://dl-web.dropbox.com/get/Public/wb-share/Facebook.png?_subject_uid=21728940&w=AACLWasaE2-rCZkp4FuoA7ClZ3exAD7ft5aGRIzftXM2dw"></a></li>' +
                        '<li><a href="'+twitterURL+'" onclick="window.open(\''+twitterURL+'\',\'newWindow\',\'width=626, height=265\'); return false;"><img src="https://dl-web.dropbox.com/get/Public/wb-share/Twitter.png?_subject_uid=21728940&w=AAB0WUBcgKOdYwu6BJU0pxWmfo6qXo50IUmwWUN_9fe2cw"></a></li>' +
                    '</ul>' +
                    caption
                    ;
                }
            }
    });
});