@extends('layouts.master')

@section('side')
    @include('layouts.side.resources')
@endsection

@section('content')

<div class="Setting Box Box--Large Box--bright utility-flex">
  <h1 class="Setting__heading tk-seravek-web">Videos</h1>

<br/>
<div class="panel panel-default">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Title</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($blogs as $blog)
      <tr>
        <td><a href="{{ route('blog.show', $blog->slug) }}">{{ $blog->title->rendered }}</a></td>
        <td>{{ date('l, F j, Y' ,strtotime($blog->date)) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<br/>

<div class="panel panel-default">
  <div class="panel-heading">
      <h3 class="panel-title tk-seravek-web">Blogs</h3>
    </div>
    <div class="panel-body">
      <p><a href="/blog/">View all blogs/videos</a></p>
      <p><a href="{{ route('feed.blog.xml') }}">RSS</a></p>
    </div>
</div>

</div>

@endsection
