@php

if ($be->theme_version == 'default') {
    $type = 'themeHome'; $theme = 'default';
}
elseif ($be->theme_version == 'dark') {
    $type = 'themeHome'; $theme = 'dark';
}
elseif ($be->theme_version == 'gym') {
    $type = 'themeHome'; $theme = 'gym';
}
elseif ($be->theme_version == 'car') {
    $type = 'themeHome'; $theme = 'car';
}
elseif ($be->theme_version == 'cleaning') {
    $type = 'themeHome'; $theme = 'cleaning';
}
elseif ($be->theme_version == 'construction') {
    $type = 'themeHome'; $theme = 'construction';
}
elseif ($be->theme_version == 'logistic') {
    $type = 'themeHome'; $theme = 'logistic';
}
elseif ($be->theme_version == 'lawyer') {
    $type = 'themeHome'; $theme = 'lawyer';
}
elseif ($be->theme_version == 'ecommerce') {
    $type = 'themeHome'; $theme = 'ecommerce';
}
@endphp

<!-- Modal -->
<div class="modal fade" id="pbLangModal" tabindex="-1" role="dialog" aria-labelledby="pbLangModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-white" id="exampleModalLongTitle">Choose a Language</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="{{route('admin.pagebuilder.content')}}" id="redirectForm" target="_blank">
            <input type="hidden" name="type" value="{{$type}}">
            <input type="hidden" name="theme" value="{{$theme}}">
            <select name="language" required class="form-control" required>
                @foreach ($langs as $lang)
                    <option value="{{$lang->code}}">{{$lang->name}}</option>
                @endforeach
            </select>
            <div class="text-center">
                <button type="submit" class="btn btn-success btn-sm mt-2" onclick="document.getElementById('redirectForm').submit();"><strong>Proceed</strong></button>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>


