<div class="selected-user">
    <span>@lang('To'): <span class="name">{{@$chat->name}}</span></span>
</div>
<div class="chat-container">

    <ul class="chat-admins{{@$chat->uuid}} chat-box chatContainerScroll">

        @if(isset($chat->message[0]))
            @for($i=count(@$chat->message)-1;$i>=0;$i--)

                @if(@$chat->message[$i]->status=="admin")
                    <li class="chat-left">

                        <div class="chat-hour">{{$chat->message[$i]->created_at->diffForHumans()}} <span class="fa fa-check-circle"></span></div>

                        <br>
                        @if(@$chat->message[$i]->type==\App\Models\Message::TEXT)

                            <div class="chat-text">
                                {{$chat->message[$i]->content}}
                            </div>

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
                    </li>
                @else

                    <li class="chat-right">
                        <div class="chat-hour">{{$chat->message[$i]->created_at->diffForHumans()}} <span class="fa fa-check-circle"></span></div>


                    @if($chat->message[$i]->type==\App\Models\Message::TEXT)
                            <div class="message chat-text">
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
                        <div class="chat-avatar">
                            <img class="image_main" src="{{$chat->image}}" alt="Retail Admin">
                            <div class="chat-name">{{$chat->name}}</div>
                        </div>
                    </li>

                @endif

            @endfor

        @endif
    </ul>


    <form class="chat-admin" method="post" action="{{route('send_msg')}}">
        @csrf
        <input type="hidden" value="{{@$chat->uuid}}" name="user_uuid">

        <div class="form-group mt-3 mb-0">
            <div class="row">

                    <textarea class="form-control col-10" id="chat" rows="1" name="message"
                              placeholder="@lang('Type your message here...')"></textarea>
                <button class=" col-1 reply-recording"><i class="fa fa-microphone fa-2x" aria-hidden="true"></i>
                </button>

                <button class=" col-1 reply-send"><i class="fa fa-send fa-2x" aria-hidden="true"></i></button>

            </div>
        </div>
    </form>
</div>


<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             JS-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<script>
    page = 2;
  user_uuid = "{{@$chat->uuid}}"
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

                    $(".chat-admins" + user_uuid).prepend(data);

                },
                error: function (jqXHR, ajaxOptions, thrownError) {
                    console.log('Server error occured');
                }
            })

        }
    });

    {{--user_uuid = "{{@$chat->uuid}}"--}}
    // $('body').on('document')
    $('body').ready(function () {
        var i=1
        window.Echo.channel("msg." + user_uuid)

            .listen('.msg', (e) => {
                i++

                console.log(e)
                console.log('chat-admins' + e['user_uuid'])

                if (!e['is_me']) {

                    --i
                    if (e['type'] == {{\App\Models\Message::TEXT}}) {
                        $('.chat-admins' + e['user_uuid']).append(`
<li class="chat-left">
<div class="chat-hour">${e['created_at']} <span class="fa fa-check-circle"></span></div>
<div class="chat-text">
${e['content']}
</div>
</li>



                            `)
                        console.log('itsmee')
                    } else if (e['type'] == {{\App\Models\Message::IMAGE}}) {
                        $('.chat-admins' + e['user_uuid']).append(`


<li class="chat-left">
<div class="chat-hour">${e['created_at']} <span class="fa fa-check-circle"></span></div>
<div class="chat-text">
<img
                                        src="${e['content']}"
                                        height="100" width="200">
</div>
</li>
                                 `)
                    } else if (e['type'] == {{\App\Models\Message::VOICE}}) {
                        $('.chat-admins' + e['user_uuid']).append(`



<li class="chat-left">
<div class="chat-hour">${e['created_at']} <span class="fa fa-check-circle"></span></div>
<div class="chat-text">
    <audio controls>
                                        <source src="${e['content']}" type="audio/ogg">
                                        <source src="${e['content']}" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>

</div>
</li>


                                 `)
                    }

                } else {
                    --i
                    if (e['type'] == {{\App\Models\Message::TEXT}}) {
                        $('.chat-admins' + e['user_uuid']).append(`
<li class="chat-right">
<div class="chat-hour">${e['created_at']}<span class="fa fa-check-circle"></span></div>
<div class="chat-text">${e['content']}</div><div class="chat-avatar">
<img class="image_main" src="${e['user_image']}" alt="Retail Admin"><div class="chat-name">${e['user_name']}</div></div></li>

`)
                    } else if (e['type'] == {{\App\Models\Message::IMAGE}}) {
                        $('.chat-admins' + e['user_uuid']).append(`
<li class="chat-right">
<div class="chat-hour">${e['created_at']}<span class="fa fa-check-circle"></span></div>
<div class="chat-text"><img
                        src="${e['content']}"
                                        height="100" width="200"></div>
<div class="chat-avatar">
<img class="image_main" src="${e['user_image']}" alt="Retail Admin"><div class="chat-name">${e['user_name']}</div></div></li>
`)
                    } else if (e['type'] == {{\App\Models\Message::VOICE}}) {
                        $('.chat-admins' + e['user_uuid']).append(`

<li class="chat-right">
<div class="chat-hour">${e['created_at']}<span class="fa fa-check-circle"></span></div>
<div class="chat-text">    <audio controls>
                                        <source src="${e['content']}" type="audio/ogg">
                                        <source src="${e['content']}" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio></div>
<div class="chat-avatar">
<img class="image_main" src="${e['user_image']}" alt="Retail Admin"><div class="chat-name">${e['user_name']}</div></div></li>


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

