<li class="nav-item dropdown  dropdown-notification">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        <span class="badge badge-warning navbar-badge" id="count_not">{{$count}}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <div class="ex1">
            <div id="not">

            </div>
            @foreach($not as $item)
                <div style={{(!$item->show)?"background-color:#1AB7EA":""}}>
                    <div class="dropdown-divider" ></div>
                    <a href="{{$item->link}}" class="dropdown-item"  >
                        <i class="fas fa-envelope mr-2"></i> {{$item->content}}
                        <span class="float-right text-secondary text-black-50 text-sm">{{$item->created_at->diffForHumans()}}</span>
                    </a>
                </div>
            @endforeach
            <div id="not-seeall">

            </div>
        </div>
        <div class="dropdown-divider"></div>
    </div>
</li>
