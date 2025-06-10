@extends('layouts.guest')

@section('header')
    {{-- override header to hid it --}}
@stop

@section('main')
    <div class=" w-full bg-emerald-50 flex flex-col ">

        @include('partials.header')
        @livewire('section-project')
        @include('partials.section_blogs')
        {{-- the footer is insid the layouts.guest --}}
    </div>
@endsection
