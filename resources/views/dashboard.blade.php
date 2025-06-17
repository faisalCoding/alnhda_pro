@extends('layouts.guest')


@section('main')
    <div class=" w-full bg-emerald-50 flex flex-col ">

        @livewire('projects')
        {{-- the footer is insid the layouts.guest --}}
    </div>
@endsection
