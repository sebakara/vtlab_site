@if (!request()->routeIs('admin.file-manager'))
<footer class="footer">
  <div class="container-fluid">
    <div class="d-block mx-auto">
      {!! replaceBaseUrl($bs->copyright_text) !!}
    </div>
  </div>
</footer>
@endif
