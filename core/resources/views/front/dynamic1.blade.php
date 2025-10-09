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

  <!--   about company section start   -->
  <div class="about-company-section pt-115 pb-80">
     <div class="container">
        <div class="row">
           <div class="col-lg-12">
             {!! replaceBaseUrl($page->body) !!}
           </div>
        </div>
     </div>
  </div>
  <!--   about company section end   -->
@endsection
