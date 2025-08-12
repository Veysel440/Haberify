<h2>Haftalık Özet</h2>
<ul>
    @foreach($articles as $a)
    <li><a href="{{ url('/news/'.$a['slug']) }}">{{ $a['title'] }}</a></li>
    @endforeach
</ul>
