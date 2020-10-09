map_height = window.innerHeight-$('.header-panel').height()-$('p.block').height()-$('.container.map-btn').height()-12;

$('#map').css('height',map_height+'px');

ymaps.ready(init);

function init() {

    var geolocation = ymaps.geolocation,
        myMap = new ymaps.Map('map', {
            center: [64.539393, 40.516939],
            zoom: 10,
            controls: ['zoomControl','searchControl']
        }, {
            provider: 'yandex#search',
            noPlacemark: true,
            suppressMapOpenBlock: true,
            restrictMapArea: true
        });
    
    geolocation.get({
        provider: 'browser',
    }).then(function (result) {
        var GeoObj = result.geoObjects.get(0);
        GeoObj = CreateMarker(GeoObj);
        if(myMap.geoObjects.add(GeoObj)) {
            //console.log('OK');
        }
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
        balloonContentBody:'Перетащите маркер или укажите место, выбрав любую точку на карте.',
        balloonContentFooter:'<sup>Если местоположение уже выбрано - нажмите "Далее".</sup>'
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
    $('input[name="coord_x"]').val(coord[0]);
    $('input[name="coord_y"]').val(coord[1]);
    getAdressByPoint(coord);
}


function getAdressByPoint(coord) {
    if(coord[0]===undefined || coord[1]===undefined)
        return false;
    
    var Geocoder = ymaps.geocode(coord, {kind: 'house'});
    Geocoder.then(
        function (res) {
            var nearest = res.geoObjects.get(0);
            $('input[name="address"]').val(nearest.properties.get('name'));
        }
    );
    
    
    var address = $('input[name="address"]').val();
    
    getDistrictByPoint(coord);

}


function getDistrictByPoint(coord) {
    var data = { page: 125, X: coord[0], Y: coord[1] };
    var districtData = SendDataJSON(data);
                         
    districtData.done(function(res) {
        $('#message_info').removeClass('active');
        if(res.distr_name!=='')
        {
            $('input[name="district"]').val(res.distr_name);
            $('#message_info').addClass('active');
        }
        else
        {
            //alert('Не удалось определить округ, вы вышли за пределы допустимой области');
        }
    });
                        
    districtData.fail(function(jqXHR, code, textStatus) {
        serverResultError(textStatus);
    });
}
