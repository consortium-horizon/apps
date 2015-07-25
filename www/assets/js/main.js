'use strict';

$(function () {
  initCarousel();
  loadVideos();
  loadNews();
  //loadDiscussions();
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

function parseRSSEntry (entry, category) {
  var o = {};
  o.title = entry.title;
  o.date = entry.publishedDate;
  o.localDate = new Date(o.date).toLocaleFormat("%d %b");
  o.link = entry.link;
  o.author = entry.author;
  o.img = /<img[^>]+src=['"]?([^"\s]+)['"]/.exec( entry.content );
  o.img = o.img ? o.img[1] : 'http://lorempixel.com/640/480/cats/8/';
  o.snippet = entry.contentSnippet;
  o.cat = /^\[([^\]]+)\]/.exec( o.title );
  o.cat = o.cat ? o.cat[1] : "";
  o.title = o.title.replace("["+o.cat+"] ", "");
  return o;
}

function parseYoutubeEntry (entry) {
  var o = {};
  o.title = entry.title;
  o.date = entry.publishedAt;
  o.localDate = new Date(o.date).toLocaleFormat("%d %b");
  o.link = "https://www.youtube.com/watch?v=" + entry.resourceId.videoId;
  o.author = entry.channelTitle;
  o.img = entry.thumbnails.high.url;
  o.cat = "vidéo";
  return o;
}

function loadNews () {
  parseRSS("https://www.consortium-horizon.com/forum/categories/actualites/feed.rss", 10, function (data) {
    data.entries.forEach(function (entry) {
      var o = parseRSSEntry(entry);
      $('[news]').append("<article><img src='"+o.img+"'/><div class='content'><span class='cat'>"+o.cat+"</span><h3><a href='"+o.link+"'>"+o.title+"</a></h3><p>"+o.snippet+" <a href='"+o.link+"#Item_0'>suite&hellip;</a></p></div></article>");
    });
  });
}

function loadDiscussions () {
  parseRSS("https://www.consortium-horizon.com/forum/discussions.rss", 9, function (data) {
    data.entries.forEach(function (entry) {
      var o = parseRSSEntry(entry);
      $('[news]').append("<article><img src='"+o.img+"'/><div class='content'><span class='cat'>"+o.cat+"</span><h3><a href='"+o.link+"'>"+o.title+"</a></h3><p>"+o.snippet+" <a href='"+o.link+"#Item_0'>suite&hellip;</a></p></div></article>");
    });
  });
}

function loadVideos () {
  // https://www.youtube.com/feeds/videos.xml?channel_id=UCEk_pNq59GlK2PNW6zNAZqQ
  var url = "https://www.googleapis.com/youtube/v3/playlistItems?playlistId=UUEk_pNq59GlK2PNW6zNAZqQ&fields=items&part=snippet&maxResults=10&key=AIzaSyALa_tLhXleuogMeQ11SESbZWToT2jN8bY";
  $.ajax({
    url: url,
    dataType: 'json',
    success: function(data) {
      data.items.forEach(function (entry) {
        var o = parseYoutubeEntry(entry.snippet);
        $('[carousel]').slick('slickAdd', "<a class='carousel-item' href='"+o.link+"' target='_blank'><img src='"+o.img+"'><div class='caption'><div class='cat'>"+o.cat+"</div><time datetime='"+o.date+"'>"+o.localDate+"</time><span class='author'> <small>par </small>"+o.author+"</span><h3>"+o.title+"</h3></div></a>");
      });
    }
  });
}
