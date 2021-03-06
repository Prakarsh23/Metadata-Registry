@extends('backpack::layout')


@section('page-title')
    {{ Translate::recursive('flows.gsimport.worksheets.title') }}
@endsection


@section('content-title')
    {{ Translate::recursive('flows.gsimport.worksheets.title') }}
@endsection


@section('content')

    {!! Form::open(array('route' => array('flows.step', $flow->id, 'worksheets'), 'method' => 'POST', 'class' => 'form-horizontal', 'role' => 'form')) !!}

        <div class="row">
            <div class="well well-large col-lg-12">
                Your flow step content goes here
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="pull-right">
                    <button type="submit" class="btn btn-lg btn-primary">{{ Translate::recursive('wizard::flows.next') }}</button>
                </div>
            </div>
        </div>

    {!! Form::close() !!}

@endsection
