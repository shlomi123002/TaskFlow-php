@extends('layouts.app')

@section('title','Search')

@section('content')
    <h1>Search Workspaces, Projects, Tasks</h1>

    <form method="GET" action="/search">
        <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search...">
        <button type="submit">Search</button>
    </form>

    @if(!auth()->check())
        <p>Please <a href="/login">login</a> to search your data.</p>
    @else
        <section>
            <h2>Workspaces</h2>
            <ul>
                @foreach($workspaces as $w)
                    <li>{{ $w->name }} ({{ $w->id }})</li>
                @endforeach
            </ul>
        </section>

        <section>
            <h2>Projects</h2>
            <ul>
                @foreach($projects as $p)
                    <li>{{ $p->name }} — workspace: {{ $p->workspace_id }}</li>
                @endforeach
            </ul>
        </section>

        <section>
            <h2>Tasks</h2>
            <ul>
                @foreach($tasks as $t)
                    <li>{{ $t->name }} — project: {{ $t->project_id }}</li>
                @endforeach
            </ul>
        </section>
    @endif

@endsection
