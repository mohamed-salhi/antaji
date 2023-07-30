<div class="chat">
    <div class="auto-load text-center">
        <svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
             x="0px" y="0px" height="60" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
                <path fill="#000"
                      d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
                    <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s"
                                      from="0 50 50" to="360 50 50" repeatCount="indefinite"/>
                </path>
            </svg>
    </div>
    <div class="chat-history">

        <ul class="chat-admins{{@$chat->uuid}}">

            @if(isset($chat->message[0]))
                @for($i=count(@$chat->message)-1;$i>=0;$i--)
                    @if(@$chat->message[$i]->status=="admin")
                        @if(@$chat->message[$i]->type==\App\Models\Message::TEXT)
                            <li class="clearfix">
                                <div class="message-data">
                                    <span
                                        class="message-data-time">{{$chat->message[$i]->created_at->diffForHumans()}}</span>
                                </div>
                                <div class="message my-message"> {{$chat->message[$i]->content}}</div>
                            </li>
                        @elseif($chat->message[$i]->type==\App\Models\Message::IMAGE)
                            <img id="flag"
                                 src="{{$chat->message[$i]->content}}"
                                 alt=""/>
                        @elseif($chat->message[$i]->type==\App\Models\Message::VOICE)
                            <audio controls>
                                <source src="{{$chat->message[$i]->content}}" type="audio/ogg">
                                <source src="{{$chat->message[$i]->content}}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        @endif
                    @else

                        <li class="clearfix">
                            <div class="message-data text-right">
                                <span
                                    class="message-data-time">{{$chat->message[$i]->created_at->diffForHumans()}}</span>
                                <img
                                    src="{{$chat->image}}"
                                    alt="avatar">
                            </div>
                            <div class="message-data text-right">

                                @if($chat->message[$i]->type==\App\Models\Message::TEXT)
                                    <div class="message other-message float-right">
                                        {{$chat->message[$i]->message}}
                                    </div>
                                @elseif($chat->message[$i]->type==\App\Models\Message::IMAGE)
                                    <img
                                        src="{{$chat->message[$i]->content}}"
                                        height="100" width="200">
                                @elseif($chat->message[$i]->type==\App\Models\Message::VOICE)
                                    <audio controls>
                                        <source src="{{$chat->message[$i]->content}}" type="audio/ogg">
                                        <source src="{{$chat->message[$i]->content}}" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                @endif
                            </div>
                        </li>
                    @endif

                @endfor

            @endif

             @if($seen)
                    <p>seen</p>
                @endif


        </ul>
    </div>
    <form class="chat-admin" method="post" action="{{route('send_msg')}}">
        @csrf
        <div class="chat-message clearfix">
            <div class="input-group mb-0">
                <div class="input-group-prepend">
                    <button>
                                                                <span class="input-group-text"><i
                                                                        class="fa fa-send"></i></span>
                    </button>
                </div>

                <input type="hidden" value="{{@$chat->uuid}}" name="user_uuid">
                <input name="message" id="chat" required type="text"
                       class="form-control"
                       placeholder="Enter text here...">
            </div>
        </div>
    </form>
</div>


<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<script>
    page = 2;
    var user_uuid = "{{@$chat->uuid}}"
    window.scrollTo(0, document.body.scrollHeight);
    window.addEventListener('scroll', function () {
        // Check if the scroll position is at the top of the page
        {{--var ENDPOINT = "{{ url('/'.app()->getLocale()) }}";--}}
        if (window.pageYOffset === 0) {
            // Call your desired function or perform your actions here
            $.ajax({
                url: '{{route('admin.support.read_more',@$chat->uuid??1)}}?page=' + page,

                datatype: "html",
                type: "get",
                beforeSend: function () {
                    $('.auto-load').show();
                },

                success: function (data) {
                    console.log(data)
                    if (data.length == 0) {
                        $('.auto-load').html("لا يوجد رساىل");
                        return;
                    }
                    page = page + 1
                    $(document).ready(function () {
                        $(this).scrollTop(500);
                    });
                    console.log(page)
                    $('.auto-load').hide();

                    $(".chat-admins"+user_uuid).prepend(data);

                },
                error: function (jqXHR, ajaxOptions, thrownError) {
                    console.log('Server error occured');
                }
            })

        }
    });

    var user_uuid = "{{@$chat->uuid}}"

    $(document).ready(function () {
        window.Echo.private("msg." + user_uuid)
            .listen('.msg', (e) => {
                console.log('chat-admins'+e[3])
                if (e[2] == 'admin') {
                    if (e[5] == {{\App\Models\Message::TEXT}}) {
                        $('.chat-admins'+e[3]).append(`
                             <div class="messag e-data">
                                  <span class="message-data-time">منذ 1 ثانية</span>
                             </div>
                                           <div class="message my-message"> ${e[0]}</div>
                            `)
                    } else if (e[5] == {{\App\Models\Message::IMAGE}}) {
                        $('.chat-admins'+e[3]).append(`
                                        <img
                                        src="${e[4]}"
                                        height="100" width="200">
                                 `)
                    } else if (e[5] == {{\App\Models\Message::IMAGE}}) {
                        $('.chat-admins'+e[3]).append(`
                                  <audio controls>
                                        <source src="${e[0]}" type="audio/ogg">
                                        <source src="${e[0]}" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                 `)
                    }

                }
                else {
                    if (e[5] == {{\App\Models\Message::TEXT}}) {
                        $('.chat-admins'+e[3]).append(`
                                 <li class="clearfix">
                            <div class="message-data text-right">
                                <span class="message-data-time">منذ 1 ثانية</span>
                                <img
                                    src=" ${e[4]}"
                                    alt="avatar">
                            </div>
                            <div class="message-data text-right">
<div class="message other-message float-right">
${e[0]}
                        </div>

`)
                    } else if (e[5] == {{\App\Models\Message::IMAGE}}) {
                        $('.chat-admins'+e[3]).append(`
                                 <li class="clearfix">
                            <div class="message-data text-right">
                                <span class="message-data-time">منذ 1 ثانية</span>
                                <img
                                    src=" ${e[4]}"
                                    alt="avatar">
                            </div>
                            <div class="message-data text-right">
         <img
                        src="${e[0]}"
                                        height="100" width="200">

`)
                    } else if (e[5] == {{\App\Models\Message::VOICE}}) {
                        $('.chat-admins'+e[3]).append(`
                                 <li class="clearfix">
                            <div class="message-data text-right">
                                <span class="message-data-time">منذ 1 ثانية</span>
                                <img
                                    src=" ${e[4]}"
                                    alt="avatar">
                            </div>
                            <div class="message-data text-right">
            <audio controls>
                                        <source src="${e[0]}" type="audio/ogg">
                                        <source src="${e[0]}" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>

`)
                    }
                }
            })


        $('.chat-admin').on('submit', function (event) {
            $('.search_input').val("").trigger("change")
            event.preventDefault();
            var data = new FormData(this);
            let url = $(this).attr('action');
            var method = $(this).attr('method');
            $.ajax({
                type: method,
                cache: false,
                contentType: false,
                processData: false,
                url: url,
                data: data,
                beforeSend: function () {
                },
                success: function (result) {
                    $('#chat').val('')
                    $(document).scrollTop($(document).height());
                },
                error: function (data) {
                    console.log('err')
                }
            });
        });

    })
</script>

