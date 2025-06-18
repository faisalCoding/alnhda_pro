@extends('layouts.admin')


@section('main')
    <div class=" w-full bg-emerald-50 flex flex-col ">

        @livewire('admin.projects')
        {{-- the footer is insid the layouts.admin --}}
    </div>
@endsection
