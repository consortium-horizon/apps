'use strict';

$(function () {
  initCarousel();
  loadNews();
  loadDiscussions();
});

function initCarousel () {
  $('[carousel]').slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 3000,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1
        }
      }
    ],
    lazyLoad: 'ondemand'
  });
}

function parseRSS (url, length, callback) {
  $.ajax({
    url: 'https://ajax.googleapis.com/ajax/services/feed/load?v=1.0&num='+length+'&callback=?&q=' + encodeURIComponent(url),
    dataType: 'json',
    success: function(data) {
      callback(data.responseData.feed);
    }
  });
}

function loadNews () {
  parseRSS("/forum/categories/actualites/feed.rss", 10, function (data) {
    data.entries.forEach(function (entry) {
      console.log(entry);
      var title = entry.title;
      var date = new Date(entry.publishedDate).toLocaleDateString();
      var link = entry.link;
      var author = entry.author;
      var img = /<img[^>]+src=['"]?([^"\s]+)['"]/.exec( entry.content );
      img = img ? img[1] : 'http://lorempixel.com/640/480/cats/8/';
      $('[carousel]').slick('slickAdd', "<a class='carousel-item' href='"+link+"'><img data-lazy='"+img+"'><div class='caption'><div class='cat'>actu</div><span class='date'>"+date+"</span> <span class='author'>par "+author+"</span><h3>"+title+"</h3></div></a>");
    });
  });
}

function loadDiscussions () {
  parseRSS("/forum/discussions.rss", 9, function (data) {
    data.entries.forEach(function (entry) {
      console.log(entry);
      var title = entry.title;
      var date = new Date(entry.publishedDate).toLocaleDateString();
      var link = entry.link;
      var author = entry.author;
      var img = /<img[^>]+src=['"]?([^"\s]+)['"]/.exec( entry.content );
      img = img ? img[1] : 'http://lorempixel.com/640/480/cats/8/';
      var snippet = entry.contentSnippet;
      var cat = /^\[([^\]]+)\]/.exec( title );
      var cat = cat ? cat[1] : "";
      $('[news]').append("<article><img src='"+img+"'/></div><div class='content'><span class='cat'>"+cat+"</span><h3><a href='"+link+"'>"+title+"</a></h3><p>"+snippet+" <a href='"+link+"#Item_0'>suite&hellip;</a></p></div></article>");
    });
  });
}
