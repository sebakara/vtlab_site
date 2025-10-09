<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Add Package Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="ajaxForm" class="modal-form" action="{{ route('admin.package.store_category', ['language' => request()->input('language')]) }}" method="post">
          @csrf
          <div class="form-group">
              <label for="">Language*</label>
              <select name="language_id" class="form-control">
                  <option value="" selected disabled>Select a Language</option>
                  @foreach ($langs as $lang)
                  <option value="{{$lang->id}}">{{$lang->name}}</option>
                  @endforeach
              </select>
              <p id="errlanguage_id" class="mt-1 mb-0 text-danger em"></p>
          </div>

          <div class="form-group">
            <label for="">Name*</label>
            <input type="text" class="form-control" name="name" placeholder="Enter Category Name">
            <p id="errname" class="mt-1 mb-0 text-danger em"></p>
          </div>

          <div class="form-group">
            <label for="">Status*</label>
            <select name="status" class="form-control ltr">
              <option selected disabled>Select a Status</option>
              <option value="1">Active</option>
              <option value="0">Deactive</option>
            </select>
            <p id="errstatus" class="mt-1 mb-0 text-danger em"></p>
          </div>

          <div class="form-group">
            <label for="">Serial Number*</label>
            <input type="number" class="form-control ltr" name="serial_number" placeholder="Enter Category Serial Number">
            <p id="errserial_number" class="mt-1 mb-0 text-danger em"></p>
            <p class="text-warning mt-2">
              <small>The higher the serial number is, the later the category will be shown.</small>
            </p>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Close
        </button>
        <button id="submitBtn" type="button" class="btn btn-primary">
          Save
        </button>
      </div>
    </div>
  </div>
</div>
