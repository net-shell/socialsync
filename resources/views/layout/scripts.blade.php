<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js"></script>
<script src="{{ asset('vendor.js') }}"></script>
<script type="text/javascript">

@yield('js')

$(function() {

	$('#buyModal .submit').click(function() {
		$(this).parents('form')[0].submit()
	})

  $(document).on('submit','.dontSubmit',function (e) {
      e.preventDefault();
      return false;
  })

  var form = $('#postModal form');
  var modal = $('#postModal');

  modal.find('.btn.submit').click(function() {
    if(form[0].checkValidity()){
      $.post(form.attr('action'), form.serialize(), function() { window.location.reload(); });
    }
    else form.find(':submit').click()
  });

  modal.find('textarea').on('keyup', function() {
    var l = this.value.length;
    var m = false;
    if(modal.find("select[name='provider']").val() == 'twitter') m = l > 140;
    $('#charcount').text(l).css('color', (m ? 'red' : ''));
  });

  modal.on('show.bs.modal', function (e) {
    var id = $(e.relatedTarget).data('id')
    modal.find("select[name='provider']").attr('disabled', !!id)
    modal.find("input[name='id']").val(id)
    if(id) $.ajax({
      url: '/api/v1/post/' + id,
      complete: function(d) {
        var data = d.responseJSON
        // modal.find("[name='schedule_date'], [name='schedule_time']").attr('disabled', (0 < moment().diff(data.posted_at)))
        form.find('[name]').each(function() {
          var k = $(this).attr('name');
          switch(k) {
            case 'schedule_date':
              $(this).val(moment(data.posted_at).format('MM/DD/YYYY'))
              break;
            case 'schedule_time':
              $(this).val(moment(data.posted_at).format('HH:MM'))
              break;
            case 'categories[]':
              $(this).val($.map(data.categories, function(c) { return c.id })).trigger('change')
              break;
            default:
  				    $(this).val(data[k])
              break;
          }
          if(k == 'text') $(this).trigger('keyup')
  			})
  		}
    });

    var provider = $(e.relatedTarget).data('provider')
    if(provider) form.find("select[name='provider']").val(provider)
  }).on('hidden.bs.modal', function (e) {
    form[0].reset();
  });
});
</script>
