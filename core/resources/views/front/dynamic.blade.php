@extends("front.$version.layout")

@section('pagename')
 - {{convertUtf8($page->name)}}
@endsection

@section('meta-keywords', "$page->meta_keywords")
@section('meta-description', "$page->meta_description")

@section('breadcrumb-title', convertUtf8($page->title))
@section('breadcrumb-subtitle', convertUtf8($page->subtitle))
@section('breadcrumb-link', convertUtf8($page->name))

@section('content')
    <style>
        {!! replaceBaseUrl($page->css) !!}
    </style>

    <div class="pagebuilder-content">
        {!! convertHtml($page->html) !!}
    </div>
@endsection
