<ul>
    <?php $counter=0; ?>    
    	@foreach($user->notifications()->orderBy('created_at')->get()->reverse() as $notification)
    		<?php
                if($counter >= $max) break;
                else $counter++;
                $info_array = $notification->render(); 
            ?>
    		@if($info_array[0] == "photo_liked")
                @if($notification->deleted_at != null) <?php continue; ?> @endif
    			<div id={{$notification->id}} class="notes {{$notification->id}}<?php if($notification->read_at == null) echo ' not-read'?>" >
                    <li>
                        <div class="read-button" title="Marcar como lida" onclick="markRead(this);"></div>
                        <div class="notification-container" onclick="markRead(this);">
                            <a href={{"photos/" . $info_array[2]}}><img class="mini" src={{"/arquigrafia-images/" . $info_array[2] . "_home.jpg"}}></a>
                            @if($info_array[6] == null)
                            <a href={{"users/" . $info_array[5]}}>{{$info_array[1]}}</a>{{" curtiu sua " }} <a href={{"photos/" . $info_array[2]}}>{{"foto"}}</a>{{"."}}
                            @else
                            <?php 
                                $users = explode(":", $info_array[6]);
                                $users_size = count($users) - 1;
                                for ($i = 0; $i < $users_size; $i++) {
                                    $user[$i] = User::find($users[$i+1]);
                                }
                            ?>
                            @if($users_size < 2)
                            <a href={{"users/" . $info_array[5]}}>{{$info_array[1]}}</a>{{" e "}}<a href={{"users/" . $user[0]->id}}>{{$user[0]->name}}</a>{{" curtiram sua " }} <a href={{"photos/" . $info_array[2]}}>{{"foto"}}</a>{{"."}}
                            @else
                            <a href={{"users/" . $info_array[5]}}>{{$info_array[1]}}</a>{{" e mais "}}<a class="fancybox" href={{"#users-from-note-" . $notification->id}}>{{$users_size . " pessoas"}}</a>{{" curtiram sua "}}<a href={{"photos/" . $info_array[2]}}>{{"foto"}}</a>{{"."}}
                            <div class="additional-users" id={{"users-from-note-" . $notification->id}}>
                                <ul>
                                    @for($i = 0; $i < $users_size; $i++)
                                    <li class="additional-user"><a href={{"users/" . $user[$i]->id}}>{{$user[$i]->name}}</a></li>
                                    @endfor
                                </ul>
                            </div>
                            @endif
                            @endif
                            </br>
                            <p class="date-and-time">{{"$info_array[3], às $info_array[4]."}}</p>
                            <a class="link-block" href={{"photos/" . $info_array[2]}}></a>
                        </div>
                    </li>
                </div>
    		@elseif($info_array[0] == "comment_liked")
                @if($notification->deleted_at != null) <?php continue; ?> @endif
    			<div id={{$notification->id}} class="notes {{$notification->id}}<?php if($notification->read_at == null) echo ' not-read'?>">
                    <li>
                        <div class="read-button" title="Marcar como lida" onclick="markRead(this);"></div>
                        <div onclick="markRead(this);">
                            <a href={{"photos/" . $info_array[2]}}><img class="mini" src={{"/arquigrafia-images/" . $info_array[2] . "_home.jpg"}}></a>
                            @if($info_array[9] == null)
                            <a href={{"users/" . $info_array[5]}}>{{ $info_array[1]}}</a>{{" curtiu seu "}}<a href={{"photos/" . $info_array[2] . "#" . $info_array[8]}}>{{"comentário"}}</a>{{", na "}}<a href={{"photos/" . $info_array[2]}}>{{"foto"}}</a>{{" de "}}<a href={{"users/" . $info_array[6]}}>{{$info_array[7]}}</a>{{"."}}
                            @else
                            <?php 
                                $users = explode(":", $info_array[9]);
                                $users_size = count($users) - 1;
                                for ($i = 0; $i < $users_size; $i++) {
                                    $user[$i] = User::find($users[$i+1]);
                                }
                            ?>
                            @if($users_size < 2)
                            <a href={{"users/" . $info_array[5]}}>{{ $info_array[1]}}</a>{{" e "}}<a href={{"users/" . $user[0]->id}}>{{$user[0]->name}}</a>{{" curtiram seu "}}<a href={{"photos/" . $info_array[2] . "#" . $info_array[8]}}>{{"comentário"}}</a>{{", na "}}<a href={{"photos/" . $info_array[2]}}>{{"foto"}}</a>{{" de "}}<a href={{"users/" . $info_array[6]}}>{{$info_array[7]}}</a>{{"."}}
                            @else
                            <a href={{"users/" . $info_array[5]}}>{{ $info_array[1]}}</a>{{" e mais "}}<a class="fancybox" href={{"#users-from-note-" . $notification->id}}>{{$users_size . " pessoas"}}</a>{{" curtiram seu "}}<a href={{"photos/" . $info_array[2] . "#" . $info_array[8]}}>{{"comentário"}}</a>{{", na "}}<a href={{"photos/" . $info_array[2]}}>{{"foto"}}</a>{{" de "}}<a href={{"users/" . $info_array[6]}}>{{$info_array[7]}}</a>{{"."}}
                            <div class="additional-users" id={{"users-from-note-" . $notification->id}}>
                                <ul>
                                    @for($i = 0; $i < $users_size; $i++)
                                    <li class="additional-user"><a href={{"users/" . $user[$i]->id}}>{{$user[$i]->name}}</a></li>
                                    @endfor
                                </ul>
                            </div>
                            @endif
                            @endif
                            </br>
                            <p class="date">{{"$info_array[3], às $info_array[4]."}}</p>
                            <a class="link-block" href={{"photos/" . $info_array[2] . "#" . $info_array[8]}}></a>
                        </div>
                    </li>
                </div>
    		@elseif($info_array[0] == "comment_posted")
                @if($notification->deleted_at != null) <?php continue; ?> @endif
    			<div id={{$notification->id}} class="notes {{$notification->id}}<?php if($notification->read_at == null) echo ' not-read'?>">
                    <li>
                        <div class="read-button" title="Marcar como lida"  onclick="markRead(this);"></div>
                        <div onclick="markRead(this);">
                            <a href={{"photos/" . $info_array[2]}}><img class="mini" src={{"/arquigrafia-images/" . $info_array[2] . "_home.jpg"}}></a>
                            @if($info_array[9] == null)
                            <a href={{"users/" . $info_array[5]}}>{{ $info_array[1]}}</a>{{" "}}<a href={{"photos/" . $info_array[2] . "#" . $info_array[8]}}>{{"comentou"}}</a>{{" sua "}}<a href={{"photos/" . $info_array[2]}}>{{"foto"}}</a>{{"."}}
                            @else
                            <?php 
                                $users = explode(":", $info_array[9]);
                                $users_size = count($users) - 1;
                                for ($i = 0; $i < $users_size; $i++) {
                                    $user[$i] = User::find($users[$i+1]);
                                }
                            ?>
                            @if($users_size < 2)
                            <a href={{"users/" . $info_array[5]}}>{{ $info_array[1]}}</a>{{" e "}}<a href={{"users/" . $user[0]->id}}>{{$user[0]->name}}</a>{{" "}}<a href={{"photos/" . $info_array[2] . "#" . $info_array[8]}}>{{"comentaram"}}</a>{{" sua "}}<a href={{"photos/" . $info_array[2]}}>{{"foto"}}</a>{{"."}}
                            @else
                            <a href={{"users/" . $info_array[5]}}>{{ $info_array[1]}}</a>{{" e mais "}}<a class="fancybox" href={{"#users-from-note-" . $notification->id}}>{{$users_size . " pessoas"}}</a>{{" "}}<a href={{"photos/" . $info_array[2] . "#" . $info_array[8]}}>{{"comentaram"}}</a>{{" sua "}}<a href={{"photos/" . $info_array[2]}}>{{"foto"}}</a>{{"."}}
                            <div class="additional-users" id={{"users-from-note-" . $notification->id}}>
                                <ul>
                                    @for($i = 0; $i < $users_size; $i++)
                                    <li class="additional-user"><a href={{"users/" . $user[$i]->id}}>{{$user[$i]->name}}</a></li>
                                    @endfor
                                </ul>
                            </div>
                            @endif
                            @endif
                            </br>
                            <p class="date">{{"$info_array[3], às $info_array[4]."}}</p>
                            <a class="link-block" href={{"photos/" . $info_array[2]}}></a>
                        </div>
                    </li>
                </div>
            @elseif($info_array[0] == "follow")
                @if($notification->deleted_at != null) <?php continue; ?> @endif
                <div id={{$notification->id}} class="notes {{$notification->id}}<?php if($notification->read_at == null) echo ' not-read'?>">
                    <li>
                        <div class="read-button" title="Marcar como lida"  onclick="markRead(this);"></div>
                        <div onclick="markRead(this);">
                            <a href={{"users/" . $info_array[4]}}>
                                @if(User::find($info_array[4])->photo != "")
                                <img class="mini" src="{{ asset(User::find($info_array[4])->photo); }}">
                                @else
                                <img class="mini" src="{{ URL::to("/") }}/img/avatar-48.png">
                                @endif
                            </a>
                            @if($info_array[5] == null)
                            <a href={{"users/" . $info_array[4]}}>{{ $info_array[1]}}</a>{{" começou a seguir você."}}
                            @else
                            <?php 
                                $users = explode(":", $info_array[5]);
                                $users_size = count($users) - 1;
                                for ($i = 0; $i < $users_size; $i++) {
                                    $user[$i] = User::find($users[$i+1]);
                                }
                            ?>
                            @if($users_size < 2)
                            <a href={{"users/" . $info_array[4]}}>{{ $info_array[1]}}</a>{{" e "}}<a href={{"users/" . $user[0]->id}}>{{$user[0]->name}}</a>{{" começaram a seguir você."}}
                            @else
                            <a href={{"users/" . $info_array[4]}}>{{ $info_array[1]}}</a>{{" e mais "}}<a class="fancybox" href={{"#users-from-note-" . $notification->id}}>{{$users_size . " pessoas"}}</a>{{" começaram a seguir você."}}
                            <div class="additional-users" id={{"users-from-note-" . $notification->id}}>
                                <ul>
                                    @for($i = 0; $i < $users_size; $i++)
                                    <li class="additional-user"><a href={{"users/" . $user[$i]->id}}>{{$user[$i]->name}}</a></li>
                                    @endfor
                                </ul>
                            </div>
                            @endif
                            @endif
                            </br>
                            <p class="date">{{"$info_array[2], às $info_array[3]."}}</p>
                            <a class="link-block" href={{"users/" . $info_array[4]}}></a>
                        </div>
                    </li>
                </div>
    		@endif
    	@endforeach
	</ul>