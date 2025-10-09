@extends("front.$version.layout")

@section('pagename')
  - {{__('All Articles')}}
@endsection

@section('breadcrumb-title', convertUtf8($bex->knowledgebase_title))
@section('breadcrumb-subtitle', convertUtf8($bse->knowledgebase_subtitle))
@section('breadcrumb-link', __('Knowledgebase'))

@section('content')

  {{-- article category and list start --}}
  <section class="knowledge-list-section">
    <div class="container">
      <div class="row">
        @if (count($article_categories) == 0)
          <div class="col-md-12">
            <div class="bg-light py-5">
              <h3 class="text-center">{{__('NO ARTICLE FOUND')}}</h3>
            </div>
          </div>
        @else
          @foreach ($article_categories as $article_category)
            <div class="col-lg-4 col-md-6 col-sm-12">
              <div class="knowledge-box mb-40">
                <div class="title">
                  <h3><a href="#">{{ $article_category->name }}</a></h3>
                </div>
                @php
                $articles = App\Article::where('article_category_id', $article_category->id)
                  ->orderBy('id', 'desc')
                  ->get();
                $count = 0;
                @endphp
                <ul class="list">
                  @foreach ($articles as $article)
                    <li><a href="{{ route('front.knowledgebase_details', ['slug' => $article->slug]) }}">
                        {{strlen($article->title) > 30 ? mb_substr($article->title,0,30,'utf-8') . '...' : $article->title}}
                    </a></li>
                    @php $count = $count + 1; @endphp
                  @endforeach
                </ul>
                <a
                  class="btn_link"
                >{{ $count . ' ' . __('Articles in this category') }}</a>
              </div>
            </div>
          @endforeach
        @endif
      </div>
    </div>
  </section>
  {{-- article category and list end --}}
@endsection
