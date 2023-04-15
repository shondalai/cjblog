var CjBlogApi = { form_submitted: false };

(function ($) {
  CjBlogApi.init = function () {
    $('a[href="#adminForm"]').click(function () {
      var target = $($(this).attr('href'))
      $('html,body').animate({ scrollTop: target.offset().top }, 'slow')
    })

    $('.js-stools-btn-filter').click(function () {
      $('.js-stools-container-filters').toggle('slow')
    })

    $('.js-stools-btn-clear').click(function () {
      $('#filter_search').val('').closest('form').submit()
    })

    if (jQuery().tooltip) {
      $('body').on('mouseover', '[data-toggle="tooltip"]', function () {$(this).tooltip('show')})
      $('body').on('mouseover', '[data-toggle="popover"]', function () {$(this).popover('show')})
    }
  }

  CjBlogApi.init_profile = function () {
  }

  CjBlogApi.init_article = function () {
    $('a.gallery').colorbox({ 'photo': true, maxWidth: 800, maxHeight: 600 })
  }

  CjBlogApi.init_activity = function () {
    $('.commentbox').keydown(function (e) {
      if (e.keyCode == 13) {
        var commentBox = $(this)
        var comment = commentBox.val()

        if (comment.length == 0) {
          return true
        }

        $.ajax(
          {
            url: $('#url_save_comment').text(),
            type: 'post',
            dataType: 'json',
            data:
              {
                'commentId': commentBox.closest('.activity').find('input[name="commentId"]').val(),
                'activtyId': commentBox.closest('.activity').find('input[name="activityId"]').val(),
                'description': comment,
              },
            beforeSend: function (xhr) {
              commentBox.val('').prop('readonly', true)
            },
          }).done(function (data) {
          if (data.success && data.data) {
            if (commentBox.closest('.activity').find('.comments').length == 0) {
              commentBox.closest('.activity').find('.panel-body').after($('<ul>', { 'class': 'list-group comments' }))
            }

            var newComment = $($('#tmpl-comment').html().replace('{COMMENT_ID}', data.data.id))
            newComment.find('.comment').html(data.data.description)
            commentBox.val('').closest('.activity').find('.comments').append(newComment)
          } else {
            alert(data.message)
          }

          commentBox.prop('readonly', false)
        }).fail(function (data) {
          alert(data.message)
        })
      }
    })

    $('.btn-load-comments').click(function () {
      var loadButton = $(this)
      $.ajax(
        {
          url: $('#url_load_comments').text(),
          type: 'post',
          dataType: 'json',
          data:
            {
              'activtyId': loadButton.closest('.activity').find('input[name="activityId"]').val(),
              'start': loadButton.closest('.activity').find('input[name="start"]').val(),
            },
          beforeSend: function (xhr) {
            loadButton.parent().find('i').removeClass('fa-comments-o').addClass('fa-spinner fa-spin')
          },
        }).done(function (data) {
        if (data.success && data.data && data.data.comments && data.data.comments.length !== 0) {
          if (data.data.start == 1) {
            loadButton.closest('.activity').find('.comments').empty()
          }

          $.each(data.data.comments, function (index, comment) {
            var newComment = $($('#tmpl-comment').html().replace('{COMMENT_ID}', comment.id))
            newComment.find('.comment').html(comment.description)
            newComment.find('.profile-link').html(comment.profile)
            newComment.find('.avatar').html(comment.avatar)
            newComment.find('.activity-time').html(comment.created)

            loadButton.closest('.activity').find('.comments').prepend(newComment)
          })

          loadButton.closest('.activity').find('input[name="start"]').val(data.data.start)

          if (data.data.comments.length < 10) {
            loadButton.closest('table').remove()
          }
        } else {
          loadButton.closest('table').remove()
        }

        loadButton.parent().find('i').addClass('fa-comments-o').removeClass('fa-spinner fa-spin')
      }).fail(function (data) {
        alert(data.message)
      })
    })
  }

  CjBlogApi.init_form = function () {
    $('.attachments').on('click', '.btn-attach-file', function () {
      $(this).closest('.attachment').find('input[type="file"]').change(function () {
        $(this).closest('.attachment').find('.filename').text($(this).val())
      })
      $(this).closest('.attachment').find('input[type="file"]').click()
    })

    $('.attachments').on('change', 'input[type="file"]:last', function () {
      $(this).closest('.attachments').find('.panel-body').append($('#tpl-attachment').html())
      $(this).closest('.attachment').find('.btn-delete-attachment').show()
    })

    $('.attachments').on('click', '.btn-delete-attachment', function () {
      $(this).closest('.attachment').remove()
    })
  }

  CjBlogApi.init_profileform = function () {
    var avatar = $('#avatar-image')
    avatar.on('load', function () {
      avatar.guillotine({
        eventOnChange: 'guillotinechange',
        width: 256,
        height: 256,
      })
      avatar.guillotine('fit')
    })

    avatar.on('guillotinechange', function (ev, data, action) {
      $('input[name="avatar-coords"]').val(data.scale.toFixed(4) + ',' + data.angle + ',' + data.x + ',' + data.y + ',' + data.w + ',' + data.h)
    })

    $('#rotate_left').click(function () { avatar.guillotine('rotateLeft') })
    $('#rotate_right').click(function () { avatar.guillotine('rotateRight') })
    $('#fit').click(function () { avatar.guillotine('fit') })
    $('#zoom_in').click(function () { avatar.guillotine('zoomIn') })
    $('#zoom_out').click(function () { avatar.guillotine('zoomOut') })
    $('#change_avatar').click(function () { $('#btn-select-avatar').click() })

    $('#btn-select-avatar').change(function () {
      var oFReader = new FileReader()
      oFReader.readAsDataURL(document.getElementById('btn-select-avatar').files[0])

      oFReader.onload = function (oFREvent) {
        avatar.guillotine('remove')
        avatar.attr('src', oFREvent.target.result)
        avatar.on('load', function () {
          avatar.guillotine({
            eventOnChange: 'guillotinechange',
            width: 256,
            height: 256,
          })
          avatar.guillotine('fit')
        })

//				$('#avatar-container').empty();
//				avatar = $('<img>', {id: 'avatar-image', src: oFREvent.target.result});
//				$('#avatar-container').append(avatar);
//			    avatar.guillotine({
//			    	onChange: function(data, action){
//			    		$('input[name="avatar-coords"]').val(data.scale.toFixed(4)+','+data.angle+','+data.x+','+data.y+','+data.w+','+data.h);
//			    	},
//			    	width: 256,
//			    	height: 256
//			    });
//			    avatar.guillotine('fit');
//			    avatar.guillotine({eventOnChange: 'guillotinechange'});
      }
    })
  }

  CjBlogApi.onBeforeLike = function (action, button, form, data) {
    button.find('i').attr('class', 'fa fa-spinner fa-spin')
    return true
  }

  CjBlogApi.onAfterLike = function (action, button, form, data) {
    button.find('.user-rating').text(data.data)
    button.closest('.user-ratings').find('.btn').removeClass('btn-success btn-danger')
    var btnClass = ''
    var iconClass = ''

    switch (action) {
      case 'rating.tlike':
      case 'rating.rlike':
        btnClass = 'btn-success'
        iconClass = 'fa fa-thumbs-o-up'
        break
      case 'rating.tdislike':
      case 'rating.rdislike':
        btnClass = 'btn-danger'
        iconClass = 'fa fa-thumbs-o-down'
        break
    }

    button.removeClass('btn-success btn-danger')
    button.attr('class', 'btn btn-dislike btn-mini ' + btnClass).find('i').attr('class', iconClass)
  }

  CjBlogApi.submitAjaxForm = function (buttonObj, formName, action, triggerBefore, triggerAfter) {
    var form = $(formName)
    var button = $(buttonObj)
    var url = form.attr('action')
    form.find('input[name="task"]').val(action)

    $.ajax({
      type: 'POST',
      url: url + (/\?/.test(url) ? '&' : '?') + 'format=json',
      data: form.serialize(),
      dataType: 'json',
      encode: true,
      beforeSend: function (xhr) {
        var result = CjBlogApi.executeFunctionByName(triggerBefore, CjBlogApi, action, button, form, null)

        if (!result) {
          return false
        }
      },
    }).done(function (data) {
      if (data.messages && data.messages.message) {
        if (typeof data.messages.message != 'undefined') {
          alert(data.messages.message)
        }
      }

      if (data.success) {
        CjBlogApi.executeFunctionByName(triggerAfter, CjBlogApi, action, button, form, data)
      } else {
        alert(data.message)
      }
    }).fail(function (data) {
      if (typeof data.message != 'undefined') {
        alert(data.message)
      }
    })
  }

  CjBlogApi.executeFunctionByName = function (functionName, context, args) {
    var args = [].slice.call(arguments).splice(2)
    var namespaces = functionName.split('.')
    var func = namespaces.pop()

    for (var i = 0; i < namespaces.length; i++) {
      context = context[namespaces[i]]
    }
    return context[func].apply(this, args)
  }
})(jQuery)

jQuery(document).ready(function ($) {
  var pageid = $('#cjblog_pageid').val()
  CjBlogApi.init()

  if (pageid == 'activity' || pageid == 'activities') {
    CjBlogApi.init_activity()
  }

  if (pageid == 'profile') {
    CjBlogApi.init_activity()
    CjBlogApi.init_profile()
  }

  if (pageid == 'article') {
    CjBlogApi.init_article()
  }

  if (pageid == 'form') {
    CjBlogApi.init_form()
  }

  if (pageid == 'profileform') {
    CjBlogApi.init_profileform()
  }
})