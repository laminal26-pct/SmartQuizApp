$(document).ready(function () {
  $('div#loading').hide();
  $('input[type=submit]').attr('disabled','disabled');
  function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
  }
  $('#login').on('keyup change click', function() {
    var username = $('input[name=email]').val();
    var password = $('input[name=password]').val();
    $label = $(this).find('p');
    $formGroup = $(this).find('.form-group');

    if (!validateEmail(username)) {
      $formGroup.eq(0).addClass('has-error');
      $label.eq(0).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Email tidak valid !');
    } else {
      $formGroup.eq(0).removeClass('has-error');
      $formGroup.eq(0).addClass('has-success');
      $label.eq(0).removeClass().html('');
    }
    if (password.length == 0) {
      $formGroup.eq(1).addClass('has-error');
      $label.eq(1).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Password tidak boleh kosong !');
    } else if (password.length < 8) {
      $formGroup.eq(1).addClass('has-error');
      $label.eq(1).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Password minimal 8 karakter !');
    } else {
      $formGroup.eq(1).removeClass('has-error');
      $formGroup.eq(1).addClass('has-success');
      $label.eq(1).removeClass().html('');
    }
    if (validateEmail(username) && password.length > 7) {
      $('input[type=submit]').removeAttr('disabled');
    } else {
      $('input[type=submit]').attr('disabled','disabled');
    }
  });
  $('#login').on('submit', function (e) {
    e.preventDefault();
    var login = $('#login').serialize();
    $input = $('#login').find('input[type=text], input[type=password]');
    $.ajax({
      url: 'api/v1/login',
      type: 'POST',
      async: false,
      data: login,
      beforeSend: function() {
        $('#message').fadeOut();
        $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
        $('input[type=submit]').attr('disabled','disabled');
      },
      success: function(response) {
        if (response.auth.kode == "1") {
          $('input[type=submit]').attr('disabled','disabled');
          setTimeout('window.location.href = "dashboard/"; ',2000);
        } else {
          $('#message').fadeIn(500, function() {
            $('input[type=submit]').attr('disabled','disabled');
            $formGroup.eq(0).removeClass('has-success');
            $formGroup.eq(0).addClass('has-error');
            $formGroup.eq(1).removeClass('has-success');
            $formGroup.eq(1).addClass('has-error');
            $input.eq(0).val('');
            $input.eq(1).val('');
            $('div#loading').hide();
            $('div#message').addClass('alert alert-danger').html('<span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response.auth.message);
          });
        }
      }
    });
    return false;
  });

  $('#register').on('keyup change click', function() {
    var tipe = $('select').val();
    var name = $('input[name=name]').val();
    var username = $('input[name=email]').val();
    var password = $('input[name=password]').val();
    $label = $(this).find('p');
    $formGroup = $(this).find('.form-group');

    if (tipe == null) {
      $formGroup.eq(0).addClass('has-error');
      $label.eq(0).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Harus Pilih Tipe User !');
    } else {
      $formGroup.eq(0).removeClass('has-error');
      $formGroup.eq(0).addClass('has-success');
      $label.eq(0).removeClass().html('');
    }
    if (name.length == 0) {
      $formGroup.eq(1).addClass('has-error');
      $label.eq(1).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Nama tidak boleh kosong !');
    } else if (name.length < 3) {
      $formGroup.eq(1).addClass('has-error');
      $label.eq(1).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Nama minimal 3 karakter !')
    } else {
      $formGroup.eq(1).removeClass('has-error');
      $formGroup.eq(1).addClass('has-success');
      $label.eq(1).removeClass().html('');
    }
    if (!validateEmail(username)) {
      $formGroup.eq(2).addClass('has-error');
      $label.eq(2).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Email tidak valid !');
    } else {
      $formGroup.eq(2).removeClass('has-error');
      $formGroup.eq(2).addClass('has-success');
      $label.eq(2).removeClass().html('');
    }
    if (password.length == 0) {
      $formGroup.eq(3).addClass('has-error');
      $label.eq(3).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Password tidak boleh kosong !');
    } else if (password.length < 8) {
      $formGroup.eq(3).addClass('has-error');
      $label.eq(3).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Password minimal 8 karakter !');
    } else {
      $formGroup.eq(3).removeClass('has-error');
      $formGroup.eq(3).addClass('has-success');
      $label.eq(3).removeClass().html('');
    }
    if ((tipe != 'undefined' || tipe != null) && name.length > 3 && validateEmail(username) && password.length > 7) {
      $('input[type=submit]').removeAttr('disabled');
    } else {
      $('input[type=submit]').attr('disabled','disabled');
    }
  });
  $('#register').on('submit', function(e) {
    e.preventDefault();
    var register = $('#register').serialize();
    $input = $('#register').find('input[type=text], input[type=password]');
    $.ajax({
      url: 'api/v1/register',
      type: 'POST',
      async: false,
      data: register,
      beforeSend: function() {
        $('#message').fadeOut();
        $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
        $('input[type=submit]').attr('disabled','disabled');
      },
      success: function(response) {
        if (response.auth.kode == "1") {
          $('#message').fadeIn(500, function() {
            $('input[type=submit]').attr('disabled','disabled');
            $formGroup.eq(0).removeClass('has-success');
            $formGroup.eq(1).removeClass('has-success');
            $formGroup.eq(2).removeClass('has-success');
            $formGroup.eq(3).removeClass('has-success');
            $input.eq(0).val('');
            $input.eq(1).val('');
            $input.eq(2).val('');
            $('div#loading').hide();
            $('div#message').addClass('alert alert-success').html('<span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response.auth.message);
          });
        } else {
          $('#message').fadeIn(500, function() {
            $('input[type=submit]').attr('disabled','disabled');
            $formGroup.eq(0).removeClass('has-success');
            $formGroup.eq(0).addClass('has-error');
            $formGroup.eq(1).removeClass('has-success');
            $formGroup.eq(1).addClass('has-error');
            $formGroup.eq(2).removeClass('has-success');
            $formGroup.eq(2).addClass('has-error');
            $formGroup.eq(3).removeClass('has-success');
            $formGroup.eq(3).addClass('has-error');
            $input.eq(0).val('');
            $input.eq(1).val('');
            $input.eq(2).val('');
            $('div#loading').hide();
            $('div#message').addClass('alert alert-danger').html('<span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response.auth.message);
          });
        }
      }
    });
    return false;
  });

  $('#forget-password').on('keyup change click', function() {
    var username = $('input[name=email]').val();
    $label = $(this).find('p');
    $formGroup = $(this).find('.form-group');

    if (!validateEmail(username)) {
      $formGroup.eq(0).addClass('has-error');
      $label.eq(0).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Email tidak valid !');
    } else {
      $formGroup.eq(0).removeClass('has-error');
      $formGroup.eq(0).addClass('has-success');
      $label.eq(0).removeClass().html('');
    }
    if (validateEmail(username)) {
      $('input[type=submit]').removeAttr('disabled');
    } else {
      $('input[type=submit]').attr('disabled','disabled');
    }
  });
  $('#forget-password').on('submit', function(e) {
    e.preventDefault();
    var email = $('#forget-password').serialize();
    $input = $('#forget-password').find('input[type=email]');
    $.ajax({
      url: 'api/v1/forget-password',
      type: 'POST',
      async: false,
      data: email,
      beforeSend: function() {
        $('#message').fadeOut();
        $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
        $('input[type=submit]').attr('disabled','disabled');
      },
      success: function(response) {
        if (response.auth.kode == "1") {
          $('#message').fadeIn(500, function() {
            $('input[type=submit]').attr('disabled','disabled');
            $formGroup.eq(0).removeClass('has-success');
            $input.eq(0).val('');
            $('div#loading').hide();
            $('div#message').addClass('alert alert-success').html('<span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response.auth.message);
          });
        } else {
          $('#message').fadeIn(500, function() {
            $('input[type=submit]').attr('disabled','disabled');
            $formGroup.eq(0).removeClass('has-success');
            $formGroup.eq(0).addClass('has-error');
            $input.eq(0).val('');
            $('div#loading').hide();
            $('div#message').addClass('alert alert-danger').html('<span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response.auth.message);
          });
        }
      }
    });
    return false;
  });

  $('#reset-password').on('keyup change click', function() {
    var password = $('input[name=password]').val();
    $label = $(this).find('p');
    $formGroup = $(this).find('.form-group');
    if (password.length == 0) {
      $formGroup.eq(0).addClass('has-error');
      $label.eq(0).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Password tidak boleh kosong !');
    } else if (password.length < 8) {
      $formGroup.eq(0).addClass('has-error');
      $label.eq(0).addClass('label label-danger col-md-12 col-xs-12').html('<i class="fa fa-times"></i>&nbsp; Password minimal 8 karakter !');
    } else {
      $formGroup.eq(0).removeClass('has-error');
      $formGroup.eq(0).addClass('has-success');
      $label.eq(0).removeClass().html('');
    }
    if (password.length > 7) {
      $('input[type=submit]').removeAttr('disabled');
    } else {
      $('input[type=submit]').attr('disabled','disabled');
    }
  });
  $('#reset-password').on('submit', function(e) {
    e.preventDefault();
    var pass = $('#reset-password').serialize();
    $input = $('#reset-password').find('input[type=password]');
    $.ajax({
      url: 'api/v1/reset-password',
      type: 'POST',
      async: false,
      data: pass,
      beforeSend: function() {
        $('#message').fadeOut();
        $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
        $('input[type=submit]').attr('disabled','disabled');
      },
      success: function(response) {
        if (response.auth.kode == "1") {
          $('#message').fadeIn(500, function() {
            $('input[type=submit]').attr('disabled','disabled');
            $formGroup.eq(0).addClass('has-success');
            $formGroup.eq(0).removeClass('has-error');
            $input.eq(0).val('');
            $('div#loading').hide();
            $('div#message').addClass('alert alert-success').html('<span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response.auth.message);
          });
        } else {
          $('#message').fadeIn(500, function() {
            $('input[type=submit]').attr('disabled','disabled');
            $formGroup.eq(0).removeClass('has-success');
            $formGroup.eq(0).addClass('has-error');
            $input.eq(0).val('');
            $('div#loading').hide();
            $('div#message').addClass('alert alert-danger').html('<span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+response.auth.message);
          });
        }
      }
    });
    return false;
  });

  function clickIE4() {
    if (event.button==2) { return false; }
  }

  function clickNS4(e) {
    if (document.layers||document.getElementById&&!document.all) {
      if (e.which==2||e.which==3) {
        return false;
      }
    }
  }

  if (document.layers) {
    document.captureEvents(Event.MOUSEDOWN);
    document.onmousedown=clickNS4;
  } else if (document.all&&!document.getElementById) {
    document.onmousedown=clickIE4;
  }

  document.oncontextmenu=new Function('return false');

});
