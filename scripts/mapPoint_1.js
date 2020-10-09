map_height = window.innerHeight-$('.header-panel').height()-$('p.block').height()-$('.container.map-btn').height()-24;

$('#map').css('height',map_height+'px');

navigator.geolocation.getCurrentPosition(onSuccess, onError);

function onSuccess(position) {
    var coord1 = position.coords.latitude;
    var coord2 = position.coords.longitude;
    init(coord1,coord2);
}

function onError(error) {
    //alert('code: '    + error.code    + '\n' + 'message: ' + error.message + '\n');
}

//ymaps.ready(init);

function init(coord1,coord1) {
    var geolocation = ymaps.geolocation,
        myMap = new ymaps.Map('map', {
            center: [64.539393, 40.516939],
            zoom: 10,
            controls: ['zoomControl', 'searchControl', 'typeSelector']
        }, {
            searchControlProvider: 'yandex#search'
        });
    
    geolocation.get({
        provider: 'auto',
    }).then(function (result) {

        var GeoObj = result.geoObjects.get(0);
        GeoObj = CreateMarker(GeoObj);

        //console.log(GeoObj);
        
        myMap.geoObjects.add(GeoObj);
        
    });
    
    
    
    myMap.events.add('click', function (e) {
        GeoObj = myMap.geoObjects.get(0);
        coords = e.get('coords');
        if(typeof(GeoObj) == 'undefined') {
            var placemark = new ymaps.Placemark(coords,{});
            placemark = CreateMarker(placemark);
        } else {
            GeoObj.geometry.setCoordinates(coords);
            placemark = CreateMarker(GeoObj);
        }
        myMap.geoObjects.add(placemark);
    });
    
}

function CreateMarker(GeoObj) {
    GeoObj.properties.set({
        balloonContentHeader:'Где зафиксировано нарушение?',
        balloonContentBody:'Перетащите данный маркер или укажите место на карте, выбрав любую точку на карте с помощью клика.',
        balloonContentFooter:'<sup>Если местоположение определено - нажмите кнопку Далее.</sup>'
    });
    
    GeoObj.options.set('preset', 'twirl#yellowStretchyIcon');
        GeoObj.options.set('draggable','true');
        setPositioData(GeoObj.geometry.getCoordinates());
    
    GeoObj.events.add('dragend', function (e) {
        setPositioData(GeoObj.geometry.getCoordinates());
    });
    
    return GeoObj;
}

function setPositioData(coord) {
    $('input[name="address"]').val('');
    $('input[name="district"]').val('');
    
    SetAddress(coord);
    SetDistrict(coord);

    $('input[name="coord_x"]').val(coord[0]);
    $('input[name="coord_y"]').val(coord[1]);
}

function SetDistrict(coord) {
    var Geocoder = ymaps.geocode(coord, {kind: 'district', results : 0});
    Geocoder.then(
        function (res) {
            var nearest = res.geoObjects.get(0).getLocalities();
            $('input[name="district"]').val(nearest);           
        }
    );
}

function SetAddress(coord) {
    var Geocoder = ymaps.geocode(coord, {kind: 'house'});
    Geocoder.then(
        function (res) {
            var nearest = res.geoObjects.get(0);
            $('input[name="address"]').val(nearest.properties.get('name'));
        }
    );
}