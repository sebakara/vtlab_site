<div class="well py-5 text-center bg-light" style="direction: ltr;">
    <h3>Use Page Builder to decorate content of the <strong><span class="text-danger">{{ucfirst($be->theme_version)}}</span> theme & <span class="text-danger">{{$currentLang->name}}</span> language</strong></h3><br>
    <a href="{{route('admin.pagebuilder.content', ['type' => 'themeHome', 'theme' => "$be->theme_version", 'language' => $currentLang->code])}}" target="_blank">Click Here</a>
    <p class="mt-4">(You can also enable / disable the Page Builder)</p>
</div>
