@extends("front.$version.layout")

@section('pagename')
- {{__('Knowledgebase')}} - {{ convertUtf8($details->title) }}
@endsection

@section('meta-keywords', "$details->meta_keywords")
@section('meta-description', "$details->meta_description")

@section('breadcrumb-title', convertUtf8($bex->knowledgebase_details_title))
@section('breadcrumb-subtitle', strlen($details->title) > 30 ? mb_substr($details->title,0,30,'utf-8') . '...' : $details->title)
@section('breadcrumb-link', __('Knowledgebase'))

@section('content')

{{-- article details strat --}}
<section class="knowledge-requirements-section pt-140">
  <div class="container">
    <div class="row mb-60">
      <div class="col-lg-4">
        <div class="requirements-nav">
          <div
            class="accordion"
            id="accordionExample"
          >
            @foreach ($article_categories as $article_category)
            <div class="card">
              <div
                class="card-header"
                id="headingone"
              >
                <a
                  class="collapsed"
                  href="#"
                  data-toggle="collapse"
                  data-target="#{{ 'collapse' . $article_category->id }}"
                  aria-expanded="{{ $details->article_category_id == $article_category->id ? 'true' : 'false' }}"
                  aria-controls="collapseone"
                >
                  {{ $article_category->name }}<span class="toggle_btn"></span>
                </a>
              </div>
              <div
                id="{{ 'collapse' . $article_category->id }}"
                class="collapse {{ $details->article_category_id == $article_category->id ? 'show' : '' }}"
                aria-labelledby="headingOne"
                data-parent="#accordionExample"
                style=""
              >
                <div class="card-body">
                  @php
                  $articles = App\Article::where('article_category_id', $article_category->id)
                  ->orderBy('id', 'desc')
                  ->get();
                  @endphp

                  @if (count($articles) == 0)
                  <ul class="list">
                    <li><a href="#">{{ '0 ' . __('Articles in this category') }}</a></li>
                  </ul>
                  @else
                  <ul class="list">
                    @foreach ($articles as $article)
                    <li>
                      <a href="{{ route('front.knowledgebase_details', ['slug' => $article->slug]) }}" class="{{$article->id == $details->id ? 'active' : ''}}">
                        {{ strlen($article->title) > 30 ? mb_substr($article->title, 0, 30, 'utf-8') . '...' : $article->title }}
                      </a>
                    </li>
                    @endforeach
                  </ul>
                  @endif
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="requirement-wrapper">
          <div class="requirement-wrapper-content">
            <div class="title">
              <h3>{{ $details->title }}</h3>
            </div>
            <p>{!! replaceBaseUrl($details->content) !!}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="post-share-date">
      <div class="row">
        <div class="col-lg-6 col-md-6">
          <span class="post-date">
            @php $date = Carbon\Carbon::parse($details->created_at); @endphp
            <i class="fa fa-clock-o"></i>{{ __('Created') . ' : ' . $date->translatedFormat('jS F, Y') }}
          </span>
        </div>
        <div class="col-lg-6 col-md-6">
          <ul class="share-list">
            <li><span>{{ __('Share') }}:</span></li>
            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
            <li><a href="#"><i class="fab fa-pinterest-p"></i></a></li>
            <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>
{{-- article details end --}}
@endsection
