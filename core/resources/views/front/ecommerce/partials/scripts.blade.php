@php
$mainbs = [];
$mainbs = json_encode($mainbs);
@endphp
<script>
var mainbs = {!! $mainbs !!};
var mainurl = "{{url('/')}}";
var vap_pub_key = "{{env('VAPID_PUBLIC_KEY')}}";

var rtl = {{ $rtl }};
var next = "{{ __('Next') }}";
var prev = "{{ __('Prev') }}";
</script>
<!-- popper js -->
<script src="{{asset('assets/front/js/popper.min.js')}}"></script>
<!-- bootstrap js -->
<script src="{{asset('assets/front/js/bootstrap.min.js')}}"></script>
<!-- Plugin js -->
<script src="{{asset('assets/front/js/plugin.min.js')}}"></script>
<!-- main js -->
<script src="{{asset('assets/front/js/ecommerce-main.js')}}"></script>
<!-- pagebuilder custom js -->
<script src="{{asset('assets/front/js/common-main.js')}}"></script>
{{-- whatsapp init code --}}
@if ($bex->is_whatsapp == 1)
<script type="text/javascript">
    var whatsapp_popup = {{$bex->whatsapp_popup}};
    var whatsappImg = "{{asset('assets/front/img/whatsapp.svg')}}";
    $(function () {
        $('#WAButton').floatingWhatsApp({
            phone: "{{$bex->whatsapp_number}}", //WhatsApp Business phone number
            headerTitle: "{{$bex->whatsapp_header_title}}", //Popup Title
            popupMessage: `{!! nl2br($bex->whatsapp_popup_message) !!}`, //Popup Message
            showPopup: whatsapp_popup == 1 ? true : false, //Enables popup display
            buttonImage: '<img src="' + whatsappImg + '" />', //Button Image
            position: "right" //Position: left | right

        });
    });
</script>
@endif

@if (session()->has('success'))
<script>
toastr["success"]("{{__(session('success'))}}");
</script>
@endif

@if (session()->has('error'))
<script>
toastr["error"]("{{__(session('error'))}}");
</script>
@endif

<!--Start of subscribe functionality-->
<script>
$(document).ready(function() {
$("#subscribeForm, #footerSubscribeForm").on('submit', function(e) {
    // console.log($(this).attr('id'));

    e.preventDefault();

    let formId = $(this).attr('id');
    let fd = new FormData(document.getElementById(formId));
    let $this = $(this);

    $.ajax({
    url: $(this).attr('action'),
    type: $(this).attr('method'),
    data: fd,
    contentType: false,
    processData: false,
    success: function(data) {
        // console.log(data);
        if ((data.errors)) {
        $this.find(".err-email").html(data.errors.email[0]);
        } else {
        toastr["success"]("You are subscribed successfully!");
        $this.trigger('reset');
        $this.find(".err-email").html('');
        }
    }
    });
});

    // lory slider responsive
    $(".gjs-lory-frame").each(function() {
        let id = $(this).parent().attr('id');
        $("#"+id).attr('style', 'width: 100% !important');
    });
});
</script>
<!--End of subscribe functionality-->

<!--Start of Tawk.to script-->
@if ($bs->is_tawkto == 1)
{!! $bs->tawk_to_script !!}
@endif
<!--End of Tawk.to script-->

<!--Start of AddThis script-->
@if ($bs->is_addthis == 1)
{!! $bs->addthis_script !!}
@endif
<!--End of AddThis script-->