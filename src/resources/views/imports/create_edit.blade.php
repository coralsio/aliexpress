@extends('layouts.crud.create_edit')

@section('content_header')
    @component('components.content_header')
        @slot('page_title')
            {{ $title_singular }}
        @endslot
        @slot('breadcrumb')
            {{ Breadcrumbs::render('alix_import_create_edit') }}
        @endslot
    @endcomponent
@endsection

@section('content')
    @parent
    <div class="row">
        <div class="col-md-12">
            @component('components.box')
                {!! CoralsForm::openForm($import) !!}
                <div class="row">
                    <div class="col-md-6">
                        {!! CoralsForm::text('title','Aliexpress::attributes.import.title',true,$import->title,[]) !!}
                        {!! CoralsForm::radio('status','Corals::attributes.status',true, trans('Aliexpress::attributes.import.status_options'),$import->exists ? $import->status : 'pending') !!}
                        {!! CoralsForm::select('categories[]', 'Aliexpress::attributes.import.categories', \Aliexpress::getAliexpressCategories(), false, null,
                                ['multiple' => true, 'help_text' => ''], 'select2') !!}

                        @if (\Modules::isModuleActive('corals-marketplace'))
                            {!! \Store::getStoreFields($import) !!}
                        @endif
                    </div>

                    <div class="col-md-6">
                        {!! CoralsForm::select('keywords[]','Aliexpress::attributes.import.keywords', $import->exists && $import->keywords ? array_combine( $import->keywords  , $import->keywords  ): [] ,true,$import->exists ?  $import->keywords : [] ,['class'=>'tags','multiple'=>true], 'select2') !!}
                        {!! CoralsForm::number('image_count','Aliexpress::attributes.import.image_count',true,null,['help_text'=>'','min'=>0]) !!}
                        {!! CoralsForm::number('max_result_pages','Aliexpress::attributes.import.max_result_pages',true,null,['help_text'=>'','min'=>0]) !!}
                        {!! CoralsForm::textarea('notes', 'Aliexpress::attributes.import.notes', false, null,['rows'=>4]) !!}
                    </div>

                    {!! CoralsForm::customFields($import) !!}
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {!! CoralsForm::formButtons() !!}
                    </div>
                </div>
                {!! CoralsForm::closeForm($import) !!}
            @endcomponent
        </div>
    </div>
@endsection

@section('js')
@endsection
