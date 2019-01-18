$.ajaxSetup({
  headers : {
      'CsrfToken': $('meta[name="csrf-token"]').attr('content')
  }
});
var LoadModal = (function () {
  var LoadModalDiv = $('<div id="loading" class="modal custom" role="dialog" aria-hidden="true"><div class="modal-dialog"><i class="fa fa-spinner fa-4x fa-spin"></i></div></div>');
  return {
    showLoadModal: function() {
      LoadModalDiv.modal({
				backdrop:'static',
				keyboard:false,
				show:true
			});
    },
    hideLoadModal: function () {
      LoadModalDiv.modal('hide');
    },
  };
})();

$(function() {
  var year = $('#year-copy'); var d = new Date();
  if (d.getFullYear() == '2018') {
    year.html('2018');
  } else {
    year.html('2018-'+d.getFullYear().toString());
  }
  function onReady(callback) {
    var intervalID = window.setInterval(checkReady, 100);
    function checkReady() {
      if (document.getElementsByTagName('body')[0] !== undefined) {
          window.clearInterval(intervalID);
          callback.call(this);
      }
    }
  }

  //$('#content-modal').empty();
  onReady(function () {
    $('div#loading').hide();
    //$('#content-field').load('page/dashboard.php');

  });

  $('#navigation').find('a.menu').on('click', function () {
    var url = $(this).attr('href');
    $.ajax({
      beforeSend: function() {
        $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
      },
      success: function(response) {
        window.location.href = url;
      }
    });
  });
  $('#account').find('a').on('click', function () {
    var url = $(this).attr('href');
    $.ajax({
      beforeSend: function() {
        $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
      },
      success: function(response) {
        window.location.href = url;
      }
    });
  });
});

$(document).ready(function () {
  var str = $('span.hidden-xs').html();
  var res = str.substr(0, 13);
  $('span.hidden-xs').html(res);
  $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');

  //LoadModal.showLoadModal();
  /*
  $(document).on('click','li.user-body a', function() {
    var page = $(this).attr('data-remote');
    var uuid = $(this).attr('data-target');

    $.ajax({
      url: 'config/route.php',
      type: 'get',
      async: true,
      dataType: 'html',
      data: 'f='+page+"&d="+uuid,
      cache: false,
      beforeSend: function() {
        $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>');
      },
      success: function(response) {
        $('#content-field').removeAttr('data-remote');
        $('#content-field').removeAttr('data-target');
        $('div#loading').fadeIn(1500).hide();
        $('#content-field').html(response);
        $('#navigation li.active').parents('li.treeview').removeClass('menu-open').find('.treeview-menu').slideUp(0);
        $('#navigation li.active').removeClass('active');
        $('#navigation a[data-remote~="'+page+'"]').parents('li').addClass('active');
        $('#navigation a[data-remote~="'+page+'"]').parents('li.treeview').addClass('menu-open').find('.treeview-menu').show(0);
      }
    });
  });

  $(document).on('click','div#account a', function() {
    var page = $(this).attr('data-remote');
    var uuid = $(this).attr('data-target');

    $.ajax({
      url: 'config/route.php',
      type: 'get',
      async: true,
      dataType: 'html',
      data: 'f='+page+"&d="+uuid,
      cache: false,
      beforeSend: function() {
        $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>');
      },
      success: function(response) {
        $('#content-field').removeAttr('data-remote');
        $('#content-field').removeAttr('data-target');
        $('div#loading').fadeIn(1500).hide();
        $('#content-field').html(response);
        $('#navigation li.active').parents('li.treeview').removeClass('menu-open').find('.treeview-menu').slideUp(0);
        $('#navigation li.active').removeClass('active');
        $('#navigation a[data-remote~="'+page+'"]').parents('li').addClass('active');
        $('#navigation a[data-remote~="'+page+'"]').parents('li.treeview').addClass('menu-open').find('.treeview-menu').show(0);
      }
    });
  });
  */
  /*
  $(document).on('click','[data-remote]',function(e) {
		e.preventDefault();
		$('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
		$('li.dropdown').removeClass("open");
		var link = $(this);
		var divTujuan = link.attr('href');
		var remoteNyo = link.data('remote');
		var targetNyo = link.data('target');
		if(targetNyo){
			$(targetNyo).load(remoteNyo,function(e){$('div#loading').fadeIn(1500).hide();});
			//$('#page-container').removeClass('sidebar-visible-xs');
		}else{
			$(divTujuan).load(remoteNyo,function(e){$('div#loading').fadeIn(1500).hide();});
		}
		//LoadModal.hideLoadModal();
		return false;
	});
  */
  /*
  $('#navigation').find('a.menu').on('click', function() {
    var page = $(this).attr('data-remote');
    var uuid = $(this).attr('data-target');

    $.ajax({
      url: 'config/route.php',
      type: 'get',
      async: true,
      dataType: 'html',
      data: 'f='+page+"&d="+uuid,
      cache: false,
  		contentType: false,
  		processData: false,
      beforeSend: function() {
        $('div#loading').show().html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
      },
      success: function(response) {
        $('#content-field').removeAttr('data-remote');
        $('#content-field').removeAttr('data-target');
        $('div#loading').fadeIn(1500).hide();
        $('#content-field').html(response);
        $('#navigation li.active').parents('li.treeview').removeClass('menu-open').find('.treeview-menu').slideUp(0);
        $('#navigation li.active').removeClass('active');
        $('#navigation a[data-remote~="'+page+'"]').parents('li').addClass('active');
        $('#navigation a[data-remote~="'+page+'"]').parents('li.treeview').addClass('menu-open').find('.treeview-menu').show(0);
      }
    });
  });
  */
  /*$('#content-field').html("");
  var page = $('#content-field').attr('data-remote');
  var uuid = $('#content-field').attr('data-target');
  $.ajax({
    url: 'config/route.php',
    async: true,
    data: 'f='+page+'&d='+uuid,
    dataType: 'html',
    cache: false,
    type: 'GET',
    success: function(res) {
      $('#content-field').append(res);
    }
  });*/
});
