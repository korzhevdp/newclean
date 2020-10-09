function init() {

    var myMap = new ymaps.Map('map', {
        center: [64.539393, 40.516939],
        zoom: 11,
        controls: ['zoomControl','searchControl']
    }, {
        provider: 'yandex#search',
        noPlacemark: true,
        suppressMapOpenBlock: true,
        restrictMapArea: true
    });
    
    
    createMenuGroup(messages);
    


    function createMenuGroup(messages) {
        clusterer = new ymaps.Clusterer({
            groupByCoordinates: true,
            clusterDisableClickZoom: false,
            clusterHideIconOnBalloonOpen: false,
            geoObjectHideIconOnBalloonOpen: false,
            preset: 'islands#invertedOrangeClusterIcons',
            clusterIconColor: '#0954a0'
        });
        
        clusterer.options.set('preset', 'islands#invertedOrangeClusterIcons');
   
        myMap.geoObjects.add(clusterer);
        
        $.each(messages, function(index, message) {
            createPlaceMarks(message, clusterer);
        });
    }

    function createPlaceMarks(item, collection) {
        var baloonCnt = '<div class="message-cont"><div class="info-cont"><div class="status '+item.status_icon+' '+item.status_color+'">'+item.status+'</div><div class="txt-cont">'+item.text+'</div></div></div>';
        
        placemark = new ymaps.Placemark(item.center, { 
            balloonContentHeader: '<h4>'+item.category+'<a href="#" data-id="'+item.id+'" id="one_message_panel" class="icon-external-link">Открыть</a></h4>',
            balloonContentBody: baloonCnt,
            balloonContentFooter: 'Последнее изменение '+item.update_time
        },{
            preset: 'islands#orangeIcon',
            iconCaption: item.text,
            iconCaptionMaxWidth: '50',
        });
        
        
        if(item.icon_type!='' && item.icon_type!=null)
        {
            placemark.options.set('preset', 'islands#'+item.status_color+item.icon_type);
        }
        else
        {
            placemark.options.set('preset', 'islands#'+item.status_color+'Icon');
        }
        //placemark.options.set('preset', 'islands#'+item.status_color+'Icon');

        collection.add(placemark);
        
    }

    myMap.setBounds(myMap.geoObjects.getBounds(),{checkZoomRange:true,preciseZoom:false});
    
}

ymaps.ready(init);
