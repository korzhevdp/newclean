
$(function() {
    CloseWindowLoader();
});


$(window).resize(function(){
    mobileWindowSetPosition();
});


$(document).on('click','a.close_button,a.slide-back', function(e) {
  panel = $(this).closest('div.panels');
  panel.removeClass('active');
  CloseWindowBlocker("200");
  setTimeout("panel.remove()", 300);
  setTimeout("removeWindowNoScrolling()", 320);
  e.preventDefault();
});

$(document).on('click','#getfile,#screenshot', function(e) {
  if($('input[name="filedata"]').length>0)
  {
    if($('input[name="filedata"]').val()=='')
    {
      $('#inputfile').click();
    }
  }
  else
  {
    $('#inputfile').click();
  }
  
  e.preventDefault();
});

$(document).on('change','.feedback-form #inputfile', function() {
    var currentText = $('#screenshot').html();
	var text = $(this).val() || currentText;
    var file = $(this);
    var uploadFile = file[0].files[0];
    getBase64(uploadFile).then(function(data) {
        $('input[name="filedata"]').val(data);
        var removeFileBtn = $('<a\>', { href: '#', class: 'bl-link', html: 'Удалить / изменить файл' });
        removeFileBtn.on('click', function(e) {
            $('#screenshot').addClass('icon-plus')
            .removeClass('icon-check')
            .html('Прикрепить файл');
            $('input[name="filedata"]').val(null);
            $('#inputfile').val(null);
            $(this).remove();
        });
        $('#screenshot').removeClass('icon-plus')
            .addClass('icon-check')
            .html('Файл прикреплен')
            .after(removeFileBtn);
    });
});


$(document).on('click','#about', function(e) {
  data = { 
    page: '9',
    route_type: 'right-to-left',
    close_button: 'false'
  };
  ShowPanel(data);
  e.preventDefault();
});


$(document).on('click','.messages .delete', function(e) {
    var deleteId = $(this).attr('data-delete-id');
    var curentMessageItem = $(this).parent('div');
    var header = 'Удалить сообщение?';
    var message = 'Сообщение № '+deleteId+' еще не рассмотрено. Вы действительно хотите его удалить?';
    var type = 'selection';
    var btnOk = '';
    if($(this).hasClass('disabled'))
    {
        header = 'Удаление запрещено';
        message = 'Сообщение № '+deleteId+' уже в работе. Вы не можете его удалить.';
        type = 'info';
    }
    else
    {
        btnOk = $('<a/>', { href: '#', class: 'mobile-window-btn mobile-window-btn-ok', html: 'Да' });
        btnOk.on('click', function(e) {
            data = {
                func: 4,
                data: {
                    message_id: deleteId
                }
            };
            
            var deleteMessage = GetServerResult(data);
            
            deleteMessage.done(function(res){
                if(res.status) {
                    closeMobileWindow();
                    curentMessageItem.remove();
                }
                else
                {
                    alert(res.message);
                }
            });
            
            deleteMessage.fail(function(jqXHR, code, textStatus) {
                serverResultError(textStatus);
            });
            
            e.preventDefault();
        });
    }
    
    showMobileWindow(header,message,type,btnOk);
    e.preventDefault();
});


$(document).on('click','.mes-category', function(e) {
  var category_id = 1;   
  if($(this).is('[data-id]')) 
    category_id = $(this).attr('data-id');
    var slide_panel = $('.mess-cat-description[data-cat="'+category_id+'"]');
    if(slide_panel.length > 0)
    {
        if($(this).hasClass('active'))
        {
            $(this).removeClass('active');
            slide_panel.removeClass('active');
        }
        else
        {
            $('.mes-category').removeClass('active');
            $('.mess-cat-description').removeClass('active');
            $(this).addClass('active');
            slide_panel.addClass('active');
        }
    }
    else
    {
        var data = { 
            page: '6', 
            category: category_id,
            route_type: 'right-to-left',
            close_button: 'false'
        };
        ShowPanel(data);
    }
  e.preventDefault();
});

$(document).on('click','.cat-selection', function(e) {
    var category_id = 1;
    category_id = $(this).attr('data-id');
    var data = { 
        page: '6', 
        category: category_id,
        route_type: 'right-to-left',
        close_button: 'false'
    };
    ShowPanel(data);
    e.preventDefault();
});


$(document).on('click','.mes-category i', function(e) {
  var description = $(this).parent().attr('data-description');
  if(description!=='')
  {
    alert(description);
  }
  e.stopPropagation();
  e.preventDefault();
});


$(document).on('click','#one_message_panel,.one_message_panel', function(e) {
  var message_id = 0;   
  if($(this).is('[data-id]')) 
    message_id = $(this).attr('data-id');
  var data = { 
      page: '16', 
      panel: 'one_message',
      close_button: 'true',
      message_id: message_id 
  };
  ShowPanel(data);
  e.preventDefault();
});


$(document).on('click', '#send-feedback', function(e) {
    var form = $(this).parent();
    var subject = form.find('input[name="subject"]');
    var text = form.find('textarea[name="message"]');
    var file = form.find('input[name="filedata"]');
    var uploadFileData = file.val();
    
    if(subject.val() == '' || text.val() == '')
    {
        alert('Все поля формы должны быть заполнены');
        return false;
    }
    
    var data = { 
        page: '32',
        subject: subject.val(),
        message: text.val(),
        filedata: uploadFileData
    };
    
    LoadPage(data);
    e.preventDefault();
});

$(document).on('click', '#public', function(e) {
  var photo_count = $('input.photo_files').length;
  var message_text = $('textarea[name="message"]').val();
  if(photo_count<=0) 
    alert('Для публикации сообщения необходимо добавить хотя бы одну фотографию.');
  else
  if(message_text==='')
    alert('Вы не ввели текст своего сообщения');
  else {
      if(($('input[name="coord_x"]').length > 0) && ($('input[name="coord_y"]').length > 0)) {
          var coord_x = $('input[name="coord_x"]').val();
          var coord_y = $('input[name="coord_y"]').val();
          var address = $('input[name="address"]').val();
          var district = $('input[name="district"]').val();
          var category = $('input[name="category"]').val();
          if((!isNaN(parseFloat(coord_x))) && (!isNaN(parseFloat(coord_y)))) {
              if(!isNaN(parseInt(category))) {
                data = { 
                    page: '8',
                    coord_x: coord_x,
                    coord_y: coord_y,
                    address: address,
                    district: district,
                    category: category,
                    message: message_text,
                    files : $('input.photo_files').serializeArray()
                };
                LoadPage(data);
              } else alert('Категория сообщения неопределена, или имеет некорректный формат.');
          } else alert('Координаты места некорректны. Перейдите к предыдущему шагу и измените координаты.');
      } else alert('При попытке публикации Вашего сообщения не удалось определить координаты места.');
  }
  e.preventDefault();
});

$(document).on('click', '#message_info', function(e) {
    if(!$(this).hasClass('active'))
    {
        alert('Для продолжения переместите маркер на территорию одного из округов г. Архангельска.');
        e.preventDefault();
        return;
    }
    
  if(($('input[name="coord_x"]').length > 0) && ($('input[name="coord_y"]').length > 0)) {
      var coord_x = $('input[name="coord_x"]').val();
      var coord_y = $('input[name="coord_y"]').val();
      var category = $('input[name="category"]').val();
      var district = $('input[name="district"]').val();
      var address = $('input[name="address"]').val();
      if((!isNaN(parseFloat(coord_x))) && (!isNaN(parseFloat(coord_y)))) {
          if(!isNaN(parseInt(category))) {
              data = { 
                  page: '7',
                  coord_x: coord_x,
                  coord_y: coord_y,
                  address: address,
                  district: district,
                  category: category,
                  route_type: 'right-to-left',
                  close_button: 'false'
              };
              ShowPanel(data);
          } else alert('Категория сообщения неопределена, или имеет некорректный формат.');
      } else alert('Координаты места не определены. Если маркер текущего местоположения не появился добавьте его самостоятельно, выбрав нужную точку на карте.');
  } else alert('При переходе на следующий шаг возникла ошибка: не удалось зафиксировать координаты места.');
  e.preventDefault();
});

$(document).on('click', '#to_reg', function(e) {
  data = { page: '10' };
  LoadPage(data);
  e.preventDefault();
});



$(document).on('click', '#repass', function(e) {
    var form = $(this).parent('div');
    if(checkDate(form)) {
        var email = $('input[name="email"]').val();
        
        var data = {
            page: '35',
            email: email
        };
        var recoveryResult = SendDataJSON(data);
        recoveryResult.done(function(res){
            if(!res.status || res.status!=true)
            {
                 $('.form-error').html(res.message);
            }
            else
            {
                form.html(null);
                form.html('<p class="form-success-message">'+ res.message +'</p>');
            }
        });
        
        recoveryResult.fail(function(jqXHR, code, textStatus) {
            serverResultError(textStatus);
        });
    }
    e.preventDefault();
});

$(document).on('click','#auth', function(e) {
    if(checkDate($(this).parent('div'))) {
        data = { 
          func: 2,
          data: {
              email: $('input[name="login"]').val(),
              password: $('input[name="password"]').val()
          }
    };
      
      
        var userAuth = GetServerResult(data);
        userAuth.done(function(res){
            if(!res.status)
            {
                 $('.form-error').html(res.message);
            }
            else
            {
                if(res.key)
                {
                    localStorage.setItem('auth_key', res.key);
                }
                else
                {
                    //alert('Не удалось сохранить ключ авторизации');
                }
                
                var data = { page: '4' };
                LoadPage(data);
                
            }
        });
        
        userAuth.fail(function(jqXHR, code, textStatus) {
            serverResultError(textStatus);
        });
        
  }
  e.preventDefault();
});

$(document).on('click','#back_lk', function(e) {
    data = { page: '4' };
    window.history.pushState(null, null, "/");
    LoadPage(data);
    e.preventDefault();
});

$(document).on('click', '#logout', function(e) {
  data = {
      page: '0',
      logout: true
  };
  
  localStorage.setItem('auth_key', null);
  LoadPage(data);
  e.preventDefault();
});

$(document).on('click','#to_auth', function(e) {
  data = {
    page: '4'
  };
  LoadPage(data);
  e.preventDefault();
});

$(document).on('click','#to_my_message', function(e) {
  data = {
      page: '12', 
      route_type: 'right-to-left', 
      close_button: 'false' 
  };
  ShowPanel(data);
  e.preventDefault();
});

$(document).on('click','#info-after-message', function(e) {
  $('.info-after-message').slideDown(200);
  e.preventDefault();
});

$(document).on('click','#to_all_message', function(e) {
  data = {
    page: '13',
    route_type: 'right-to-left',
    close_button: 'false'
  };
  
  ShowPanel(data);
  e.preventDefault();
});

$(document).on('click','#my_message_map', function(e) {
  data = {
    page: '14',
    route_type: 'right-to-left',
    close_button: 'false'
  };
  ShowPanel(data);
  e.preventDefault();
});

$(document).on('click','#all_message_map', function(e) {
  data = {
    page: '15',
    route_type: 'right-to-left',
    close_button: 'false'
  };
  ShowPanel(data);
  e.preventDefault();
});


$(document).on('click', '#to_lk', function(e) {
    window.location.reload(); 
    e.preventDefault();
});



$(document).on('click', '.btn-menu-closer', function(e) {
    menu.toggle();
    e.preventDefault();
});



$(document).on('click', '.top-feedback-btn,#feedback', function(e) {
    data = {
        page: '31', 
        route_type: 'right-to-left', 
        close_button: 'false' 
    };
    ShowPanel(data);

    e.preventDefault();
});

$(document).on('click', '.top-menu-btn', function(e) {
    alert('Данный раздел находится на стадии разработки.');
    e.preventDefault();
});

/*
$(document).on('click', '.top-menu-btn', function(e) {
    var data = {
        page: '33', 
        route_type: 'left-to-right', 
        close_button: 'false' 
    };
    ShowPanel(data);

    e.preventDefault();
});
*/


$(document).on('click', '#password_recovery', function(e) {
    var data = {
        page: '34', 
        route_type: 'right-to-left', 
        close_button: 'false' 
    };
    ShowPanel(data);
    e.preventDefault();
});

$(document).on('click', '#message', function(e) {
  var data = { 
    page: '5',
    route_type: 'right-to-left',
    close_button: 'false'
  };
  ShowPanel(data);
  e.preventDefault();
});


$(document).on('keyup','.search input',function () {
    var value = $(this).val().toLowerCase();
    mlist = $('.messages.items');
    var ActiveCcount = 0;
    mlist.find('a[data-id]').each(function(index) {
        if($(this).attr('data-display')!='none') {
            thisVal = $(this).html().toLowerCase();
            var obj = $(this).parent('div');
            if(false == thisVal.indexOf(value)+1) {
                obj.css('display','none');
            } else {
                obj.css('display','block');
                ActiveCcount++;
            }
        }
    });
    
    //ShowInfoLine(ActiveCcount,'По вашему запросу не найдено ни одного сообщения');
});


$(document).scroll(function () {
    s_top = $("html").scrollTop();
    yes = $("body").offset().top;
    if(s_top >= yes){
        $('.top-menu-btn, .top-feedback-btn').addClass('active');
    } else {
        $('.top-menu-btn, .top-feedback-btn').removeClass('active');
    }
});


$(document).on('click', '#new_password', function(e) {
    var form = $(this).parent('div');
    if(checkDate(form)) {
        var recovery_key = 0;
        if($('input[name="recovery_key"]'))
        {
            recovery_key = $('input[name="recovery_key"]').val();
        }
        var data = { 
            page: 37,
            data: {
                recovery_key: recovery_key,
                password: $('input[name="password2"]').val(),
            }
        };
        var recoveryResult = SendDataJSON(data);
        recoveryResult.done(function(res){
            if(!res.status || res.status!=true)
            {
                 $('.form-error').html(res.message);
            }
            else
            {
                form.html(null);
                form.html('<p class="form-success-message">'+ res.message +'</p>');
            }
        });
        
        recoveryResult.fail(function(jqXHR, code, textStatus) {
            serverResultError(textStatus);
        });
    }
    e.preventDefault();
});

$(document).on('click', '#reg_user', function(e) {
  var captcha = 0;
  var captchaField = $('input[name="captcha"]');
  $('.form-error').html(null);
  if(captchaField.length>0)
  {
    captcha = captchaField.val();
  }
  
    if(checkDate($(this).parent('div'))) {
        var data = { 
            func: 1,
            data: {
                email: $('input[name="email"]').val(),
                alias: $('input[name="user_name"]').val(),
                phone: '',//$('input[name="phone"]').val(),
                password: $('input[name="password2"]').val(),
                captcha: captcha
            }
        };
        
        var regUser = GetServerResult(data);
        
        regUser.done(function(res){
          if(!res.status)
          {
              $('.reg-captcha img').attr('src','http://gorod.arhcity.ru/captcha/');
              $('.reg-captcha input').val(null);
              $('.form-error').html(res.message);
          }
          else
          {
              $('.container').html('<div class="green-blk">'+res.message+'</div>');
              $('.container').append('<a href="#" class="bl-link icon-key" id="to_auth">В личный кабинет</a><br/><br/>');
          }
        });
        
        
        regUser.fail(function(jqXHR, code, textStatus) {
            $('.reg-captcha img').attr('src','http://gorod.arhcity.ru/captcha/');
            serverResultError(textStatus);
        });
        
    }

  e.preventDefault();
});


function closeMobileWindow() {
    $('.mobile-window').remove();
}

function showMobileWindow(header,message,type,btnOk) {
    if($('.mobile-window').length > 0)
    {
        return false;
    }
    // type = {selection, info}
    if(type == '') type = 'info';
    var mobileWindow = $('<div/>', { class: 'mobile-window' });
    var mobileWindowCont = $('<div/>', { class: 'mobile-window-cont' });
    var mobileWindowHeader = $('<div/>', { class: 'mobile-window-header', html: '<i>'+header+'</i>' });
    var mobileWindowClose = $('<a/>', { href: '#', class: 'mobile-window-close' });
    var mobileWindowMessage = $('<div/>', { class: 'mobile-window-message', html: message });
    var mobileWindowBtnOk = false;
    
    if((type=='selection') && (btnOk.length > 0))
    {
        var mobileWindowBtnOk = btnOk;
    }
    
    var closeBtnText = 'Отменить';
    if(type=='info') closeBtnText = 'Ок';
    var mobileWindowBtnClose = $('<a/>', { href: '#', class: 'mobile-window-btn mobile-window-btn-close', html: closeBtnText });
    
    mobileWindowBtnClose.on('click', function(e) {
        closeMobileWindow();
        e.preventDefault();
    });
    
    mobileWindowClose.on('click', function(e) {
        closeMobileWindow();
        e.preventDefault();
    });
    
    mobileWindowHeader.append(mobileWindowClose);
    mobileWindowCont
    .append(mobileWindowHeader)
    .append(mobileWindowMessage)
    .append(mobileWindowBtnClose);
    
    if((type=='selection') && (btnOk.length > 0))
    {
       mobileWindowCont.append(mobileWindowBtnOk); 
    }
    
    mobileWindow.append(mobileWindowCont);
    $('body').append(mobileWindow);
    mobileWindowSetPosition();
}

function mobileWindowSetPosition() {
    var mobileWondowCont = $('.mobile-window-cont');
    if(mobileWondowCont.length > 0)
    {
        var windowHeight = $(window).innerHeight();
        var windowWidth = $(window).innerWidth();
        
        //alert('Height='+windowHeight+' | Width='+windowWidth);
        if(windowHeight > 350)
        {
            mobileWondowCont
            .css('margin-top','120px')
            .css('height','240px')
            .css('width','265px');
        }
        else
        {
            mobileWondowCont
            .css('margin-top','80px')
            .css('height','180px')
            .css('width','265px');
        }
        
    }
}


function getBase64(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = error => reject(error);
  });
}

function GetServerResult(sendData) {
    return $.ajax({
        method: "POST",
        url: "db/db_functions.php",
        data: sendData,
        dataType: "JSON"
    });
}


/*
function LoadPage(data) {
    ShowWindowLoader();
    $('body').load('handler.php', data, function(){ //.php #container - для определения источника
        $('body').animate({scrollTop: 0},10);
        CloseWindowLoader();
    });
}
*/


function LoadPage(data) {
    ShowWindowLoader();
    var loadContent = loadPageContent(data);
        
    loadContent.done(function(res){
        $('body').html(res);
        $('body').animate({scrollTop: 0},10);
        CloseWindowLoader();
    });
    
    
    loadContent.fail(function(jqXHR, code, textStatus) {
        alert('При получении данных произошла ошибка. Пожалуйста, обратитесь к системному администратору.');
    });
}


function loadPageContent(data) {
    return $.ajax({
        method: "POST",
        url: "handler.php",
        data: data,
        dataType: "html"
    });
}



function ShowPanel(data) {
    var route_type = 'left-to-right';
    var close_btn = 'true';
    
    if(typeof data.route_type != "undefined")
        route_type = data.route_type;
    if(typeof data.close_button != "undefined")
        close_btn = data.close_button;

    panel = $('<div/>', { id: data.panel, class: 'panels '+route_type });
    panel_container = $('<div/>', { class: 'panel-container' });
    close_button = $('<a/>', { class: 'close_button', href: '#' });
    
    var loadContent = loadPageContent(data);
        
    loadContent.done(function(res){
        panel_container.html(res);
        if(close_btn=='true') panel_container.find('.hdr').append(close_button);
        panel.append(panel_container);
        $('body').append(panel);
        setTimeout("panel.addClass('active')", 50);
        setWindowNoScrolling()
        //CloseWindowLoader();
        ShowWindowBlocker();
    });
    
    
    loadContent.fail(function(jqXHR, code, textStatus) {
        alert('При получении данных произошла ошибка. Пожалуйста, обратитесь к системному администратору.');
    });
    
}


function setWindowNoScrolling() {
    if(!$('html').hasClass('page-no-scrolling'))
    {
        $('html').addClass('page-no-scrolling');
    }
}


function removeWindowNoScrolling() {
    if(!$(document).find('.panels').length > 0)
    {
        if($('html').hasClass('page-no-scrolling'))
        {
            $('html').removeClass('page-no-scrolling');
        }
    }
}


function CloseWindowBlocker(close_time) {
    blocker = $(document).find('.window-blocker');
    blocker.removeClass('active');
    setTimeout("blocker.remove()", close_time);
}
function ShowWindowBlocker(close_time) {
    blocker = $('<div/>', { class: 'window-blocker white'});
    $('body').append(blocker);
    setTimeout("blocker.addClass('active')", close_time);
}

function CloseWindowLoader() {
    loader = $(document).find('.window-loader');
    loader.removeClass('active');
    setTimeout("loader.remove()", 300);
}
function ShowWindowLoader() {
    loader = $('<div/>', { class: 'window-loader white'});
    //loader_img = '<div align="center" class="cssload-fond">';
    loader_img = '<div class="gif-loader-2"></div>';
    /*
	loader_img +='<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_1"> </div></div>';
	loader_img +='<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_2"> </div></div>';
	loader_img +='<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_3"> </div></div>';
	loader_img +='<div class="cssload-internal"><div class="cssload-ballcolor cssload-ball_4"> </div></div>';
	*/
	//loader_img +='</div>';
    loader.append(loader_img);
    $('body').append(loader);
    setTimeout("loader.addClass('active')", 50);
}


function checkDate(form) {
    var res = false;
    var err_count = 0;
    var err_index = 0;
    var err_mess = '';
    var pass = '';
    form.find('.input-error').remove();
    var data = form.find('input[type=text],input[type=password],input[type=tel]');
    
    $.each(data, function(i) {
        err_index = 0; err_mess = '';
        if($(this).is('[data-type]')) {
            if($(this).attr('data-type')=='email')
                if(!validateEmail($(this).val())) {
                    err_count++;
                    err_index=1;
                    err_mess = 'Проверьте email';
                }
        } else {
            if($(this).val() == '') {
                err_count++; 
                err_index=1;
                err_mess = 'Поле ожидает ввод данных';
            } else {
                if($(this).attr('type')=='password') {
                    if($(this).attr('name')=='password1') 
                        pass = $(this).val();
                    if($(this).attr('name')=='password2') 
                        if((pass!='') && $(this).val() != pass) {
                            err_count++; 
                            err_index=1;
                            err_mess = 'Введенные пароли не совпадают';
                        }
                }
            }
            
        }
        
        if(err_index) {
            $(this).after('<p class="input-error">'+err_mess+'</p>');
        }
    });
    if(err_count==0)
        res = true;
    return res;
}


function SendDataJSON_Admin(sendData) {
    return $.ajax({
        type: "POST",
        url: "/admin/handler.php",
        data: sendData,
        dataType: "JSON"
    });
}

function SendDataJSON(sendData) {
    return $.ajax({
        type: "POST",
        url: "/handler.php",
        data: sendData,
        dataType: "JSON"
    });
}

function SendDataJSONMail(sendData) {
    return $.ajax({
        type: "POST",
        url: "/handler.php",
        data: sendData,
        dataType: "JSON"
    });
}

function userAuthByKey() {
    
    if(localStorage.getItem('auth_key')==null)
    {
        return false;
    }
    
    ShowWindowLoader();

    var Data = {
        page: 122,
        key: localStorage.getItem('auth_key')
    };
    
    
    
    var userAuth = SendDataJSON_Admin(Data);
                        
    userAuth.done(function(res) {
        if(res.status)
        {
            location.reload();
        }
        CloseWindowLoader();
    });
                        
    userAuth.fail(function(jqXHR, code, textStatus) {
        serverResultError(textStatus);
        CloseWindowLoader();
    });
}

function serverResultError(textStatus) {
    alert("При выполнении запроса произошла ошибка [ "+textStatus+" ]. Пожалуйста, обратитесь к системному администратору.");
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
