@extends('layouts.master')

@section('title', $title_singular)

@section('content_header')
    @component('components.content_header')
        @slot('page_title')
            {{ $title_singular }}
        @endslot

        @slot('breadcrumb')
            {{ Breadcrumbs::render('aliexpress_settings') }}
        @endslot
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @if(count($settings))
            <div class="col-md-10">
                <div class="row">
                    @if (\Modules::isModuleActive('corals-marketplace'))
                        @foreach(user()->stores as $store)
                            <div class="col-md-6">
                                @component('components.box')
                                    @slot('box_title')
                                        {{ $store->name }}
                                    @endslot
                                    <div class="tab-content">
                                        <hr/>
                                        {!! CoralsForm::link(url("aliexpress/load-categories/".$store->hashed_id), trans('Aliexpress::labels.load_categories'),
                                            ['class'=>'btn btn-primary btn-sm','data' => [
                                                'action' => 'post',
                                                'confirmation' => trans('Aliexpress::labels.load_categories_confirmation'),
                                            ]])
                                        !!}
                                        <hr/>
                                        {!! CoralsForm::openForm(null,['url' => url('aliexpress/settings/'.$store->hashed_id),'method'=>'POST']) !!}
                                        @foreach($settings as $setting_key => $setting)
                                            @foreach($setting['settings'] as $key => $setting)
                                                @if($setting['type'] == 'text')
                                                    {!! CoralsForm::text($setting_key.'_'.$key,$setting['label'],$setting['required'],$store->getSettingValue($setting_key.'_'.$key,''),$setting['attributes']) !!}
                                                @elseif($setting['type'] == 'boolean')
                                                    {!! CoralsForm::boolean($setting_key.'_'.$key,$setting['label'],false,$store->getSettingValue($setting_key.'_'.$key,'true')) !!}
                                                @endif
                                            @endforeach

                                            {!! CoralsForm::formButtons(trans('Corals::labels.save',['title' => $title_singular]),[],['href'=>url('dashboard')]) !!}

                                            {!! CoralsForm::closeForm() !!}
                                        @endforeach
                                    </div>
                                @endcomponent
                            </div>
                        @endforeach
                    @else
                        <div class="col-md-6">
                            @component('components.box')
                                <ul class="nav nav-tabs">
                                    @foreach($settings as $setting_key => $setting)
                                        <li class="nav-item {{ $loop->first ? 'active':'' }}">
                                            <a data-toggle="tab" href="#{{ $setting_key }}"
                                               class="{{ $loop->first ? 'active':'' }} nav-link">{{  $setting['name'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                                <hr/>
                                {!! CoralsForm::link(url("aliexpress/load-categories"), trans('Aliexpress::labels.load_categories'),
                                    ['class'=>'btn btn-primary btn-sm','data' => [
                                        'action' => 'post',
                                        'confirmation' => trans('Aliexpress::labels.load_categories_confirmation'),
                                    ]])
                                !!}
                                <hr/>
                                @foreach($settings as $setting_key => $setting)
                                    <div id="{{ $setting_key }}"
                                         class="tab-pane {{ $loop->first ? 'active':'' }} ">
                                        {!!   CoralsForm::openForm() !!}
                                        @foreach($setting['settings'] as $key => $setting)
                                            @if($setting['type'] == 'text')
                                                {!! CoralsForm::text($setting_key.'_'.$key,$setting['label'],$setting['required'],\Settings::get($setting_key.'_'.$key,''),$setting['attributes']) !!}
                                            @elseif($setting['type'] == 'boolean')
                                                {!! CoralsForm::boolean($setting_key.'_'.$key,$setting['label'],false,\Settings::get($setting_key.'_'.$key,'true')) !!}
                                            @endif
                                        @endforeach
                                        {!! CoralsForm::formButtons(trans('Corals::labels.save',['title' => $title_singular]),[],['href'=>url('dashboard')]) !!}

                                        {{ CoralsForm::closeForm() }}
                                    </div>
                                @endforeach
                            @endcomponent
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="col-md-4">
                <div class="alert alert-warning">
                    <h4>@lang('Aliexpress::labels.form.no_setting_found')</h4>
                </div>
            </div>
        @endif
    </div>
@endsection
