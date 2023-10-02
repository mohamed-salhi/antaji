<div class="selected-user">
{{--    <span>@lang('To'): <span class="name">{{@$conversation->name}}</span></span>--}}
</div>
<div class="chat-container">

    <ul class="chat-admins{{@$conversation->uuid}} chat-box chatContainerScroll">

        @if(isset($conversation->chat[0]))
            @for($i=count(@$conversation->chat)-1;$i>=0;$i--)

                @if(@$conversation->chat[$i]->user->uuid==$uuid_user)
                    <li class="chat-left">

                        <div class="chat-hour">{{$conversation->chat[$i]->created_at->diffForHumans()}} <span class="fa fa-check-circle"></span></div>

                        <br>
                        @if(@$conversation->chat[$i]->type==\App\Models\Chat::TEXT)

                            <div class="chat-text">
                                {{$conversation->chat[$i]->content}}
                            </div>

                        @elseif($conversation->chat[$i]->type==\App\Models\Chat::IMAGE)
                            <img id="flag"
                                 src="{{$conversation->chat[$i]->content}}"
                                 alt=""/>
                        @elseif($conversation->chat[$i]->type==\App\Models\Chat::VOICE)
                            <audio controls>
                                <source src="{{$conversation->chat[$i]->content}}" type="audio/ogg">
                                <source src="{{$conversation->chat[$i]->content}}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        @endif
                        <div class="chat-avatar">
                            <img class="image_main" src="{{@$conversation->chat[$i]->user->image}}" alt="Retail Admin">
                            <div class="chat-name">{{@$conversation->chat[$i]->user->name}}</div>
                        </div>
                    </li>
                @else

                    <li class="chat-right">
                        <div class="chat-hour">{{$conversation->chat[$i]->created_at->diffForHumans()}} <span class="fa fa-check-circle"></span></div>


                    @if($conversation->chat[$i]->type==\App\Models\Chat::TEXT)
                            <div class="message chat-text">
                                {{$conversation->chat[$i]->content}}
                            </div>
                        @elseif($chat->message[$i]->type==\App\Models\Chat::IMAGE)

                              <img
                                  src="{{$conversation->chat[$i]->content}}"
                                  height="100" width="200">

                        @elseif($conversation->chat[$i]->type==\App\Models\Chat::VOICE)
                            <audio controls>
                                <source src="{{$conversation->chat[$i]->content}}" type="audio/ogg">
                                <source src="{{$conversation->chat[$i]->content}}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        @endif
                        <div class="chat-avatar">
                            <img class="image_main" src="{{@$conversation->chat[$i]->user->image}}" alt="Retail Admin">
                            <div class="chat-name">{{$conversation->chat[$i]->user->name}}</div>
                        </div>
                    </li>

                @endif

            @endfor

        @endif
    </ul>


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

</script>

