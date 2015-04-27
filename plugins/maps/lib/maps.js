if (window.armapCenter !== undefined) {
    ymaps.ready(init);
}

var myMap;

function init () {    
    myMap = new ymaps.Map('map', {
        center: armapCenter,
        zoom: mapZoom
    });

    myMap.behaviors.enable('scrollZoom');
    size_array = myMap.container.getSize();
    
    if(size_array[1]>250) {
        myMap.controls.add('zoomControl', { top: 5, left: 5 });
    } else {
        myMap.controls.add('smallZoomControl', { top: 5, left: 5 });
    }  
       
    var myCollection = new ymaps.GeoObjectCollection({}, {
        preset: 'twirl#blueDotIcon'
    });

    for (var i = 0; i<mapCoords.length; i++) {
        placemark = mapMyPlacemark(mapCoords[i]);
        myCollection.add(placemark);
    }

    myMap.geoObjects.add(myCollection);
}

function mapMyPlacemark(arr) {
    var box = $('#map-'+arr[0]);

    var myPlacemark = new ymaps.Placemark([arr[1], arr[2]], {
        balloonContent: '<b>'+arr[3]+'</b><br/>'+arr[4]
    });
        
    myPlacemark.events.add('click', function () {
        $('.maps-address').removeClass('current');
        box.addClass('current');
    });
    
    box.bind("click", function() {
        if (box.hasClass("current")) {
            myPlacemark.balloon.close();
            $('.maps-address').removeClass('current');
            myMap.setCenter(armapCenter, mapZoom);
        } else {
            myPlacemark.balloon.open();
            $('.maps-address').removeClass('current');
            box.addClass('current');
            myMap.setCenter([arr[1], arr[2]], mapZoomc);
        }
        return false;
    });
    
    return myPlacemark;
}