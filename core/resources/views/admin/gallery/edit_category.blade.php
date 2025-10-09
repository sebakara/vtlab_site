<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Update Gallery Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="ajaxEditForm" class="modal-form" action="{{ route('admin.gallery.update_category') }}" method="post">
          @csrf
          <input type="hidden" name="categoryId" id="inid">

          <div class="form-group">
            <label for="">Category Name*</label>
            <input type="text" id="inname" class="form-control" name="name" placeholder="Enter Category Name">
            <p id="eerrname" class="mt-1 mb-0 text-danger em"></p>
          </div>

          <div class="form-group">
            <label for="">Category Status*</label>
            <select name="status" id="instatus" class="form-control">
              <option disabled>Select a Status</option>
              <option value="1">Active</option>
              <option value="0">Deactive</option>
            </select>
            <p id="eerrstatus" class="mt-1 mb-0 text-danger em"></p>
          </div>

          <div class="form-group">
            <label for="">Category Serial Number*</label>
            <input type="number" id="inserial_number" class="form-control ltr" name="serial_number" placeholder="Enter Category Serial Number">
            <p id="eerrserial_number" class="mt-1 mb-0 text-danger em"></p>
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
        <button id="updateBtn" type="button" class="btn btn-primary">
          Update
        </button>
      </div>
    </div>
  </div>
</div>
