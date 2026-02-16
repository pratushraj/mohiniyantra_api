
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Wallet Balance of <span id="showUniqueName"></span> (<span id="showUniqueId"></span>)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <label for=""  class="form-label">Current Balance (in INR)</label>
            <input type="text" class="form-control" value="" id="currentBlnce" disabled>
          </div>
          <div class="col-md-6">
            <label for=""  class="form-label">Add Balance (in INR)</label>
            <input type="text" id="addBalance" class="form-control" placeholder="e.g 120.35" >
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" id="addBalance_btn" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>